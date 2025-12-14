<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip check if on 2FA routes or login/logout
        if ($request->routeIs('2fa.*') || 
            $request->routeIs('login') || 
            $request->routeIs('logout')) {
            return $next($request);
        }

        // If user is logged in and has a pending 2FA code
        if ($user && $user->two_factor_code && $user->two_factor_expires_at) {
            // Check if code is still valid
            if (now()->lessThan($user->two_factor_expires_at)) {
                return redirect()->route('2fa.show');
            } else {
                // Code expired, clear it and log them out
                $user->two_factor_code = null;
                $user->two_factor_expires_at = null;
                $user->save();
                Auth::logout();
                return redirect()->route('login')->withErrors(['email' => 'Verification code expired.']);
            }
        }

        return $next($request);
    }
}
