<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file contains all web routes for the CineBook cinema booking system.
| Routes are organized by feature: movies, auth, booking, payment, admin.
|
*/

use App\Http\Controllers\SearchController;


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use Illuminate\Auth\Events\Login;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminMovieController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminRoomController;
use App\Http\Controllers\Admin\AdminShowtimeController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\QRCheckInController;
use App\Http\Controllers\User\ProfileController;

//***Movie Controller */
//movie list Route
Route::get('/index', [MovieController::class, 'index'])->name('movies.index');
//movie detail Route
Route::get('/movies/{id}', [MovieController::class, 'show']);
// Homepage Route
Route::get('/', [MovieController::class, 'homepage'])->name('homepage');
// Now Showing Route
Route::get('/now-showing', [MovieController::class, 'nowShowing'])->name('now_showing');
// Upcoming Movies Route
Route::get('/upcoming-movies', [MovieController::class, 'upcomingMovies'])->name('upcoming_movies');
// Promotions Route
Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions');
// Sitemap Route
Route::get('/sitemap', [MovieController::class, 'sitemap'])->name('sitemap');

// Coming Soon Route
Route::get('/coming-soon', function () {
    return view('coming-soon');
})->name('coming_soon');

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login',[LoginController::class, 'login'])->name('login.post');
// Logout Route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/password/forgot', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.forgot');
Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/password/update', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Register Routes
Route::get('register',[LoginController::class, 'showRegisterForm']);
Route::post('register',[LoginController::class, 'register'])->name('register');

/**Showtime Controller */
//showtime Route
Route::get('/movies/{id}/showtimes', [ShowtimeController::class, 'showtimes'])->name('movies.showtimes');

//**Booking Controller */
// Seatmap Page - No auth required, anyone can view seatmap
Route::get('/showtimes/{showtime_id}/seats', [BookingController::class, 'seatMap'])->name('booking.seatmap');

// Handle direct GET access to booking route - redirect to seatmap
Route::get('/showtimes/{showtime_id}/book', function($showtime_id) {
    return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                     ->with('info', 'Please select your seats first.');
});

// Routes requiring authentication with redirect
Route::middleware('auth.redirect')->group(function () {
    // Book Seats Page
    Route::post('/showtimes/{showtime_id}/book', [BookingController::class, 'bookSeats'])->name('booking.book');
    // Cancel Reserved Seats
    Route::post('/booking/cancel-reserved', [BookingController::class, 'cancelReservedSeats'])->name('booking.cancel-reserved');
    // Cancel Entire Booking
    Route::post('/booking/cancel', [BookingController::class, 'cancelBooking'])->name('booking.cancel');
    // Select Seats Page
    Route::post('/showtimes/{showtime_id}/seats/select', [ShowtimeController::class, 'selectSeats'])->name('movies.selectseats');
    // Confirm Booking
    Route::get('/booking/confirm/{booking_id}', [BookingController::class, 'confirmBooking'])->name('booking.confirm');
    // Booking Success
    Route::get('/booking/success/{booking_id}', [BookingController::class, 'bookingSuccess'])->name('booking.success');
});

//** Payment Controller */
// Routes requiring authentication with redirect
Route::middleware('auth.redirect')->group(function () {
    // Process Booking & Payment
    Route::post('/booking/process', [PaymentController::class, 'processBooking'])->name('booking.process');
    // Mock Payment Gateway
    Route::get('payment/mock/{booking_id}', [PaymentController::class, 'showMockPayment'])->name('payment.mock');
    // Confirm Payment
    Route::post('payment/confirm/{booking_id}', [PaymentController::class, 'confirmPayment'])->name('payment.confirm');
});

//** User Profile - Protected by auth middleware */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'userProfile'])->name('user.profile');
    Route::get('/profile/bookings', [ProfileController::class, 'bookingsList'])->name('user.bookings.list');
    Route::get('/profile/bookings/{booking_id}', [BookingController::class, 'bookingDetails'])->name('user.booking.details');

    // Edit Profile - GET for form, POST for submit
    Route::get('/profile/edit', [ProfileController::class, 'editProfile'])->name('user.profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('user.profile.update');
    
    // Change Password - GET for form, POST for submit
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('user.profile.change-password');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('user.profile.change-password.post');
    
    // User Reviews Management
    Route::get('/profile/reviews', [ProfileController::class, 'reviewsList'])->name('user.reviews.list');
    
    // SeatType price management
    Route::get('seat-types/prices', [\App\Http\Controllers\Admin\SeatTypeController::class, 'editPrices'])->name('admin.seat_types.edit_prices');
    Route::post('seat-types/prices', [\App\Http\Controllers\Admin\SeatTypeController::class, 'updatePrices'])->name('admin.seat_types.update_prices');
});

// Review Routes (only for movie detail page)
Route::middleware('auth')->group(function () {
    Route::post('/movies/{movie_id}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/{id}/helpful', [\App\Http\Controllers\ReviewController::class, 'toggleHelpful'])->name('reviews.helpful');
    // User delete review functionality removed - only admin can delete reviews
});

//Admin Routes - Grouped with 'admin' prefix, protected by auth & role:admin
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Movies Management
    Route::get('movies', [AdminMovieController::class, 'index'])->name('admin.movies.index');
    Route::get('movies/create', [AdminMovieController::class, 'create'])->name('admin.movies.create');
    Route::post('movies', [AdminMovieController::class, 'store'])->name('admin.movies.store');
    Route::get('movies/{movie}', [AdminMovieController::class, 'show'])->name('admin.movies.show');
    Route::get('movies/{movie}/edit', [AdminMovieController::class, 'edit'])->name('admin.movies.edit');
    Route::put('movies/{movie}', [AdminMovieController::class, 'update'])->name('admin.movies.update');

    // Users Management
    Route::get('users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::post('users/{user}/toggle-role', [AdminUserController::class, 'toggleRole'])->name('admin.users.toggle-role');

    // Rooms Management
    Route::resource('rooms', AdminRoomController::class, ['as' => 'admin']);
    Route::post('rooms/{room}/update-seats', [AdminRoomController::class, 'updateSeats'])->name('admin.rooms.update-seats');
    Route::post('rooms/{room}/update-prices', [AdminRoomController::class, 'updatePrices'])->name('admin.rooms.update-prices');

    // Showtimes Management
    Route::resource('showtimes', AdminShowtimeController::class, ['as' => 'admin']);

    // Bookings Management
    Route::get('bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');
    Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('admin.bookings.show');
    Route::post('bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('admin.bookings.cancel');

    // QR Check-in Management
    Route::get('qr-checkin', [QRCheckInController::class, 'index'])->name('admin.qr.index');
    Route::post('qr-checkin/check', [QRCheckInController::class, 'checkIn'])->name('admin.qr.checkin');
    Route::post('qr-checkin/preview', [QRCheckInController::class, 'preview'])->name('admin.qr.preview');

    // Reviews Management
    Route::get('reviews', [\App\Http\Controllers\Admin\AdminReviewController::class, 'index'])->name('admin.reviews.index');
    Route::delete('reviews/{id}', [\App\Http\Controllers\Admin\AdminReviewController::class, 'destroy'])->name('admin.reviews.destroy');
});

// Search Route
Route::get('/search', [SearchController::class, 'search'])->name('search');
