@extends('layouts.public')
@section('title', $event->title . ' — Form Closed')
@section('content')
    <section class="card form-card">
        <h1>{{ $event->title }}</h1>
        <p>This event is no longer accepting media release submissions.</p>
    </section>
@endsection
