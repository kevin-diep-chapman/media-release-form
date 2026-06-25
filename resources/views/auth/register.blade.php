@extends('layouts.app')
@section('title', 'Register')
@section('content')
    <section class="card auth-card">
    <h1>Create Account</h1>
    <p class="subtitle">Register a dashboard user for event administration.</p>
    <form class="stack-form" method="POST" action="{{ route('register') }}">
        @csrf
        <label for="name">Name</label>
        <input id="name" type="text" name="name" required>
        <label for="email">Email</label>
        <input id="email" type="email" name="email" required>
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>
        <label for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>
        <button type="submit">Register</button>
    </form>
    </section>
@endsection
