@extends('admin.layout.app')
@section('title', 'Import preview · BidFreq')
@section('content')
<main class="app-content">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Console</a><span class="breadcrumbs__sep">/</span>
        <a href="{{ route('admin.library.index') }}">Pricing Library</a>
    </nav>
    <header class="page-header">
        <div class="page-header__text"><h1 class="page-header__title">Import preview</h1></div>
        <div class="page-header__actions"><a class="btn btn--secondary" href="{{ route('admin.library.import') }}">Upload another file</a></div>
    </header>

<section class="card">
    <div class="card__header"><h2 class="card__title">{{ $preview->filename }}</h2></div>
    <div class="card__body">
        <p>{{ $counts['new'] }} new · {{ $counts['rate_changed'] }} rate changes · {{ $counts['updated'] }} other updates · {{ $counts['unchanged'] }} unchanged · {{ count($preview->errors) }} errors</p>
        <p class="text-muted">Tasks are matched by task ID. Tasks omitted from the file stay in the Library.</p>
        @if ($stale)
            <p class="alert alert--warning" role="alert">The Library changed after this preview. Upload the file again before applying it.</p>
        @endif
        @if ($preview->errors)
            <div class="alert alert--danger" role="alert">
                <strong>Correct these errors and upload the file again.</strong>
                <ul>@foreach ($preview->errors as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @include('shared.auth-field-error', ['name' => 'file'])
        @if (! $stale && ! $preview->errors)
            <form method="POST" action="{{ route('admin.library.apply', $preview) }}">
                @csrf
                <button type="submit" class="btn btn--primary" data-loading-text="Applying import…">Apply import</button>
            </form>
        @endif
    </div>
    <div class="table-wrap"><div class="table-scroll"><table class="table">
    <thead><tr><th scope="col">Task ID</th><th scope="col">Task</th><th scope="col">Action</th><th scope="col">Changes</th></tr></thead>
    <tbody>
        @forelse ($changes as $id => $change)
            <tr><td>{{ $id }}</td><td>{{ $change['after']['task_name'] }}</td>
                <td>{{ ['new' => 'New task', 'rate_changed' => 'Rate changed', 'updated' => 'Task updated'][$change['kind']] }}</td>
                <td>
                    @if ($change['before'] === null)
                        {{ $change['after']['bidfreq_standard_rate'] }} · {{ $change['after']['unit'] }}
                    @else
                        <details><summary>View changed fields</summary><dl>
    @foreach ($change['after'] as $field => $value)
        @if ($change['before'] === null || ($change['before'][$field] ?? null) !== $value)
            <dt>{{ str($field)->headline() }}</dt><dd>{{ $change['before'][$field] ?? '—' }} → {{ $value }}</dd>
        @endif
    @endforeach
</dl></details>
                    @endif
                </td></tr>
        @empty
            <tr><td colspan="4">No task changes to display.</td></tr>
        @endforelse</tbody>
</table></div></div>
</section>
</main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
@endpush
