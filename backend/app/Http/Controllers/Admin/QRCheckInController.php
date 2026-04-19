<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingSeat;
use App\Models\Booking;
use Illuminate\Http\Request;

/**
 * QRCheckInController
 * 
 * Handles QR code check-in functionality including:
 * - QR scanner interface display
 * - Booking validation through QR codes
 * - Seat check-in processing
 * - Status updates for booking confirmations
 */
class QRCheckInController extends Controller
{
    /**
     * Show QR scanner page
     */
    public function index()
    {
        return view('admin.qr_checkin.index');
    }

    /**
     * Process QR code scan and check-in
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $qrCode = $request->input('qr_code');

        // Check-in using BookingSeat model
        $result = BookingSeat::checkInWithQR($qrCode);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        // Get booking details
        $bookingSeats = BookingSeat::with(['seat', 'booking.showtime.movie', 'booking.user'])
            ->where('qr_code', $qrCode)
            ->get();

        $booking = $bookingSeats->first()->booking;

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => [
                'booking_id' => $booking->id,
                'customer_name' => $booking->user->name,
                'movie_title' => $booking->showtime->movie->title,
                'show_date' => $booking->showtime->show_date->format('d/m/Y'),
                'show_time' => $booking->showtime->show_time,
                'seats' => $bookingSeats->pluck('seat.seat_code')->toArray(),
                'total_seats' => $bookingSeats->count(),
                'checked_at' => $bookingSeats->first()->checked_at->format('d/m/Y H:i:s')
            ]
        ]);
    }

    /**
     * Get booking info by QR code (preview before check-in)
     */
    public function preview(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $qrCode = $request->input('qr_code');

        $bookingSeats = BookingSeat::with(['seat', 'booking.showtime.movie', 'booking.user'])
            ->where('qr_code', $qrCode)
            ->get();

        if ($bookingSeats->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'QR code does not exist'
            ], 404);
        }

        $booking = $bookingSeats->first()->booking;
        $firstSeat = $bookingSeats->first();

        return response()->json([
            'success' => true,
            'data' => [
                'booking_id' => $booking->id,
                'customer_name' => $booking->user->name,
                'movie_title' => $booking->showtime->movie->title,
                'show_date' => $booking->showtime->show_date->format('d/m/Y'),
                'show_time' => $booking->showtime->show_time,
                'seats' => $bookingSeats->pluck('seat.seat_code')->toArray(),
                'qr_status' => $firstSeat->qr_status,
                'checked_at' => $firstSeat->checked_at ? $firstSeat->checked_at->format('d/m/Y H:i:s') : null
            ]
        ]);
    }
}
