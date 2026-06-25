<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank you for your submission</title>
</head>
<body style="font-family: Arial, sans-serif; color: #231f20; line-height: 1.6;">
    <p>Hello {{ $release->first_name }},</p>

    <p>
        Thank you for signing up for
        <strong>{{ $release->event->title }}</strong>.
    </p>

    <p>
        We have received your media release form submission and appreciate you taking the time to complete it.
    </p>

    <p>Chapman Media</p>
</body>
</html>
