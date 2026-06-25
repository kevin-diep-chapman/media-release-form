@extends('layouts.app')
@section('title', 'Edit Event')
@section('content')
    <section class="card form-card">
    <h1>Edit Event</h1>
    <form class="stack-form" method="POST" action="{{ route('events.update', $event->id) }}">
        @csrf
        @method('PUT')
        <label for="title">Title</label>
        <input id="title" type="text" name="title" value="{{ $event->title }}" required>
        <label for="description">Description</label>
        <textarea id="description" name="description" required>{{ $event->description }}</textarea>
        @include('components.event-date-fields', [
            'dateType' => $event->event_end_date ? 'range' : 'single',
            'eventDate' => $event->event_date->format('Y-m-d'),
            'eventEndDate' => $event->event_end_date?->format('Y-m-d') ?? '',
        ])
        <label for="location">Location</label>
        <input id="location" type="text" name="location" value="{{ $event->location }}" required>
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="active" @if($event->status=='active') selected @endif>Active</option>
            <option value="inactive" @if($event->status=='inactive') selected @endif>Inactive</option>
        </select>
        <button type="submit">Save Changes</button>
    </form>
    </section>
@endsection
