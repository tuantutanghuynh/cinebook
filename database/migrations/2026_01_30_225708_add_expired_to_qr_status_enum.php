<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add 'expired' to qr_status ENUM in booking_seats table
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE booking_seats MODIFY COLUMN qr_status ENUM('active', 'checked', 'cancelled', 'expired') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any 'expired' to 'cancelled' before removing the enum value
        DB::statement("UPDATE booking_seats SET qr_status = 'cancelled' WHERE qr_status = 'expired'");
        DB::statement("ALTER TABLE booking_seats MODIFY COLUMN qr_status ENUM('active', 'checked', 'cancelled') DEFAULT 'active'");
    }
};
