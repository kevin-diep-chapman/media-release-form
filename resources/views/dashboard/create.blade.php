@extends('layouts.app')
@section('title', 'Create Event')
@section('content')
    <section class="card form-card">
    <h1>Create New Event</h1>
    <form id="create-event-form" class="stack-form" method="POST" action="{{ route('events.store') }}" novalidate>
        @csrf
        <label for="title">Title</label>
        <input id="title" type="text" name="title" autocomplete="off">
        <p id="title-error" class="field-error" hidden></p>
        <label for="description">Description</label>
        <textarea id="description" name="description"></textarea>
        <p id="description-error" class="field-error" hidden></p>
        @include('components.event-date-fields', [
            'minDate' => \Carbon\Carbon::now('America/Los_Angeles')->toDateString(),
        ])
        <button type="submit">Create Event</button>
    </form>
    </section>
    <script src="{{ asset('js/create-event-form.js') }}"></script>
@endsection
