<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\PasswordResetMail;
use Carbon\Carbon;

/**
 * PasswordResetController
 * 
 * Handles password reset functionality including:
 * - Password reset request handling
 * - Email token generation and sending
 * - Token validation and verification
 * - Password update processing
 */
class PasswordResetController extends Controller
{
    //1. Show form to request password reset
    public function showForgotPasswordForm()
    {
        return view('password.forgot');
    }

    //2. Send reset link to email
    public function sendResetLink(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'email.exists' => 'The email address does not exist.',
        ]);

        $email = trim($request->email);

        // Find user by email
        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'The email address does not exist.']);
        }

        // Generate token
        $token = Str::random(60);

        // Delete existing tokens
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Insert new token
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        // Send email with token link
        Mail::to($email)->send(new PasswordResetMail($token, $email));

        // Redirect back with success message
        return back()->with('success', 'Password reset link has been sent to your email');
    }

    //3. Show reset password form
    public function showResetPasswordForm($token)
    {
        $email = request()->email;
        
        // Validate token exists in database
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();
        
        // If token not found or expired
        if (!$resetRecord) {
            return redirect('/password/forgot')
                ->withErrors(['token' => 'Password reset link is invalid or expired. Please request again']);
        }
        
        // Check if token is expired (configurable in config/auth.php)
        $expireMinutes = config('auth.passwords.users.expire', 60);
        $createdAt = Carbon::parse($resetRecord->created_at);
        $expiresAt = $createdAt->addMinutes($expireMinutes);
        
        if (Carbon::now()->isAfter($expiresAt)) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect('/password/forgot')
                ->withErrors(['token' => 'Password reset link has expired. Please request again']);
        }
        
        return view('password.reset', [
            'token' => $token,
            'email' => $email
        ]);
    }

    //4. Handle reset password
    public function resetPassword(Request $request)
    {
        // Validate input
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8|regex:/^\S+$/',
        ], [
            'password.regex' => 'Password cannot contain spaces',
        ]);

        $email = trim($request->email);
        $password = $request->password;

        // Verify token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['token' => 'Invalid token']);
        }

        // Check if token is expired (configurable in config/auth.php)
        $expireMinutes = config('auth.passwords.users.expire', 60);
        $createdAt = Carbon::parse($resetRecord->created_at);
        $expiresAt = $createdAt->addMinutes($expireMinutes);
        
        if (Carbon::now()->isAfter($expiresAt)) {
            return back()->withErrors(['token' => 'Token has expired. Please request a new password reset']);
        }

        // Update password
        User::where('email', $email)
            ->update(['password' => $password]);

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Redirect to login with success message
        return redirect('/login')->with('success', 'Password has been reset successfully! Please login');
    }
}