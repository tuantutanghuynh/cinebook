<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * CheckRole Middleware
 *
 * Verifies user has required role to access protected routes.
 * Used for admin panel access control.
 */
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You need to log in to continue');
        }
        // Check if user has the required role
        if (Auth::user()->role !== $role) {
            abort(403, 'You do not have permission to access this page');
        }
        return $next($request);
    }
}