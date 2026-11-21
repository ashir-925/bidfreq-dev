@extends('admin.layout.app')
@section('title', 'Project details · BidFreq')
@push('styles')
<link rel="stylesheet" href="{{ asset('front_assets/pages_css/project_detail.css') }}">
@endpush
@section('content')
<main class="app-content project-detail-page"><nav class="breadcrumbs"><a href="{{ route('admin.projects') }}">Projects</a><span class="breadcrumbs__sep">/</span><span>Project details</span></nav>
@include('shared.project_detail')
</main>
@endsection
@push('scripts')<script src="{{ asset('front_assets/pages_js/project_workflow.js') }}" defer></script>@endpush

