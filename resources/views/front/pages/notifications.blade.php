@extends('front.layout.app')
@section('title', 'Notifications · BidFreq')

@push('styles')
    <link rel="stylesheet" href="{{ asset('front_assets/pages_css/notifications.css') }}">
@endpush

@section('content')
    @include('shared.account_notices')
@endsection
