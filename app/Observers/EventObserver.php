<?php

namespace App\Observers;

use App\Models\Event;
use App\Services\Rag\RagIndexerService;
use App\Services\SettingsService;

class EventObserver
{
    public function __construct(
        private readonly RagIndexerService $indexer,
        private readonly SettingsService $settings,
    ) {}

    public function saved(Event $event): void
    {
        if (! $this->shouldIndex()) {
            return;
        }

        $this->indexer->indexEvent($event);
    }

    public function deleted(Event $event): void
    {
        $this->indexer->deleteSource('event', $event->id);
    }

    private function shouldIndex(): bool
    {
        return $this->settings->isOpenAiConfigured();
    }
}
