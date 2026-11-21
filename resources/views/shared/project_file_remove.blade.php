@if ($staff && $open && $file->kind !== 'certificate')
    <form method="POST" action="{{ route('admin.projects.files.remove', [$project, $file]) }}" data-confirm-file-removal>
        @csrf
        @method('DELETE')
        <button class="btn btn--danger-outline btn--sm" aria-label="Delete {{ $file->name }}" data-loading-text="Deleting…">Delete file</button>
    </form>
@endif
