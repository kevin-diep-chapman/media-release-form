@extends('layouts.app')
@section('title', 'Event Details')
@section('content')
    <section class="card">
        <h1>{{ $event->title }}</h1>
        <p><strong>Date:</strong> {{ $event->event_date_display }}</p>
        <p><strong>Location:</strong> {{ $event->location }}</p>
        <p><strong>Status:</strong>
            <span class="status-dot status-dot-{{ $event->display_status }}">
                <span class="status-dot-icon" aria-hidden="true"></span>
                {{ ucfirst($event->display_status) }}
            </span>
        </p>
        <p><strong>Description:</strong> {{ $event->description }}</p>
    </section>

    <section class="card table-card">
        <h2>Media Release Submissions</h2>

        @if ($event->media_releases_count > 0)
            <section class="dashboard-filters submissions-filters">
                <form method="GET" action="{{ route('events.show', $event->id) }}" class="submissions-search-form">
                    <div class="submissions-search-field">
                        <label class="visually-hidden" for="submission_search">Search submissions by name or email</label>
                        <input
                            id="submission_search"
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search by name or email..."
                        >
                        <button type="submit" class="submissions-search-button" aria-label="Search submissions">
                            <svg viewBox="0 0 20 20" aria-hidden="true" focusable="false">
                                <path d="M8.5 3a5.5 5.5 0 0 1 4.33 8.87l3.57 3.57a.75.75 0 1 1-1.06 1.06l-3.57-3.57A5.5 5.5 0 1 1 8.5 3Zm0 1.5a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
                @if ($search !== '')
                    <p class="submissions-search-clear">
                        <a href="{{ route('events.show', $event->id) }}">Clear search</a>
                    </p>
                @endif
            </section>
        @endif

        @if ($event->media_releases_count === 0)
            <p>No media release forms have been submitted for this event yet.</p>
        @elseif ($mediaReleases->isEmpty())
            <p>No submissions match your search.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Submit Date</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mediaReleases as $release)
                        <tr>
                            <td>{{ $release->full_name }}</td>
                            <td>{{ $release->email }}</td>
                            <td>{{ $release->submitted_at_pacific }}</td>
                            <td>
                                <a class="button-link button-link-sm button-link-view" href="{{ route('dashboard.media-releases.show', $release->id) }}">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>

    @if ($event->status === 'active')
        @include('components.media-release-modal')
    @endif
@endsection
