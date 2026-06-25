<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Events')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('head')
</head>
<body class="app-shell public-shell">
    <header class="site-header">
        <nav class="site-nav page-wrap">
            @auth
                @if(auth()->user()->isAdmin() || auth()->user()->isUser())
                    <a class="brand-mark" href="{{ route('public.permitted-events') }}">Chapman Events</a>
                @else
                    <a class="brand-mark" href="{{ url('/') }}">Chapman Events</a>
                @endif
            @else
                <a class="brand-mark" href="{{ url('/') }}">Chapman Events</a>
            @endauth
            <div class="nav-links">
                @auth
                    <a href="{{ route('public.permitted-events') }}">My Events</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="link-button" type="submit">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Admin Login</a>
                @endauth
            </div>
        </nav>
    </header>
    <main class="page-main page-wrap">
        @yield('content')
    </main>

    @include('components.site-footer')
</body>
</html>
