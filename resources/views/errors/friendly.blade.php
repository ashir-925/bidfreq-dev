<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status }} · BidFreq</title>
    <link rel="stylesheet" href="{{ asset('front_assets/css/styles.css') }}">
</head>
<body>
    <main class="app-content">
        <h1>{{ $status }}</h1>
        <p>{{ $message }}</p>
        <a class="btn btn--primary" href="{{ url('/') }}">Return to BidFreq</a>
    </main>
</body>
</html>
