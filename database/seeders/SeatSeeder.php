<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seat;

/**
 * SeatSeeder
 *
 * Seeds seats for cinema rooms.
 * Creates seat layout with different types: Standard, VIP, Couple.
 */
class SeatSeeder extends Seeder
{
    public function run()
    {
        $roomData = [
            ['room_id' => 1, 'rows' => 10, 'seats_per_row' => 14],
            // ... add more rooms as needed
        ];
        foreach ($roomData as $room) {
            for ($row = 1; $row <= $room['rows']; $row++) {
                $seat = 1;
                while ($seat <= $room['seats_per_row']) {
                    $seatCode = chr(64 + $row) . $seat;
                    $seatType = $this->determineSeatType($row, $seat, $room['seats_per_row']);
                    if ($seatType == 3 && $seat < $room['seats_per_row']) { // Couple, set for 2 adjacent seats
                        // First seat of pair
                        Seat::create([
                            'room_id' => $room['room_id'],
                            'seat_row' => $row,
                            'seat_number' => $seat,
                            'seat_code' => $seatCode,
                            'seat_type_id' => 3,
                        ]);
                        // Second seat of pair
                        $nextSeat = $seat + 1;
                        $nextSeatCode = chr(64 + $row) . $nextSeat;
                        Seat::create([
                            'room_id' => $room['room_id'],
                            'seat_row' => $row,
                            'seat_number' => $nextSeat,
                            'seat_code' => $nextSeatCode,
                            'seat_type_id' => 3,
                        ]);
                        $seat += 2;
                        continue;
                    } else {
                        Seat::create([
                            'room_id' => $room['room_id'],
                            'seat_row' => $row,
                            'seat_number' => $seat,
                            'seat_code' => $seatCode,
                            'seat_type_id' => $seatType,
                        ]);
                    }
                    $seat++;
                }
            }
        }
    }

    private function determineSeatType(int $row, int $seat, int $seatsPerRow): int
    {
        // VIP: middle of room
        if ($row >= 5 && $row <= 7 && $seat >= intdiv($seatsPerRow,2)-1 && $seat <= intdiv($seatsPerRow,2)+2) return 2;
        // Couple: back rows, paired together
        if ($row >= 8 && $seat % 2 == 1) return 3; // only set first seat of pair as couple
        return 1; // Standard
    }
}
