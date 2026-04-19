<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;

/**
 * AdminUserController
 * 
 * Handles admin user management including:
 * - User listing with filtering and search
 * - Role-based user filtering
 * - User booking statistics
 * - User profile management
 */
class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('bookings');

        // Filter by role
        if ($request->role) {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'users' => User::where('role', 'user')->count(),
            'regular_users' => User::where('role', 'user')->count(),
            'users_with_bookings' => User::has('bookings')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function show(User $user)
    {
        $user->load(['bookings.showtime.movie', 'bookings.showtime.room']);

        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'confirmed_bookings' => $user->bookings()->where('status', 'confirmed')->count(),
            'cancelled_bookings' => $user->bookings()->where('status', 'cancelled')->count(),
            'total_spent' => $user->bookings()->where('payment_status', 'paid')->sum('total_price'),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[^\s].*[^\s]$/|regex:/^(?!.*\s{2}).*$/',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'role' => 'required|in:user,admin',
        ], [
            'name.regex' => 'Name cannot start or end with spaces, or contain consecutive spaces',
        ]);

        // Trim inputs
        $validated['name'] = trim($validated['name']);
        $validated['email'] = trim($validated['email']);
        if (isset($validated['phone'])) $validated['phone'] = trim($validated['phone']);
        if (isset($validated['city'])) $validated['city'] = trim($validated['city']);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    public function toggleRole(User $user)
    {
        // Prevent changing your own role
        if ($user->id === (int) auth()->id()) {
            return back()->with('error', 'You cannot change your own role');
        }

        $newRole = $user->role === 'admin' ? 'user' : 'admin';
        $user->update(['role' => $newRole]);

        return back()->with('success', "User role changed to {$newRole} successfully");
    }
}
