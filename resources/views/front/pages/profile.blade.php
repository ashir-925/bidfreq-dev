@extends('front.layout.app')
@section('title', 'My Profile · BidFreq')
@push('styles')
<link rel="stylesheet" href="{{ asset('front_assets/pages_css/profile.css') }}">
@endpush
@section('content')
<main class="app-content">
<nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('dashboard') }}">Home</a><span class="breadcrumbs__sep">/</span><span class="breadcrumbs__current">Profile</span></nav>
<header class="page-header"><div class="page-header__text"><h1 class="page-header__title">My Profile</h1><p class="page-header__subtitle">Manage your contact details, password, and active sessions.</p></div></header>
@include('shared.profile_panels')
</main>
@endsection
