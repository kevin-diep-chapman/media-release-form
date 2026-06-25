@extends('layouts.auth')
@section('title', 'Login')
@section('content')
    <div class="login-layout">
        <section class="login-panel">
            <a class="login-brand" href="{{ url('/') }}">Chapman Media</a>

            <div class="login-content">
                <header class="login-header">
                    <h1>Welcome back, Panther!</h1>
                    <p>Use your Chapman Media account to manage events.</p>
                </header>

                @if (session('saml2.error') || session('saml2.error_detail'))
                    <div class="login-errors" role="alert">
                        @foreach ((array) session('saml2.error', []) as $error)
                            <p>{{ is_array($error) ? json_encode($error) : $error }}</p>
                        @endforeach
                        @foreach ((array) session('saml2.error_detail', []) as $error)
                            <p>{{ is_array($error) ? json_encode($error) : $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if ($errors->any())
                    <div class="login-errors" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form class="login-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <label for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="placeholder@chapman.edu"
                        required
                        autocomplete="email"
                    >

                    <label for="password">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="password123"
                        required
                        autocomplete="current-password"
                    >

                    <a class="login-forgot" href="#">Forgot password?</a>

                    <button class="login-submit" type="submit">Login</button>
                </form>

                <div class="login-divider" aria-hidden="true">
                    <span>OR</span>
                </div>

                @if ($samlTenantUuid ?? null)
                    <a
                        class="login-sso"
                        href="{{ route('saml.login', ['uuid' => $samlTenantUuid, 'returnTo' => url('/my-events')]) }}"
                    >
                        <span class="login-sso-icon" aria-hidden="true"></span>
                        <span>Continue with Chapman SSO</span>
                    </a>
                @else
                    <button class="login-sso" type="button" disabled aria-disabled="true" title="SSO is not configured yet">
                        <span class="login-sso-icon" aria-hidden="true"></span>
                        <span>Continue with Chapman SSO</span>
                    </button>
                @endif

                <p class="login-register">
                    Don't have an account?
                    <a href="{{ route('register') }}"><strong>Register Now</strong></a>
                </p>
            </div>
        </section>

        <aside class="login-hero" aria-hidden="true">
            <img
                class="login-hero-image"
                src="{{ asset('storage/imgs/Night-Campus-79.jpg') }}"
                alt=""
            >
        </aside>
    </div>
@endsection
