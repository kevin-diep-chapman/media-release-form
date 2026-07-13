<?php

namespace App\Mail;

use App\Models\MediaRelease;
use App\Services\EncryptedImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MediaReleaseConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MediaRelease $release)
    {
        $this->release->loadMissing('event');
    }

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
            with: [
                'photoDataUri' => $this->imageDataUri(
                    $this->release->photo_encrypted,
                    $this->release->photo_mime,
                ),
                'signatureDataUri' => $this->imageDataUri(
                    $this->release->signature_encrypted,
                    $this->release->signature_mime,
                ),
            ],
        );
    }

    private function imageDataUri(?string $encrypted, ?string $mime): ?string
    {
        if (! $encrypted) {
            return null;
        }

        $binary = app(EncryptedImageService::class)->decryptToBinary($encrypted);

        return 'data:'.($mime ?: 'image/png').';base64,'.base64_encode($binary);
    }
}
