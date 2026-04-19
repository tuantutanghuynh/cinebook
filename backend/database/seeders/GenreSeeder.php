<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * GenreSeeder
 *
 * Seeds the genres table with movie categories.
 * Includes: Action, Comedy, Drama, Horror, Romance, etc.
 */
class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $genres = [
            ['name' => 'Action', 'description' => 'High-energy movies with exciting sequences'],
            ['name' => 'Comedy', 'description' => 'Funny and humorous movies'],
            ['name' => 'Drama', 'description' => 'Serious and emotional storylines'],
            ['name' => 'Horror', 'description' => 'Scary and frightening movies'],
            ['name' => 'Romance', 'description' => 'Love stories and romantic relationships'],
            ['name' => 'Thriller', 'description' => 'Suspenseful and tension-filled movies'],
            ['name' => 'Sci-Fi', 'description' => 'Science fiction and futuristic themes'],
            ['name' => 'Fantasy', 'description' => 'Magical and supernatural elements'],
            ['name' => 'Adventure', 'description' => 'Exciting journeys and quests'],
            ['name' => 'Animation', 'description' => 'Animated movies for all ages'],
            ['name' => 'Crime', 'description' => 'Criminal activities and investigations'],
            ['name' => 'Documentary', 'description' => 'Non-fiction and educational content'],
        ];

        foreach ($genres as $genre) {
            DB::table('genres')->updateOrInsert(
                ['name' => $genre['name']], 
                [
                    'name' => $genre['name'],
                    'description' => $genre['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}