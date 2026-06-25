<?php

namespace App\Services\Rag;

use App\Models\Event;
use App\Models\MediaRelease;
use App\Models\RagChunk;
use App\Models\User;
use Illuminate\Support\Collection;

class RagDatabaseSummaryService
{
    public function __construct(
        private readonly RagSourceUrlResolver $urlResolver,
    ) {}

    public function totalsBlock(): string
    {
        return implode("\n", [
            'Database summary (authoritative totals — use these for "how many" questions):',
            '- Events: '.Event::query()->count(),
            '- Media release submissions: '.MediaRelease::query()->count(),
            '- Users: '.User::query()->count(),
        ]);
    }

    /**
     * @return Collection<int, RagChunk>
     */
    public function chunksForSourceType(string $sourceType): Collection
    {
        return RagChunk::query()
            ->where('source_type', $sourceType)
            ->orderBy('source_id')
            ->get();
    }

    public function recordsBlock(string $sourceType): string
    {
        $chunks = $this->chunksForSourceType($sourceType);

        if ($chunks->isEmpty()) {
            return 'No indexed records found for '.$this->labelForSourceType($sourceType).'.';
        }

        $uniqueChunks = $chunks->unique(fn (RagChunk $chunk) => $chunk->source_type.'-'.$chunk->source_id);

        $lines = [
            'Complete indexed list of '.$this->labelForSourceType($sourceType).' ('.$uniqueChunks->count().' total):',
        ];

        foreach ($uniqueChunks->values() as $index => $chunk) {
            $metadata = $chunk->metadata ?? [];
            $label = $metadata['label'] ?? 'Record '.$chunk->source_id;
            $url = $this->urlResolver->urlForChunk($chunk);

            $lines[] = ($index + 1).'. '.$label.' (URL: '.$url.')';
        }

        return implode("\n", $lines);
    }

    private function labelForSourceType(string $sourceType): string
    {
        return match ($sourceType) {
            'event' => 'events',
            'media_release' => 'media release submissions',
            'user' => 'users',
            default => $sourceType,
        };
    }
}
