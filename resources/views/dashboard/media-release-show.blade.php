@extends('layouts.app')
@section('title', $release->full_name . ' Submission')
@section('content')
    @php
        $backUrl = auth()->user()->isAdmin()
            ? route('dashboard.media-releases.index')
            : route('events.show', $release->event_id);

        $affiliationDisplay = $release->affiliation ?: '—';
        if ($release->affiliation_details) {
            $affiliationDisplay = trim($affiliationDisplay.' — '.$release->affiliation_details);
        }
    @endphp

    <nav class="media-release-breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('public.permitted-events') }}">Home</a>
        <span aria-hidden="true">&gt;</span>
        <a href="{{ route('public.permitted-events') }}">My Events</a>
        <span aria-hidden="true">&gt;</span>
        <a href="{{ route('events.show', $release->event_id) }}">{{ $release->event->title }} Details</a>
        <span aria-hidden="true">&gt;</span>
        <span>Submission</span>
    </nav>

    <h1 class="media-release-title">{{ $release->full_name }}'s {{ $release->event->title }} Submission</h1>

    <div class="media-release-detail">
        <div class="media-release-detail-hero">
            <div class="media-release-photo-panel">
                @if ($release->hasPhoto())
                    <div class="media-release-photo-box">
                        <img src="{{ $release->photoUrl() }}" alt="Photo for {{ $release->full_name }}">
                    </div>
                @else
                    <div class="media-release-photo-box media-release-photo-empty">No photo</div>
                @endif
            </div>

            <div class="media-release-detail-identity">
                <div class="media-release-row media-release-row-2">
                    <div class="media-release-field">
                        <span class="media-release-field-label">First Name</span>
                        <div class="media-release-value">{{ $release->first_name ?: '—' }}</div>
                    </div>
                    <div class="media-release-field">
                        <span class="media-release-field-label">Last Name</span>
                        <div class="media-release-value">{{ $release->last_name ?: '—' }}</div>
                    </div>
                </div>

                <div class="media-release-row media-release-row-2">
                    <div class="media-release-field">
                        <span class="media-release-field-label">Email</span>
                        <div class="media-release-value">{{ $release->email }}</div>
                    </div>
                    <div class="media-release-field">
                        <span class="media-release-field-label">Phone</span>
                        <div class="media-release-value">{{ $release->phone ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="media-release-detail-fields">
            <div class="media-release-row">
                <div class="media-release-field">
                    <span class="media-release-field-label">Address</span>
                    <div class="media-release-value">{{ $release->address ?: '—' }}</div>
                </div>
            </div>

            <div class="media-release-row media-release-row-3">
                <div class="media-release-field">
                    <span class="media-release-field-label">City</span>
                    <div class="media-release-value">{{ $release->city ?: '—' }}</div>
                </div>
                <div class="media-release-field">
                    <span class="media-release-field-label">State</span>
                    <div class="media-release-value">{{ $release->state ?: '—' }}</div>
                </div>
                <div class="media-release-field">
                    <span class="media-release-field-label">Zip</span>
                    <div class="media-release-value">{{ $release->zip_code ?: '—' }}</div>
                </div>
            </div>

            <div class="media-release-row">
                <div class="media-release-field">
                    <span class="media-release-field-label">Affiliation</span>
                    <div class="media-release-value">{{ $affiliationDisplay }}</div>
                </div>
            </div>

            <div class="media-release-row media-release-row-2">
                <div class="media-release-field">
                    <span class="media-release-field-label">Consent Agreed?</span>
                    <div class="media-release-value @unless($release->consent_agreed) media-release-value-no @endunless">
                        {{ $release->consent_agreed ? 'Yes' : 'No' }}
                    </div>
                </div>
                <div class="media-release-field">
                    <span class="media-release-field-label">IP Address</span>
                    <div class="media-release-value">{{ $release->ip_address ?: '—' }}</div>
                </div>
            </div>

            <div class="media-release-row">
                <div class="media-release-field">
                    <span class="media-release-field-label">Event</span>
                    <div class="media-release-value">{{ $release->event->title }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="media-release-form-actions">
        <a class="media-release-btn-back" href="{{ $backUrl }}">&lt; Back to All</a>
        @if ($nextReleaseId)
            <a class="media-release-btn-submit" href="{{ route('dashboard.media-releases.show', $nextReleaseId) }}">Next Submission &gt;</a>
        @endif
    </div>
@endsection
