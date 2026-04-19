{{--
/**
 * Admin Layout Template
 * 
 * Main layout template for admin panel including:
 * - Fixed sidebar navigation with admin management sections
 * - Clean navbar with page title display
 * - Comprehensive admin menu (Dashboard, Movies, Rooms, Showtimes, etc.)
 * - Alert system for admin notifications
 * - Bootstrap and FontAwesome integration
 */
--}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin Panel - TCA Cine')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/root.css', 'resources/css/admin_layout.css'])

    @yield('extra-css')
</head>

<body>
    <div class="admin-layout-wrapper">

        <aside class="admin-layout-sidebar">

            <div class="admin-layout-sidebar-header">
                <h3>
                    <i class="fas fa-film"></i>
                    <a href="/">TCA Cine Admin</a>
                </h3>
            </div>

            <nav class="admin-layout-sidebar-nav">

                <ul class="admin-layout-sidebar-menu">

                    {{-- Menu Item 1: Dashboard --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Menu Item 2: Movies --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.movies.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.movies.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-film"></i>
                            <span>Movies</span>
                        </a>
                    </li>

                    {{-- Menu Item 3: Rooms --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.rooms.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-chair"></i>
                            <span>Rooms</span>
                        </a>
                    </li>

                    {{-- Menu Item 4: Showtimes --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.showtimes.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.showtimes.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-clock"></i>
                            <span>Showtimes</span>
                        </a>
                    </li>

                    {{-- Menu Item 5: Bookings --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.bookings.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Bookings</span>
                        </a>
                    </li>

                    {{-- Menu Item 6: Users --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>

                    {{-- Menu Item 7: Reviews --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.reviews.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-star"></i>
                            <span>Reviews</span>
                        </a>
                    </li>

                    {{-- Menu Item 8: QR Check-in --}}
                    <li
                        class="admin-layout-sidebar-item {{ request()->routeIs('admin.qr_checkin.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.qr_checkin.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-qrcode"></i>
                            <span>QR Check-in</span>
                        </a>
                    </li>

                    {{-- Menu Item 9: Seat Type Prices --}}
                    <li
                        class="admin-layout-sidebar-item {{ request()->routeIs('admin.seat_types.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.seat_types.prices') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-money-bill"></i>
                            <span>Seat Prices</span>
                        </a>
                    </li>

                </ul>
            </nav>
        </aside>

        <div class="admin-layout-main">

            <nav class="navbar navbar-expand-lg admin-layout-navbar">

                <div class="container-fluid">

                    <span class="navbar-text navbar-title">
                        @yield('page-title', 'Admin Panel')
                    </span>

                    <div class="navbar-nav ms-auto">

                        <div class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-shield"></i>
                                <span class="ms-2">{{ Auth::user()->name }}</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">

                                <li>
                                    <a class="dropdown-item" href="{{ route('homepage') }}">
                                        <i class="fas fa-home me-2"></i>
                                        View Website
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger admin-dropdown-logout">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
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
                                © 2026 TCA Cine Admin Panel
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

</body>

</html>