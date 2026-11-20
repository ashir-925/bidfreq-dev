@extends('admin.layout.app')
@section('title', 'Task history · BidFreq')
@section('content')
<main class="app-content">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Console</a><span class="breadcrumbs__sep">/</span>
        <a href="{{ route('admin.library.index') }}">Pricing Library</a>
    </nav>
    <header class="page-header">
        <div class="page-header__text"><h1 class="page-header__title">Task history</h1></div>
        <div class="page-header__actions"><a class="btn btn--secondary" href="{{ route('admin.library.show', $task) }}">View task</a></div>
    </header>

<p>{{ $task->task_id }} · {{ $task->task_name }}</p>
<div class="table-wrap"><div class="table-scroll"><table class="table">
    <thead><tr><th scope="col">Version</th><th scope="col">Changed by</th><th scope="col">Date</th><th scope="col">Changes</th></tr></thead>
    <tbody>
    @forelse ($changes as $change)
        <tr><td>#{{ $change->library_version_id }} · {{ ucfirst($change->version->action) }}
                @if ($change->version->filename)<div class="text-muted">{{ $change->version->filename }}</div>@endif
            </td><td>{{ $change->version->user->name }}</td><td>{{ $change->created_at->format('Y-m-d H:i') }}</td>
            <td><details><summary>{{ $change->before === null ? 'Task added' : 'View changes' }}</summary>
                <dl>
    @foreach ($change->after as $field => $value)
        @if ($change->before === null || ($change->before[$field] ?? null) !== $value)
            <dt>{{ str($field)->headline() }}</dt><dd>{{ $change->before[$field] ?? '—' }} → {{ $value }}</dd>
        @endif
    @endforeach
</dl>
            </details></td></tr>
    @empty
        <tr><td colspan="4">No changes recorded.</td></tr>
    @endforelse</tbody>
</table></div></div>
@include('admin.library.pagination', ['pages' => $changes])
</main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
@endpush
