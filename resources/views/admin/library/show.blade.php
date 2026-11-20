@extends('admin.layout.app')
@section('title', 'Task details · BidFreq')
@section('content')
<main class="app-content">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Console</a><span class="breadcrumbs__sep">/</span>
        <a href="{{ route('admin.library.index') }}">Pricing Library</a>
    </nav>
    <header class="page-header">
        <div class="page-header__text"><h1 class="page-header__title">Task details</h1></div>
        <div class="page-header__actions"><a class="btn btn--secondary" href="{{ route('admin.library.history', $task) }}">Task history</a><a class="btn btn--primary" href="{{ route('admin.library.edit', $task) }}">Edit task</a></div>
    </header>

<section class="card">
    <div class="card__header">
        <div><h2 class="card__title">{{ $task->task_name }}</h2>
            <p class="card__subtitle">{{ $task->task_id }} · Library version #{{ $task->library_version_id }}</p></div>
        <div>@include('admin.library.flags', ['task' => $task])</div>
    </div>
    <div class="card__body">
        <p>Evergreen reference range (USD) · Low {{ $fields['evergreen_low'] }} · Typical {{ $fields['evergreen_typical'] }} · High {{ $fields['evergreen_high'] }}</p>
        <p class="text-muted">Historical prices, labor hours, and market labor costs are reference figures.</p>
        @if ($task->pricing_mode === 'quote_by_scope')
            <p class="alert alert--info">Quote by scope. This task has no fixed rate.
                @if ($task->reference_rate !== null) The amount in the imported label is a reference only. @endif
            </p>
        @endif
    </div>
    <div class="table-wrap"><div class="table-scroll"><table class="table">
    <thead><tr><th scope="col">Field</th><th scope="col">Value</th></tr></thead>
    <tbody>@foreach ($fields as $field => $value)
        <tr><th scope="row">{{ str($field)->headline() }}</th><td>{{ $value }}</td></tr>
    @endforeach</tbody>
</table></div></div>
</section>
</main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
@endpush
