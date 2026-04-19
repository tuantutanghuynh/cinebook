{{--
/**
 * Coming Soon Page
 */
--}}
@extends('layouts.main')

@section('title', 'Coming Soon - TCA Cine')

@push('styles')
<style>
    .coming-soon-container {
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .coming-soon-title {
        font-size: 4rem;
        color: var(--color-primary, #1a2233);
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="coming-soon-container">
    <h1 class="coming-soon-title">Coming Soon</h1>
</div>
@endsection
