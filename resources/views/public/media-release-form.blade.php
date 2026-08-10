@extends('layouts.public')
@section('title', $event->title . ' — Media Release')
@section('content')
    @if (request()->query('success') === 'media-release')
        <section class="card dashboard-alert dashboard-alert-success" role="alert">
            <p>Thank you! Your media release form was submitted successfully.</p>
        </section>
    @endif

    <section class="card form-card media-release-public-card">
        <h1 class="media-release-title">{{ $event->title }} Media Release</h1>
        <p class="media-release-public-intro">Complete the form below. No login is required.</p>

        <form
            id="mediaReleaseForm"
            class="media-release-form media-release-form-public"
            method="POST"
            action="{{ route('media-release.store') }}"
            enctype="multipart/form-data"
            novalidate
            data-require-photo="0"
            data-is-public="1"
            data-success-redirect="{{ route('media-release.public-form', $event->form_token) }}"
        >
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <div class="media-release-form-body">
                @include('components.media-release-form-fields', [
                    'requirePhoto' => false,
                    'eventTitle' => $event->title,
                ])
            </div>

            <div class="media-release-form-actions">
                <button type="submit" class="media-release-btn-submit">Submit Form &gt;</button>
            </div>
        </form>
    </section>

    <script src="{{ asset('js/signature-pad.js') }}?v={{ filemtime(public_path('js/signature-pad.js')) }}"></script>
    <script src="{{ asset('js/media-release-form.js') }}?v={{ filemtime(public_path('js/media-release-form.js')) }}"></script>
    <script>
        window.initSignaturePad?.();
    </script>
@endsection
