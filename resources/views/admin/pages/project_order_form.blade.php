<form method="POST" data-order-form action="{{ route('admin.projects.orders.save', $order ? [$project, $order] : [$project]) }}" class="mt-4">@csrf
<label class="label">Change Order title <input name="title" class="input" value="{{ $order?->title }}" required maxlength="255"></label>
<label class="label">Related report <select name="report_id" class="select"><option value="">No related report</option>
@foreach ($reportOptions as $report)<option value="{{ $report->id }}" @selected($order?->project_daily_report_id === $report->id)>{{ $report->report_date->format('M j, Y') }}</option>@endforeach
</select></label>
<div data-order-rows>
@foreach ($order?->lines ?? [[]] as $line)
<fieldset class="form-row" data-order-row>
<legend class="label">Work item</legend>
<label class="label">Library task <select class="select" name="lines[{{ $loop->index }}][task_id]"><option value="">Unmatched work</option>
@foreach ($tasks as $task)<option value="{{ $task->task_id }}" @selected(($line['task_id'] ?? '') === $task->task_id)>{{ $task->task_id }} · {{ $task->task_name }} · {{ $task->unit }} · {{ $task->bidfreq_standard_rate === null ? 'Quote by scope' : \App\Services\Estimates\EstimateDisplay::currency($task->bidfreq_standard_rate) }}</option>@endforeach
</select></label>
<label class="label">Description for unmatched work <input class="input" name="lines[{{ $loop->index }}][description]" value="{{ empty($line['task_id']) ? ($line['name'] ?? '') : '' }}"></label>
<label class="label">Quantity <input class="input" inputmode="decimal" name="lines[{{ $loop->index }}][quantity]" value="{{ isset($line['quantity']) ? \App\Services\Estimates\EstimateDisplay::quantity($line['quantity']) : '' }}"></label>
<label class="label">Admin quote or override rate (USD) <input class="input" inputmode="decimal" name="lines[{{ $loop->index }}][rate]" value="{{ (!empty($line['override_reason']) || empty($line['library_rate'])) ? ($line['rate'] ?? '') : '' }}"></label>
<label class="label">Override reason <input class="input" name="lines[{{ $loop->index }}][reason]" value="{{ $line['override_reason'] ?? '' }}"></label>
<label class="label"><input type="checkbox" name="lines[{{ $loop->index }}][confirmed]" value="1" @checked($line['confirmed'] ?? false)> Confirm Library unit and quantity</label>
<button type="button" data-remove-order-line class="btn btn--ghost btn--sm">Remove line</button>
</fieldset>
@endforeach
</div>
<button type="button" data-add-order-line class="btn btn--secondary btn--sm">Add line</button>
<button class="btn btn--primary" data-loading-text="Saving draft…">Save draft</button>
</form>
