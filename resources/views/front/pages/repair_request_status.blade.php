@extends('front.layout.app')
@section('title', 'Estimate request received · BidFreq')
@section('content')
<main class="app-content">

<nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('estimate-request') }}">Estimate Requests</a></nav>
<header class="page-header"><div class="page-header__text"><h1 class="page-header__title">Estimate request received</h1></div></header>
<section class="panel mb-4"><div class="panel__body">
    <p>Your request <strong>{{ $repair->number() }}</strong> has been received and is now under review. We will email you as soon as your estimate is ready.</p>
    <span class="badge badge--blue">Under Review</span>
</div></section>
@include('shared.repair_evidence', ['downloadRoute' => 'repair-requests.download'])
</main>
@endsection
@push('scripts')
    <script src="{{ asset('front_assets/pages_js/dashboard.js') }}"></script>
    
@endpush
