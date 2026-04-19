{{--
/**
 * Admin Users List
 * 
 * User management interface including:
 * - User list with role filtering
 * - User search and sorting
 * - Account status management
 * - User statistics display
 * - Bulk user operations
 */
--}}
@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<h2 class="mb-4" style="color: var(--prussian-blue)">
    <i class="bi bi-people"></i> Manage Users
</h2>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-white" style="background: linear-gradient(135deg, var(--deep-teal), var(--deep-space-blue));">
            <div class="card-body">
                <h6 class="card-title">Total Users</h6>
                <h2 class="mb-0">{{ $stats['total'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h6 class="card-title">Administrators</h6>
                <h2 class="mb-0">{{ $stats['admins'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h6 class="card-title">Regular Users</h6>
                <h2 class="mb-0">{{ $stats['regular_users'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="card-title">Active Customers</h6>
                <h2 class="mb-0">{{ $stats['users_with_bookings'] }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, Email, or Phone" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-cinebook w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-body">
        @if($users->isEmpty())
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> No users found.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Role</th>
                            <th>Bookings</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                    @if($user->id === auth()->id())
                                        <span class="badge bg-info">You</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>{{ $user->city ?? '-' }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @else
                                        <span class="badge bg-primary">User</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $user->bookings_count }}</span>
                                </td>
                                <td>
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
            <div class="cine-pagination-wrapper">
                <nav aria-label="Users pagination">
                    <ul class="cine-pagination">
                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            <li class="cine-page-item {{ $page == $users->currentPage() ? 'is-active' : '' }}">
                                <a class="cine-page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
            @endif
        @endif
    </div>
</div>
@endsection
