<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

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
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone'    => 'nullable|string|max:20',
            'city'     => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'city'     => $request->city,
        ]);

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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
