<?php

namespace App\Services\Rag;

use Illuminate\Support\Collection;

use OpenAI\Laravel\Facades\OpenAI;

class ChatService
{
    public function __construct(
        private readonly RagSearchService $searchService,
        private readonly RagSourceUrlResolver $urlResolver,
        private readonly RagQueryAnalyzer $queryAnalyzer,
        private readonly RagDatabaseSummaryService $databaseSummary,
    ) {}

    /**
     * @return array{answer: string, sources: list<array{label: string, type: string, id: int, url: string|null}>}
     */
    public function respond(string $message): array
    {
        $aggregateQuery = $this->queryAnalyzer->aggregateQuery($message);

        if ($aggregateQuery !== null) {
            $chunks = $this->databaseSummary->chunksForSourceType($aggregateQuery['source_type']);
            $context = $this->buildAggregateContext($aggregateQuery);
        } else {
            $chunks = $this->searchService->search($message);
            $context = $this->buildContext($chunks);
        }

        $sources = $this->extractSources($chunks);

        $response = OpenAI::chat()->create([
            'model' => config('rag.chat_model'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->systemPrompt(),
                ],
                [
                    'role' => 'user',
                    'content' => "Context:\n{$context}\n\nQuestion: {$message}",
                ],
            ],
            'temperature' => 0.2,
        ]);

        $answer = trim($response->choices[0]->message->content ?? '');

        if ($answer === '') {
            $answer = 'I was unable to generate a response. Please try again.';
        }

        return [
            'answer' => $answer,
            'sources' => $sources,
        ];
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
You are a helpful assistant for Chapman Media, a Chapman University events and media release platform.

Answer questions using only the context provided from the application's database.

For "how many" or count questions, use the numbers in the "Database summary" or "Complete indexed list" sections. Those totals are authoritative. Do not say you lack information when those sections contain the answer.

When multiple individual records appear in the context, you may count them, list them, or summarize them as appropriate to the user's question.

Only say "I don't have that information in the Chapman Media database." when the context is empty or truly unrelated to the question.

Do not invent events, users, or submissions. Keep answers concise and professional.

When you mention a specific event or media release submission, include a markdown link to its detail page using the URL from the context. Use this format: [visible label](URL).

When the user asks for a listing of events or submissions, present each matching item as a markdown link on its own line rather than only plain text. If a list page URL is provided in the context, you may also link to it for "view all" style requests.

For user records, link to the user directory when a URL is available.
PROMPT;
    }

    /**
     * @param  array{source_type: string, intent: string}  $aggregateQuery
     */
    private function buildAggregateContext(array $aggregateQuery): string
    {
        $parts = [
            $this->databaseSummary->totalsBlock(),
            $this->databaseSummary->recordsBlock($aggregateQuery['source_type']),
        ];

        $listUrl = $this->urlResolver->listUrlFor($aggregateQuery['source_type']);
        $listLabel = $this->urlResolver->listLabelFor($aggregateQuery['source_type']);

        if ($listUrl && $listLabel) {
            $parts[] = 'List page: '.$listLabel.' — '.$listUrl;
        }

        return implode("\n\n", $parts);
    }

    /**
     * @param  Collection<int, \App\Models\RagChunk>  $chunks
     */
    private function buildContext(Collection $chunks): string
    {
        $summary = $this->databaseSummary->totalsBlock();

        if ($chunks->isEmpty()) {
            return $summary."\n\nNo relevant records were found in the database.";
        }

        $sourceTypes = $chunks->pluck('source_type')->unique()->values();

        $context = $chunks
            ->map(function ($chunk, $index) {
                $url = $this->urlResolver->urlForChunk($chunk);
                $header = '[Source '.($index + 1).']';

                if ($url) {
                    $header .= ' (URL: '.$url.')';
                }

                return $header."\n".$chunk->content;
            })
            ->implode("\n\n");

        $listLinks = $sourceTypes
            ->map(function (string $sourceType) {
                $url = $this->urlResolver->listUrlFor($sourceType);
                $label = $this->urlResolver->listLabelFor($sourceType);

                if (! $url || ! $label) {
                    return null;
                }

                return $label.': '.$url;
            })
            ->filter()
            ->implode("\n");

        $parts = [$summary, $context];

        if ($listLinks !== '') {
            $parts[] = "List pages:\n".$listLinks;
        }

        return implode("\n\n", $parts);
    }

    /**
     * @param  Collection<int, \App\Models\RagChunk>  $chunks
     * @return list<array{label: string, type: string, id: int, url: string|null}>
     */
    private function extractSources(Collection $chunks): array
    {
        $sources = [];

        foreach ($chunks as $chunk) {
            $metadata = $chunk->metadata ?? [];

            if (! isset($metadata['label'], $metadata['type'], $metadata['id'])) {
                continue;
            }

            $key = $metadata['type'].'-'.$metadata['id'];

            if (isset($sources[$key])) {
                continue;
            }

            $sources[$key] = [
                'label' => (string) $metadata['label'],
                'type' => (string) $metadata['type'],
                'id' => (int) $metadata['id'],
                'url' => $this->urlResolver->urlForChunk($chunk),
            ];
        }

        return array_values($sources);
    }
}
