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
        @yield('content')
    </div>
</div>

@stack('scripts')

</body>

</html>
