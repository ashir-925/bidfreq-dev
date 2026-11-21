@extends('admin.layout.app')
@section('title', 'Estimator details · BidFreq')
@section('content')
<main class="app-content">
    <header class="page-header"><div class="page-header__text"><h1 class="page-header__title">Estimator details</h1></div></header>
<section class="sec"><div class="sec__body">@include('admin.pages.estimator_form')</div></section><h2>Assigned projects</h2><div class="table-wrap"><div class="table-scroll"><table class="table">
<thead><tr><th scope="col">Property</th><th scope="col">Status</th></tr></thead>
<tbody>@forelse ($projects as $item)<tr><td><a href="{{ route('admin.projects.show', $item) }}">{{ $item->address }}</a></td><td>{{ Str::headline($item->status) }}</td></tr>@empty<tr><td colspan="2">No projects yet.</td></tr>@endforelse</tbody></table></div></div>
{{ $projects->withQueryString()->links() }}
</main>
@endsection
