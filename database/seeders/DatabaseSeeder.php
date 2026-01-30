<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 *
 * Main seeder that orchestrates all other seeders.
 * Runs seeders in logical order for database initialization.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call Seeders in logical order
        $this->call([
            UserSeeder::class,
            GenreSeeder::class,
            // MovieSeeder::class, // Need to create this file or import SQL first
            // MovieGenreSeeder::class, // Can only run when Movies exist
        ]);
    }
}