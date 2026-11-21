@extends('front.layout.app')
@section('title', 'Estimate Details · BidFreq')
@push('styles')
    <link rel="stylesheet" href="{{ asset('front_assets/pages_css/estimate_request_detail.css') }}">
@endpush
@section('content')
<main class="app-content">
    <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('estimate-request') }}">Estimate Requests</a><span class="breadcrumbs__sep">/</span><span>Estimate Details</span></nav>
    <header class="page-header">
        <div class="page-header__text"><h1 class="page-header__title">Estimate Details</h1>
            @if ($repair)<p class="page-header__subtitle">{{ $repair->status === 'approved' ? 'Human-verified estimate ready for your review.' : $repair->customerStatus() }}</p>@endif
        </div>
        @if ($release = $repair?->currentRelease)
            <div class="page-header__actions"><a class="btn btn--secondary" href="{{ route('repair-requests.pdf', [$repair, $release]) }}">Download PDF</a><button type="button" class="btn btn--ghost" data-estimate-print>Print</button></div>
        @endif
    </header>
    @if ($repair)
        <div class="detail-grid">
            <div class="detail-main">
                <section class="est-header">
                    <div class="est-header__top">
                        <div class="est-header__eyebrow">{{ $release ? 'Estimate ID' : 'Estimate request' }}</div>
                        <div class="est-header__id">{{ $repair->number() }} <span class="chip {{ $repair->statusClass() }}"><span class="chip__dot"></span>{{ $repair->customerStatus() }}</span></div>
                        <div class="est-header__property">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><span>{{ $repair->street_address }}, {{ $repair->city }}, {{ $repair->state }} {{ $repair->zip }}</span>
                            <span class="dot"></span><span>{{ collect($repair->descriptions)->pluck('trade')->unique()->implode(' / ') }}</span>
                            @php($photoCount = $repair->files->where('kind', 'photo')->count())
                            <span class="dot"></span><span>{{ $photoCount }} {{ Str::plural('photo', $photoCount) }}</span>
                        </div>
                    </div>
                    <div class="est-header__meta">
                        <div class="est-header__meta-item"><div class="est-header__meta-label">Submitted</div><div class="est-header__meta-value">{{ $repair->created_at->format('M j, Y · g:i A') }}</div></div>
                        @if ($release)
                            <div class="est-header__meta-item"><div class="est-header__meta-label">Released version</div><div class="est-header__meta-value">{{ $release->version }} · {{ $release->created_at->format('M j, Y') }}</div></div>
                        @endif
                    </div>
                </section>
                @if ($repair->status === 'needs_info' || $repair->status === 'declined')
                    <section class="sec"><div class="sec__head"><div class="sec__title">{{ $repair->customerStatus() }}</div></div><div class="sec__body">
                        <p>{{ $repair->events->firstWhere('action', $repair->status)?->details['message'] ?? '' }}</p>
                        @if ($repair->status === 'needs_info')
                            <form method="POST" enctype="multipart/form-data" action="{{ route('repair-requests.respond', $repair) }}">
                                @csrf
                                <label class="label" for="customer-info">Additional information</label>
                                <textarea id="customer-info" class="textarea" name="message" maxlength="3000">{{ is_string(old('message')) ? old('message') : '' }}</textarea>
                                @include('shared.auth-field-error', ['name' => 'message'])
                                <label class="label" for="customer-files">Photos or supporting files</label>
                                <input id="customer-files" class="input" type="file" name="files[]" multiple>
                                @include('shared.auth-field-error', ['name' => 'files'])
                                <button class="btn btn--primary mt-2" type="submit" data-loading-text="Sending information…">Send information</button>
                            </form>
                        @endif
                    </div></section>
                @endif
                @if ($release)
                    <section class="sec">
                        <div class="sec__head"><div class="sec__head-left"><div class="sec__icon sec__icon--emerald">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div><div><div class="sec__title">Scope of Work</div><div class="sec__sub">Reviewed and approved by a BidFreq estimator</div></div></div><span class="tag-verified">Human Verified</span></div>
                        <div class="sec__body">
                            <div class="trade-group">
                                <div class="trade-group__head">
                                    <div class="trade-group__title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg> {{ collect($repair->descriptions)->pluck('trade')->unique()->implode(' / ') ?: 'Estimate line items' }}</div>
                                    <div class="trade-group__total">{{ \App\Services\Estimates\EstimateDisplay::currency($release->snapshot['total']) }}</div>
                                </div>
                                <div class="table-scroll"><table class="line-items">
                                    <thead><tr><th scope="col">Description</th><th scope="col">Qty / unit</th><th scope="col" class="num">Unit rate</th><th scope="col" class="num">Total</th></tr></thead>
                                    <tbody>@foreach ($release->snapshot['lines'] as $line)
                                        <tr><td class="desc"><strong>{{ $line['name'] }}</strong></td>
                                            <td><span class="qty-cell"><strong>{{ \App\Services\Estimates\EstimateDisplay::quantity($line['quantity']) }}</strong><span>{{ $line['unit'] }}</span></span></td>
                                            <td class="num">{{ \App\Services\Estimates\EstimateDisplay::currency($line['rate']) }}</td>
                                            <td class="num">{{ \App\Services\Estimates\EstimateDisplay::currency($line['line_total']) }}</td></tr>
                                    @endforeach</tbody>
                                </table></div>
                            </div>
                        </div>
                    </section>
                    @foreach (['assumptions' => 'Assumptions', 'exclusions' => 'Exclusions'] as $field => $title)
                        @if (! empty($release->snapshot['analysis'][$field]))
                            <section class="sec"><div class="sec__head"><div class="sec__title">{{ $title }}</div></div>
                                <div class="sec__body"><ul class="estimate-note-list">
                                    @foreach ($release->snapshot['analysis'][$field] as $note)
                                        <li class="list-item"><span class="list-item__marker list-item__marker--{{ $field === 'assumptions' ? 'assume' : 'exclude' }}" aria-hidden="true">{{ $field === 'assumptions' ? '✓' : '×' }}</span><span>{{ $note }}</span></li>
                                    @endforeach
                                </ul></div>
                            </section>
                        @endif
                    @endforeach
                @else
                    <section class="sec"><div class="sec__body">Your request is under review. We will email you as soon as your estimate is ready.</div></section>
                @endif
                <section class="sec" id="submitted-evidence">
                    <div class="sec__head"><div class="sec__head-left"><div class="sec__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg></div>
                        <div><div class="sec__title">Submitted Evidence</div><div class="sec__sub">{{ $repair->files->count() }} {{ Str::plural('attachment', $repair->files->count()) }} from your request</div></div>
                    </div></div>
                    <div class="sec__body">
                        @if ($repair->files->isNotEmpty())
                            <div class="photo-grid">
                                @foreach ($repair->files->values() as $file)
                                    @php($isImage = in_array($file->mime, \App\Models\RepairRequest::PHOTO_MIMES, true))
                                    <a class="photo-tile" href="{{ route($isImage ? 'repair-requests.image' : 'repair-requests.download', [$repair, $file]) }}" @if ($isImage) target="_blank" rel="noopener" @endif aria-label="{{ $isImage ? 'Open photo' : 'Download file' }} {{ $loop->iteration }}: {{ $file->name }}">
                                        @if ($isImage)<img class="photo-tile__img" src="{{ route('repair-requests.image', [$repair, $file]) }}" alt="" loading="lazy">
                                        @else <span class="photo-tile__img"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg></span>@endif
                                        <span class="photo-tile__badge">{{ $loop->iteration }}</span><span class="photo-tile__label">{{ $file->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        <details class="estimate-submitted-details">
                            <summary>Request description and measurements</summary>
                            <p><strong>Home size:</strong> {{ \App\Services\Estimates\EstimateDisplay::quantity($repair->home_sqft) }} sq ft · {{ $repair->stories }} {{ Str::plural('story', $repair->stories) }}</p>
                            <p><strong>Priority:</strong> {{ ucfirst($repair->priority) }}</p>
                            @foreach ($repair->descriptions as $item)<p><strong>{{ $item['trade'] }}:</strong> {{ $item['description'] }}</p>@endforeach
                            @foreach ($repair->dimensions as $area)
                                <p><strong>{{ $area['label'] }}</strong>
                                @foreach (['length' => 'Length', 'width' => 'Width', 'height' => 'Height', 'area' => 'Area'] as $key => $label)
                                    @if (isset($area[$key])) · {{ $label }} {{ \App\Services\Estimates\EstimateDisplay::quantity((string) $area[$key]) }} {{ $key === 'area' ? 'sq ft' : 'ft' }} @endif
                                @endforeach
                                </p>
                            @endforeach
                            @foreach ($repair->files->whereIn('mime', \App\Models\RepairRequest::PHOTO_MIMES) as $file)
                                <p><a href="{{ route('repair-requests.download', [$repair, $file]) }}">Download {{ $file->name }}</a></p>
                            @endforeach
                        </details>
                    </div>
                </section>
            </div>
            <aside class="detail-side">
                @if ($release)
                    <div class="action-card">
                        <div class="action-card__top">
                            <div class="action-card__label">Final Estimate</div>
                            <div class="action-card__amount">{{ '$'.\App\Services\Estimates\EstimateDisplay::money($release->snapshot['total']) }}</div>
                            <div class="action-card__sub">Human-verified · Version {{ $release->version }}</div>
                        </div>
                        <div class="action-card__body">
                            @if ($project = $repair->project)
                                <a class="btn btn--secondary btn--block" href="{{ route('project-detail', $project) }}">View project</a>
                            @endif
                            @if ($repair->status === 'approved' && ! $release->decided_at)
                                <button type="button" class="btn btn--success btn--lg btn--block" data-open-estimate-modal="approveModal" @if ($errors->any() && old('decision') === 'approve') data-open-on-load @endif>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg> Approve Estimate
                                </button>
                                <button type="button" class="btn btn--secondary btn--block" data-open-estimate-modal="revisionModal" @if ($errors->any() && old('decision') === 'revise') data-open-on-load @endif>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.3 2.5L3 8"/><path d="M3 3v5h5"/></svg> Request Revision
                                </button>
                                <button type="button" class="btn btn--danger-outline btn--block" data-open-estimate-modal="declineModal" @if ($errors->any() && old('decision') === 'reject') data-open-on-load @endif>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg> Decline
                                </button>
                            @endif
                            <a class="btn btn--ghost btn--block" href="{{ route('repair-requests.pdf', [$repair, $release]) }}">Download PDF</a>
                        </div>
                    </div>

                    <section class="sec">
                        <div class="sec__head"><div class="sec__head-left"><div class="sec__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
                            <div><div class="sec__title">Status history</div><div class="sec__sub">Submitted request and released estimates</div></div>
                        </div></div>
                        <div class="sec__body"><ol class="mini-timeline estimate-note-list">
                            <li class="mini-tl-item is-done"><span class="mini-tl-dot" aria-hidden="true"></span><div class="mini-tl-body">
                                <div class="mini-tl-title">Request submitted</div><div class="mini-tl-meta">{{ $repair->created_at->format('M j, Y · g:i A') }}</div>
                            </div></li>
                            @foreach ($repair->releases->reverse() as $version)
                                <li @class(['mini-tl-item', 'is-done' => $version->decided_at, 'is-active' => $version->is($release) && ! $version->decided_at])>
                                    <span class="mini-tl-dot" aria-hidden="true"></span><div class="mini-tl-body">
                                        <a class="mini-tl-title" href="{{ route('repair-requests.pdf', [$repair, $version]) }}">Version {{ $version->version }} PDF</a>
                                        <div class="mini-tl-meta">{{ $version->created_at->format('M j, Y') }}
                                            @unless ($version->is($release)) · Superseded @endunless
                                            @if ($version->decision) · {{ ['approve' => 'Approved', 'reject' => 'Declined', 'revise' => 'Revision requested'][$version->decision] }} @endif
                                        </div>
                                        @if ($version->customer_comment)<p>{{ $version->customer_comment }}</p>@endif
                                    </div>
                                </li>
                            @endforeach
                        </ol></div>
                    </section>
                @endif
            </aside>
        </div>
        @if ($release && $repair->status === 'approved' && ! $release->decided_at)
            @foreach (['approve' => ['approveModal','Approve estimate'], 'revise' => ['revisionModal','Request a revision'], 'reject' => ['declineModal','Decline estimate']] as $decision => [$id, $title])
                <div class="modal-overlay" id="{{ $id }}">
                    <form class="modal" method="POST" action="{{ route('repair-requests.decision', $repair) }}" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
                        @csrf
                        <input type="hidden" name="release_id" value="{{ $release->id }}">
                        <input type="hidden" name="decision" value="{{ $decision }}">
                        <div class="modal__head"><div class="modal__title" id="{{ $id }}-title">{{ $title }}</div></div>
                        <div class="modal__body">
                            @if ($decision === 'approve')
                                <label for="estimate-signature" class="label">Full name</label>
                                <input class="input" id="estimate-signature" name="signature" required minlength="3" maxlength="150" value="{{ is_string(old('signature')) ? old('signature') : '' }}">
                                <label><input type="checkbox" name="acceptance" value="1" required> I approve this scope and its total.</label>
                            @else
                                <label class="label" for="{{ $id }}-comment">{{ $decision === 'revise' ? 'What needs to change?' : 'Reason (optional)' }}</label>
                                <textarea class="textarea" id="{{ $id }}-comment" name="comment" maxlength="2000" @required($decision === 'revise')>{{ old('decision') === $decision && is_string(old('comment')) ? old('comment') : '' }}</textarea>
                            @endif
                        </div>
                        <div class="modal__footer">
                            <button type="button" class="btn btn--ghost" data-close-estimate-modal>Cancel</button>
                            <button type="submit" class="btn btn--primary" data-loading-text="Recording decision…">{{ $title }}</button>
                        </div>
                    </form>
                </div>
            @endforeach
        @endif
    @else
        <section class="sec"><div class="sec__body">Select a request to see its details. <a href="{{ route('estimate-request') }}">View Estimate Requests</a></div></section>
    @endif
</main>
@endsection
@push('scripts')
    <script src="{{ asset('front_assets/pages_js/dashboard.js') }}"></script>
    <script src="{{ asset('front_assets/pages_js/estimate_request_detail.js') }}" defer></script>
@endpush
