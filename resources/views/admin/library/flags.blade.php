@if (in_array($task->task_id, \App\Models\LibraryTask::AMBIGUOUS, true))
    <span class="badge badge--blue">Confirm unit and quantity</span>
@endif
@if (in_array($task->task_id, \App\Models\LibraryTask::WIDE_RANGE, true))
    <span class="badge badge--violet">Wide reference range</span>
@endif
@if ($task->times_seen_scopes === 1)
    <span class="badge badge--blue">Provisional</span>
@endif
