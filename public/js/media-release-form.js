(function () {
    const affiliationsWithDetails = [
        'Current Student',
        'Faculty/Staff',
        'Alumni',
        'Parent/Family Member',
        'Other',
    ];

    const affiliationLabels = {
        'Current Student': 'Program Name',
        'Faculty/Staff': 'Department or School/College Name',
        'Alumni': 'What is your Program Name and Graduation Year',
        'Parent/Family Member': 'Briefly describe your connection to Chapman.',
        'Other': 'Briefly describe your connection to Chapman.',
    };

    function initMediaReleaseForm() {
        const form = document.getElementById('mediaReleaseForm');
        if (!form) {
            return;
        }

        const requirePhoto = form.dataset.requirePhoto !== 'false' && form.dataset.requirePhoto !== '0';
        const isPublicForm = form.dataset.isPublic === 'true' || form.dataset.isPublic === '1';

        const affiliationSelect = form.querySelector('#affiliation');
        const affiliationDetailsField = form.querySelector('#affiliationDetailsField');
        const affiliationDetailsLabel = form.querySelector('#affiliation_details_label');
        const affiliationDetailsTextarea = form.querySelector('#affiliation_details');
        const affiliationDetailsSelect = form.querySelector('#affiliation_details_select');
        const affiliationDetailsTextareaWrap = form.querySelector('#affiliationDetailsTextareaWrap');
        const affiliationDetailsSelectWrap = form.querySelector('#affiliationDetailsSelectWrap');

        function shouldShowAffiliationDetails(selected) {
            return affiliationsWithDetails.includes(selected);
        }

        function isCurrentStudent(selected) {
            return selected === 'Current Student';
        }

        function getActiveAffiliationDetailsInput() {
            return isCurrentStudent(affiliationSelect.value)
                ? affiliationDetailsSelect
                : affiliationDetailsTextarea;
        }

        function showTextareaDetails() {
            affiliationDetailsTextareaWrap.hidden = false;
            affiliationDetailsSelectWrap.hidden = true;
            affiliationDetailsTextarea.disabled = false;
            affiliationDetailsSelect.disabled = true;
            affiliationDetailsTextarea.setAttribute('name', 'affiliation_details');
            affiliationDetailsSelect.removeAttribute('name');
            affiliationDetailsLabel.setAttribute('for', 'affiliation_details');
        }

        function showProgramSelect() {
            affiliationDetailsTextareaWrap.hidden = true;
            affiliationDetailsSelectWrap.hidden = false;
            affiliationDetailsTextarea.disabled = true;
            affiliationDetailsSelect.disabled = false;
            affiliationDetailsTextarea.removeAttribute('name');
            affiliationDetailsSelect.setAttribute('name', 'affiliation_details');
            affiliationDetailsLabel.setAttribute('for', 'affiliation_details_select');
        }

        function syncAffiliationField() {
            const selected = affiliationSelect.value;

            if (!shouldShowAffiliationDetails(selected)) {
                affiliationDetailsField.hidden = true;
                affiliationDetailsField.classList.add('is-hidden');
                affiliationDetailsTextarea.value = '';
                affiliationDetailsSelect.value = '';
                showTextareaDetails();
                return;
            }

            affiliationDetailsField.hidden = false;
            affiliationDetailsField.classList.remove('is-hidden');
            affiliationDetailsLabel.textContent = affiliationLabels[selected] || 'Briefly describe your connection to Chapman.';

            if (isCurrentStudent(selected)) {
                affiliationDetailsTextarea.value = '';
                showProgramSelect();
                return;
            }

            affiliationDetailsSelect.value = '';
            showTextareaDetails();
        }

        affiliationSelect?.addEventListener('change', () => {
            clearErrors();
            syncAffiliationField();
        });

        syncAffiliationField();
        window.syncMediaReleaseAffiliationField = syncAffiliationField;

        const zipCodeInput = form.querySelector('#zip_code');

        zipCodeInput?.addEventListener('input', () => {
            zipCodeInput.value = zipCodeInput.value.replace(/\D/g, '').slice(0, 5);
        });

        const fields = {
            first_name: {
                input: form.querySelector('#first_name'),
                error: form.querySelector('#first_name-error'),
                validate() {
                    if (!this.input.value.trim()) {
                        return 'First name is required.';
                    }
                    return '';
                },
            },
            last_name: {
                input: form.querySelector('#last_name'),
                error: form.querySelector('#last_name-error'),
                validate() {
                    if (!this.input.value.trim()) {
                        return 'Last name is required.';
                    }
                    return '';
                },
            },
            email: {
                input: form.querySelector('#email'),
                error: form.querySelector('#email-error'),
                validate() {
                    const value = this.input.value.trim();
                    if (!value) {
                        return 'Email is required.';
                    }
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                        return 'Please enter a valid email address.';
                    }
                    return '';
                },
            },
            affiliation: {
                input: affiliationSelect,
                error: form.querySelector('#affiliation-error'),
                validate() {
                    if (!this.input.value) {
                        return 'Affiliation is required.';
                    }
                    return '';
                },
            },
            affiliation_details: {
                get input() {
                    return getActiveAffiliationDetailsInput();
                },
                error: form.querySelector('#affiliation_details-error'),
                validate() {
                    const activeInput = getActiveAffiliationDetailsInput();
                    if (shouldShowAffiliationDetails(affiliationSelect.value) && !activeInput.value.trim()) {
                        return isCurrentStudent(affiliationSelect.value)
                            ? 'Program name is required.'
                            : 'This field is required.';
                    }
                    return '';
                },
            },
            zip_code: {
                input: zipCodeInput,
                error: form.querySelector('#zip_code-error'),
                validate() {
                    const value = this.input.value.trim();
                    if (value && !/^\d{5}$/.test(value)) {
                        return 'Zip code must be exactly 5 digits.';
                    }
                    return '';
                },
            },
            signature_path: {
                input: form.querySelector('#signature_path'),
                error: form.querySelector('#signature_path-error'),
                validate() {
                    if (!window.hasSignaturePadValue?.()) {
                        return 'Signature is required.';
                    }
                    return '';
                },
            },
        };

        if (requirePhoto) {
            fields.photo_path = {
                input: form.querySelector('#photo_path'),
                error: form.querySelector('#photo_path-error'),
                validate() {
                    if (!this.input?.value) {
                        return 'Photo is required.';
                    }
                    return '';
                },
            };
        }

        function clearErrors() {
            Object.values(fields).forEach(({ input, error }) => {
                input?.classList.remove('input-invalid');
                if (error) {
                    error.textContent = '';
                    error.hidden = true;
                }
            });
        }

        function showError(field, message) {
            field.input?.classList.add('input-invalid');
            if (field.error) {
                field.error.textContent = message;
                field.error.hidden = false;
            }
        }

        function validateForm() {
            clearErrors();

            let firstInvalidInput = null;
            let isValid = true;

            Object.values(fields).forEach((field) => {
                const message = field.validate();
                if (message) {
                    showError(field, message);
                    if (!firstInvalidInput) {
                        firstInvalidInput = field.input;
                    }
                    isValid = false;
                }
            });

            if (firstInvalidInput) {
                firstInvalidInput.focus();
            }

            return isValid;
        }

        form.addEventListener('input', clearErrors);
        form.addEventListener('change', clearErrors);

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (!validateForm()) {
                return;
            }

            const signatureInput = form.querySelector('#signature_path');
            const signatureCanvas = document.getElementById('signaturePad');
            if (signatureInput && signatureCanvas && window.hasSignaturePadValue?.()) {
                signatureInput.value = signatureCanvas.toDataURL('image/png');
            }

            const formData = new FormData(form);

            try {
                const res = await fetch(form.action || '/media-release', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.head.querySelector('[name=csrf-token]').content,
                    },
                    body: formData,
                });

                if (res.ok) {
                    const redirectUrl = form.dataset.successRedirect || '/my-events';
                    const successTarget = `${redirectUrl}${redirectUrl.includes('?') ? '&' : '?'}success=media-release`;

                    form.reset();
                    syncAffiliationField();
                    window.clearSignaturePad?.();
                    window.initSignaturePad?.();

                    if (isPublicForm) {
                        window.location.href = successTarget;
                        return;
                    }

                    closeMediaReleaseModal?.();
                    showMediaReleaseSuccessToast?.();

                    window.setTimeout(() => {
                        window.location.href = successTarget;
                    }, 1800);
                } else {
                    const error = await res.json();
                    const messages = error.errors
                        ? Object.values(error.errors).flat().join('\n')
                        : (error.message || 'Form submission failed');
                    alert('Error: ' + messages);
                }
            } catch (err) {
                alert('An error occurred: ' + err.message);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMediaReleaseForm);
    } else {
        initMediaReleaseForm();
    }
})();
