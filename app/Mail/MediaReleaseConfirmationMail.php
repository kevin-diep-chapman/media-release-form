<?php

namespace App\Mail;

use App\Models\MediaRelease;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MediaReleaseConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MediaRelease $release) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('media_release.confirmation_email.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.media-release-confirmation',
        );
    }
}
