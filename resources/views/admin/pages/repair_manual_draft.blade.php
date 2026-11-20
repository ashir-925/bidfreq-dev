<section class="card mt-4">
    <div class="card__header"><h2 class="card__title">Prepare a manual draft</h2></div>
    <form class="card__body" method="POST" action="{{ route('admin.repair-requests.manual', $repair) }}">
        @csrf
        <div class="form-row">
            @foreach (['complaint', 'cause', 'correction', 'complications'] as $field)
                <div class="form-group">
                    <label class="label" for="manual-{{ $field }}">{{ ucfirst($field) }}</label>
                    <textarea class="textarea" id="manual-{{ $field }}" name="{{ $field }}" required maxlength="5000">{{ is_string(old($field)) ? old($field) : '' }}</textarea>
                    @include('shared.auth-field-error', ['name' => $field])
                </div>
            @endforeach
        </div>
        <div id="manual-lines"></div>
        <template id="manual-line-template">
            <fieldset class="form-row">
                <legend class="label">Library item</legend>
                <label class="form-group">Task
                    <select class="select" data-field="task_id" required>
                        <option value="">Select a Library task</option>
                        @foreach ($libraryTasks as $task)
                            <option value="{{ $task->task_id }}" data-quote="{{ $task->pricing_mode === 'quote_by_scope' ? '1' : '0' }}">{{ $task->task_id }} · {{ $task->task_name }} ({{ $task->unit }})</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-group">Quantity <input class="input" data-field="quantity" type="number" min="0.000001" step="0.000001" required></label>
                <label class="form-group">Quote-by-scope rate (USD) <input class="input" data-field="rate" type="number" min="0" step="0.000001" disabled></label>
                <button class="btn btn--ghost btn--sm" type="button" data-remove>Remove item</button>
            </fieldset>
        </template>
        <div class="page-header__actions">
            <button class="btn btn--secondary" type="button" id="manual-add">Add Library item</button>
            <button class="btn btn--primary" type="submit" data-loading-text="Saving draft…">Save internal draft</button>
        </div>
    </form>
</section>
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/repair-manual-draft.js') }}" defer></script>
@endpush
