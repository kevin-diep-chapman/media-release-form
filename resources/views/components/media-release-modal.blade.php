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

<div id="mediaReleaseModal" class="modal-shell media-release-modal" style="display:none;">
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
                <div class="media-release-fields">
                    <div class="media-release-row media-release-row-2">
                        <div class="media-release-field">
                            <label class="media-release-field-label" for="first_name">First Name*</label>
                            <input id="first_name" type="text" name="first_name" autocomplete="given-name">
                            <p id="first_name-error" class="field-error" hidden></p>
                        </div>
                        <div class="media-release-field">
                            <label class="media-release-field-label" for="last_name">Last Name*</label>
                            <input id="last_name" type="text" name="last_name" autocomplete="family-name">
                            <p id="last_name-error" class="field-error" hidden></p>
                        </div>
                    </div>

                    <div class="media-release-row media-release-row-2">
                        <div class="media-release-field">
                            <label class="media-release-field-label" for="email">Email*</label>
                            <input id="email" type="email" name="email" autocomplete="email">
                            <p id="email-error" class="field-error" hidden></p>
                        </div>
                        <div class="media-release-field">
                            <label class="media-release-field-label" for="phone">Phone</label>
                            <input id="phone" type="text" name="phone" autocomplete="tel">
                        </div>
                    </div>

                    <div class="media-release-row">
                        <div class="media-release-field">
                            <label class="media-release-field-label" for="address">Address</label>
                            <input id="address" type="text" name="address" autocomplete="street-address">
                        </div>
                    </div>

                    <div class="media-release-row media-release-row-3">
                        <div class="media-release-field">
                            <label class="media-release-field-label" for="city">City</label>
                            <input id="city" type="text" name="city" autocomplete="address-level2">
                        </div>
                        <div class="media-release-field">
                            <label class="media-release-field-label" for="state">State</label>
                            <select id="state" name="state" autocomplete="address-level1">
                                <option value="">Select state</option>
                                @foreach (App\Models\MediaRelease::STATE_CODES as $stateCode)
                                    <option value="{{ $stateCode }}">{{ $stateCode }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="media-release-field">
                            <label class="media-release-field-label" for="zip_code">Zip</label>
                            <input
                                id="zip_code"
                                type="text"
                                name="zip_code"
                                inputmode="numeric"
                                pattern="[0-9]{5}"
                                maxlength="5"
                                autocomplete="postal-code"
                            >
                            <p id="zip_code-error" class="field-error" hidden></p>
                        </div>
                    </div>

                    <div class="media-release-row">
                        <div class="media-release-field">
                            <label class="media-release-field-label" for="affiliation">Affiliation</label>
                            <select id="affiliation" name="affiliation">
                                <option value="">Select affiliation</option>
                                @foreach (App\Models\MediaRelease::AFFILIATIONS as $affiliation)
                                    <option value="{{ $affiliation }}">{{ $affiliation }}</option>
                                @endforeach
                            </select>
                            <p id="affiliation-error" class="field-error" hidden></p>
                        </div>
                    </div>

                    <div id="affiliationDetailsField" class="media-release-row affiliation-details-field is-hidden" hidden>
                        <div class="media-release-field">
                            <label class="media-release-field-label" id="affiliation_details_label" for="affiliation_details">Briefly describe your connection to Chapman.</label>
                            <textarea id="affiliation_details" name="affiliation_details" rows="3"></textarea>
                            <p id="affiliation_details-error" class="field-error" hidden></p>
                        </div>
                    </div>

                    <div class="media-release-row">
                        <div class="media-release-field">
                            <span class="media-release-field-label">Photo*</span>
                            <div class="media-release-photo-panel">
                                <div id="cameraContainer" class="media-release-photo-box">
                                    <video id="video" autoplay style="display:none;"></video>
                                    <canvas id="canvas" style="display:none;"></canvas>
                                    <img id="photoPreview" src="#" alt="Photo preview" style="display:none;">
                                    <input type="hidden" name="photo_path" id="photo_path">
                                </div>
                                <button id="takePhotoBtn" type="button" class="media-release-photo-trigger" onclick="startCamera()">Take Photo</button>
                                <div class="media-release-photo-actions">
                                    <button id="captureBtn" type="button" class="media-release-photo-action" style="display:none;" onclick="capturePhoto()">Capture</button>
                                    <button id="retakeBtn" type="button" class="media-release-photo-action" style="display:none;" onclick="retakePhoto()">Retake</button>
                                </div>
                                <p id="photo_path-error" class="field-error" hidden></p>
                            </div>
                        </div>
                    </div>

                    <div class="media-release-row">
                        <div class="media-release-field signature-field">
                            <p class="signature-disclaimer">
                                By signing this form, you grant Chapman University permission to photograph, record, and use your image, likeness, voice, and/or statements in university publications, marketing materials, websites, social media, and other promotional communications.
                            </p>
                            <label class="media-release-field-label" for="signaturePad">Signature*</label>
                            <div class="signature-pad-wrap">
                                <canvas id="signaturePad" aria-label="Signature pad"></canvas>
                            </div>
                            <button id="clearSignatureBtn" type="button" class="signature-clear-btn">Clear Signature</button>
                            <input type="hidden" name="signature_path" id="signature_path">
                            <p id="signature_path-error" class="field-error" hidden></p>
                        </div>
                    </div>

                    <div class="media-release-row">
                        <div class="media-release-field">
                            <span class="media-release-field-label">Consent Agreed?</span>
                            <label class="media-release-consent-line">
                                <input id="consent_agreed" type="checkbox" name="consent_agreed" value="1">
                                <span>I consent to being contacted by Chapman University regarding future photo, video, or interview requests.</span>
                            </label>
                        </div>
                    </div>

                    <div class="media-release-row">
                        <div class="media-release-field">
                            <label class="media-release-field-label" for="mediaReleaseEventDisplay">Event</label>
                            <input id="mediaReleaseEventDisplay" type="text" readonly tabindex="-1" aria-readonly="true">
                        </div>
                    </div>
                </div>
            </div>

            <div class="media-release-form-actions">
                <button type="button" class="media-release-btn-back" onclick="closeMediaReleaseModal()">&lt; Cancel</button>
                <button type="submit" class="media-release-btn-submit">Submit Form &gt;</button>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('js/camera.js') }}"></script>
<script src="{{ asset('js/signature-pad.js') }}"></script>
<script src="{{ asset('js/media-release-form.js') }}"></script>
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
        document.getElementById('mediaReleaseModal').style.display = 'block';
        document.getElementById('release_event_id').value = eventId;

        const title = eventTitle || 'Event';
        document.getElementById('mediaReleaseBreadcrumbEvent').textContent = title;
        document.getElementById('mediaReleaseEventDisplay').value = title;
        document.getElementById('mediaReleaseModalTitle').textContent = title + ' Media Release Submission';

        window.initSignaturePad?.();
    }

    function closeMediaReleaseModal() {
        document.getElementById('mediaReleaseModal').style.display = 'none';
        resetMediaReleaseModal();
        stopCamera();
    }
</script>
