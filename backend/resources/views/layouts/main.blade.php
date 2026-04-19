{{--
/**
 * Main Layout Template
 * 
 * Base layout template for all public pages including:
 * - HTML document structure and meta tags
 * - Global CSS/JS asset loading via Vite
 * - Bootstrap and FontAwesome integration
 * - Header and footer components inclusion
 * - Content section placeholder
 */
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TCA Cine')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite([
        'resources/css/app.css',
        'resources/css/header.css',
        'resources/css/footer.css',
        'resources/css/movie-filter.css',
        'resources/js/app.js'
    ])
    @stack('styles')
</head>
<body>
    @include('partials.header')
    
    <main>
        @yield('content')
    </main>
    
    @include('partials.footer')
    
    @stack('scripts')
</body>
</html>