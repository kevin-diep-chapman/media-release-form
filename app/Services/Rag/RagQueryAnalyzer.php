<?php

namespace App\Services\Rag;

use App\Models\Event;
use App\Models\MediaRelease;
use App\Models\RagChunk;
use App\Models\User;
use Illuminate\Support\Collection;

class RagQueryAnalyzer
{
    /**
     * @return array{source_type: string, intent: string}|null
     */
    public function aggregateQuery(string $message): ?array
    {
        if (! $this->isAggregateQuestion($message)) {
            return null;
        }

        $sourceType = $this->detectSourceType($message);

        if ($sourceType === null) {
            return null;
        }

        $intent = $this->isCountQuestion($message) ? 'count' : 'list';

        return [
            'source_type' => $sourceType,
            'intent' => $intent,
        ];
    }

    public function isAggregateQuestion(string $message): bool
    {
        return (bool) preg_match(
            '/\b(how many|how much|number of|count of|total (number of)?|list( all)?|show( me)? all|all the)\b/i',
            $message
        );
    }

    public function isCountQuestion(string $message): bool
    {
        return (bool) preg_match(
            '/\b(how many|how much|number of|count of|total (number of)?)\b/i',
            $message
        );
    }

    public function detectSourceType(string $message): ?string
    {
        if (preg_match('/\b(events?)\b/i', $message)) {
            return 'event';
        }

        if (preg_match('/\b(submissions?|media releases?|release forms?|form submissions?)\b/i', $message)) {
            return 'media_release';
        }

        if (preg_match('/\b(users?|staff|people|accounts?)\b/i', $message)) {
            return 'user';
        }

        return null;
    }
}
