<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // When REQUIRE_EMAIL_VERIFICATION=false in .env, skip the check entirely
        if (!config('auth.require_email_verification', true)) {
            return $next($request);
        }

        $user = $request->user();

        if (!$user || !$user->hasVerifiedEmail()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your email address is not verified.'], 403);
            }
            return Redirect::route('verification.notice');
        }

        return $next($request);
    }
}
