<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('front_assets/css/styles.css') }}">

    @stack('styles')
</head>
<body>

<div class="app" id="app">
    <!-- ============================================================
       SIDEBAR — CUSTOMER
       ============================================================ -->
    @include('front.includes.sidebar')


    <!-- ============================================================
       MAIN
       ============================================================ -->

    <div class="app-main">
         @include('front.includes.header')
        @if (session('status') || session('error') || $errors->any())
            <div class="app-content" style="padding-bottom: 0" data-feedback-fallback>@include('shared.flash')</div>
        @endif
        @yield('content')
    </div>
</div>

<script>
window.bidFreqRoutes = {{ Illuminate\Support\Js::from([
    'estimate_request_detail' => route('estimate_request_detail'),
    'estimate-request' => route('estimate-request'),
    'new-estimate-request' => route('new-estimate-request'),
    'profile' => route('profile'),
    'project-detail' => route('project-detail'),
    'projects' => route('projects'),
]) }};
</script>
@include('shared.feedback')
@stack('scripts')

</body>

</html>
