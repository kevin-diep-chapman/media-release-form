<?php

namespace App\Services\Rag;

use App\Models\RagChunk;
use Illuminate\Support\Collection;
use OpenAI\Laravel\Facades\OpenAI;

class RagSearchService
{
    /**
     * @return Collection<int, RagChunk>
     */
    public function search(string $query, ?int $topK = null): Collection
    {
        $topK = $topK ?? (int) config('rag.top_k', 5);
        $queryEmbedding = $this->createEmbedding($query);

        $chunks = RagChunk::query()->get();

        if ($chunks->isEmpty()) {
            return collect();
        }

        return $chunks
            ->map(function (RagChunk $chunk) use ($queryEmbedding) {
                $score = $this->cosineSimilarity($queryEmbedding, $chunk->embedding ?? []);

                return ['chunk' => $chunk, 'score' => $score];
            })
            ->sortByDesc('score')
            ->take($topK)
            ->pluck('chunk')
            ->values();
    }

    /**
     * @return list<float>
     */
    private function createEmbedding(string $text): array
    {
        $response = OpenAI::embeddings()->create([
            'model' => config('rag.embedding_model'),
            'input' => $text,
        ]);

        return $response->embeddings[0]->embedding;
    }

    /**
     * @param  list<float>  $vectorA
     * @param  list<float>  $vectorB
     */
    private function cosineSimilarity(array $vectorA, array $vectorB): float
    {
        if ($vectorA === [] || $vectorB === [] || count($vectorA) !== count($vectorB)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($vectorA as $index => $value) {
            $other = $vectorB[$index];
            $dotProduct += $value * $other;
            $normA += $value * $value;
            $normB += $other * $other;
        }

        if ($normA === 0.0 || $normB === 0.0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
