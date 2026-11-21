@extends('admin.layout.app')
@section('title', 'Customers · BidFreq')
@section('content')
<main class="app-content">
    <header class="page-header"><div class="page-header__text"><h1 class="page-header__title">Customers</h1></div></header>
<form class="filter-bar" method="GET">
<label class="label">Search <input class="input" name="search" type="search" value="{{ request('search') }}"></label>
<label class="label">Status <select class="select" name="status"><option value="">All statuses</option>@foreach (['active', 'inactive'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ Str::headline($status) }}</option>@endforeach</select></label>
<label class="label">Sort <select class="select" name="sort"><option value="">Default</option><option value="newest" @selected(request('sort') === 'newest')>Newest first</option><option value="name" @selected(request('sort') === 'name')>Name</option></select></label>

<button class="btn btn--primary btn--sm">Apply filters</button>
</form>

<div class="table-wrap"><div class="table-scroll"><table class="table">
<thead><tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Projects</th><th scope="col">Status</th></tr></thead>
<tbody>@forelse ($customers as $person)<tr><td><a href="{{ route('admin.customers.show', $person) }}">{{ $person->name }}</a></td><td>{{ $person->email }}</td><td>{{ $person->projects_count }}</td><td>{{ ucfirst($person->status) }}</td></tr>@empty<tr><td colspan="4">No customers found.</td></tr>@endforelse</tbody></table></div></div>
{{ $customers->links() }}
</main>
@endsection
