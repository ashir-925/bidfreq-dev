<section class="sec" id="project-billing"><div class="sec__head"><h2 class="sec__title">Invoices and payments</h2></div><div class="sec__body">
@forelse ($invoices as $invoice)
@php($attempt = $invoice->payments->first(fn ($payment) => $payment->provider !== 'manual' && in_array($payment->status, ['pending', 'failed'], true)))
<article class="trade-group"><div class="trade-group__head"><strong>{{ Str::headline($invoice->invoice_type) }} invoice #{{ $invoice->id }}</strong><span class="chip">{{ Str::headline($invoice->status) }}</span></div><div class="sec__body">
@foreach ($invoice->lines as $line)
<div class="status-row"><span>{{ $line['description'] }}</span><strong>{{ \App\Services\Estimates\EstimateDisplay::currency($line['amount']) }}</strong></div>
@endforeach
<div class="status-row"><span>Invoice total</span><strong>{{ \App\Services\Estimates\EstimateDisplay::currency($invoice->total) }}</strong></div>
<div class="status-row"><span>Net paid</span><strong>{{ \App\Services\Estimates\EstimateDisplay::currency((string) $invoice->netPaid()) }}</strong></div>
<div class="status-row"><span>Outstanding balance</span><strong>{{ \App\Services\Estimates\EstimateDisplay::currency((string) $invoice->balance()) }}</strong></div>
<p>Due {{ $invoice->due_date->format('M j, Y') }} · {{ $invoice->paymentStatus() }} @if ($invoice->overdue()) <span class="chip chip--danger">Overdue</span>@endif</p>

@if ($invoice->payments->isNotEmpty())
<details class="mt-4"><summary class="btn btn--ghost btn--sm">Payment history</summary>
@foreach ($invoice->payments as $payment)
<div class="status-row"><span>{{ $payment->method ?? Str::headline($payment->provider) }} · {{ Str::headline($payment->status) }}
@if ($payment->received_on) · {{ $payment->received_on->format('M j, Y') }}@endif
@if ($staff && $payment->external_reference) · {{ $payment->external_reference }}@endif
</span><strong>{{ \App\Services\Estimates\EstimateDisplay::currency($payment->amount) }}</strong></div>
@if ($staff && $payment->status === 'refunded')
<details><summary class="btn btn--ghost btn--sm">Edit refund details</summary>
<form method="POST" action="{{ route('admin.projects.refund', [$project, $invoice, $payment]) }}">@csrf @method('PATCH')
<div class="form-row">
<label class="label">Refund date <input name="received_on" class="input" type="date" required value="{{ $payment->received_on?->toDateString() }}"></label>
<label class="label">Method <input name="method" class="input" required value="{{ $payment->method }}"></label>
<label class="label">External reference <input name="external_reference" class="input" value="{{ $payment->external_reference }}"></label>
</div><button class="btn btn--secondary btn--sm">Save refund details</button></form>
</details>
@endif
@endforeach
</details>
@endif

@if ($attempt)
<p class="mt-4">Card attempt for {{ \App\Services\Estimates\EstimateDisplay::currency($attempt->amount) }} · {{ Str::headline($attempt->status) }}</p>
<div class="page-header__actions">
<form method="POST" action="{{ route($staff ? 'admin.projects.payment.verify' : 'projects.payment.verify', [$project, $invoice]) }}">@csrf <button class="btn btn--secondary btn--sm" data-loading-text="Checking Stripe…">Check card payment</button></form>
<form method="POST" action="{{ route($staff ? 'admin.projects.payment.cancel' : 'projects.payment.cancel', [$project, $invoice]) }}">@csrf <button class="btn btn--ghost btn--sm">Clear card attempt</button></form>
</div>
@endif

@if (!$staff && $invoice->status === 'sent' && $invoice->balance()->isGreaterThanOrEqualTo('0.50'))
@if (config('project_payments.stripe.key') && config('project_payments.stripe.secret'))
<form method="POST" data-stripe-payment data-key="{{ config('project_payments.stripe.key') }}" data-verify-url="{{ route('projects.payment.verify', [$project, $invoice]) }}"
    action="{{ route('projects.payment.start', [$project, $invoice]) }}" class="mt-4">@csrf
<input type="hidden" name="reference" value="{{ $attempt?->reference ?? Str::uuid() }}">
<label class="label">Card payment amount (USD)
    <input class="input" name="amount" type="number" required min="0.50" max="{{ $invoice->balance() }}" step="0.01"
        value="{{ $attempt?->amount ?? (string) $invoice->balance() }}" @readonly($attempt)></label>
