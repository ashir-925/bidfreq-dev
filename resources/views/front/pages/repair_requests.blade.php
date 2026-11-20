@extends('front.layout.app')
@section('title', 'Estimate Requests · BidFreq')
@section('content')
<main class="app-content">

<header class="page-header"><div class="page-header__text"><h1 class="page-header__title">Estimate Requests</h1></div><div class="page-header__actions"><a class="btn btn--primary" href="{{ route('new-estimate-request') }}">New Estimate Request</a></div></header>
<div class="table-wrap"><div class="table-scroll"><table class="table">
    <thead><tr><th scope="col">Request</th><th scope="col">Property</th><th scope="col">Priority</th><th scope="col">Submitted</th><th scope="col">Status</th></tr></thead>
    <tbody>
    @forelse ($requests as $repair)
        <tr><td><a href="{{ route('repair-requests.show', $repair) }}">{{ $repair->number() }}</a></td>
            <td>{{ $repair->property_name ?: $repair->street_address }}</td>
            <td>{{ ucfirst($repair->priority) }}</td><td>{{ $repair->created_at->format('M j, Y') }}</td>
            <td><span class="badge badge--blue">Under Review</span></td></tr>
    @empty
        <tr><td colspan="5">You have no estimate requests yet.</td></tr>
    @endforelse</tbody>
</table></div></div>
@include('admin.library.pagination', ['pages' => $requests])
</main>
@endsection
@push('scripts')
    <script src="{{ asset('front_assets/pages_js/dashboard.js') }}"></script>
    
@endpush
