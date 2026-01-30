<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * MovieGenreSeeder
 *
 * Seeds the movie_genres pivot table.
 * Links movies to their respective genres.
 */
class MovieGenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear existing data
        // DB::table('movie_genres')->truncate();

        $movieGenres = [
            // ================= NOW SHOWING =================
            // 1. Avengers: Endgame
            ['movie_id' => 1, 'genre_id' => 1],  // Action
            ['movie_id' => 1, 'genre_id' => 7],  // Sci-Fi
            ['movie_id' => 1, 'genre_id' => 9],  // Adventure
            // 2. John Wick: Chapter 4
            ['movie_id' => 2, 'genre_id' => 1],  // Action
            ['movie_id' => 2, 'genre_id' => 6],  // Thriller
            ['movie_id' => 2, 'genre_id' => 11], // Crime
            // 3. Parasite
            ['movie_id' => 3, 'genre_id' => 3],  // Drama
            ['movie_id' => 3, 'genre_id' => 6],  // Thriller
            // 4. Train to Busan
            ['movie_id' => 4, 'genre_id' => 1],  // Action
            ['movie_id' => 4, 'genre_id' => 4],  // Horror
            ['movie_id' => 4, 'genre_id' => 6],  // Thriller
            // 5. The Dark Knight
            ['movie_id' => 5, 'genre_id' => 1],  // Action
            ['movie_id' => 5, 'genre_id' => 11], // Crime
            ['movie_id' => 5, 'genre_id' => 6],  // Thriller
            // 6. Avatar
            ['movie_id' => 6, 'genre_id' => 1],  // Action
            ['movie_id' => 6, 'genre_id' => 7],  // Sci-Fi
            ['movie_id' => 6, 'genre_id' => 9],  // Adventure
            ['movie_id' => 6, 'genre_id' => 8],  // Fantasy
            // 7. La La Land
            ['movie_id' => 7, 'genre_id' => 5],  // Romance
            ['movie_id' => 7, 'genre_id' => 3],  // Drama
            ['movie_id' => 7, 'genre_id' => 2],  // Comedy
            // 8. Your Name
            ['movie_id' => 8, 'genre_id' => 10], // Animation
            ['movie_id' => 8, 'genre_id' => 5],  // Romance
            ['movie_id' => 8, 'genre_id' => 8],  // Fantasy
            // 9. Spirited Away
            ['movie_id' => 9, 'genre_id' => 10], // Animation
            ['movie_id' => 9, 'genre_id' => 8],  // Fantasy
            ['movie_id' => 9, 'genre_id' => 9],  // Adventure
            // 10. Intouchables
            ['movie_id' => 10, 'genre_id' => 2], // Comedy
            ['movie_id' => 10, 'genre_id' => 3], // Drama
            // 11. Toy Story
            ['movie_id' => 11, 'genre_id' => 10], // Animation
            ['movie_id' => 11, 'genre_id' => 9],  // Adventure
            ['movie_id' => 11, 'genre_id' => 2],  // Comedy
            // 12. The Conjuring
            ['movie_id' => 12, 'genre_id' => 4],  // Horror
            ['movie_id' => 12, 'genre_id' => 6],  // Thriller
            // 13. Furie
            ['movie_id' => 13, 'genre_id' => 1],  // Action
            ['movie_id' => 13, 'genre_id' => 6],  // Thriller
            ['movie_id' => 13, 'genre_id' => 11], // Crime
            // 14. Forrest Gump (NOW SHOWING version)
            ['movie_id' => 14, 'genre_id' => 3],  // Drama
            ['movie_id' => 14, 'genre_id' => 5],  // Romance
            // 15. Train to Busan (Duplicate)
            ['movie_id' => 15, 'genre_id' => 1],  // Action
            ['movie_id' => 15, 'genre_id' => 4],  // Horror
            ['movie_id' => 15, 'genre_id' => 6],  // Thriller
            // ================= COMING SOON =================
            // 16. Dune: Part Two
            ['movie_id' => 16, 'genre_id' => 7],  // Sci-Fi
            ['movie_id' => 16, 'genre_id' => 9],  // Adventure
            ['movie_id' => 16, 'genre_id' => 1],  // Action
            // 17. Oppenheimer
            ['movie_id' => 17, 'genre_id' => 3],  // Drama
            ['movie_id' => 17, 'genre_id' => 6],  // Thriller
            // 18. Weathering With You
            ['movie_id' => 18, 'genre_id' => 10], // Animation
            ['movie_id' => 18, 'genre_id' => 5],  // Romance
            ['movie_id' => 18, 'genre_id' => 8],  // Fantasy
            // 19. Finding Nemo
            ['movie_id' => 19, 'genre_id' => 10], // Animation
            ['movie_id' => 19, 'genre_id' => 9],  // Adventure
            ['movie_id' => 19, 'genre_id' => 2],  // Comedy
            // 20. Decision to Leave
            ['movie_id' => 20, 'genre_id' => 3],  // Drama
            ['movie_id' => 20, 'genre_id' => 5],  // Romance
            ['movie_id' => 20, 'genre_id' => 6],  // Thriller
            ['movie_id' => 20, 'genre_id' => 11], // Crime
            // 21. Paddington
            ['movie_id' => 21, 'genre_id' => 2],  // Comedy
            ['movie_id' => 21, 'genre_id' => 9],  // Adventure
            // 22. Rurouni Kenshin
            ['movie_id' => 22, 'genre_id' => 1],  // Action
            ['movie_id' => 22, 'genre_id' => 9],  // Adventure
            ['movie_id' => 22, 'genre_id' => 3],  // Drama
            // 23. Blue Is the Warmest Color
            ['movie_id' => 23, 'genre_id' => 5],  // Romance
            ['movie_id' => 23, 'genre_id' => 3],  // Drama
            // 24. Intimate Strangers
            ['movie_id' => 24, 'genre_id' => 3],  // Drama
            ['movie_id' => 24, 'genre_id' => 2],  // Comedy
            // 25. The Medium
            ['movie_id' => 25, 'genre_id' => 4],  // Horror
            ['movie_id' => 25, 'genre_id' => 6],  // Thriller
            // ================= ENDED =================
            // 26. Titanic
            ['movie_id' => 26, 'genre_id' => 5],  // Romance
            ['movie_id' => 26, 'genre_id' => 3],  // Drama
            // 27. Forrest Gump (ENDED version - duplicate)
            ['movie_id' => 27, 'genre_id' => 3],  // Drama
            ['movie_id' => 27, 'genre_id' => 5],  // Romance
            // 28. The Shawshank Redemption
            ['movie_id' => 28, 'genre_id' => 3],  // Drama
            // 29. The Wailing
            ['movie_id' => 29, 'genre_id' => 4],  // Horror
            ['movie_id' => 29, 'genre_id' => 6],  // Thriller
            ['movie_id' => 29, 'genre_id' => 11], // Crime
            // 30. Call Me by Your Name
            ['movie_id' => 30, 'genre_id' => 5],  // Romance
            ['movie_id' => 30, 'genre_id' => 3],  // Drama
            // 31. Belle
            ['movie_id' => 31, 'genre_id' => 10], // Animation
            ['movie_id' => 31, 'genre_id' => 8],  // Fantasy
            ['movie_id' => 31, 'genre_id' => 9],  // Adventure
            // 32. The Nun II
            ['movie_id' => 32, 'genre_id' => 4],  // Horror
            ['movie_id' => 32, 'genre_id' => 6],  // Thriller
        ];

        DB::table('movie_genres')->insert($movieGenres);

        $this->command->info('✅ Movie-Genre relationships seeded successfully!');
        $this->command->info('📊 Total relationships: ' . count($movieGenres));
    }
}