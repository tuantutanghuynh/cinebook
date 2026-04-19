<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Showtime;
use App\Models\Room;
use App\Models\Seat;
use App\Models\Booking;
use App\Models\ShowtimePrice;

/**
 * BookingController
 * 
 * Handles movie booking operations including:
 * - Seat map display with availability status
 * - Booking process and seat selection
 * - Booking confirmation and validation
 * - Session management for booking flow
 * - Seat reservation and release logic
 */
class BookingController extends Controller
{
    //Display seat map for a specific showtime
    public function seatMap($showtime_id)
    {
        // Get showtime details using Eloquent
        $showtime = Showtime::with('movie')->find($showtime_id);
        
        if (!$showtime) {
            return redirect()->route('homepage')->with('error', 'Showtime not found');
        }
        
        // Get room with screen type and pricing using relationships
        $room = Room::with('screenType')->find($showtime->room_id);
        if (!$room) {
            return redirect()->route('homepage')->with('error', 'Room not found.');
        }
        
        // Get all seats in the room with their types using relationships
        $seats = $room->seats()->with('seatType')
            ->orderBy('seat_row', 'asc')
            ->orderBy('seat_number', 'asc')
            ->get();
        
        // Auto-clean expired reserved seats (no scheduler needed!)
        DB::table('showtime_seats')
            ->where('status', 'reserved')
            ->where('reserved_until', '<', now())
            ->update([
                'status' => 'available',
                'reserved_until' => null,
                'reserved_by_user_id' => null,
            ]);

        // Auto-expire pending bookings older than 2 minutes
        $expiredBookings = Booking::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(2))
            ->with('bookingSeats')
            ->get();

        foreach ($expiredBookings as $booking) {
            try {
                $booking->update(['status' => 'expired']);

                // Update QR status to expired
                DB::table('booking_seats')
                    ->where('booking_id', $booking->id)
                    ->update(['qr_status' => 'expired']);
            } catch (\Exception $e) {
                \Log::error('Failed to expire booking #' . $booking->id . ': ' . $e->getMessage());
            }
        }
        
        // Get booked seats (confirmed bookings only)
        $bookedSeats = DB::table('showtime_seats')
            ->where('showtime_id', $showtime_id)
            ->where('status', 'booked')
            ->pluck('seat_id')
            ->toArray();
        
        // Get reserved seats (temporarily held by other users)
        $user_id = Session::get('user_id');
        $reservedSeats = DB::table('showtime_seats')
            ->where('showtime_id', $showtime_id)
            ->where('status', 'reserved')
            ->where('reserved_until', '>', now())
            ->when($user_id, function ($query) use ($user_id) {
                // Exclude seats reserved by current user
                return $query->where('reserved_by_user_id', '!=', $user_id);
            })
            ->pluck('seat_id')
            ->toArray();
            
