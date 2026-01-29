<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\User;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * AdminDashboardController
 * 
 * Handles admin dashboard operations including:
 * - Statistics overview and metrics
 * - Revenue and booking analytics
 * - User activity tracking
 * - System performance monitoring
 */
class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $now = Carbon::now();

        // ========================================
        // ROW 1: BUSINESS PULSE
        // ========================================

        // Tickets Sold Today - tickets sold for showtimes that have ended today
        $ticketsSoldToday = DB::table('booking_seats')
            ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
            ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
            ->join('movies', 'showtimes.movie_id', '=', 'movies.id')
            ->whereDate('showtimes.show_date', $today)
            ->whereRaw("DATE_ADD(CONCAT(showtimes.show_date, ' ', showtimes.show_time), INTERVAL movies.duration MINUTE) <= ?", [$now])
            ->where('bookings.payment_status', 'paid')
            ->count();

        // Revenue Today - revenue from showtimes that have ENDED today (100% confirmed)
        $revenueToday = Booking::where('payment_status', 'paid')
            ->whereHas('showtime', function ($query) use ($today, $now) {
                $query->whereDate('show_date', $today)
                    ->whereHas('movie', function ($q) use ($now) {
                        $q->whereRaw("DATE_ADD(CONCAT(showtimes.show_date, ' ', showtimes.show_time), INTERVAL movies.duration MINUTE) <= ?", [$now]);
                    });
            })
            ->sum('total_price');

        // Showtimes With Bookings Today - number of showtimes today with at least 1 booking
        $showtimesWithBookingsToday = Showtime::whereDate('show_date', $today)
            ->whereHas('showtimeSeats', function ($query) {
                $query->where('status', 'booked');
            })
            ->count();

        // Active Showtimes (ongoing and upcoming)
        $activeShowtimes = Showtime::with('movie')->get()->filter(function($showtime) {
            return in_array($showtime->status, ['ongoing', 'upcoming']);
        })->count();

        // ========================================
        // ROW 2: RISK & FUTURE
        // ========================================

        // Revenue at Risk (24h) - revenue from showtimes NOT YET started in next 24h
        // May be refunded if customer cancels ticket or showtime is cancelled
        $next24h = $now->copy()->addHours(24);
        $upcomingRevenue24h = Booking::where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->whereHas('showtime', function ($query) use ($now, $next24h) {
                $query->whereRaw("CONCAT(show_date, ' ', show_time) BETWEEN ? AND ?", [
                    $now->format('Y-m-d H:i:s'),
                    $next24h->format('Y-m-d H:i:s')
                ]);
            })
            ->sum('total_price');

        // Refund Amount This Month - cancelled bookings that were previously paid
        $refundAmountThisMonth = Booking::whereYear('updated_at', $today->year)
            ->whereMonth('updated_at', $today->month)
            ->where('status', 'cancelled')
            ->where('payment_status', 'paid') // Only count bookings that were paid then cancelled
            ->sum('total_price');

        // ========================================
        // ROW 3: PERFORMANCE
        // ========================================

        // Top Movie by Revenue (all time)
        $topMovieByRevenue = Movie::leftJoin('showtimes', 'movies.id', '=', 'showtimes.movie_id')
            ->leftJoin('bookings', function ($join) {
                $join->on('showtimes.id', '=', 'bookings.showtime_id')
                    ->where('bookings.payment_status', 'paid');
            })
            ->select('movies.id', 'movies.title', DB::raw('COALESCE(SUM(bookings.total_price), 0) as revenue'))
            ->groupBy('movies.id', 'movies.title')
            ->orderBy('revenue', 'desc')
            ->first();

        // Top Showtime Today - showtime with most tickets sold today
        $topShowtimeToday = Showtime::with(['movie', 'room'])
            ->whereDate('show_date', $today)
            ->withCount(['showtimeSeats as booked_count' => function ($query) {
                $query->where('status', 'booked');
            }])
            ->orderBy('booked_count', 'desc')
            ->first();

        // ========================================
        // ADDITIONAL STATS (kept for backward compatibility)
        // ========================================
        $totalUsers = User::where('role', 'user')->count();
        $totalMovies = Movie::count();
        $nowShowingMovies = Movie::where('status', 'now_showing')->count();
        $comingSoonMovies = Movie::where('status', 'coming_soon')->count();
        $totalRevenue = Booking::where('payment_status', 'paid')->sum('total_price');
        $revenueThisMonth = Booking::whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        // Recent bookings
        $recentBookings = Booking::with(['user', 'showtime.movie', 'bookingSeats'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            // Row 1: Business Pulse
            'ticketsSoldToday',
            'revenueToday',
            'showtimesWithBookingsToday',
            'activeShowtimes',
            // Row 2: Risk & Future
            'upcomingRevenue24h',
            'refundAmountThisMonth',
            // Row 3: Performance
            'topMovieByRevenue',
            'topShowtimeToday',
            // Additional stats
            'totalUsers',
            'totalMovies',
            'nowShowingMovies',
            'comingSoonMovies',
            'totalRevenue',
            'revenueThisMonth',
            'recentBookings'
        ));
    }
}
