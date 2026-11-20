@extends('admin.layout.app')
@section('title', 'Estimate request · BidFreq')
@section('content')
<main class="app-content">

<nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('admin.repair-requests.index') }}">Request inbox</a></nav>
<header class="page-header"><div class="page-header__text"><h1 class="page-header__title">Estimate request {{ $repair->number() }}</h1></div></header>
<p class="mb-4"><strong>{{ Str::headline($repair->status) }}</strong> · {{ $repair->customer->name }}</p>
@include('shared.repair_evidence', ['downloadRoute' => 'admin.repair-requests.download'])
<form method="POST" action="{{ route('admin.repair-requests.nte', $repair) }}" class="filter-bar mt-4">
    @csrf
    @method('PATCH')
    <div class="filter-bar__group">
        <label for="staff-nte" class="filter-bar__label">Not-to-Exceed (USD)</label>
        <input class="input" id="staff-nte" name="nte" type="number" min="0" step="0.01" value="{{ $repair->nte }}">
        @include('shared.auth-field-error', ['name' => 'nte'])
    </div>
    <button class="btn btn--secondary" type="submit" data-loading-text="Updating NTE…">Update NTE</button>
</form>
@if ($repair->status === 'ai_failed')
    <form method="POST" action="{{ route('admin.repair-requests.retry', $repair) }}" class="mt-4">
        @csrf
        <button class="btn btn--primary" type="submit" data-loading-text="Queueing analysis…">Retry AI</button>
    </form>
    @include('admin.pages.repair_manual_draft')
@endif
@if ($draft = $repair->draft)
    <section class="card mt-4">
        <div class="card__header"><h2 class="card__title">Internal draft</h2></div>
        <div class="card__body">
            <dl>
                @foreach (['complaint', 'cause', 'correction', 'complications', 'uncertainty', 'confidence'] as $field)
                    @if (isset($draft->analysis[$field]))<dt>{{ ucfirst($field) }}</dt><dd>{{ $draft->analysis[$field] }}</dd>@endif
                @endforeach
            </dl>
            @foreach (['assumptions' => 'Assumptions', 'risk_flags' => 'Risk flags'] as $field => $label)
                @if (! empty($draft->analysis[$field]))<p><strong>{{ $label }}</strong> · {{ implode(' · ', $draft->analysis[$field]) }}</p>@endif
            @endforeach
            <p><strong>Draft total (USD)</strong> · {{ $draft->total }}</p>
            @if ($draft->over_nte)<p class="alert alert--warning" role="alert">This draft exceeds the Not-to-Exceed amount of {{ $repair->nte }} USD.</p>@endif
        </div>
        <div class="table-wrap"><div class="table-scroll"><table class="table">
    <thead><tr><th scope="col">Task</th><th scope="col">Quantity / Unit</th><th scope="col">Rate (USD)</th><th scope="col">Total (USD)</th><th scope="col">Evidence and flags</th></tr></thead>
    <tbody>
            @forelse ($draft->lines as $line)
                <tr><td>{{ $line->task_id }} {{ $line->name }}
                    @if ($line->library_version_id)<div class="text-muted">Library version #{{ $line->library_version_id }} · {{ $line->priced_on ?? 'Unpriced' }}</div>@endif
                    @if ($line->task)<div class="text-muted">Reference range {{ $line->task->evergreen_low }} / {{ $line->task->evergreen_typical }} / {{ $line->task->evergreen_high }}</div>@endif
                </td><td>{{ $line->quantity ?? '—' }} {{ $line->unit }}
                    @if ($line->quantity_basis)<div class="text-muted">{{ Str::headline($line->quantity_basis) }}</div>@endif
                </td><td>{{ $line->rate ?? '—' }}</td><td>{{ $line->line_total ?? '—' }}</td>
                    <td>{{ implode(' · ', $line->evidence) }}
                        @if ($line->flags)<p class="text-muted">{{ implode(' · ', $line->flags) }}</p>@endif
                    </td></tr>
            @empty<tr><td colspan="5">The draft has no work items.</td></tr>@endforelse</tbody>
</table></div></div>
    </section>
@endif
@if ($repair->attempts->isNotEmpty())
    <section class="card mt-4">
        <div class="card__header"><h2 class="card__title">Analysis history</h2></div>
        <div class="table-wrap"><div class="table-scroll"><table class="table">
    <thead><tr><th scope="col">Run</th><th scope="col">Attempt</th><th scope="col">Outcome</th><th scope="col">Date</th></tr></thead>
    <tbody>
            @foreach ($repair->attempts as $attempt)
                <tr><td>{{ $attempt->run }}</td><td>{{ $attempt->attempt }}</td>
                    <td>{{ Str::headline($attempt->outcome) }} {{ $attempt->error }}</td><td>{{ $attempt->created_at->format('M j, Y H:i') }}</td></tr>
            @endforeach</tbody>
</table></div></div>
    </section>
@endif
</main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
    
@endpush
