<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * ProfileController
 * 
 * Handles user profile management and related operations including:
 * - User profile display and statistics
 * - Booking history (upcoming and past bookings)
 * - Profile editing and password changes
 * - User reviews management with filtering and pagination
 * - Date range validation for review filters
 */
class ProfileController extends Controller
{
    /**
     * Show user profile page
     */
    public function userProfile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();//Authenticated user who is logged in (1: admin, 2: regular user, 3: guest)
        
        // Get profile stats using User model methods
        if($user->isAdmin()){
            return redirect('/admin/dashboard');
        }
        $userInfo = $user->getUserInfo();
        
        return view('profile.userprofile', compact('user', 'userInfo'));
    }

    /**
     * Show user's booking history
     */
    public function bookingsList()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();//Authenticated user who is logged in (1: admin, 2: regular user, 3: guest)
        
        // Get bookings using User model methods
        $upcomingBookings = $user->getUpcomingBookings();//Get all upcoming bookings for the user-this is an array of Booking objects
        $pastBookings = $user->getPastBookings();//Get all past bookings for the user-this is an array of Booking objects
        
        return view('profile.bookings_list', compact('upcomingBookings', 'pastBookings'));
    }

    //CRUD user profile
    /**
     * Update user profile
     */
    // Show edit profile form
    public function editProfile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();//Authenticated user who is logged in (1: admin, 2: regular user, 3: guest)
        return view('profile.edit', compact('user'));
    }
    // Handle profile update
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();//Authenticated user who is logged in (1: admin, 2: regular user, 3: guest)
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[^\s].*[^\s]$/|regex:/^(?!.*\s{2}).*$/',
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'city' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|max:2048',
        ], [
            'name.regex' => 'Name cannot start or end with spaces, or contain consecutive spaces',
        ]);

        $user->name = trim($validated['name']);
        $user->phone = $validated['phone'] ? trim($validated['phone']) : null;
        $user->city = $validated['city'] ? trim($validated['city']) : null;
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar_url && \Storage::disk('public')->exists($user->avatar_url)) {
                \Storage::disk('public')->delete($user->avatar_url);
            }
            
            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = $avatarPath;
        }
        
        $user->save();
                
        return redirect()->route('user.profile')->with('success', 'Profile updated successfully');
    }
    /**
     * Change user password
     */
    // Show change password form
    public function showChangePasswordForm()
    {
        return view('profile.changepw');
    } 
    // Handle password change
    public function changePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();//Authenticated user who is logged in (1: admin, 2: regular user, 3: guest)
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed|regex:/^\S+$/',
        ], [
            'new_password.regex' => 'Password cannot contain spaces',
        ]);

        // Check if current password matches (plain text comparison)
        if ($user->password !== $validated['current_password']) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        
        //check new password is different from current password
        if ($validated['new_password'] === $validated['current_password']) {
            return redirect()->back()->withErrors(['new_password' => 'New password must be different from the current password']);
        }
        
        // Update to new password (store as plain text)
        $user->password = $validated['new_password'];
        $user->save();

        return redirect()->route('user.profile')->with('success', 'Password changed successfully');
    }

    /**
     * Show user's reviews with filtering and search
     */
    public function reviewsList(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Validate date range logic
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'rating' => 'nullable|integer|between:1,5',
            'search' => 'nullable|string|max:255'
        ], [
            'date_to.after_or_equal' => 'To Date must be after or equal to From Date.',
            'rating.between' => 'Rating must be between 1 and 5 stars.',
        ]);
        
        $query = $user->reviews()->with(['movie.genres']);
        
        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }
        
        // Filter by date range with validation
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search by movie title
        if ($request->filled('search')) {
            $query->whereHas('movie', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }
        
        // Sort by latest reviews first
        $reviews = $query->latest()->paginate(10);
        
        return view('profile.reviews_list', compact('reviews'));
    }
}