(function () {
    const form = document.getElementById('create-event-form');
    if (!form) return;

    const titleInput = form.querySelector('#title');
    const descriptionInput = form.querySelector('#description');
    const eventDateInput = form.querySelector('#event_date');
    const eventEndDateInput = form.querySelector('#event_end_date');
    const dateTypeInputs = form.querySelectorAll('input[name="date_type"]');
    const eventFormatInputs = form.querySelectorAll('input[name="event_format"]');
    const minDate = eventDateInput?.min || getTodayPacific();

    function getTodayPacific() {
        return new Intl.DateTimeFormat('en-CA', {
            timeZone: 'America/Los_Angeles',
        }).format(new Date());
    }

    function isBeforeMinDate(dateValue) {
        return dateValue < minDate;
    }

    const fields = {
        title: {
            input: titleInput,
            error: form.querySelector('#title-error'),
            validate() {
                if (!titleInput.value.trim()) {
                    return 'Title is required.';
                }
                return '';
            },
        },
        description: {
            input: descriptionInput,
            error: form.querySelector('#description-error'),
            validate() {
                if (!descriptionInput.value.trim()) {
                    return 'Description is required.';
                }
                return '';
            },
        },
        eventFormat: {
            input: eventFormatInputs[0],
            error: form.querySelector('#event-format-error'),
            validate() {
                if (!form.querySelector('input[name="event_format"]:checked')) {
                    return 'Please select whether this event is for in person or print.';
                }
                return '';
            },
        },
        eventDate: {
            input: eventDateInput,
            error: form.querySelector('#event-date-error'),
            validate() {
                const dateType = form.querySelector('input[name="date_type"]:checked');
                if (!dateType) {
                    return 'Please select a date type.';
                }
                if (!eventDateInput.value) {
                    return dateType.value === 'range'
                        ? 'Start date is required.'
                        : 'Date is required.';
                }
                if (isBeforeMinDate(eventDateInput.value)) {
                    return dateType.value === 'range'
                        ? 'Start date cannot be in the past.'
                        : 'Date cannot be in the past.';
                }
                if (dateType.value === 'range' && !eventEndDateInput.value) {
                    return 'End date is required.';
                }
                if (dateType.value === 'range' && isBeforeMinDate(eventEndDateInput.value)) {
                    return 'End date cannot be in the past.';
                }
                return '';
            },
        },
    };

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

    form.addEventListener('submit', (event) => {
        if (!validateForm()) {
            event.preventDefault();
        }
    });

    [titleInput, descriptionInput, eventDateInput, eventEndDateInput].forEach((input) => {
        input?.addEventListener('input', clearErrors);
    });

    dateTypeInputs.forEach((input) => {
        input.addEventListener('change', clearErrors);
    });

    eventFormatInputs.forEach((input) => {
        input.addEventListener('change', clearErrors);
    });
})();
