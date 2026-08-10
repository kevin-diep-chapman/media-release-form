@extends('layouts.app')
@section('title', 'Media Release Form')
@section('content')
    <nav class="dashboard-breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('public.permitted-events') }}">Home</a>
        <span aria-hidden="true">&gt;</span>
        <a href="{{ route('public.permitted-events') }}">My Events</a>
        <span aria-hidden="true">&gt;</span>
        <span>Media Release Form</span>
    </nav>

    <header class="dashboard-intro">
        <h1>Media Release Form</h1>
        <p>Search, review, and manage media release submissions across all of your events.</p>
    </header>

    <section class="dashboard-filters submissions-filters">
        <form method="GET" action="{{ route('dashboard.media-releases.index') }}" class="submissions-search-form">
            @if ($sortDirection !== 'desc')
                <input type="hidden" name="sort" value="{{ $sortDirection }}">
            @endif
            <div class="submissions-search-field">
                <label class="visually-hidden" for="search">Search for submissions</label>
                <input
                    id="search"
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search for submissions..."
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
                <a href="{{ route('dashboard.media-releases.index', ['sort' => $sortDirection]) }}">Clear search</a>
            </p>
        @endif
    </section>

    <section class="dashboard-table-card submissions-table-card">
        @if ($releases->isEmpty())
            <p class="dashboard-empty">No media release submissions found.</p>
        @else
            <table class="dashboard-table submissions-table">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>
                            @php
                                $nextSort = $sortDirection === 'desc' ? 'asc' : 'desc';
                                $sortQuery = array_filter([
                                    'search' => $search !== '' ? $search : null,
                                    'sort' => $nextSort,
                                ]);
                            @endphp
                            <a
                                href="{{ route('dashboard.media-releases.index', $sortQuery) }}"
                                class="submissions-table-sort"
                            >
                                Submission Date
                                <span class="submissions-table-sort-icon" aria-hidden="true">{{ $sortDirection === 'desc' ? '▾' : '▴' }}</span>
                            </a>
                        </th>
                        <th>Event/Form Name</th>
                        <th>Form Type</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($releases as $release)
                        <tr>
                            <td>{{ $release->first_name ?: $release->full_name }}</td>
                            <td>{{ $release->last_name ?: '—' }}</td>
                            <td>{{ $release->submitted_at_display }}</td>
                            <td>{{ $release->event->title }}</td>
                            <td>{{ $release->event->event_format_display }}</td>
                            <td class="action-column">
                                <a class="button-details" href="{{ route('dashboard.media-releases.show', $release->id) }}">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
@endsection
