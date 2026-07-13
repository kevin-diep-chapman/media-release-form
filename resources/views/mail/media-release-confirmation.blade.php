@php
    $affiliationDetailsLabel = match ($release->affiliation) {
        'Current Student' => 'Program Name',
        'Faculty/Staff' => 'Department or School/College Name',
        'Alumni' => 'Program Name and Graduation Year',
        default => 'Connection to Chapman',
    };

    $display = fn (?string $value) => filled($value) ? $value : '—';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank you for your submission</title>
</head>
<body style="font-family: Arial, sans-serif; color: #231f20; line-height: 1.6;">

    <p>Hello {{ $release->first_name }},</p>

    <p>Thank you for completing the Media Release form. We appreciate your willingness to allow Chapman University to use your photo, video, audio, and/or other likeness in university communications and marketing materials.</p>

    <p>Below is a copy of the information you submitted for your records.</p>

    <p><strong>Your Submission</strong></p>

    <table cellpadding="0" cellspacing="0" style="width: 100%; max-width: 640px; border-collapse: collapse; margin: 0 0 1.5rem;">
        <tbody>
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top; width: 38%;">First Name</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">{{ $display($release->first_name) }}</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">Last Name</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">{{ $display($release->last_name) }}</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">Email</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">{{ $display($release->email) }}</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">Phone</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">{{ $display($release->phone) }}</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">Address</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">{{ $display($release->address) }}</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">City</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">{{ $display($release->city) }}</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">State</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">{{ $display($release->state) }}</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">Zip</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">{{ $display($release->zip_code) }}</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">Affiliation</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">{{ $display($release->affiliation) }}</td>
            </tr>
            @if ($release->affiliation_details)
                <tr>
                    <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">{{ $affiliationDetailsLabel }}</td>
                    <td style="padding: 0.5rem 0; vertical-align: top;">{{ $release->affiliation_details }}</td>
                </tr>
            @endif
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">Event</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">{{ $display($release->event?->title) }}</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">Photo</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">
                    @if ($photoDataUri)
                        <img src="{{ $photoDataUri }}" alt="Submitted photo" style="display: block; max-width: 220px; width: 100%; height: auto; border: 1px solid #ececec; border-radius: 8px;">
                    @else
                        —
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">Signature</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">
                    @if ($signatureDataUri)
                        <img src="{{ $signatureDataUri }}" alt="Submitted signature" style="display: block; max-width: 320px; width: 100%; height: auto; border: 1px solid #ececec; border-radius: 8px;">
                    @else
                        —
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 1rem 0.5rem 0; font-weight: 700; vertical-align: top;">Consent Agreed?</td>
                <td style="padding: 0.5rem 0; vertical-align: top;">{{ $release->consent_agreed ? 'Yes' : 'No' }}</td>
            </tr>
        </tbody>
    </table>

    <p>If you notice any errors in your submission or have questions about your authorization, please contact the Strategic Marketing &amp; Communications team at smc@chapman.edu</p>

    <p>Thank you again for your support in helping us share the stories and experiences of our campus community.</p>

    <p>Warm regards,</p>
    <p>Strategic Marketing &amp; Communications</p>
    <p>Chapman University</p>

</body>
</html>
