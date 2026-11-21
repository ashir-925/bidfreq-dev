@extends('admin.layout.app')
@section('title', 'Review & Verify Estimate · BidFreq')
@section('content')
<main class="app-content">
    <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('admin.dashboard') }}">Console</a><span class="breadcrumbs__sep">/</span><a href="{{ route('admin.estimates') }}">Estimate Queue</a><span class="breadcrumbs__sep">/</span><span class="breadcrumbs__current">{{ $repair?->number() ?? 'Review & Verify' }}</span></nav>
    <header class="page-header">
        <div class="page-header__text"><h1 class="page-header__title">Review &amp; Verify Estimate</h1>
            <p class="page-header__subtitle">Edit the AI draft, add internal notes, and issue the final human-verified estimate to the customer.</p>
        </div>
        @if ($repair?->draft && in_array($repair->status, ['draft','under_review','revision_requested'], true))
            <div class="page-header__actions"><button type="submit" form="estimate-review-form" class="btn btn--secondary" data-loading-text="Saving review…"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M7 3v5h8V3M7 21v-8h10v8"/></svg>Save draft</button></div>
        @endif
    </header>
    @if ($repair)
        @php($draft = $repair->draft)
        @php($photoEvidence = $repair->files->where('kind', 'photo'))
        @php($supportingDocuments = $repair->files->where('kind', 'document'))
        @php($linkedEvidence = session()->hasOldInput('review_version') ? old('evidence_links', []) : ($draft->analysis['evidence_links'] ?? []))
        @php($attachmentNumbers = $repair->files->values()->mapWithKeys(fn ($file, $index) => [$file->id => $index + 1]))
        @php($editable = $draft && in_array($repair->status, ['draft', 'under_review', 'revision_requested'], true))
        <div class="review-grid">
            <div class="review-main">
                <section class="rev-header">
                    <div class="rev-header__top">
                        <div class="rev-header__eyebrow">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>
                            @if (isset($draft->analysis['confidence'])) AI Draft · {{ round($draft->analysis['confidence'] * 100) }}% confidence · @endif {{ $editable ? 'Awaiting Human Verification' : Str::headline($repair->status) }}
                        </div>
                        <div class="rev-header__id"><span class="review-reference">{{ $repair->number() }}</span> <span @class(['chip', 'chip--danger' => in_array($repair->priority, ['high', 'urgent'], true)])><span class="chip__dot"></span>{{ ucfirst($repair->priority) }} Priority</span></div>
                        <div class="rev-header__property">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>{{ $repair->street_address }}, {{ $repair->city }}, {{ $repair->state }} {{ $repair->zip }}</span>
                            <span class="dot"></span><span>{{ collect($repair->descriptions)->pluck('trade')->unique()->implode(' / ') }}</span>
                            @php($photoCount = $repair->files->whereIn('mime', \App\Models\RepairRequest::PHOTO_MIMES)->count())
                            <span class="dot"></span><span>{{ $photoCount }} {{ Str::plural('photo', $photoCount) }}</span>
                        </div>
                    </div>
                    <div class="rev-header__meta">
                        <div><div class="rev-header__meta-label">Customer</div><div class="rev-header__meta-value">{{ $repair->customer->company_name ?: $repair->customer->name }}</div></div>
                        <div><div class="rev-header__meta-label">Submitted</div><div class="rev-header__meta-value">{{ $repair->created_at->format('M j, Y · g:i A') }}</div></div>
                        @if (isset($draft->analysis['confidence']))<div><div class="rev-header__meta-label">AI drafted</div><div class="rev-header__meta-value">{{ $draft->created_at->format('M j, Y · g:i A') }}</div></div>@endif
                        @if ($repair->currentRelease)<div><div class="rev-header__meta-label">Latest release</div><div class="rev-header__meta-value">Version {{ $repair->currentRelease->version }}</div></div>@endif
                    </div>
                </section>
                @if ($repair->status === 'revision_requested')
                    <p class="alert alert--warning">Customer revision requested: {{ $repair->currentRelease?->customer_comment }}</p>
                @endif
                @if (in_array($repair->status, ['ai_failed', 'under_review'], true))
                    <form method="POST" action="{{ route('admin.repair-requests.retry', $repair) }}" class="mt-4">
                        @csrf
                        <button class="btn btn--primary" type="submit" data-loading-text="Queueing analysis…">Retry AI</button>
                    </form>
                    @if (! $draft) @include('admin.pages.repair_manual_draft') @endif
                @endif
                @if ($repair->status === 'needs_info' || $repair->status === 'declined')
                    <p class="alert alert--info">{{ $repair->events->firstWhere('action', $repair->status)?->details['message'] ?? '' }}</p>
                @endif
                @if ($draft)
                    <section class="sec">
                        <div class="sec__head"><div class="sec__head-left"><div class="sec__icon sec__icon--violet">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>
                </div><div>
                            <div class="sec__title">Scope of Work</div>
                            <div class="sec__sub">{{ $editable ? 'Review the 4Cs, quantities and flagged lines before approval.' : 'Estimate review record' }}</div>
                        </div></div>
                            @if (isset($draft->analysis['confidence']))<span class="tag-ai"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg>AI draft v{{ $repair->ai_run ?? 1 }}</span>@endif
                        </div>
                        <div class="sec__body">
                            @if ($editable)
                                <form method="POST" action="{{ route('admin.repair-requests.save', $repair) }}" id="estimate-review-form">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="review_version" value="{{ is_scalar(old('review_version')) ? old('review_version') : $draft->review_version }}">
                            @endif

                            @if ($editable)
                                <div class="diff-banner"><div class="diff-banner__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 11v5m0-8h.01"/></svg></div><div class="diff-banner__text"><strong>{{ isset($draft->analysis['confidence']) ? 'You are editing the AI-generated draft.' : 'You are editing the estimate draft.' }}</strong> Saved changes are recorded in the audit log.</div></div>
                            @endif
                            <div class="trade-group">
                                <div class="trade-group__head">
                                    <div class="trade-group__title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>{{ collect($repair->descriptions)->pluck('trade')->unique()->implode(' / ') }}</div>
                                    <div class="trade-group__total">{{ \App\Services\Estimates\EstimateDisplay::currency($draft->total) }}</div>
                                </div>
                            <div class="table-scroll"><table class="line-items review-line-items">
                                <thead><tr><th>Description</th><th>Qty / unit</th><th class="num">Unit rate</th><th class="num">Total</th><th>Line actions</th></tr></thead>
                                <tbody id="review-lines">
                                    @foreach ($draft->lines as $i => $line)
                                        <tr @class(['line-item-edited' => $line->override_reason || old("lines.$i.exclude")])>
                                            <td class="desc">
                                                <strong>{{ $line->name }}</strong>
                                                @if ($line->task_id)<span>{{ $line->task_id }}</span>@endif
                                                @foreach ($line->flags ?? [] as $flag)
                                                    @php([$message, $target] = \App\Services\Estimates\EstimateDisplay::reviewFlag($flag))
                                                    <div class="flag flag--warning review-line-flag"><span class="flag__body">{{ $message }}</span>@if ($editable)<button type="button" class="btn btn--ghost btn--sm" data-resolve-line="{{ $target }}">Resolve</button>@endif</div>
                                                @endforeach
                                            </td>
                                            <td><span class="qty-cell"><strong>{{ \App\Services\Estimates\EstimateDisplay::quantity($line->quantity) }}</strong><span>{{ $line->unit }}</span></span></td>
                                            <td class="num">{{ $line->rate === null ? 'Unpriced' : \App\Services\Estimates\EstimateDisplay::currency($line->rate) }}</td>
                                            <td class="num">{{ \App\Services\Estimates\EstimateDisplay::currency($line->line_total) }}</td>
                                            <td class="review-row-action">
                                                <button class="item-action" type="button" data-line-editor="line-{{ $i }}-details" aria-controls="line-{{ $i }}-details" aria-expanded="{{ $errors->has("lines.$i.*") ? 'true' : 'false' }}" aria-label="{{ $editable ? 'Edit' : 'View' }} line {{ $i + 1 }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                                    {{ $editable ? 'Edit' : 'Details' }}
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="review-line-details" id="line-{{ $i }}-details" @unless ($errors->has("lines.$i.*")) hidden @endunless>
                                            <td colspan="5">
                                                <div class="review-line-fields">
                                                    @if ($editable)
                                                        <input type="hidden" name="lines[{{ $i }}][line_id]" value="{{ $line->id }}">
                                                        <div class="form-group"><label class="label" for="line-{{ $i }}-task_id">Pricing Library task</label>
                                                            <input id="line-{{ $i }}-task_id" type="hidden" aria-label="Library task for line {{ $i + 1 }}" name="lines[{{ $i }}][task_id]" value="{{ is_scalar(old("lines.$i.task_id", $line->task_id)) ? old("lines.$i.task_id", $line->task_id) : '' }}">
                                                            @include('shared.auth-field-error', ['name' => "lines.$i.task_id"])
                                                        </div>
                                                        <div class="form-group"><label class="label" for="line-{{ $i }}-quantity">Quantity</label>
                                                        <input id="line-{{ $i }}-quantity" class="input" name="lines[{{ $i }}][quantity]" aria-label="Quantity for line {{ $i + 1 }}" inputmode="decimal" value="{{ is_scalar(old("lines.$i.quantity", $line->quantity === null ? '' : \App\Services\Estimates\EstimateDisplay::quantity($line->quantity))) ? old("lines.$i.quantity", $line->quantity === null ? '' : \App\Services\Estimates\EstimateDisplay::quantity($line->quantity)) : '' }}">
                                                        @include('shared.auth-field-error', ['name' => "lines.$i.quantity"])</div>
                                                        <div class="form-group"><label class="label" for="line-{{ $i }}-rate">Admin rate (USD)</label>
                                                        <input id="line-{{ $i }}-rate" class="input" name="lines[{{ $i }}][rate]" aria-label="Admin rate for line {{ $i + 1 }}" inputmode="decimal" value="{{ is_scalar(old("lines.$i.rate", $line->override_reason && $line->rate !== null ? \App\Services\Estimates\EstimateDisplay::quantity($line->rate) : null)) ? old("lines.$i.rate", $line->override_reason && $line->rate !== null ? \App\Services\Estimates\EstimateDisplay::quantity($line->rate) : null) : '' }}">
                                                        @include('shared.auth-field-error', ['name' => "lines.$i.rate"])</div>
                                                        <div class="form-group"><label class="label" for="line-{{ $i }}-override_reason">Rate override reason</label>
                                                        <input id="line-{{ $i }}-override_reason" class="input" name="lines[{{ $i }}][override_reason]" aria-label="Override reason for line {{ $i + 1 }}" maxlength="1000" value="{{ is_string(old("lines.$i.override_reason", $line->override_reason)) ? old("lines.$i.override_reason", $line->override_reason) : '' }}">
                                                        @include('shared.auth-field-error', ['name' => "lines.$i.override_reason"])</div>
                                                        <p class="text-muted review-line-help">Leave Admin rate blank to use the Library rate. Enter an Admin rate for a quote-by-scope task. Give a reason when replacing a Library rate.</p>
                                                        <div class="review-line-checks">
                                                            <label class="review-check"><input type="checkbox" name="lines[{{ $i }}][confirmed]" value="1" @checked(old("lines.$i.confirmed"))> Confirm Library unit and quantity</label>
                                                            <label class="review-check"><input type="checkbox" name="lines[{{ $i }}][exclude]" value="1" @checked(old("lines.$i.exclude"))> Exclude from this estimate</label>
                                                        </div>
                                                    @endif
                                                    <div class="review-line-help">
                                                        @if ($line->original_rate !== null)<p>Original Library rate {{ \App\Services\Estimates\EstimateDisplay::currency($line->original_rate) }} · {{ $line->override_reason }}</p>@endif
                                                        @if ($line->library_version_id)
                                                    @php($snapshot = $librarySnapshots->get($line->task_id.':'.$line->library_version_id))
                                                    @php($dataVersion = $snapshot?->after['version'] ?? ($line->task?->library_version_id === $line->library_version_id ? $line->task?->version : null))
                                                    <div class="text-muted">Data {{ $dataVersion ?: 'version unavailable' }} · Library {{ $snapshot?->version?->action === 'edit' ? 'revision' : 'import' }} #{{ $line->library_version_id }} · {{ $line->priced_on ?? ($line->rate === null ? 'Unpriced' : 'Pricing date unavailable') }} · {{ $line->source === 'ai' ? 'AI' : ucfirst($line->source) }}</div>
                                                @endif
                                                @if ($line->task)<div class="text-muted">Reference range {{ \App\Services\Estimates\EstimateDisplay::currency($line->task->evergreen_low) }} / {{ \App\Services\Estimates\EstimateDisplay::currency($line->task->evergreen_typical) }} / {{ \App\Services\Estimates\EstimateDisplay::currency($line->task->evergreen_high) }} · seen {{ $line->task->times_seen_scopes }}
                                                    @if ($line->task->times_seen_scopes === 1) · Provisional @endif
                                                    <br>Reference hours {{ \App\Services\Estimates\EstimateDisplay::hours($line->task->standard_hours) }} · Reference labor cost {{ \App\Services\Estimates\EstimateDisplay::currency($line->task->labor_cost_market_avg) }}</div>@endif
                                                @if ($line->evidence)<p class="text-muted"><strong>Supporting evidence:</strong> {{ preg_replace_callback('/\bAttachment ID\s*:\s*(\d+)\b/i', fn ($match) => isset($attachmentNumbers[$match[1]]) ? 'Attachment #'.$attachmentNumbers[$match[1]] : 'Submitted evidence', implode(' · ', $line->evidence)) }}</p>@endif
                                                @if ($line->quantity_basis)<p class="text-muted"><strong>Quantity source:</strong> {{ Str::headline($line->quantity_basis) }}</p>@endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table></div></div>
                            @if ($editable)
                                <template id="review-tasks" data-search-placeholder="Search task name or ID">
                                    @foreach ($libraryTasks as $task)
                                        @php($taskLabel = $task->task_name.' · '.$task->unit.' · '.($task->bidfreq_standard_rate === null ? 'Quote by scope' : \App\Services\Estimates\EstimateDisplay::currency($task->bidfreq_standard_rate)))
                                        <button type="button" data-task-id="{{ $task->task_id }}" data-task-name="{{ $task->task_name }}">{{ $task->task_id }} · {{ $taskLabel }}</button>
                                    @endforeach
                                </template>
                                <template id="review-line-template"><tr>
                                    <td colspan="5"><div class="review-line-fields"><div class="form-group"><span class="label">Pricing Library task</span><input type="hidden" aria-label="Pricing Library task" data-review-field="task_id"></div>
                                    <label class="form-group"><span class="label">Quantity</span><input class="input" inputmode="decimal" aria-label="Quantity" data-review-field="quantity"></label>
                                    <label class="form-group"><span class="label">Admin rate (USD)</span><input class="input" inputmode="decimal" aria-label="Admin rate" data-review-field="rate"></label>
                                        <label class="form-group"><span class="label">Rate override reason</span><input class="input" aria-label="Override reason" maxlength="1000" data-review-field="override_reason"></label>
                                    <div class="review-line-checks"><label class="review-check"><input type="checkbox" value="1" data-review-field="confirmed"> Confirm Library unit and quantity</label>
                                        <button class="btn btn--ghost btn--sm" type="button" data-remove-line>Remove new line</button></div></div></td>
                                </tr></template>
                                <button type="button" class="btn btn--secondary btn--sm btn--block" id="add-review-line" data-next-index="{{ $draft->lines->count() }}">Add Library item</button>
                            @endif
                            <div class="review-analysis-heading"><div class="sec__title">Assessment and supporting evidence</div><div class="sec__sub">Review the four findings and link their supporting photos. Link documents in Submitted Evidence.</div></div>
                            <div class="form-row review-analysis-fields">
                                @foreach (['complaint', 'cause', 'correction', 'complications', 'assumptions', 'exclusions'] as $field)
                                    @php($value = $draft->analysis[$field] ?? '')
                                    @php($previous = old($field, is_array($value) ? implode("\n", $value) : $value))
                                    <div class="form-group">
                                        <label class="label" for="review-{{ $field }}">{{ match ($field) { 'complaint' => 'Complaint · What was reported?', 'cause' => 'Cause · What led to the damage?', 'correction' => 'Correction · What work is needed?', 'complications' => 'Complications · What could affect the work?', 'assumptions' => 'Assumptions · Conditions used in this estimate', 'exclusions' => 'Exclusions · Work outside this estimate' } }}</label>
                                        @if ($editable)
                                            <textarea class="textarea" id="review-{{ $field }}" name="{{ $field }}" maxlength="5000" @required(in_array($field, ['complaint','cause','correction','complications'], true))>{{ is_string($previous) ? $previous : '' }}</textarea>
                                            @include('shared.auth-field-error', ['name' => $field])

                                        @else
                                            <p>{{ is_array($value) ? implode(' · ', $value) : $value }}</p>
                                        @endif
                                        @if (in_array($field, ['complaint','cause','correction','complications'], true) && $repair->files->isNotEmpty())
                                            @php($selected = array_map('strval', (array) data_get($linkedEvidence, $field, [])))
                                            <div data-evidence-links>
                                                <div class="review-evidence-chips">
                                                    @foreach ($photoEvidence as $file)
                                                        <button type="button" class="chip review-evidence-chip" data-preview-file="{{ $file->id }}" data-evidence-chip="{{ $file->id }}" title="{{ $file->name }}" @if (!in_array((string) $file->id, $selected, true)) hidden @endif>
                                                            @if (in_array($file->mime, \App\Models\RepairRequest::PHOTO_MIMES, true))<img src="{{ route('admin.repair-requests.image', [$repair, $file]) }}" alt="" loading="lazy">@endif
                                                            #{{ $attachmentNumbers[$file->id] }} <span>{{ $file->name }}</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                                @if ($editable && $photoEvidence->isNotEmpty())
                                                    <details class="review-evidence-options">
                                                        <summary class="btn btn--ghost btn--sm">Edit linked photos</summary>
                                                        @foreach ($photoEvidence as $file)
                                                            <label class="review-check"><input type="checkbox" name="evidence_links[{{ $field }}][]" value="{{ $file->id }}" data-link-file @checked(in_array((string) $file->id, $selected, true))> #{{ $attachmentNumbers[$file->id] }} · {{ $file->name }}</label>
                                                        @endforeach
                                                    </details>
                                                @endif
                                                <a class="btn btn--ghost btn--sm" href="#submitted-evidence">See submitted evidence</a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @if ($editable)
                                <div class="note-box mt-4">
                                    <div class="note-box__tabs"><label for="review-note" class="note-box__tab is-active">Internal review note (required)</label></div>
                                    <div class="note-box__body">
                                        <textarea class="textarea" placeholder="Record what you verified or changed" id="review-note" name="review_note" required maxlength="2000">{{ is_string(old('review_note')) ? old('review_note') : '' }}</textarea>
                                        @include('shared.auth-field-error', ['name' => 'review_note'])
                                    </div>
                                </div>
                                @if ($errors->any())<ul class="alert alert--danger">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
                                <button class="btn btn--secondary" type="submit" data-loading-text="Saving review…">Save changes</button>
                                </form>
                            @endif


                        </div>
                    </section>
                    @if (! empty($draft->analysis['risk_flags']) || ! empty($draft->analysis['uncertainty']))
                        <section class="sec"><div class="sec__head"><div class="sec__head-left"><div class="sec__icon sec__icon--warning">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div><div class="sec__title">AI-detected risk flags</div></div></div><div class="sec__body">
                            @foreach ($draft->analysis['risk_flags'] ?? [] as $risk)
                                <div class="flag flag--warning"><div class="flag__body">{{ rtrim($risk, '.') }}. Check the submitted evidence and document your decision in the review note.</div>
                                    @if ($editable)<a class="btn btn--ghost btn--sm" href="#review-note">Resolve</a>@endif
                                </div>
                            @endforeach
                            <p>{{ $draft->analysis['uncertainty'] ?? '' }}</p>
                        </div></section>
                    @endif
                @endif
                @if ($repair->attempts->isNotEmpty() || $repair->events->isNotEmpty())
                    <section class="sec"><div class="sec__head"><div class="sec__title">Notes &amp; audit log</div></div><div class="sec__body">
                        @foreach ($repair->events as $event)<p>{{ $event->created_at->format('M j, Y H:i') }} · {{ Str::headline($event->action) }} · User #{{ $event->user_id }} @if (! empty($event->details['note'])) · {{ $event->details['note'] }} @endif @if (! empty($event->details['message'])) · {{ $event->details['message'] }} @endif</p>@endforeach
                        @foreach ($repair->attempts as $attempt)<p>Run {{ $attempt->run }}, attempt {{ $attempt->attempt }} · {{ ucfirst($attempt->outcome) }} {{ $attempt->error }}</p>@endforeach
                    </div></section>
                @endif
            </div>
            <aside class="review-side">
                @if ($draft || $repair->nte !== null)
                    <div class="action-card">
                        <div class="action-card__top">
                            <div class="action-card__label">{{ $editable ? 'Draft Total' : 'Estimate Total' }}</div>
                            <div class="action-card__amount">{{ $draft ? '$'.\App\Services\Estimates\EstimateDisplay::money($draft->total) : '—' }}</div>
                            <div class="action-card__sub">
                                @if ($repair->nte === null) No NTE specified.
                                @elseif (! $draft || $draft->lines->isEmpty() || $draft->lines->contains(fn ($line) => $line->line_total === null))
                                    Cannot check yet: unpriced lines remain.
                                @elseif ($draft->over_nte)<span class="chip status--revision">Over NTE of {{ \App\Services\Estimates\EstimateDisplay::currency($repair->nte) }}</span>
                                @else Within NTE of {{ \App\Services\Estimates\EstimateDisplay::currency($repair->nte) }}
                                @endif
                            </div>
                        </div>
                        <div class="action-card__body">
                            @if ($editable)
                                <form method="POST" action="{{ route('admin.repair-requests.release', $repair) }}">
                                    @csrf
                                    <input type="hidden" name="review_version" value="{{ $draft->review_version }}">
                                    <button class="btn btn--success btn--lg btn--block" type="submit" data-loading-text="Releasing estimate…">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                                        Verify &amp; Send to Customer
                                    </button>
                                </form>
                                <a class="btn btn--secondary btn--block" href="#request-message"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9 9 0 0 0-6.7 3M3 3v6h6"/></svg>Request Info from Customer</a>
                            @endif
                            <div class="divider"></div>
                            <a class="btn btn--ghost btn--block" href="#submitted-evidence">Submitted Evidence</a>
                        </div>
                    </div>
                @endif
                @if (isset($draft->analysis['confidence']))
                    @php($confidence = (int) round($draft->analysis['confidence'] * 100))
                    <section class="sec"><div class="sec__body"><div class="conf-widget">
                        <div class="conf-widget__head"><span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 2l2.4 7.4H22l-6 4.5 2.3 7.1-6.3-4.6L5.7 21 8 14 2 9.5h7.6z"/></svg> AI Confidence</span><span class="conf-widget__value">{{ $confidence }}%</span></div>
                        <div class="confidence"><div class="confidence__bar"><div class="confidence__fill" style="width:{{ $confidence }}%"></div></div></div>
                        <div class="conf-widget__note">{{ $draft->analysis['uncertainty'] ?? '' }}</div>
                    </div></div></section>
                @endif
                <section class="sec" id="submitted-evidence">
                    <div class="sec__head"><div class="sec__head-left"><div class="sec__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div><div><div class="sec__title">Submitted Evidence</div><div class="sec__sub">{{ $repair->files->count() }} {{ Str::plural('attachment', $repair->files->count()) }} from customer</div></div></div></div>
                    <div class="sec__body">
                        @if ($photoEvidence->isNotEmpty())
                            <div class="photo-grid">
                                @foreach ($photoEvidence as $file)
                                    <button type="button" class="photo-tile review-photo" data-preview-file="{{ $file->id }}" data-file-name="{{ $file->name }}" data-file-number="{{ $attachmentNumbers[$file->id] }}" data-file-download="{{ route('admin.repair-requests.download', [$repair, $file]) }}" aria-label="Preview attachment #{{ $attachmentNumbers[$file->id] }}: {{ $file->name }}">
                                        @if (in_array($file->mime, \App\Models\RepairRequest::PHOTO_MIMES, true))
                                            <img class="photo-tile__img" src="{{ route('admin.repair-requests.image', [$repair, $file]) }}" alt="" loading="lazy">
                                        @else
                                            <span class="photo-tile__img">File</span>
                                        @endif
                                        <span class="photo-tile__badge">#{{ $attachmentNumbers[$file->id] }}</span>
                                        <span class="photo-tile__label">{{ $file->name }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @if ($supportingDocuments->isNotEmpty())
                            <div class="review-details">
                                <div class="label">Supporting documents</div>
                                @foreach ($supportingDocuments as $file)
                                    <div class="status-row"><a class="status-row__value" href="{{ route('admin.repair-requests.download', [$repair, $file]) }}">#{{ $attachmentNumbers[$file->id] }} · {{ $file->name }}</a></div>
                                    @if ($draft)
                                        @php($fields = ['complaint', 'cause', 'correction', 'complications'])
                                        @if ($editable)
                                            <fieldset class="review-document-fields">
                                                <legend class="label">Findings supported by document #{{ $attachmentNumbers[$file->id] }}</legend>
                                                @foreach ($fields as $field)
                                                    <label class="review-check"><input type="checkbox" form="estimate-review-form" name="evidence_links[{{ $field }}][]" value="{{ $file->id }}" @checked(in_array((string) $file->id, array_map('strval', (array) data_get($linkedEvidence, $field, [])), true))> {{ ucfirst($field) }}</label>
                                                @endforeach
                                            </fieldset>
                                        @else
                                            <p class="text-muted">{{ collect($fields)->filter(fn ($field) => in_array((string) $file->id, array_map('strval', (array) data_get($linkedEvidence, $field, [])), true))->map(fn ($field) => ucfirst($field))->implode(', ') ?: 'No linked findings' }}</p>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        @if ($repair->files->isEmpty())<p class="text-muted">No attachments were submitted.</p>@endif
                        <div class="review-details">
                            <div class="status-row"><span class="status-row__label">Property</span><span class="status-row__value">
                                @if (filled($repair->property_name)){{ trim($repair->property_name) }}<br>@endif
                                {{ $repair->street_address }}, {{ $repair->city }}, {{ $repair->state }} {{ $repair->zip }}
                            </span></div>
                            <div class="status-row"><span class="status-row__label">Home size</span><span class="status-row__value">{{ \App\Services\Estimates\EstimateDisplay::quantity($repair->home_sqft) }} sq ft · {{ $repair->stories }} {{ Str::plural('story', $repair->stories) }}</span></div>
                            <div class="label">Repair description</div>
                            @foreach ($repair->descriptions as $item)<p><strong>{{ $item['trade'] }}</strong> · {{ $item['description'] }}</p>@endforeach
                            @if ($repair->dimensions)
                                <div class="label">Customer-provided damaged-area measurements</div>
                                @foreach ($repair->dimensions as $area)
                                    <p>{{ $area['label'] }}
                                        @foreach (['length' => 'Length', 'width' => 'Width', 'height' => 'Height', 'area' => 'Area'] as $key => $label)
                                            @if (isset($area[$key])) · {{ $label }} {{ \App\Services\Estimates\EstimateDisplay::quantity((string) $area[$key]) }} {{ $key === 'area' ? 'sq ft' : 'ft' }} @endif
                                        @endforeach
                                    </p>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </section>
                <dialog class="modal review-preview" id="review-evidence-preview" aria-labelledby="review-preview-title">
                    <div class="modal__header"><h2 class="modal__title" id="review-preview-title"></h2><button type="button" class="modal__close" data-close-preview aria-label="Close preview">×</button></div>
                    <div class="modal__body"><img alt="" hidden><p hidden data-preview-fallback>Download this file to view it.</p></div>
                    <div class="modal__footer"><a class="btn btn--secondary" data-preview-download>Download file</a></div>
                </dialog>
                @foreach (\App\Services\Estimates\EstimateDisplay::measurementWarnings($repair) as $warning)
                    <p class="alert alert--warning" role="alert">{{ $warning }}</p>
                @endforeach
                @if (in_array($repair->status, ['draft','under_review','ai_failed','revision_requested'], true))
                    <section class="sec"><div class="sec__head"><div class="sec__title">Request information or decline</div></div><div class="sec__body">
                        <form method="POST" action="{{ route('admin.repair-requests.disposition', $repair) }}">
                            @csrf
                            <label class="label" for="request-message">Message to customer (required)</label>
                            <textarea class="textarea" id="request-message" name="message" maxlength="2000" required>{{ is_string(old('message')) ? old('message') : '' }}</textarea>
                            @include('shared.auth-field-error', ['name' => 'message'])
                            <div class="page-header__actions">
                                <button class="btn btn--secondary" type="submit" name="action" value="needs_info" data-loading-text="Sending request…">Request more information</button>
                                <button class="btn btn--danger" type="submit" name="action" value="declined" data-loading-text="Rejecting request…">Reject request</button>
                            </div>
                        </form>
                    </div></section>
                @endif
                <section class="sec mt-4"><div class="sec__head"><div class="sec__title">Not-to-Exceed</div></div><div class="sec__body">
                    <form method="POST" action="{{ route('admin.repair-requests.nte', $repair) }}">
                        @csrf
                        @method('PATCH')
                        <label for="staff-nte" class="label">Amount (USD)</label>
                        <input class="input" id="staff-nte" name="nte" type="number" min="0" step="0.01" value="{{ $repair->nte }}">
                        @include('shared.auth-field-error', ['name' => 'nte'])
                        <button class="btn btn--secondary btn--sm mt-2" type="submit" data-loading-text="Updating NTE…">Update NTE</button>
                    </form>
                </div></section>
            </aside>
        </div>
    @else
        <section class="sec"><div class="sec__body">Select a request to review. <a href="{{ route('admin.estimates') }}">Open Estimate Queue</a></div></section>
    @endif
</main>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
    <script src="{{ asset('admin_assets/pages_js/estimate_review.js') }}" defer></script>
@endpush
