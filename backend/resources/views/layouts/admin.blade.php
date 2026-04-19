{{--
/**
 * Alternative Admin Layout Template
 * 
 * Secondary admin layout with simplified structure:
 * - Bootstrap Icons integration
 * - User authentication display
 * - Clean admin navigation menu
 * - Fixed sidebar with admin functions
 * - Responsive design without toggle functionality
 */
--}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - CineBook</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    @vite(['resources/css/admin_layout.css'])

    @stack('styles')
</head>

<body>
    <div class="admin-layout-wrapper">
        <!-- Sidebar -->
        <aside class="admin-layout-sidebar" id="sidebar">
            <div class="admin-layout-sidebar-header">
                <h3><a href="/"><i class="bi bi-film"></i> <span>CineBook Admin</span></a></h3>
            </div>
            <nav class="admin-layout-sidebar-nav">
                <ul class="admin-layout-sidebar-menu">
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="admin-layout-sidebar-link">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.movies.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.movies.index') }}" class="admin-layout-sidebar-link">
                            <i class="bi bi-film"></i>
                            <span>Movies</span>
                        </a>
                    </li>
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.rooms.index') }}" class="admin-layout-sidebar-link">
                            <i class="bi bi-door-open"></i>
                            <span>Rooms</span>
                        </a>
                    </li>
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.showtimes.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.showtimes.index') }}" class="admin-layout-sidebar-link">
                            <i class="bi bi-calendar-event"></i>
                            <span>Showtimes</span>
                        </a>
                    </li>
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.bookings.index') }}" class="admin-layout-sidebar-link">
                            <i class="bi bi-ticket-perforated"></i>
                            <span>Bookings</span>
                        </a>
                    </li>
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.qr.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.qr.index') }}" class="admin-layout-sidebar-link">
                            <i class="bi bi-qr-code-scan"></i>
                            <span>QR Check-in</span>
                        </a>
                    </li>
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="admin-layout-sidebar-link">
                            <i class="bi bi-people"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li class="admin-layout-sidebar-item">
                        <a href="{{ route('homepage') }}" class="admin-layout-sidebar-link">
                            <i class="bi bi-house"></i>
                            <span>View Website</span>
                        </a>
                    </li>
                    <li class="admin-layout-sidebar-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="admin-layout-sidebar-link w-100"
                                style="border: none; background: none;">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="admin-layout-main" id="main-content">
            @php
            $currentUser = session('user_id') ? \App\Models\User::find(session('user_id')) : null;
            @endphp
            <nav class="admin-layout-navbar">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="admin-layout-navbar-right">
                        <div class="admin-layout-user-info">
                            <i class="bi bi-person-circle"></i>
                            <span>Welcome, <strong>{{ $currentUser?->name ?? 'Guest' }}</strong></span>
                        </div>
                        <span class="badge bg-danger">{{ $currentUser?->role ?? 'guest' }}</span>
                    </div>
                </div>
            </nav>

            <div class="admin-layout-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>