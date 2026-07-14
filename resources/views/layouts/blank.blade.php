<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/jpeg">
    <link rel="shortcut icon" href="{{ asset('images/logo.jpg') }}" type="image/jpeg">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.jpg') }}">
    <link rel="mask-icon" href="{{ asset('images/logo.jpg') }}" color="#1A3A6B">
    @stack('styles')
</head>
<body class="bg-white min-h-screen">
    @yield('content')
    @stack('scripts')
</body>
</html>
