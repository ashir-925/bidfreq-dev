@extends('admin.layout.app')
@section('title', 'Import Library · BidFreq')
@section('content')
<main class="app-content">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Console</a><span class="breadcrumbs__sep">/</span>
        <a href="{{ route('admin.library.index') }}">Pricing Library</a>
    </nav>
    <header class="page-header">
        <div class="page-header__text"><h1 class="page-header__title">Import Library</h1></div>
        <div class="page-header__actions"><a class="btn btn--secondary" href="{{ route('admin.library.index') }}">Back to Library</a></div>
    </header>

<section class="card">
    <div class="card__header"><h2 class="card__title">Upload a CSV or JSON file</h2></div>
    <form class="card__body" method="POST" enctype="multipart/form-data" action="{{ route('admin.library.upload') }}">
        @csrf
        <div class="form-group">
            <label class="label" for="file">Library file</label>
            <input class="input" id="file" name="file" type="file" accept=".csv,.json" required aria-describedby="file-guide">
            <p class="text-muted" id="file-guide">Include all 15 Library fields. Maximum 2 MB and 2,000 task rows.</p>
            @include('shared.auth-field-error', ['name' => 'file'])
        </div>
        <button type="submit" class="btn btn--primary" data-loading-text="Validating file…">Preview import</button>
    </form>
</section>
</main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
@endpush
