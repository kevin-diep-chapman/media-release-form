<?php

namespace App\Http\Controllers;

use App\Models\MediaRelease;
use App\Services\EncryptedImageService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaReleaseImageController extends Controller
{
    public function __construct(
        private readonly EncryptedImageService $images,
    ) {}

    public function photo(int $id): Response|StreamedResponse
    {
        $release = MediaRelease::with('event')->findOrFail($id);
        $this->authorizeReleaseAccess($release);

        return $this->serveImage(
            encrypted: $release->photo_encrypted,
            mime: $release->photo_mime,
            legacyPath: $release->photo_path,
        );
    }

    public function signature(int $id): Response|StreamedResponse
    {
        $release = MediaRelease::with('event')->findOrFail($id);
        $this->authorizeReleaseAccess($release);

        return $this->serveImage(
            encrypted: $release->signature_encrypted,
            mime: $release->signature_mime,
            legacyPath: $release->signature_path,
        );
    }

    private function serveImage(?string $encrypted, ?string $mime, ?string $legacyPath): Response|StreamedResponse
    {
        if ($encrypted) {
            $binary = $this->images->decryptToBinary($encrypted);

            return response($binary, 200, [
                'Content-Type' => $mime ?: 'image/jpeg',
                'Cache-Control' => 'private, no-store, max-age=0',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        if ($legacyPath) {
            $relativePath = str_starts_with($legacyPath, 'storage/')
                ? substr($legacyPath, strlen('storage/'))
                : $legacyPath;

            if (Storage::disk('public')->exists($relativePath)) {
                return Storage::disk('public')->response($relativePath, headers: [
                    'Cache-Control' => 'private, no-store, max-age=0',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }
        }

        abort(404);
    }

    private function authorizeReleaseAccess(MediaRelease $release): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        if ($user->isUser() && $release->event->created_by != $user->id) {
            abort(403);
        }
    }
}
