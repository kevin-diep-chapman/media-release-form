@extends('layouts.app')
@section('title', 'My Events')
@section('content')
    <nav class="dashboard-breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('public.permitted-events') }}">Home</a>
        <span aria-hidden="true">&gt;</span>
        <span>My Events</span>
    </nav>

    <header class="dashboard-intro">
        <h1>Welcome back, Panther!</h1>
        <p>View all of your events below.</p>
    </header>

    @if (session('success') || request()->query('success') === 'media-release')
        <section class="card dashboard-alert dashboard-alert-success" id="myEventsSuccessAlert">
            <p>{{ session('success', 'Thank you! Your media release form was submitted successfully.') }}</p>
        </section>
    @endif

    @if ($errors->any())
        <section class="card dashboard-alert dashboard-alert-error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </section>
    @endif

    <section class="dashboard-filters">
        <form method="GET" action="{{ route('public.permitted-events') }}" class="search-form">
            <div class="search-form-row">
                <input
                    id="search"
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search events"
                    aria-label="Search events"
                >
                <label class="search-form-inline-label" for="date_from">From</label>
                <input
                    id="date_from"
                    type="date"
                    name="date_from"
                    value="{{ $dateFrom }}"
                    aria-label="Filter from date"
                >
                <label class="search-form-inline-label" for="date_to">To</label>
                <input
                    id="date_to"
                    type="date"
                    name="date_to"
                    value="{{ $dateTo }}"
                    aria-label="Filter to date"
                >
                <button type="submit">Search</button>
                @if ($search !== '' || $dateFrom !== '' || $dateTo !== '')
                    <a class="button-details" href="{{ route('public.permitted-events') }}">Clear</a>
                @endif
            </div>
        </form>
    </section>

    <section class="dashboard-table-card">
        @if ($events->isEmpty())
            <p class="dashboard-empty">No events found.</p>
        @else
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Event Name</th>
                        <th>Format</th>
                        <th>Event Date</th>
                        <th>Number of Forms</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $event)
                        <tr>
                            <td>
                                <span class="status-dot status-dot-{{ $event->display_status }}">
                                    <span class="status-dot-icon" aria-hidden="true"></span>
                                    {{ ucfirst($event->display_status) }}
                                </span>
                            </td>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->event_format_display }}</td>
                            <td>{{ $event->event_date_display }}</td>
                            <td>
                                <span class="form-count-badge" aria-label="{{ $event->media_releases_count }} forms submitted">
                                    {{ $event->media_releases_count }}
                                </span>
                            </td>
                            <td class="action-column">
                                <div class="table-actions">
                                    @php($canAddForm = $event->status !== 'inactive' && $event->display_status !== 'inactive')
                                    @php($canDelete = $event->status === 'active' && $event->display_status === 'active')
                                    @php($isPrintEvent = $event->isPrintFormat())
                                    <button
                                        type="button"
                                        class="button-add-form"
                                        @disabled(!$canAddForm)
                                        @if ($canAddForm && $isPrintEvent)
                                            onclick="showPrintFormLink(@js($event->public_form_url), @js($event->title))"
                                        @elseif ($canAddForm)
                                            onclick="showMediaReleaseForm({{ $event->id }}, @js($event->title))"
                                        @endif
                                        @if (!$canAddForm) title="This event is inactive and not accepting forms" @endif
                                    >{{ $isPrintEvent ? 'Form Link' : 'Add Form' }}</button>
                                    <a class="button-details" href="{{ route('events.show', $event->id) }}">Details</a>
                                    <a class="button-edit" href="{{ route('events.edit', $event->id) }}">Edit</a>
                                    @if ($canDelete)
                                        <form
                                            method="POST"
                                            action="{{ route('events.destroy', $event->id) }}"
                                            class="table-action-form"
                                            onsubmit="return confirm('Are you sure you want to delete this event?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button-delete">Delete</button>
                                        </form>
                                    @else
                                        <button
                                            type="button"
                                            class="button-delete button-delete-placeholder"
                                            disabled
                                            aria-hidden="true"
                                            tabindex="-1"
                                        >Delete</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>

    @if (request()->query('success') === 'media-release')
        <script>
            if (window.history.replaceState) {
                const url = new URL(window.location.href);
                url.searchParams.delete('success');
                window.history.replaceState({}, '', url);
            }
        </script>
    @endif
@endsection
