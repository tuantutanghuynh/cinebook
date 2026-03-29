<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\ScreenType;
use App\Models\SeatType;
use App\Models\Seat;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AdminRoomController
 * 
 * Handles admin room management including:
 * - Room creation and editing
 * - Seat layout configuration
 * - Screen type assignment
 * - Room capacity management
 */
class AdminRoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('screenType')->get();
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $screenTypes = ScreenType::all();
        $seatTypes = SeatType::all();
        return view('admin.rooms.create', compact('screenTypes', 'seatTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_rows' => 'required|integer|min:1|max:26',
            'seats_per_row' => 'required|integer|min:1|max:30',
            'screen_type_id' => 'required|exists:screen_types,id',
            'seat_configs' => 'nullable|array',
            'seat_configs.*.row' => 'required_with:seat_configs|string|max:1',
            'seat_configs.*.number' => 'required_with:seat_configs|integer|min:1',
            'seat_configs.*.type' => 'required_with:seat_configs|integer|in:1,2,3',
        ]);

        DB::beginTransaction();
        try {
            $room = Room::create([
                'name' => $validated['name'],
                'total_rows' => $validated['total_rows'],
                'seats_per_row' => $validated['seats_per_row'],
                'screen_type_id' => $validated['screen_type_id'],
            ]);

            // Get default seat type (standard)
            $defaultSeatType = SeatType::where('name', 'Standard')->first();
            if (!$defaultSeatType) {
                $defaultSeatType = SeatType::first();
            }

            // Check if seat_configs is provided (from preview)
            if (!empty($validated['seat_configs'])) {
                // Create seats with custom types from preview
                foreach ($validated['seat_configs'] as $config) {
                    Seat::create([
                        'room_id' => $room->id,
                        'seat_row' => $config['row'],
                        'seat_number' => $config['number'],
                        'seat_code' => $config['row'] . $config['number'],
                        'seat_type_id' => $config['type'],
                    ]);
                }
            } else {
                // Generate seats automatically with default type
                $rowLabels = range('A', 'Z');
                for ($row = 0; $row < $validated['total_rows']; $row++) {
                    for ($seat = 1; $seat <= $validated['seats_per_row']; $seat++) {
                        Seat::create([
                            'room_id' => $room->id,
                            'seat_row' => $rowLabels[$row],
                            'seat_number' => $seat,
                            'seat_code' => $rowLabels[$row] . $seat,
                            'seat_type_id' => $defaultSeatType->id,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room created successfully with ' . ($validated['total_rows'] * $validated['seats_per_row']) . ' seats');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create room: ' . $e->getMessage());
        }
    }

    public function edit(Room $room)
    {
        $room->load('seats.seatType', 'screenType');
        $screenTypes = ScreenType::all();
        $seatTypes = SeatType::all();

        // Group seats by row and sort by seat_number within each row
        $seatsByRow = $room->seats
            ->sortBy('seat_number')
            ->groupBy('seat_row')
            ->sortKeys(); // Sort rows alphabetically (A, B, C, ...)

        // Kiểm tra room có suất chiếu trong tương lai không
        $hasFutureShowtimes = Showtime::where('room_id', $room->id)
            ->where('show_date', '>=', now()->toDateString())
            ->exists();

        return view('admin.rooms.edit', compact('room', 'screenTypes', 'seatTypes', 'seatsByRow', 'hasFutureShowtimes'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'screen_type_id' => 'required|exists:screen_types,id',
        ]);

        $room->update($validated);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room updated successfully');
    }

    public function updateSeats(Request $request, Room $room)
    {
        // Kiểm tra room có suất chiếu trong tương lai không
        $hasFutureShowtimes = Showtime::where('room_id', $room->id)
            ->where('show_date', '>=', now()->toDateString())
            ->exists();

        if ($hasFutureShowtimes) {
            return back()->with('error', 'Không thể thay đổi loại ghế vì phòng đang có suất chiếu trong tương lai.');
        }

        $validated = $request->validate([
            'seats' => 'required|array',
            'seats.*.seat_id' => 'required|exists:seats,id',
            'seats.*.seat_type_id' => 'required|exists:seat_types,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['seats'] as $seatData) {
                $seat = Seat::find($seatData['seat_id']);
                $newTypeId = $seatData['seat_type_id'];
                if ($newTypeId == 3) { // Couple
                    // Set this seat and adjacent seat as couple
                    $seat->update(['seat_type_id' => $newTypeId]);
                    $nextSeat = Seat::where('room_id', $seat->room_id)
                        ->where('seat_row', $seat->seat_row)
                        ->where('seat_number', $seat->seat_number + 1)
                        ->first();
                    if ($nextSeat) $nextSeat->update(['seat_type_id' => $newTypeId]);
                } else {
                    $seat->update(['seat_type_id' => $newTypeId]);
                }
            }

            DB::commit();
            return back()->with('success', 'Seat types updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update seats: ' . $e->getMessage());
        }
    }

    // Seat price management has been moved to SeatTypeController

    public function destroy(Room $room)
    {
        try {
            $room->delete();
            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete room: ' . $e->getMessage());
        }
    }
}