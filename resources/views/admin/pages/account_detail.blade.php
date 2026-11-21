@extends('admin.layout.app')
@section('title', request()->routeIs('admin.customers.show') ? 'Customer Details · BidFreq' : 'Account details · BidFreq')
@if (request()->routeIs('admin.customers.show'))
    @push('styles')
        <link rel="stylesheet" href="{{ asset('admin_assets/css/customer-detail.css') }}">
    @endpush
@endif
@section('content')
@if (request()->routeIs('admin.customers.show'))
    @include('admin.pages.customer_detail')
@else
<main class="app-content">
    <header class="page-header"><div class="page-header__text"><h1 class="page-header__title">Account details</h1></div></header>
<section class="sec"><div class="sec__head"><strong>{{ $account->name }}</strong> <span class="chip">{{ $projects->total() ? 'Customer' : Str::headline($account->status) }}</span></div><div class="sec__body">
<p>{{ $account->email }} · {{ $account->phone ?: 'No phone supplied' }}</p><p>{{ $account->company_name ?: 'No company supplied' }}</p>
<p>{{ ucfirst($account->status) }} · Registered {{ $account->created_at->format('M j, Y') }} · Last login {{ $account->last_login_at?->format('M j, Y') ?? 'Never' }}</p>
</div></section><h2>Estimate requests</h2>
<div class="table-wrap"><div class="table-scroll"><table class="table">
<thead><tr><th scope="col">Request</th><th scope="col">Property</th><th scope="col">Status</th><th scope="col">Submitted</th></tr></thead>
<tbody>@forelse ($estimates as $estimate)<tr><td><a href="{{ route('admin.estimate-review', $estimate) }}">{{ $estimate->number() }}</a></td><td>{{ $estimate->street_address }}</td><td>{{ Str::headline($estimate->status) }}</td><td>{{ $estimate->created_at->format('M j, Y') }}</td></tr>@empty<tr><td colspan="4">No estimate requests yet.</td></tr>@endforelse</tbody></table></div></div>
{{ $estimates->withQueryString()->links() }}<h2>Project history</h2><div class="table-wrap"><div class="table-scroll"><table class="table">
<thead><tr><th scope="col">Property</th><th scope="col">Status</th></tr></thead>
<tbody>@forelse ($projects as $item)<tr><td><a href="{{ route('admin.projects.show', $item) }}">{{ $item->address }}</a></td><td>{{ Str::headline($item->status) }}</td></tr>@empty<tr><td colspan="2">No projects yet.</td></tr>@endforelse</tbody></table></div></div>
{{ $projects->withQueryString()->links() }}
</main>
@endif
@endsection
