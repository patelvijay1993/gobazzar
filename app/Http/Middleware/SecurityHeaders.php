<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        header_remove('X-Powered-By');

        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');
        $response->headers->set('X-XSS-Protection', '0');
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // HSTS — only enforce on production (HTTPS)
        if ($request->secure() || app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content-Security-Policy
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' fonts.googleapis.com cdnjs.cloudflare.com",
            "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com data:",
            "img-src * data: blob:",
            "media-src 'self'",
            "connect-src 'self' api.groq.com *.pusher.com wss://*.pusher.com",
            "frame-src 'self' js.stripe.com",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
