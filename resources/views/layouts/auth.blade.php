<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('head')
</head>
<body class="login-shell">
    @yield('content')
    @include('components.site-footer')
</body>
</html>
