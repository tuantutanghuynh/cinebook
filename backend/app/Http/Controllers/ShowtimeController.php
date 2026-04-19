<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Showtime;
use App\Models\Movie;
use App\Models\Room;

/**
 * ShowtimeController
 * 
 * Handles movie showtime operations including:
 * - Showtime listing for specific movies
 * - Date and time filtering
 * - Room and movie relationship management
 * - Showtime availability checking
 */
class ShowtimeController extends Controller
{
    //1. showtimes function to fetch showtimes for a specific movie using relationships
    public function showtimes($id)
    {
        $movie = Movie::find($id);
        $now = now();
        
        $showtimes = Showtime::with(['room', 'room.screenType'])
            ->where('movie_id', $id)
            ->where(function($query) use ($now) {
                // Only get future showtimes
                $query->where('show_date', '>', $now->toDateString())
                      ->orWhere(function($q) use ($now) {
                          // Or same day but show time has not passed yet
                          $q->where('show_date', '=', $now->toDateString())
                            ->where('show_time', '>', $now->toTimeString());
                      });
            })
            ->orderBy('show_date', 'asc')
            ->orderBy('show_time', 'asc')
            ->get();
        return view('movie.showtimes', compact('movie', 'showtimes'));
    }
    //3. selectSeats function to handle seat selection and booking
    public function selectSeats(Request $request, $showtime_id)
    {
        // take selected seats from request by decoding JSON array
        $selectedSeatsJson = $request->input('seats', '[]');
        $selectedSeats = json_decode($selectedSeatsJson, true);
        
        // Check if user is logged in
        $user_id = Session::get('user_id');
        if (!$user_id) {
            return redirect('/login')->with('error', 'Please log in to book seats');
        }
        
        // Validate selected seats input by checking if it's empty or not an array
        if (empty($selectedSeats) || !is_array($selectedSeats)) {
            return redirect()->route('movies.seatmap', ['showtime_id' => $showtime_id])//redirect back with error
                           ->with('error', 'No seats selected');
        }
        
        // start transaction for booking seats for data integrity
        DB::beginTransaction();
        // try-catch block to handle booking process
        try {
            $bookedSeats = [];
            $alreadyBookedSeats = [];
            
            foreach ($selectedSeats as $seat_id) {
                // Validate seat exists
                $seat = DB::table('seats')->where('id', $seat_id)->first();
                if (!$seat) {
                    DB::rollback();// Rollback transaction
                    return redirect()->route('movies.seatmap', ['showtime_id' => $showtime_id])//redirect back with error
                                   ->with('error', 'Invalid seat selected');
                }
                
                // Check if the seat is already booked for this showtime
                $existingBooking = DB::table('showtime_seats')
                    ->where('showtime_id', $showtime_id)
                    ->where('seat_id', $seat_id)
                    ->first();
                // If not booked, proceed to book
                if (!$existingBooking) {
                    // Book the seat
                    DB::table('showtime_seats')->insert([
                        'showtime_id' => $showtime_id,
                        'seat_id' => $seat_id,
                        'status' => 'booked',
                        'user_id' => $user_id,
                    ]);
                    $bookedSeats[] = $seat->seat_code;// Collect successfully booked seat codes
                } else {
                    $alreadyBookedSeats[] = $seat->seat_code;// Collect already booked seat codes
                }
            }
            // Commit transaction after processing all seats
            DB::commit();
            
            // Prepare success message
            $message = '';
            if (!empty($bookedSeats)) {
                $message .= 'Successfully booked seats: ' . implode(', ', $bookedSeats) . '. ';
            }
            if (!empty($alreadyBookedSeats)) {
                $message .= 'Some seats were already booked: ' . implode(', ', $alreadyBookedSeats) . '.';
            }
            // Redirect back with success message
            return redirect()->route('movies.seatmap', ['showtime_id' => $showtime_id])
                           ->with('success', $message ?: 'Seats booked successfully');
        // catch block to handle exceptions                   
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movies.seatmap', ['showtime_id' => $showtime_id])
                           ->with('error', 'An error occurred while booking seats. Please try again');
        }
    }
    }