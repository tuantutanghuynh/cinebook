{{--
/**
 * Profile Layout Template
 * 
 * Main layout template for user profile section including:
 * - Responsive sidebar navigation with profile menu items
 * - Clean navbar with page title display
 * - Alert system for success/error messages
 * - Bootstrap integration and FontAwesome icons
 * - CSS and JavaScript asset management
 */
--}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Your Profile')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/root.css', 'resources/css/admin_layout.css'])

    @stack('styles')

    @yield('extra-css')
</head>

<body>
    <div class="admin-layout-wrapper">

        <aside class="admin-layout-sidebar">

            <div class="admin-layout-sidebar-header">
                <h3 style="margin: 0;">
                    <i class="fas fa-film"></i>
                    <a href="/" style="color: inherit; text-decoration: none;">TCA Cine</a>
                </h3>
            </div>

            <nav class="admin-layout-sidebar-nav">

                <ul class="admin-layout-sidebar-menu">

                    {{-- Menu Item 1: User Profile --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                        <a href="{{ route('user.profile') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-user"></i>
                            <span>User Profile</span>
                        </a>
                    </li>
                    {{-- Menu Item 2: Booking History --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('user.bookings.*') ? 'active' : '' }}">
                        <a href="{{ route('user.bookings.list') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-history"></i>
                            <span>Booking History</span>
                        </a>
                    </li>
                    {{-- Menu Item 3: My Reviews --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('user.reviews.*') ? 'active' : '' }}">
                        <a href="{{ route('user.reviews.list') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-star"></i>
                            <span>My Reviews</span>
                        </a>
                    </li>
                    {{-- Menu Item 4: View Website --}}
                    <li class="admin-layout-sidebar-item">
                        <a href="{{ route('homepage') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-home"></i>
                            <span>View Website</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        <div class="admin-layout-main">

            <nav class="navbar navbar-expand-lg admin-layout-navbar">

                <div class="container-fluid">

                    <span class="navbar-text navbar-title">
                        @yield('page-title', 'Your Profile')
                    </span>

                </div>
            </nav>

            <main class="admin-layout-content">

                <div class="container-fluid">

                    {{-- Breadcrumb navigation --}}
                    @yield('breadcrumb')

                    {{-- Alert Messages: show success/error --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    {{-- Main Content Area --}}
                    @yield('content')
                </div>
            </main>

            <footer class="admin-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted mb-0">
                                © 2026 TCA Cine
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p class="text-muted mb-0">
                                Version 1.0.0
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @yield('extra-js')

    {{-- Bootstrap JS for dropdowns and components --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>