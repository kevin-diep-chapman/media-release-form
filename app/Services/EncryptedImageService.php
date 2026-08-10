<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use InvalidArgumentException;

class EncryptedImageService
{
    /**
     * @return array{binary: string, mime: string}
     */
    public function fromDataUrl(string $dataUrl): array
    {
        if (! preg_match('/^data:(image\/[\w.+-]+);base64,(.+)$/s', $dataUrl, $matches)) {
            throw new InvalidArgumentException('Invalid image data URL.');
        }

        $binary = base64_decode($matches[2], true);

        if ($binary === false) {
            throw new InvalidArgumentException('Invalid base64 image data.');
        }

        return [
            'binary' => $binary,
            'mime' => strtolower($matches[1]),
        ];
    }

    public function encryptBinary(string $binary): string
    {
        return Crypt::encryptString(base64_encode($binary));
    }

    /**
     * @return array{binary: string, mime: string}
     */
    public function fromUploadedFile(\Illuminate\Http\UploadedFile $file): array
    {
        $binary = file_get_contents($file->getRealPath());

        if ($binary === false) {
            throw new InvalidArgumentException('Unable to read uploaded image.');
        }

        $mime = strtolower($file->getMimeType() ?: 'image/jpeg');

        if (! str_starts_with($mime, 'image/')) {
            throw new InvalidArgumentException('Uploaded file must be an image.');
        }

        return [
            'binary' => $binary,
            'mime' => $mime,
        ];
    }

    public function decryptToBinary(string $encrypted): string
    {
        $decoded = base64_decode(Crypt::decryptString($encrypted), true);

        if ($decoded === false) {
            throw new InvalidArgumentException('Unable to decrypt image data.');
        }

        return $decoded;
    }
}
