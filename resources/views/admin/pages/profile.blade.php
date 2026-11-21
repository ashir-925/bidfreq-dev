@extends('admin.layout.app')
@section('title', 'My Profile · BidFreq')

@push('styles')
    <link rel="stylesheet" href="{{ asset('admin_assets/css/profile.css') }}">
@endpush

@section('content')
<main class="app-content admin-profile">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Console</a>
        <span class="breadcrumbs__sep" aria-hidden="true">/</span>
        <span class="breadcrumbs__current" aria-current="page">Profile</span>
    </nav>
    <header class="page-header">
        <div class="page-header__text">
            <h1 class="page-header__title">My Profile</h1>
            <p class="page-header__subtitle">Manage your contact details and password.</p>
        </div>
    </header>
    @include('shared.profile_panels')
</main>
@endsection
