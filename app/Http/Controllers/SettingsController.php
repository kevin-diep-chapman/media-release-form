<?php

namespace App\Http\Controllers;

use App\Models\RagChunk;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SettingsController extends Controller
{
    public function index(SettingsService $settings)
    {
        $chunkCount = RagChunk::query()->count();
        $lastReindexAt = $settings->get('last_reindex_at');
        $lastIndexedAt = RagChunk::query()->max('indexed_at');

        return view('dashboard.settings.index', [
            'chatboxEnabled' => $settings->isChatboxEnabled(),
            'openAiConfigured' => $settings->isOpenAiConfigured(),
            'chunkCount' => $chunkCount,
            'lastReindexAt' => $lastReindexAt ? Carbon::parse($lastReindexAt) : null,
            'lastIndexedAt' => $lastIndexedAt ? Carbon::parse($lastIndexedAt) : null,
        ]);
    }

    public function update(Request $request, SettingsService $settings)
    {
        $validated = $request->validate([
            'chatbox_enabled' => 'nullable|boolean',
        ]);

        $settings->set('chatbox_enabled', $request->boolean('chatbox_enabled'));

        return redirect()
            ->route('dashboard.settings.index')
            ->with('success', 'Settings saved successfully.');
    }

    public function reindex(SettingsService $settings)
    {
        if (! $settings->isOpenAiConfigured()) {
            return redirect()
                ->route('dashboard.settings.index')
                ->withErrors(['openai' => 'OPENAI_API_KEY is not configured. Add it to your .env file before reindexing.']);
        }

        $exitCode = $this->callReindexCommand();

        if ($exitCode !== 0) {
            return redirect()
                ->route('dashboard.settings.index')
                ->withErrors(['reindex' => 'Reindexing failed. Check your OpenAI configuration and try again.']);
        }

        return redirect()
            ->route('dashboard.settings.index')
            ->with('success', 'Knowledge base reindexed successfully.');
    }

    private function callReindexCommand(): int
    {
        return \Illuminate\Support\Facades\Artisan::call('rag:reindex');
    }
}
