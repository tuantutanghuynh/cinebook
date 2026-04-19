<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('price_standard', 10, 0)->default(80000)->after('screen_type_id');
            $table->decimal('price_vip', 10, 0)->default(120000)->after('price_standard');
            $table->decimal('price_couple', 10, 0)->default(200000)->after('price_vip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['price_standard', 'price_vip', 'price_couple']);
        });
    }
};
