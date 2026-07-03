<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    @stack('styles')
</head>
<body class="bg-white min-h-screen">
    @yield('content')
    @stack('scripts')
</body>
</html>
