<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeatType;
use Illuminate\Http\Request;

/**
 * SeatTypeController
 * 
 * Handles seat type management including:
 * - Seat type price configuration
 * - Seat category management
 * - Pricing updates for different seat types
 */
class SeatTypeController extends Controller
{
    public function editPrices()
    {
        $seatTypes = SeatType::all();
        return view('admin.seat_types.prices', compact('seatTypes'));
    }

    public function updatePrices(Request $request)
    {
        $validated = $request->validate([
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0',
        ]);
        foreach ($validated['prices'] as $id => $price) {
            SeatType::where('id', $id)->update(['base_price' => $price]);
        }
        return redirect()->route('admin.rooms.index')->with('success', 'Seat type prices updated successfully');
    }
}