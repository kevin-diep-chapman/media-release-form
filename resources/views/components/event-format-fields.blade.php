@php
    $eventFormat = old('event_format', $eventFormat ?? 'in_person');
@endphp

<fieldset class="event-date-fieldset">
    <legend class="event-date-legend">Event format</legend>

    <div class="event-date-type" role="radiogroup" aria-label="Event format">
        <label class="event-date-type-option">
            <input type="radio" name="event_format" value="in_person" @checked($eventFormat === 'in_person')>
            In person
        </label>
        <label class="event-date-type-option">
            <input type="radio" name="event_format" value="print" @checked($eventFormat === 'print')>
            Print
        </label>
    </div>
    <p id="event-format-error" class="field-error" hidden></p>
</fieldset>
