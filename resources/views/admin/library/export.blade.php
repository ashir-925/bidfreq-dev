@extends('admin.layout.app')
@section('title', 'Export Library · BidFreq')
@section('content')
<main class="app-content">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Console</a><span class="breadcrumbs__sep">/</span>
        <a href="{{ route('admin.library.index') }}">Pricing Library</a>
    </nav>
    <header class="page-header">
        <div class="page-header__text"><h1 class="page-header__title">Export Library</h1></div>
    </header>
    <section class="card">
        <div class="card__header"><h2 class="card__title">Choose a file format</h2></div>
        <div class="card__body">
            <p>Each download contains the current Library and all 15 fields. Either format can be imported again.</p>
            <div class="page-header__actions">
                <a class="btn btn--primary" href="{{ route('admin.library.export', 'csv') }}">Download CSV</a>
                <a class="btn btn--secondary" href="{{ route('admin.library.export', 'json') }}">Download JSON</a>
            </div>
        </div>
    </section>
</main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
@endpush
