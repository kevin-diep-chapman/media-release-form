<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('head')
</head>
<body class="app-shell dashboard-shell">
    <header class="site-header dashboard-header">
        <nav class="dashboard-nav page-wrap">
            <a class="brand-mark" href="{{ route('public.permitted-events') }}">Chapman Media</a>

            @auth
                <div class="dashboard-nav-links">
                    @if(auth()->user()->isAdmin() || auth()->user()->isUser())
                        <a
                            class="dashboard-nav-link @if(request()->routeIs('public.permitted-events', 'events.*')) is-active @endif"
                            href="{{ route('public.permitted-events') }}"
                        >My Events</a>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <a
                            class="dashboard-nav-link @if(request()->routeIs('dashboard.media-releases.*')) is-active @endif"
                            href="{{ route('dashboard.media-releases.index') }}"
                        >Media Release Form</a>
                        <a
                            class="dashboard-nav-link @if(request()->routeIs('dashboard.users.*')) is-active @endif"
                            href="{{ route('dashboard.users.index') }}"
                        >User</a>
                    @else
                        <span class="dashboard-nav-link dashboard-nav-link-muted">Placeholder</span>
                    @endif
                </div>

                <div class="dashboard-nav-actions">
                    <a class="dashboard-new-event" href="{{ route('events.create') }}">+ New Event</a>
                    <div class="dashboard-user-menu">
                        <div class="dashboard-avatar" title="{{ auth()->user()->name }}">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dashboard-logout" type="submit">Log out</button>
                        </form>
                    </div>
                </div>
            @endauth
        </nav>
    </header>

    <main class="page-main dashboard-main">
        @yield('content')
    </main>

    @include('components.site-footer')

    @include('components.media-release-modal')
</body>
</html>