<button class="btn btn--primary btn--sm" type="button" data-prepare-payment>{{ $attempt ? 'Resume card payment' : 'Enter card details' }}</button>
<div data-payment-fields hidden class="mt-4">
    <div class="label" id="stripe-card-label-{{ $invoice->id }}">Card details</div>
    <div class="input" data-stripe-card aria-labelledby="stripe-card-label-{{ $invoice->id }}"></div>
    <button class="btn btn--primary mt-4" type="button" data-confirm-payment>Confirm card payment</button>
    <button class="btn btn--secondary mt-4" type="button" data-check-payment>Check payment status</button>
</div>
<p role="status" aria-live="polite" data-payment-feedback></p>
</form>
@endif
@endif

@if ($staff && $invoice->status !== 'cancelled')
<details class="mt-4"><summary class="btn btn--ghost btn--sm">Manage invoice</summary>
<form method="POST" action="{{ route('admin.projects.invoice', [$project, $invoice]) }}">@csrf
<div class="form-row"><label class="label">Due date <input class="input" name="due_date" type="date" required value="{{ $invoice->due_date->toDateString() }}"></label>
<label class="label">Invoice status <select class="select" name="status">
@foreach ($invoice->status === 'draft' ? ['draft','sent','cancelled'] : ['sent','cancelled'] as $state)
<option value="{{ $state }}" @selected($invoice->status === $state)>{{ Str::headline($state) }}</option>@endforeach
</select></label></div><button class="btn btn--secondary btn--sm">Save invoice status</button>
</form>
@if ($invoice->status === 'sent' && !$attempt)
<form method="POST" action="{{ route('admin.projects.payment', [$project, $invoice]) }}" class="mt-4">@csrf
<input type="hidden" name="reference" value="{{ Str::uuid() }}">
<div class="form-row">
<label class="label">Amount (USD) <input class="input" name="amount" type="number" step="0.01" min="0.01" required></label>
<label class="label">Date recorded externally <input class="input" name="received_on" type="date" value="{{ today()->toDateString() }}" required></label>
<label class="label">Method <input class="input" name="method" maxlength="80" required></label>
<label class="label">External reference <input class="input" name="external_reference" maxlength="150"></label>
<label class="label">Record type <select class="select" name="status"><option value="completed">Completed payment</option><option value="refunded">Refund recorded externally</option><option value="pending">Pending payment</option><option value="failed">Failed payment</option></select></label>
</div><button class="btn btn--secondary btn--sm">Record transaction</button></form>
@endif
</details>
@endif
</div></article>
@empty<p class="text-muted">No invoices issued.</p>@endforelse

@if ($staff && $project->status !== 'cancelled')
<details class="mt-4"><summary class="btn btn--secondary btn--sm">Create invoice</summary>
<form method="POST" action="{{ route('admin.projects.invoice', $project) }}" class="mt-4">@csrf
<input type="hidden" name="reference" value="{{ old('reference', Str::uuid()) }}">
<div class="form-row">
<label class="label">Invoice type <select class="select" name="invoice_type">
@if ($project->status !== 'completed')<option value="initial">Initial installment</option>@else<option value="final">Final balance</option>@endif
<option value="additional">Approved additional work</option></select></label>
<label class="label">Due date <input class="input" name="due_date" type="date" required></label>
<label class="label">Initial installment amount, if applicable (USD) <input class="input" name="initial_amount" type="number" min="0.01" step="0.01"></label>
<label class="label">Invoice status <select class="select" name="status"><option value="draft">Draft</option><option value="sent">Sent</option></select></label>
</div>
@php($billed = $invoices->where('status', '!=', 'cancelled')->flatMap(fn ($item) => $item->lines)->pluck('order_id')->filter())
@if ($orders->where('status', 'approved')->whereNotIn('id', $billed)->isNotEmpty())
<fieldset><legend class="label">Select Change Orders for an additional-work invoice</legend>
@foreach ($orders->where('status', 'approved')->whereNotIn('id', $billed) as $order)
<label class="label"><input type="checkbox" name="orders[]" value="{{ $order->id }}"> {{ $order->title }} · {{ \App\Services\Estimates\EstimateDisplay::currency($order->total) }}</label>
@endforeach
</fieldset>
@endif
<button class="btn btn--primary" data-loading-text="Creating invoice…">Create invoice</button>
</form></details>
@endif
</div></section>


