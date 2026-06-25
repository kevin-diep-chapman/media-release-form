@php
    $dateType = old('date_type', $dateType ?? 'single');
    $eventDate = old('event_date', $eventDate ?? '');
    $eventEndDate = old('event_end_date', $eventEndDate ?? '');
    $minDate = $minDate ?? null;
@endphp

<fieldset class="event-date-fieldset">
    <legend class="event-date-legend">Date</legend>

    <div class="event-date-type" role="radiogroup" aria-label="Date type">
        <label class="event-date-type-option">
            <input type="radio" name="date_type" value="single" @checked($dateType === 'single')>
            Single date
        </label>
        <label class="event-date-type-option">
            <input type="radio" name="date_type" value="range" @checked($dateType === 'range')>
            Date range
        </label>
    </div>

    <label for="event_date" class="event-date-label" data-label-single="Date" data-label-range="Start date">Date</label>
    <input
        id="event_date"
        type="date"
        name="event_date"
        value="{{ $eventDate }}"
        @if($minDate) min="{{ $minDate }}" @endif
    >

    <div class="event-date-range" @if($dateType !== 'range') hidden @endif>
        <label for="event_end_date">End date</label>
        <input
            id="event_end_date"
            type="date"
            name="event_end_date"
            value="{{ $eventEndDate }}"
            @if($minDate) min="{{ $minDate }}" @endif
        >
    </div>
    <p id="event-date-error" class="field-error" hidden></p>
</fieldset>

<script>
    (function () {
        const fieldset = document.currentScript.previousElementSibling;
        if (!fieldset) return;

        const dateTypeInputs = fieldset.querySelectorAll('input[name="date_type"]');
        const dateLabel = fieldset.querySelector('.event-date-label');
        const startDateInput = fieldset.querySelector('#event_date');
        const endDateField = fieldset.querySelector('.event-date-range');
        const endDateInput = fieldset.querySelector('#event_end_date');
        const minDate = startDateInput?.min || '';

        function syncEndDateMin() {
            if (!endDateInput || !minDate) return;

            const startDate = startDateInput.value;
            endDateInput.min = startDate && startDate > minDate ? startDate : minDate;
        }

        function syncDateFields() {
            const selected = fieldset.querySelector('input[name="date_type"]:checked');
            const isRange = selected && selected.value === 'range';

            endDateField.hidden = !isRange;
            if (!isRange) {
                endDateInput.value = '';
            }

            dateLabel.textContent = isRange
                ? dateLabel.dataset.labelRange
                : dateLabel.dataset.labelSingle;

            syncEndDateMin();
        }

        dateTypeInputs.forEach((input) => {
            input.addEventListener('change', syncDateFields);
        });

        startDateInput?.addEventListener('change', syncEndDateMin);

        syncDateFields();
    })();
</script>
