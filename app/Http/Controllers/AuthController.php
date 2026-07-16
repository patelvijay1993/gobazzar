<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRules;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Auth::check() ? redirect()->route('account') : view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        $request->session()->regenerate();
        ActivityLog::log($request, 'login');

        // Block unverified users only if setting is ON
        if (Setting::bool('email_verification_required', true) && !Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()
                ->withErrors(['email' => 'Please verify your email address before logging in. Check your inbox for the verification link.'])
                ->withInput()
                ->with('unverified_email', $request->email);
        }

        return redirect()->intended(route('account'));
    }

    public function showRegister()
    {
        return Auth::check() ? redirect()->route('account') : view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => ['required', 'confirmed', PasswordRules::min(8)],
            'phone'    => 'nullable|string|max:20',
            'city'     => 'nullable|string|max:100',
        ]);

        $trialPlan     = Setting::get('trial_plan_slug', '');
        $trialMonths   = (int) Setting::get('trial_duration_months', 3);
        $trialExpiresAt = ($trialPlan && $trialMonths > 0)
            ? now()->addMonths($trialMonths)
            : null;

        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'phone'           => $request->phone,
            'city'            => $request->city,
            'plan'            => $trialPlan ?: null,
            'plan_expires_at' => $trialExpiresAt,
        ]);
        ActivityLog::log($request, 'registered', ['meta' => ['name' => $user->name, 'email' => $user->email]]);

        if (Setting::bool('email_verification_required', true)) {
            // Send verification email
            try {
                event(new Registered($user));
            } catch (\Throwable $e) {
                // Email failed but account is created — user can resend from verify page
            }
            return redirect()->route('verification.notice')
                ->with('success', 'Account created! Please check your email to verify your account.');
        }

        // Verification OFF — login immediately
        Auth::login($user);
        return redirect()->route('account')->with('success', 'Welcome to GoBazaar, '.$user->name.'!');
    }

    // ── Email Verification ────────────────────────────────────────

    public function verificationNotice()
    {
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('account');
        }
        return view('auth.verify-email');
    }

    public function verificationVerify(Request $request, $id, $hash)
    {
        // Find user by ID
        $user = User::findOrFail($id);

        // Check hash matches
        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            abort(403, 'Invalid verification link.');
        }

        // Check signature is valid
        if (!$request->hasValidSignature()) {
            return redirect()->route('verification.notice')
                ->withErrors(['email' => 'This verification link has expired. Please request a new one.']);
        }

        // Already verified
        if ($user->hasVerifiedEmail()) {
            Auth::login($user);
            return redirect()->route('account')->with('success', 'Email already verified. Welcome back, '.$user->name.'!');
        }

        // Mark as verified
        $user->markEmailAsVerified();
        event(new Verified($user));

        Auth::login($user);
        return redirect()->route('account')->with('success', 'Email verified! Welcome to GoBazaar, '.$user->name.'!');
    }

    public function verificationSend(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'This email is already verified. You can log in.');
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            return back()->with('success', 'If your email is registered, a verification link has been sent.');
        }

        return back()->with('success', 'Verification link sent! Please check your inbox (and spam folder).');
    }

    // ── Forgot / Reset Password ───────────────────────────────────

    public function showForgotPassword()
    {
        return Auth::check() ? redirect()->route('account') : view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            $status = Password::sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            // Mail delivery failed (misconfigured mailer, sandbox restriction, etc.)
            // Return a generic success to avoid leaking account existence
            return back()->with('success', 'If that email is registered, a password reset link has been sent. Please check your inbox.');
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'If that email is registered, a password reset link has been sent. Please check your inbox.')
            : back()->withErrors(['email' => __($status)])->withInput();
    }

    public function showResetPassword(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', PasswordRules::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                     ->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password reset successfully. You can now log in.')
            : back()->withErrors(['email' => __($status)])->withInput();
    }

    // ── Google OAuth ──────────────────────────────────────────────

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Google login failed. Please try again.']);
        }

        // Find existing user by google_id or email
        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Link google_id if not already linked
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        } else {
            // New user — apply free trial if configured
            $trialPlan      = Setting::get('trial_plan_slug', '');
            $trialMonths    = (int) Setting::get('trial_duration_months', 3);
            $trialExpiresAt = ($trialPlan && $trialMonths > 0)
                ? now()->addMonths($trialMonths)
                : null;

            $user = User::create([
                'name'            => $googleUser->getName(),
                'email'           => $googleUser->getEmail(),
                'google_id'       => $googleUser->getId(),
                'avatar'          => $googleUser->getAvatar(),
                'password'        => Hash::make(Str::random(24)),
                'email_verified_at' => now(), // Google already verified the email
                'plan'            => $trialPlan ?: null,
                'plan_expires_at' => $trialExpiresAt,
            ]);

            ActivityLog::log($request, 'registered', ['meta' => ['name' => $user->name, 'email' => $user->email, 'via' => 'google']]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('account'))->with('success', 'Welcome, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        ActivityLog::log($request, 'logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
