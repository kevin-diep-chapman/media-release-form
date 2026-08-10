<!-- Media Release Modal (hidden by default) -->
<div id="mediaReleaseSuccessToast" class="media-release-success-toast" hidden role="alert" aria-live="polite">
    <div class="media-release-success-toast-card">
        <div class="media-release-success-icon" aria-hidden="true">✓</div>
        <div>
            <h3>Thank you!</h3>
            <p>Your media release form was submitted successfully.</p>
        </div>
    </div>
</div>

<div id="mediaReleaseModal" class="modal-shell media-release-modal" hidden>
    <div class="media-release-modal-card">
        <button type="button" class="modal-close" onclick="closeMediaReleaseModal()" aria-label="Close">&times;</button>

        <nav class="media-release-breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('public.permitted-events') }}">Home</a>
            <span aria-hidden="true">&gt;</span>
            <a href="{{ route('public.permitted-events') }}">My Events</a>
            <span aria-hidden="true">&gt;</span>
            <span id="mediaReleaseBreadcrumbEvent">Event</span>
            <span aria-hidden="true">&gt;</span>
            <span>Submission</span>
        </nav>

        <h1 class="media-release-title" id="mediaReleaseModalTitle">Media Release Submission</h1>

        <form
            id="mediaReleaseForm"
            class="media-release-form"
            method="POST"
            action="{{ route('media-release.store') }}"
            enctype="multipart/form-data"
            novalidate
            data-success-redirect="{{ url('/my-events') }}"
        >
            @csrf
            <input type="hidden" name="event_id" id="release_event_id">

            <div class="media-release-form-body">
                @include('components.media-release-form-fields', [
                    'requirePhoto' => true,
                ])
            </div>

            <div class="media-release-form-actions">
                <button type="button" class="media-release-btn-back" onclick="closeMediaReleaseModal()">&lt; Cancel</button>
                <button type="submit" class="media-release-btn-submit">Submit Form &gt;</button>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('js/camera.js') }}?v={{ filemtime(public_path('js/camera.js')) }}"></script>
<script src="{{ asset('js/signature-pad.js') }}?v={{ filemtime(public_path('js/signature-pad.js')) }}"></script>
<script src="{{ asset('js/media-release-form.js') }}?v={{ filemtime(public_path('js/media-release-form.js')) }}"></script>
<script>
    function resetMediaReleaseModal() {
        const form = document.getElementById('mediaReleaseForm');
        form.reset();
        window.syncMediaReleaseAffiliationField?.();
        window.clearSignaturePad?.();
        window.initSignaturePad?.();
    }

    function showMediaReleaseSuccessToast() {
        const toast = document.getElementById('mediaReleaseSuccessToast');
        if (toast) {
            toast.hidden = false;
        }
    }

    function hideMediaReleaseSuccessToast() {
        const toast = document.getElementById('mediaReleaseSuccessToast');
        if (toast) {
            toast.hidden = true;
        }
    }

    function showMediaReleaseForm(eventId, eventTitle) {
        resetMediaReleaseModal();
        const modal = document.getElementById('mediaReleaseModal');
        modal.hidden = false;
        document.body.classList.add('modal-open');
        document.getElementById('release_event_id').value = eventId;

        const title = eventTitle || 'Event';
        document.getElementById('mediaReleaseBreadcrumbEvent').textContent = title;
        document.getElementById('mediaReleaseEventDisplay').value = title;
        document.getElementById('mediaReleaseModalTitle').textContent = title + ' Media Release Submission';

        window.initSignaturePad?.();
    }

    function closeMediaReleaseModal() {
        const modal = document.getElementById('mediaReleaseModal');
        modal.hidden = true;
        document.body.classList.remove('modal-open');
        resetMediaReleaseModal();
        stopCamera();
    }
</script>