        return view('booking.seat_map', compact('showtime', 'room', 'seats', 'bookedSeats', 'reservedSeats'));
    }
    
    /**
     * Process seat booking with proper validation and pricing
    */
    public function bookSeats(Request $request, $showtime_id)
    {
        // User authentication is handled by middleware now
        $user_id = Session::get('user_id');
        
        // Block admins from booking tickets
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->back()->with('error', 'Admins cannot book tickets.');
        }
        
        //2. Get selected seats from request (support both array and JSON string)
        $seatsInput = $request->input('seats', '[]');
        
        // Auto-detect if it's already an array or JSON string
        if (is_array($seatsInput)) {
            $selectedSeats = $seatsInput; // Already an array
        } else {
            $selectedSeats = json_decode($seatsInput, true); // Decode JSON string
        }
        
        //3. Validate input
        if (empty($selectedSeats) || !is_array($selectedSeats))
        {
            return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                           ->with('error', 'No seats selected. Please select at least one seat.');
        }
        
        //4. Get showtime and room information for pricing
        $showtime = Showtime::find($showtime_id);
        if (!$showtime) {
            return redirect()->route('homepage')->with('error', 'Invalid showtime');
        }
        // Block booking for showtimes in the past
        if (
            $showtime->show_date < now()->toDateString() ||
            ($showtime->show_date == now()->toDateString() && $showtime->show_time < now()->toTimeString())
        ) {
            return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                ->with('error', 'Cannot book seats for a showtime in the past.');
        }
        $room = $showtime->room()->with('screenType')->first();
        if (!$room) {
            return redirect()->route('homepage')->with('error', 'Room not found.');
        }
        
        //5. START TRANSACTION WITH PESSIMISTIC LOCKING
        DB::beginTransaction();
        
        try {
            // STEP 1: LOCK selected seats to prevent race conditions
            // lockForUpdate() prevents other transactions from reading/writing these rows
            $lockedSeats = Seat::with('seatType')
                ->whereIn('id', $selectedSeats)
                ->lockForUpdate() // ← CRITICAL: This locks the rows until transaction commits
                ->get()
                ->keyBy('id'); // Convert to associative array for easy access
            
            // STEP 2: Validate all selected seats exist
            if ($lockedSeats->count() !== count($selectedSeats)) {
                DB::rollBack();
                return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                    ->with('error', 'Some selected seats are invalid');
            }
            
            // STEP 3: Check if any locked seats are already BOOKED (confirmed)
            $bookedSeatIds = DB::table('showtime_seats')
                ->where('showtime_id', $showtime_id)
                ->whereIn('seat_id', $selectedSeats)
                ->where('status', 'booked') // Only check booked, not reserved
                ->lockForUpdate()
                ->pluck('seat_id')
                ->toArray();
            
            if (!empty($bookedSeatIds)) {
                DB::rollBack();
                $bookedCodes = $lockedSeats->whereIn('id', $bookedSeatIds)->pluck('seat_code')->implode(', ');
                return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                    ->with('error', "Seats {$bookedCodes} are already booked");
            }
            
            // STEP 3.5: Check if any seats are RESERVED by OTHER users (have not expired)
            foreach ($selectedSeats as $seat_id) {
                $reservedSeat = DB::table('showtime_seats')
                    ->where('showtime_id', $showtime_id)
                    ->where('seat_id', $seat_id)
                    ->where('status', 'reserved')
                    ->where('reserved_until', '>', now()) // have not expired
                    ->lockForUpdate()
                    ->first();
                
                if ($reservedSeat) {
                    // IF reserved by DIFFERENT user → ERROR
                    if ($reservedSeat->reserved_by_user_id != $user_id) {
                        DB::rollBack();
                        $seatCode = $lockedSeats->get($seat_id)->seat_code ?? $seat_id;
                        return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                            ->with('error', "Seat {$seatCode} is temporarily reserved by another user. Please select different seats");
                    }
                    // IF reserved by SAME user → OK, proceed
                }
            }
            
            // STEP 4: Collect seat information for pricing (seats are now safely locked)
            $seatDetails = [];
            $totalPrice = 0;
            $validatedCouplePairs = [];

            // Get peak hour surcharges for this showtime (indexed by seat_type_id)
            $peakSurcharges = ShowtimePrice::where('showtime_id', $showtime_id)
                ->pluck('price', 'seat_type_id')
                ->toArray();
            
            foreach ($selectedSeats as $seat_id) {
                $seat = $lockedSeats->get($seat_id);
            
            // Couple seat validation
            if ($seat->seat_type_id == 3) {
                $pairKey = $this->getCouplePairKey($seat->seat_code);
                if (!in_array($pairKey, $validatedCouplePairs)) {
                    $validation = $this->validateCoupleSeat($seat, $selectedSeats, $showtime_id);
                    if (!$validation['valid']) {
                        DB::rollBack(); // Rollback on validation error
                        return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                            ->with('error', $validation['message']);
                    }
                    // Logic to calculate price for couple seats (base + screen type + peak hour surcharge)
                    $peakSurcharge = $peakSurcharges[$seat->seat_type_id] ?? 0;
                    $seatPrice = ($room->screenType->price ?? 0) + ($seat->seatType->base_price ?? 0) + $peakSurcharge;
                    $totalPrice += $seatPrice;
                    $seatDetails[] = [
                        'id' => $seat->id,
                        'seat_code' => $seat->seat_code . ' + ' . $this->getCouplePairCode($seat->seat_code),
                        'seat_type' => $seat->seatType->name ?? 'Unknown',
                        'price' => $seatPrice,
                    ];
                    $validatedCouplePairs[] = $pairKey;
                }
            } else {
                // Regular seat: base + screen type + peak hour surcharge
                $peakSurcharge = $peakSurcharges[$seat->seat_type_id] ?? 0;
                $seatPrice = ($room->screenType->price ?? 0) + ($seat->seatType->base_price ?? 0) + $peakSurcharge;
                $totalPrice += $seatPrice;
                $seatDetails[] = [
                    'id' => $seat->id,
                    'seat_code' => $seat->seat_code,
                    'seat_type' => $seat->seatType->name ?? 'Unknown',
                    'price' => $seatPrice,
                ];
            }
        }
        //STEP 4.5: Reserve seat 120 seconds
        foreach ($selectedSeats as $seat_id) {
            //check if already reserved (should not happen due to locks, but double-check)
            $existingSeat = DB::table('showtime_seats')
                ->where('showtime_id', $showtime_id)
                ->where('seat_id', $seat_id)
                ->first();
            $reserveData = [
                'status' => 'reserved',
                'reserved_until'=> now()->addSeconds(120),
                'reserved_by_user_id'=> $user_id,
            ];
            if ($existingSeat) {
                //update existing record
                DB::table('showtime_seats')
                    ->where('showtime_id', $showtime_id)
                    ->where('seat_id', $seat_id)
                    ->update($reserveData);
            } else {
                //insert new record
                DB::table('showtime_seats')
                    ->insert(array_merge($reserveData, [
                        'showtime_id' => $showtime_id,
                        'seat_id' => $seat_id,
                        'status' => 'reserved',
                        'reserved_until'=> now()->addSeconds(120),
                        'reserved_by_user_id'=> $user_id
                    ]));
            }
        }

        // STEP 5: Commit transaction (releases all locks)
        DB::commit();
        
        } catch (\Exception $e) {
            // STEP 6: Rollback on any error
            DB::rollBack();
            return redirect()->route('booking.seatmap', ['showtime_id' => $showtime_id])
                ->with('error', 'Booking failed: ' . $e->getMessage());
        }
        
        //8. Get Movie info using relationship
        $movie = $showtime->movie;
        //9. Redirect to confirmation page with booking details
        return view('booking.confirm', compact('movie', 'showtime', 'room', 'seatDetails', 'totalPrice', 'showtime_id'));
    }
    
    /**
     * Validate couple seat logic to call into bookSeats method
     */
    private function validateCoupleSeat($seat, $selectedSeats, $showtime_id)
    {
        // Extract row and seat number
        $rowLetter = substr($seat->seat_code, 0, 1);
        $seatNumber = (int)substr($seat->seat_code, 1);
        
        // Determine pair seat number
        $pairSeatNumber = ($seatNumber % 2 === 1) ? $seatNumber + 1 : $seatNumber - 1;
        $pairSeatCode = $rowLetter . $pairSeatNumber;
        
        // Find pair seat
        $pairSeat = DB::table('seats')
            ->where('seat_code', $pairSeatCode)
            ->where('room_id', $seat->room_id)
            ->first();
            
        if (!$pairSeat) {
            return ['valid' => false, 'message' => 'Couple seat pair not found.'];
        }
        
        // Check if pair seat is also selected
        if (!in_array($pairSeat->id, $selectedSeats)) {
            return ['valid' => false, 'message' => 'Both seats in couple pair must be selected.'];
        }
        
        // Check if pair seat is already booked or reserved
        $pairBooked = DB::table('showtime_seats')
            ->where('showtime_id', $showtime_id)
            ->where('seat_id', $pairSeat->id)
            ->whereIn('status', ['booked', 'reserved'])
            ->exists();
            
        if ($pairBooked) {
            return ['valid' => false, 'message' => 'Couple seat pair is not available.'];
        }
        
        return ['valid' => true, 'message' => ''];
    }
    /**
     * Generate a unique key for couple seat pairs
     */
    private function getCouplePairKey($seatCode)
    {
        $rowLetter = substr($seatCode, 0, 1);
        $seatNumber = (int)substr($seatCode, 1);
        $lowerNumber = ($seatNumber % 2 === 1) ? $seatNumber : $seatNumber - 1;
        return $rowLetter . $lowerNumber . '-' . ($lowerNumber + 1);
    }
    /**
     * Get the code of the pair seat in a couple
     */
    private function getCouplePairCode($seatCode)
    {
        $rowLetter = substr($seatCode, 0, 1);
        $seatNumber = (int)substr($seatCode, 1);
        $pairSeatNumber = ($seatNumber % 2 === 1) ? $seatNumber + 1 : $seatNumber - 1;
        return $rowLetter . $pairSeatNumber;
    }
    /**
     * Display booking confirmation page
     */
    public function confirmBooking($booking_id)
    {
        //check logged in
        $user_id = Session::get('user_id');
        if (!$user_id) {
            return redirect('/login')->with('error', 'Please log in to view booking details.');
        }
        //get booking details using relationships
        $booking = Booking::with(['showtime.movie', 'showtime.room'])
            ->where('id', $booking_id)
            ->where('user_id', $user_id)
            ->first();
        //check if booking exists
        if (!$booking) {
            return redirect()->route('homepage')->with('error', 'Booking not found');
        }
        //get booked seats details using relationships
        $seats = $booking->bookingSeats()->with(['seat.seatType'])->get();
        //return confirmation view with data
        return view('booking.confirmation_details', compact('booking', 'seats'));
    }

    /**
     * Display booking success page
     */
    public function bookingSuccess($booking_id){
        //Check logged in
        $user_id = Session::get('user_id');
        if (!$user_id) {
            return redirect('/login')->with('error', 'Please log in to view booking details');
        }

        //Get booking details using relationships
        $booking = Booking::with(['showtime.movie', 'showtime.room'])
            ->where('id', $booking_id)
            ->where('user_id', $user_id)
            ->first();

        //check if booking exists
        if (!$booking) {
            return redirect()->route('homepage')->with('error', 'Booking not found');
        }

        //Get booked seats details using relationships
        $seats = $booking->bookingSeats()->with(['seat.seatType'])->get();
 
        //Generate QR code data (booking info)
        $qrData = "Booking ID: " . $booking->id . "\n"
                . "Movie: " . $booking->showtime->movie->title . "\n"
                . "Showtime: " . $booking->showtime->show_date->format('Y-m-d') . " " . $booking->showtime->show_time . "\n"
                . "Seats: " . implode(', ', $seats->pluck('seat.seat_code')->toArray()) . "\n"
                . "Total Price: " . number_format($booking->total_price, 0) . " VND";
        //Return success view with data
        return view('booking.success', compact('booking', 'seats', 'qrData'));
    }

    /**
     * Cancel reserved seats for a showtime (called when user goes back or timeout)
     */
    public function cancelReservedSeats(Request $request)
    {
        $showtime_id = $request->input('showtime_id');
        $seats = $request->input('seats', []);
        
        if (empty($seats) || !is_array($seats)) {
            return response()->json(['success' => false, 'message' => 'No seats provided']);
        }
        
        // Delete reserved seats from showtime_seats table
        DB::table('showtime_seats')
            ->where('showtime_id', $showtime_id)
            ->whereIn('seat_id', $seats)
            ->where('status', 'reserved')
            ->delete();
        
        return response()->json(['success' => true, 'message' => 'Reserved seats released']);
    }

    /**
     * Cancel entire booking (delete booking and release seats)
     */
    public function cancelBooking(Request $request)
    {
        $booking_id = $request->input('booking_id');
        
        if (!$booking_id) {
            return response()->json(['success' => false, 'message' => 'Booking ID required']);
        }
        
        // Check if booking exists and belongs to user
        $user_id = Session::get('user_id');
        $booking = Booking::where('id', $booking_id)
            ->where('user_id', $user_id)
            ->where('status', 'pending')
            ->first();
            
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found or already confirmed']);
        }
        
        DB::beginTransaction();
        try {
            // Get seat IDs from booking_seats
            $seatIds = DB::table('booking_seats')
                ->where('booking_id', $booking_id)
                ->pluck('seat_id')
                ->toArray();
            
            // Delete from showtime_seats (release seats)
            DB::table('showtime_seats')
                ->where('showtime_id', $booking->showtime_id)
                ->whereIn('seat_id', $seatIds)
                ->delete();
            
            // Delete booking_seats
            DB::table('booking_seats')
                ->where('booking_id', $booking_id)
                ->delete();
            
            // Delete booking
            $booking->delete();
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Booking cancelled successfully']);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error canceling booking: ' . $e->getMessage()]);
        }
    }
}