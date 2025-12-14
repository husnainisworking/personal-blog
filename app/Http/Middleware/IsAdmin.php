<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        //Check if user has admin or super-admin role
        if (!auth()->user()->hasAnyRole(['admin', 's'])) {
            abort(403, 'Unauthorized action. Adin access required.');
        }

        return $next($request);
    }
}
