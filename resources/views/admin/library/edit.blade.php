@extends('admin.layout.app')
@section('title', 'Edit task · BidFreq')
@section('content')
<main class="app-content">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Console</a><span class="breadcrumbs__sep">/</span>
        <a href="{{ route('admin.library.index') }}">Pricing Library</a>
    </nav>
    <header class="page-header">
        <div class="page-header__text"><h1 class="page-header__title">Edit task</h1></div>
        <div class="page-header__actions"><a class="btn btn--secondary" href="{{ route('admin.library.show', $task) }}">View task</a></div>
    </header>

<section class="card">
    <div class="card__header"><div><h2 class="card__title">{{ $task->task_name }}</h2>
        <p class="card__subtitle">{{ $task->task_id }} · Task IDs are permanent.</p></div></div>
    <form class="card__body" method="POST" action="{{ route('admin.library.update', $task) }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="library_version_id" value="{{ is_scalar(old('library_version_id')) ? old('library_version_id') : $task->library_version_id }}">
        @include('shared.auth-field-error', ['name' => 'library_version_id'])
        <p class="text-muted">Use a numeric rate, “Quote by scope”, or “Quote by scope (reference $100)” for the rate field.</p>
        <div class="form-row">
            @foreach (array_diff_key($fields, ['task_id' => true]) as $field => $value)
                <div class="form-group">
                    <label class="label" for="{{ $field }}">{{ str($field)->headline() }}</label>
                    @php($previous = old($field, $value))
                    @if ($field === 'source')
                        <textarea class="textarea" id="{{ $field }}" name="{{ $field }}" required maxlength="2000">{{ is_scalar($previous) ? $previous : '' }}</textarea>
                    @else
                        <input class="input" id="{{ $field }}" name="{{ $field }}"
                            type="{{ in_array($field, ['effective_date', 'last_updated'], true) ? 'date' : 'text' }}"
                            value="{{ is_scalar($previous) ? $previous : '' }}" required>
                    @endif
                    @include('shared.auth-field-error', ['name' => $field])
                </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn--primary" data-loading-text="Saving task…">Save task</button>
        <a class="btn btn--secondary" href="{{ route('admin.library.show', $task) }}">Cancel</a>
    </form>
</section>
</main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
@endpush
