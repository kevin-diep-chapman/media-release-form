<div id="printFormLinkModal" class="modal-shell media-release-modal print-form-link-modal" hidden>
    <div class="media-release-modal-card print-form-link-modal-card">
        <button type="button" class="modal-close" onclick="closePrintFormLinkModal()" aria-label="Close">&times;</button>

        <h2 id="printFormLinkModalTitle" class="media-release-title">Share Form Link</h2>
        <p class="print-form-link-intro">
            Send this link to participants. They can complete the media release form without logging in. A photo is not required for print events.
        </p>

        <label class="print-form-link-label" for="printFormLinkUrl">Form URL</label>
        <input id="printFormLinkUrl" type="text" class="print-form-link-input" readonly>
        <p id="printFormLinkCopyStatus" class="print-form-link-status" hidden role="status">Link copied to clipboard.</p>

        <div class="print-form-link-actions">
            <button type="button" class="button-add-form" onclick="copyPrintFormLink()">Copy Link</button>
            <a id="printFormLinkOpen" class="button-details" href="#" target="_blank" rel="noopener noreferrer">Open Form</a>
            <button type="button" class="media-release-btn-back" onclick="closePrintFormLinkModal()">Close</button>
        </div>
    </div>
</div>

<script>
    function showPrintFormLink(formUrl, eventTitle) {
        const modal = document.getElementById('printFormLinkModal');
        const urlInput = document.getElementById('printFormLinkUrl');
        const openLink = document.getElementById('printFormLinkOpen');
        const title = document.getElementById('printFormLinkModalTitle');
        const copyStatus = document.getElementById('printFormLinkCopyStatus');

        if (!modal || !urlInput) {
            return;
        }

        urlInput.value = formUrl;
        openLink.href = formUrl;
        title.textContent = (eventTitle || 'Event') + ' — Share Form Link';
        copyStatus.hidden = true;

        modal.hidden = false;
        document.body.classList.add('modal-open');
    }

    function closePrintFormLinkModal() {
        const modal = document.getElementById('printFormLinkModal');
        if (modal) {
            modal.hidden = true;
        }
        document.body.classList.remove('modal-open');
    }

    async function copyPrintFormLink() {
        const urlInput = document.getElementById('printFormLinkUrl');
        const copyStatus = document.getElementById('printFormLinkCopyStatus');

        if (!urlInput?.value) {
            return;
        }

        try {
            await navigator.clipboard.writeText(urlInput.value);
            if (copyStatus) {
                copyStatus.hidden = false;
            }
        } catch (error) {
            urlInput.select();
            document.execCommand('copy');
            if (copyStatus) {
                copyStatus.hidden = false;
            }
        }
    }
</script>
