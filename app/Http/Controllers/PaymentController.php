<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Showtime;
use App\Models\Seat;
use App\Models\Booking;
use App\Models\ShowtimePrice;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\Log;

/**
 * PaymentController
 * 
 * Handles payment processing and booking confirmation including:
 * - Booking confirmation and payment processing
 * - Seat reservation finalization
 * - Email confirmation sending
 * - Payment validation and error handling
 * - Transaction management for bookings
 */
class PaymentController extends Controller
{
    /**
     * Process booking confirmation and payment
     */
    public function processBooking(Request $request)
    {
        //1. Get data from form
        $showtime_id = $request->input('showtime_id');
        $total_price = $request->input('total_price');  
        $user_id = Session::get('user_id');
        
        if (!$user_id) {
            return redirect('/login')->with('error', 'Please log in to complete booking.');
        }
        
        $payment_method = $request->input('payment_method');
        
        //2. Get seat ids from form (support both array and JSON string)
        $seatsInput = $request->input('seats', '[]');
        
        // Auto-detect if it's already an array or JSON string
        if (is_array($seatsInput)) {
            $selectedSeats = $seatsInput;
        } else {
            $selectedSeats = json_decode($seatsInput, true);
        }
        
        // Validate seats data
        if (empty($selectedSeats) || !is_array($selectedSeats)) {
            return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                           ->with('error', 'Invalid seat selection. Please try again.');
        }
        
        //3. Start transaction
        DB::beginTransaction();
        
        try {
            //a. Re-check seat availability
            foreach ($selectedSeats as $seat_id) {
                $seatStatus = DB::table('showtime_seats')
                    ->where('showtime_id', $showtime_id)
                    ->where('seat_id', $seat_id)
                    ->first();
                
                // If booked -> show error
                if ($seatStatus && $seatStatus->status === 'booked') {
                    DB::rollback();
                    return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                        ->with('error', 'Seat already booked. Please select different seats');
                }
                
                // Cannon book if reserved by another user or reservation expired
                if ($seatStatus && $seatStatus->status === 'reserved') {
                    // check if reserved by current user
                    if ($seatStatus->reserved_by_user_id != $user_id) {
                        DB::rollback();
                        return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                            ->with('error', 'Seat reserved by another user. Please try again');
                    }
                    
                    // Check if reservation expired 
                    if ($seatStatus->reserved_until && now() > $seatStatus->reserved_until) {
                        DB::rollback();
                        return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                            ->with('error', 'Your reservation has expired. Please select seats again');
                    }
                    
                    // If reserved by current user and still valid, proceed
                }
            }
            
            //c. Create booking record
            $bookingId = DB::table('bookings')->insertGetId([
                'user_id' => $user_id,
                'showtime_id' => $showtime_id,
                'total_price' => $total_price,
                'status' => 'pending',
                'payment_method' => $payment_method,
                'payment_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            //d. Get showtime with room and screen type for pricing
            $showtime = Showtime::with(['room.screenType'])->find($showtime_id);

            // Get peak hour surcharges for this showtime (indexed by seat_type_id)
            $peakSurcharges = ShowtimePrice::where('showtime_id', $showtime_id)
                ->pluck('price', 'seat_type_id')
                ->toArray();

            //e. Insert into booking_seats table with pricing and QR code for each seat
            $coupleSeatsProcessed = []; // Track couple seats to avoid duplicate QR
            
            foreach ($selectedSeats as $seat_id) {
                // Get seat with seat type using Eloquent
                $seat = Seat::with('seatType')->find($seat_id);
                
                if ($seat && $showtime && $showtime->room && $showtime->room->screenType) {
                    // Calculate seat price: base + screen type + peak hour surcharge
                    $peakSurcharge = $peakSurcharges[$seat->seat_type_id] ?? 0;
                    $seatPrice = ($seat->seatType->base_price ?? 0) + ($showtime->room->screenType->price ?? 0) + $peakSurcharge;
                    
                    // Check if this is a couple seat
                    $isCouple = ($seat->seatType->name === 'Couple' || $seat->seat_type_id == 3);
                    $qrCode = null;
                    
                    if ($isCouple) {
                        // Get couple pair key (A1-A2)
                        $pairKey = $this->getCouplePairKey($seat->seat_code);
                        
                        // If this couple pair not processed yet, generate QR for both seats
                        if (!isset($coupleSeatsProcessed[$pairKey])) {
                            // Generate QR for couple pair
                            $qrCode = \App\Models\BookingSeat::generateQRCode($bookingId, $pairKey);
                            $coupleSeatsProcessed[$pairKey] = $qrCode;
                        } else {
                            // Use existing QR from the pair
                            $qrCode = $coupleSeatsProcessed[$pairKey];
                        }
                    } else {
                        // Regular seat: generate unique QR
                        $qrCode = \App\Models\BookingSeat::generateQRCode($bookingId, $seat->seat_code);
                    }
                    
                    DB::table('booking_seats')->insert([
                        'booking_id' => $bookingId,
                        'showtime_id' => $showtime_id,
                        'seat_id' => $seat_id,
                        'price' => $seatPrice,
                        'qr_code' => $qrCode,
                        'qr_status' => 'active',
                    ]);
                }
            }

            //f. Commit transaction
            DB::commit();

            //g. Load booking with relationships and show payment page
            $booking = Booking::with(['showtime.movie', 'showtime.room'])
                ->find($bookingId);
            
            return view('payment.mock', compact('booking'));
            
        } catch (\Exception $e) {
            //h. Rollback on error
            DB::rollback();
            return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                           ->with('error', 'An error occurred during booking: ' . $e->getMessage());
        }
    }
    
    /**
     * Confirm payment and finalize booking
     */
    public function confirmPayment(Request $request, $booking_id)
    {
        //1. Check if user is logged in
        $user_id = Session::get('user_id');
        if (!$user_id) {
            return redirect('/login')->with('error', 'Please log in to confirm payment.');
        }
        
        //2. Get booking
        $booking = Booking::with(['showtime.movie', 'showtime.room'])
            ->where('id', $booking_id)
            ->where('user_id', $user_id)
            ->first();
            
        if (!$booking) {
            return redirect()->route('homepage')->with('error', 'Booking not found.');
        }
        
        //3. Update booking status and confirm seats
        DB::beginTransaction();
        try {
            // Update booking status
            DB::table('bookings')
                ->where('id', $booking_id)
                ->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'updated_at' => now(),
                ]);
            
            // Update seats from 'reserved' to 'booked'
            DB::table('showtime_seats')
                ->join('booking_seats', 'showtime_seats.seat_id', '=', 'booking_seats.seat_id')
                ->where('booking_seats.booking_id', $booking_id)
                ->where('showtime_seats.showtime_id', $booking->showtime_id)
                ->where('showtime_seats.status', 'reserved')
                ->update([
                    'showtime_seats.status' => 'booked',
                    'showtime_seats.reserved_until' => null,
                    'showtime_seats.reserved_by_user_id' => null,
                ]);
            
            DB::commit();
            
            // Send Booking Confirmation Email
            try {
                // Load all necessary relationships for the email
                $booking->load([
                    'user', 
                    'bookingSeats.seat', 
                    'showtime.movie', 
                    'showtime.room'
                ]);
                
                if ($booking->user && $booking->user->email) {
                    Mail::to($booking->user->email)->send(new BookingConfirmationMail($booking));
                }
            } catch (\Exception $e) {
                // Log the error but don't stop the flow
                Log::error("Failed to send booking confirmation email for booking #{$booking_id}: " . $e->getMessage());
            }

            //4. Redirect to success page
            return redirect()->route('booking.success', ['booking_id' => $booking_id]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('homepage')
                           ->with('error', 'Payment confirmation failed: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Generate couple seat pair key (e.g., "A1-A2")
     */
    private function getCouplePairKey($seatCode)
    {
        $rowLetter = substr($seatCode, 0, 1);
        $seatNumber = (int)substr($seatCode, 1);
        $lowerNumber = ($seatNumber % 2 === 1) ? $seatNumber : $seatNumber - 1;
        return $rowLetter . $lowerNumber . '-' . ($lowerNumber + 1);
    }
}
