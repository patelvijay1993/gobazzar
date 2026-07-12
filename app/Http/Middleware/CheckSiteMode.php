<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSiteMode
{
    public function handle(Request $request, Closure $next)
    {
        // Skip for admin routes, coming-soon, maintenance, and auth routes
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        // Maintenance mode — admin bypasses
        if (Setting::bool('maintenance_mode', false)) {
            if (Auth::check() && Auth::user()->is_admin) {
                return $next($request);
            }
            return response()->view('maintenance', [], 503);
        }

        // Coming soon mode — admin bypasses
        if (Setting::bool('coming_soon_mode', false)) {
            if (Auth::check() && Auth::user()->is_admin) {
                return $next($request);
            }
            return response()->view('coming-soon', [], 200);
        }

        return $next($request);
    }

    private function shouldSkip(Request $request): bool
    {
        $path = $request->path();
        return str_starts_with($path, 'admin') ||
               str_starts_with($path, 'livewire') ||
               in_array($path, ['login', 'logout', 'register', 'coming-soon', 'maintenance']) ||
               str_starts_with($path, '_debugbar');
    }
}
