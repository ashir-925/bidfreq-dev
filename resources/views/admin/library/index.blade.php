@extends('admin.layout.app')
@section('title', 'Pricing Library · BidFreq')
@section('content')
<main class="app-content">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Console</a><span class="breadcrumbs__sep">/</span>
        <a href="{{ route('admin.library.index') }}">Pricing Library</a>
    </nav>
    <header class="page-header">
        <div class="page-header__text"><h1 class="page-header__title">Pricing Library</h1></div>
        <div class="page-header__actions"><a class="btn btn--secondary" href="{{ route('admin.library.export-options') }}">Export Library</a>
<a class="btn btn--primary" href="{{ route('admin.library.import') }}">Import Library</a></div>
    </header>

<form method="GET" action="{{ route('admin.library.index') }}" class="filter-bar">
    <div class="filter-bar__group">
        <label for="library-search" class="filter-bar__label">Task ID or name</label>
        <input class="input" type="search" id="library-search" name="search" maxlength="100" value="{{ $filters['search'] ?? '' }}">
    </div>
    @foreach (['unit' => array_combine($units->all(), $units->all()), 'mode' => ['fixed' => 'Fixed rate', 'quote_by_scope' => 'Quote by scope'], 'flag' => ['ambiguous' => 'Confirm unit', 'wide_range' => 'Wide range', 'provisional' => 'Provisional']] as $field => $options)
        <div class="filter-bar__group">
            <label for="library-{{ $field }}" class="filter-bar__label">{{ ucfirst($field) }}</label>
            <select id="library-{{ $field }}" name="{{ $field }}">
                <option value="">All</option>
                @foreach ($options as $key => $label)
                    <option value="{{ $key }}" @selected(($filters[$field] ?? '') === (string) $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    @endforeach
    <button class="btn btn--primary btn--sm" type="submit" data-loading-text="Filtering…">Filter</button>
    <a class="btn btn--secondary" href="{{ route('admin.library.index') }}">Clear</a>
</form>
<p class="text-sm text-muted">{{ $tasks->total() }} matching tasks</p>
<div class="table-wrap"><div class="table-scroll"><table class="table">
    <thead><tr><th scope="col">Task ID</th><th scope="col">Task</th><th scope="col">Unit</th><th scope="col">Standard rate (USD)</th><th scope="col">Flags</th></tr></thead>
    <tbody>
    @forelse ($tasks as $task)
        <tr><td>{{ $task->task_id }}</td><td><a href="{{ route('admin.library.show', $task) }}">{{ $task->task_name }}</a></td>
            <td>{{ $task->unit }}</td>
            <td>{{ $task->pricing_mode === 'fixed' ? '$'.rtrim(rtrim($task->bidfreq_standard_rate, '0'), '.') : 'Quote by scope' }}</td>
            <td>@include('admin.library.flags', ['task' => $task])</td></tr>
    @empty
        <tr><td colspan="5">No tasks match. Clear the filters or import a Library file.</td></tr>
    @endforelse</tbody>
</table></div></div>
@include('admin.library.pagination', ['pages' => $tasks])
@if ($versions->isNotEmpty())
    <section class="card">
        <div class="card__header"><h2 class="card__title">Recent Library versions</h2></div>
        <div class="table-wrap"><div class="table-scroll"><table class="table">
    <thead><tr><th scope="col">Version</th><th scope="col">Action</th><th scope="col">Source file</th><th scope="col">Changed by</th><th scope="col">Date</th></tr></thead>
    <tbody>
            @foreach ($versions as $version)
                <tr><td>#{{ $version->id }}</td><td>{{ ucfirst($version->action) }}</td>
                    <td>{{ $version->filename ?? '—' }}</td><td>{{ $version->user->name }}</td>
                    <td>{{ $version->created_at->format('Y-m-d H:i') }}</td></tr>
            @endforeach</tbody>
</table></div></div>
    </section>
@endif
</main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
@endpush
