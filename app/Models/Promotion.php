<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Promotion Model
 *
 * Represents a promotional offer or deal.
 * Contains promotion details, validity, and display settings.
 * Grouped by categories for organized display.
 */
class Promotion extends Model
{
    protected $fillable = [
        'category',
        'icon',
        'title',
        'description',
        'details_title',
        'details_items',
        'cta_text',
        'cta_link',
        'validity_text',
        'status',
        'display_order'
    ];

    protected $casts = [
        'details_items' => 'array',
    ];

    /**
     * Scope to get only active promotions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get promotions by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }
}
