import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/admin_layout.css",
                "resources/css/header.css",
                "resources/css/footer.css",
                "resources/css/homepage.css",
                "resources/css/movie_details.css",
                "resources/css/movie-filter.css",
                "resources/css/now_showing.css",
                "resources/css/upcoming_movies.css",
                "resources/css/showtimes.css",
                "resources/css/seat_map.css",
                "resources/css/auth.css",
                "resources/css/base.css",
                "resources/css/buttons.css",
                "resources/css/review_edit.css",
                "resources/css/reviews_index.css",
                "resources/css/profile_reviews.css",
                "resources/css/root.css",
                "resources/css/qr_checkin.css",
                "resources/css/sitemap.css",
                "resources/css/admin-room-create.css",
                "resources/css/admin-room-edit.css",
                "resources/css/admin-room-index.css",
                "public/js/qr_checkin.js",
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
