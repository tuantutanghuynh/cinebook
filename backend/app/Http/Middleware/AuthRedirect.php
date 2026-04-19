<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AuthRedirect Middleware
 *
 * Ensures user is authenticated before accessing protected routes.
 * Stores intended URL for redirect after login.
 */
class AuthRedirect
{
    /**
     * Handle an incoming request.
     * Store intended URL before redirecting to login
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            // For POST requests (like booking), store the referring page instead
            if ($request->isMethod('POST')) {
                $intendedUrl = $request->header('referer', '/');
            } else {
                $intendedUrl = $request->fullUrl();
            }
            
            // Store the intended URL in session
            session(['intended_url' => $intendedUrl]);
            
            // Redirect to login with message
            return redirect()->route('login')->with('info', 'Please log in to continue');
        }

        return $next($request);
    }
}