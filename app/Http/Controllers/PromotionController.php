<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

/**
 * PromotionController
 *
 * Handles promotion display operations including:
 * - Listing all active promotions
 * - Grouping promotions by category
 * - Ordering promotions by display priority
 */
class PromotionController extends Controller
{
    /**
     * Display the promotions page
     */
    public function index()
    {
        // Get all active promotions grouped by category, ordered by display_order
        $promotions = Promotion::active()
            ->ordered()
            ->get()
            ->groupBy('category');

        return view('promotions', compact('promotions'));
    }
}
