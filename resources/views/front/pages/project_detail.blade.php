@extends('front.layout.app')
@section('title', 'Project details · BidFreq')
@push('styles')
<link rel="stylesheet" href="{{ asset('front_assets/pages_css/project_detail.css') }}">
@endpush
@section('content')
<main class="app-content project-detail-page"><nav class="breadcrumbs"><a href="{{ route('projects') }}">Approved Projects</a><span class="breadcrumbs__sep">/</span><span>Project details</span></nav>
@include('shared.project_detail')
</main>
@endsection
@if (config('project_payments.stripe.key'))
    @push('scripts')
        <script src="https://js.stripe.com/v3/" defer></script>
        <script src="{{ asset('front_assets/pages_js/project_payment.js') }}" defer></script>
    @endpush
@endif
