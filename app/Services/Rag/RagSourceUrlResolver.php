<?php

namespace App\Services\Rag;

use App\Models\RagChunk;

class RagSourceUrlResolver
{
    public function urlFor(string $sourceType, int $sourceId): ?string
    {
        return match ($sourceType) {
            'event' => route('events.show', $sourceId),
            'media_release' => route('dashboard.media-releases.show', $sourceId),
            'user' => route('dashboard.users.index'),
            default => null,
        };
    }

    public function urlForChunk(RagChunk $chunk): ?string
    {
        $metadata = $chunk->metadata ?? [];

        if (isset($metadata['url']) && is_string($metadata['url']) && $metadata['url'] !== '') {
            return $metadata['url'];
        }

        return $this->urlFor($chunk->source_type, $chunk->source_id);
    }

    public function listUrlFor(string $sourceType): ?string
    {
        return match ($sourceType) {
            'event' => route('public.permitted-events'),
            'media_release' => route('dashboard.media-releases.index'),
            'user' => route('dashboard.users.index'),
            default => null,
        };
    }

    public function listLabelFor(string $sourceType): ?string
    {
        return match ($sourceType) {
            'event' => 'View all events',
            'media_release' => 'View all media release submissions',
            'user' => 'View all users',
            default => null,
        };
    }
}
