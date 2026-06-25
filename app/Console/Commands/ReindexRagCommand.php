<?php

namespace App\Console\Commands;

use App\Services\Rag\RagIndexerService;
use App\Services\SettingsService;
use Illuminate\Console\Command;

class ReindexRagCommand extends Command
{
    protected $signature = 'rag:reindex';

    protected $description = 'Rebuild the RAG knowledge base from database records';

    public function handle(RagIndexerService $indexer, SettingsService $settings): int
    {
        if (! $settings->isOpenAiConfigured()) {
            $this->error('OPENAI_API_KEY is not configured. Set it in your .env file before reindexing.');

            return self::FAILURE;
        }

        $this->info('Reindexing RAG knowledge base...');

        $count = $indexer->reindexAll();

        $settings->set('last_reindex_at', now()->toIso8601String());

        $this->info("Indexed {$count} chunks.");

        return self::SUCCESS;
    }
}
