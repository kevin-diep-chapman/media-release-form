<?php

namespace App\Observers;

use App\Models\MediaRelease;
use App\Services\Rag\RagIndexerService;
use App\Services\SettingsService;

class MediaReleaseObserver
{
    public function __construct(
        private readonly RagIndexerService $indexer,
        private readonly SettingsService $settings,
    ) {}

    public function saved(MediaRelease $release): void
    {
        if (! $this->shouldIndex()) {
            return;
        }

        $this->indexer->indexMediaRelease($release);
    }

    public function deleted(MediaRelease $release): void
    {
        $this->indexer->deleteSource('media_release', $release->id);
    }

    private function shouldIndex(): bool
    {
        return $this->settings->isOpenAiConfigured();
    }
}
