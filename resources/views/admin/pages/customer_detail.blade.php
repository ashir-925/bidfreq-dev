<main class="app-content customer-detail">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.customers') }}">Customers</a>
        <span class="breadcrumbs__sep" aria-hidden="true">/</span>
        <span class="breadcrumbs__current" aria-current="page">Customer Details</span>
    </nav>

    <header class="customer-detail__hero">
        <div class="customer-detail__identity">
            <span class="customer-detail__monogram" aria-hidden="true">{{ Str::upper(Str::substr($account->first_name, 0, 1).Str::substr($account->last_name, 0, 1)) }}</span>
            <div>
                <h1>{{ $account->name }}</h1>
                <p>{{ $account->company_name ?: 'No company supplied' }}</p>
            </div>
        </div>
        <div class="customer-detail__badges">
            <span class="chip">Customer</span>
            <span @class(['chip', 'chip--emerald' => $account->status === 'active'])><span class="chip__dot"></span>{{ Str::headline($account->status) }}</span>
        </div>
    </header>

    <div class="customer-detail__grid">
        <section class="sec customer-detail__contact" aria-labelledby="customer-contact-heading">
            <div class="sec__head"><div class="sec__head-left">
                <span class="sec__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 6 0 0 1 16 0v2"/></svg></span>
                <h2 id="customer-contact-heading" class="sec__title">Contact and account</h2>
            </div></div>
            <dl class="sec__body customer-detail__facts">
                <div><dt>Email address</dt><dd>{{ $account->email }}</dd></div>
                <div><dt>Phone number</dt><dd>{{ $account->phone ?: 'No phone supplied' }}</dd></div>
                <div><dt>Registered</dt><dd>{{ $account->created_at->format('M j, Y') }}</dd></div>
                <div><dt>Last login</dt><dd>{{ $account->last_login_at?->format('M j, Y') ?? 'Never' }}</dd></div>
            </dl>
        </section>

        <div class="customer-detail__activity">
            <section class="sec" aria-labelledby="customer-estimate-heading">
                <div class="sec__head"><div class="sec__head-left">
                    <span class="sec__icon sec__icon--violet"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M8 13h8m-8 4h5"/></svg></span>
                    <h2 id="customer-estimate-heading" class="sec__title">Estimate requests</h2>
                </div><span class="chip">{{ $estimates->total() }} {{ Str::plural('request', $estimates->total()) }}</span></div>

                @if ($estimates->isEmpty())
                    <p class="sec__body text-muted">No estimate requests yet.</p>
                @else
                    <div class="table-scroll" tabindex="0" role="region" aria-label="Estimate requests table">
                        <table class="table">
                            <thead><tr><th scope="col">Request</th><th scope="col">Property</th><th scope="col">Status</th><th scope="col">Submitted</th></tr></thead>
                            <tbody>
                            @foreach ($estimates as $estimate)
                                <tr>
                                    <th scope="row"><a class="customer-detail__reference" href="{{ route('admin.estimate-review', $estimate) }}">{{ $estimate->number() }}</a></th>
                                    <td>{{ $estimate->street_address }}</td>
                                    <td><span class="chip {{ $estimate->statusClass(true) }}">{{ Str::headline($estimate->status) }}</span></td>
                                    <td class="customer-detail__date">{{ $estimate->created_at->format('M j, Y') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                @if ($estimates->hasPages())<div class="customer-detail__pagination">{{ $estimates->withQueryString()->links() }}</div>@endif
            </section>

            <section class="sec" aria-labelledby="customer-project-heading">
                <div class="sec__head"><div class="sec__head-left">
                    <span class="sec__icon sec__icon--emerald"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 10 9-8 9 8v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2zM9 22v-8h6v8"/></svg></span>
                    <h2 id="customer-project-heading" class="sec__title">Project history</h2>
                </div><span class="chip">{{ $projects->total() }} {{ Str::plural('project', $projects->total()) }}</span></div>

                @if ($projects->isEmpty())
                    <p class="sec__body text-muted">No projects yet.</p>
                @else
                    <div class="table-scroll" tabindex="0" role="region" aria-label="Project history table">
                        <table class="table">
                            <thead><tr><th scope="col">Property</th><th scope="col">Status</th></tr></thead>
                            <tbody>
                            @foreach ($projects as $item)
                                <tr>
                                    <th scope="row"><a class="customer-detail__reference" href="{{ route('admin.projects.show', $item) }}">{{ $item->address }}</a></th>
                                    <td><span class="chip">{{ Str::headline($item->status) }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                @if ($projects->hasPages())<div class="customer-detail__pagination">{{ $projects->withQueryString()->links() }}</div>@endif
            </section>
        </div>
    </div>
</main>
