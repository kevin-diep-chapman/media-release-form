<?php

namespace App\Observers;

use App\Models\User;
use App\Services\Rag\RagIndexerService;
use App\Services\SettingsService;

class UserObserver
{
    public function __construct(
        private readonly RagIndexerService $indexer,
        private readonly SettingsService $settings,
    ) {}

    public function saved(User $user): void
    {
        if (! $this->shouldIndex()) {
            return;
        }

        $this->indexer->indexUser($user);
    }

    public function deleted(User $user): void
    {
        $this->indexer->deleteSource('user', $user->id);
    }

    private function shouldIndex(): bool
    {
        return $this->settings->isOpenAiConfigured();
    }
}
