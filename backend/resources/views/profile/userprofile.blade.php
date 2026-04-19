{{--
/**
 * User Profile Dashboard
 * 
 * User profile overview page including:
 * - Personal information display
 * - Account statistics summary
 * - Quick action buttons
 * - Recent activity overview
 * - Profile completion status
 */
--}}
@extends('profile.profilepage')

@section('title','User Profile - TCA Cine')

@section('page-title','Your Profile')

@section('content')
<div class="admin-header">
    <h2><i class="fas fa-user"></i> User Profile</h2>
    <p class="text-muted">Manage your account information</p>
</div>

<div class="container-fluid">
    <div class="row">
        {{-- Left Column: Avatar --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header" style="background-color: var(--deep-teal); color: white;">
                    <h5 class="mb-0"><i class="fas fa-user-circle"></i> Profile Photo</h5>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <div class="mb-3">
                        <img src="{{ $user->avatar_url ? asset('storage/' . $user->avatar_url) : asset('images/default-avatar.png') }}" 
                             alt="Profile Picture" 
                             class="rounded-circle" 
                             style="width: 200px; height: 200px; object-fit: cover; border: 5px solid var(--deep-teal);">
                    </div>
                    <h4 class="mb-2">{{ $user->name }}</h4>
                    <p class="text-muted mb-0">
                        @if($user->role == 1)
                            <span class="badge bg-danger">Administrator</span>
                        @else
                            <span class="badge bg-primary">Regular User</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Right Column: Personal Information --}}
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header" style="background-color: var(--deep-teal); color: white;">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small text-uppercase fw-bold">Full Name</label>
                            <p class="fs-5 mb-0">
                                <i class="fas fa-user me-2 text-primary"></i>{{ $user->name }}
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small text-uppercase fw-bold">Email Address</label>
                            <p class="fs-5 mb-0">
                                <i class="fas fa-envelope me-2 text-primary"></i>{{ $user->email }}
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small text-uppercase fw-bold">Phone Number</label>
                            @if($user->phone)
                                <p class="fs-5 mb-0">
                                    <i class="fas fa-phone me-2 text-primary"></i>{{ $user->phone }}
                                </p>
                            @else
                                <p class="fs-5 mb-0 text-muted">
                                    <i class="fas fa-phone me-2"></i><em>Not provided</em>
                                </p>
                            @endif
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small text-uppercase fw-bold">City</label>
                            @if($user->city)
                                <p class="fs-5 mb-0">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                    @if($user->city == 'tphcm') Ho Chi Minh
                                    @elseif($user->city == 'hanoi') Hanoi
                                    @elseif($user->city == 'danang') Da Nang
                                    @else {{ $user->city }}
                                    @endif
                                </p>
                            @else
                                <p class="fs-5 mb-0 text-muted">
                                    <i class="fas fa-map-marker-alt me-2"></i><em>Not provided</em>
                                </p>
                            @endif
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small text-uppercase fw-bold">Account Type</label>
                            <p class="fs-5 mb-0">
                                @if($user->role == 1)
                                    <span class="badge bg-danger fs-6">Administrator</span>
                                @else
                                    <span class="badge bg-primary fs-6">Regular User</span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label class="text-muted small text-uppercase fw-bold">Member Since</label>
                            <p class="fs-5 mb-0">
                                <i class="fas fa-calendar-check me-2 text-primary"></i>{{ $user->created_at->format('F d, Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons Row --}}
    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <a href="{{ route('user.profile.edit') }}" class="btn btn-primary btn-lg w-100 py-3">
                <i class="fas fa-edit me-2"></i> Edit Profile
            </a>
        </div>
        <div class="col-md-6 mb-3">
            <a href="{{ route('user.profile.change-password') }}" class="btn btn-warning btn-lg w-100 py-3">
                <i class="fas fa-key me-2"></i> Change Password
            </a>
        </div>
    </div>
</div>
@endsection