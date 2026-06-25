<?php

namespace App\Services\Rag;

use App\Models\Event;
use App\Models\MediaRelease;
use App\Models\RagChunk;
use App\Models\User;
use Illuminate\Support\Collection;
use OpenAI\Laravel\Facades\OpenAI;

class RagIndexerService
{
    public function __construct(
        private readonly RagSourceUrlResolver $urlResolver,
    ) {}
    public function reindexAll(): int
    {
        RagChunk::query()->delete();

        $count = 0;
        $count += $this->indexEvents();
        $count += $this->indexMediaReleases();
        $count += $this->indexUsers();

        return $count;
    }

    public function indexEvent(Event $event): int
    {
        $this->deleteSource('event', $event->id);

        $event->loadMissing(['creator']);
        $event->loadCount('mediaReleases');

        $document = $this->buildEventDocument($event);
        $metadata = [
            'label' => $event->title,
            'type' => 'Event',
            'id' => $event->id,
            'url' => $this->urlResolver->urlFor('event', $event->id),
        ];

        return $this->storeChunks('event', $event->id, $document, $metadata);
    }

    public function indexMediaRelease(MediaRelease $release): int
    {
        $this->deleteSource('media_release', $release->id);

        $release->loadMissing(['event']);

        $document = $this->buildMediaReleaseDocument($release);
        $metadata = [
            'label' => $release->full_name.' — '.($release->event?->title ?? 'Unknown event'),
            'type' => 'Media Release',
            'id' => $release->id,
            'url' => $this->urlResolver->urlFor('media_release', $release->id),
        ];

        return $this->storeChunks('media_release', $release->id, $document, $metadata);
    }

    public function indexUser(User $user): int
    {
        $this->deleteSource('user', $user->id);

        $document = $this->buildUserDocument($user);
        $metadata = [
            'label' => $user->name,
            'type' => 'User',
            'id' => $user->id,
            'url' => $this->urlResolver->urlFor('user', $user->id),
        ];

        return $this->storeChunks('user', $user->id, $document, $metadata);
    }

    public function deleteSource(string $sourceType, int $sourceId): void
    {
        RagChunk::query()
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->delete();
    }

    private function indexEvents(): int
    {
        $count = 0;

        Event::query()
            ->with(['creator'])
            ->withCount('mediaReleases')
            ->orderBy('id')
            ->chunkById(50, function (Collection $events) use (&$count) {
                foreach ($events as $event) {
                    $count += $this->indexEvent($event);
                }
            });

        return $count;
    }

    private function indexMediaReleases(): int
    {
        $count = 0;

        MediaRelease::query()
            ->with(['event'])
            ->orderBy('id')
            ->chunkById(50, function (Collection $releases) use (&$count) {
                foreach ($releases as $release) {
                    $count += $this->indexMediaRelease($release);
                }
            });

        return $count;
    }

    private function indexUsers(): int
    {
        $count = 0;

        User::query()
            ->orderBy('id')
            ->chunkById(50, function (Collection $users) use (&$count) {
                foreach ($users as $user) {
                    $count += $this->indexUser($user);
                }
            });

        return $count;
    }

    private function buildEventDocument(Event $event): string
    {
        $url = $this->urlResolver->urlFor('event', $event->id);

        $lines = [
            'Type: Event',
            'Title: '.$event->title,
            'Description: '.($event->description ?: 'No description provided.'),
            'Location: '.($event->location ?: 'Not specified'),
            'Status: '.$event->status,
            'Display status: '.$event->display_status,
            'Event date: '.$event->event_date_display,
            'Created by: '.($event->creator?->name ?? 'Unknown'),
            'Media release submissions: '.$event->media_releases_count,
            'Detail URL: '.$url,
        ];

        return implode("\n", $lines);
    }

    private function buildMediaReleaseDocument(MediaRelease $release): string
    {
        $url = $this->urlResolver->urlFor('media_release', $release->id);

        $lines = [
            'Type: Media Release Submission',
            'Submitter name: '.$release->full_name,
            'Event: '.($release->event?->title ?? 'Unknown event'),
            'Affiliation: '.($release->affiliation ?? 'Not specified'),
            'Connection to Chapman: '.($release->affiliation_details ?? 'Not provided'),
            'Submitted at: '.($release->submitted_at?->toDateTimeString() ?? 'Unknown'),
            'Detail URL: '.$url,
        ];

        return implode("\n", $lines);
    }

    private function buildUserDocument(User $user): string
    {
        $lines = [
            'Type: Chapman Media User',
            'Name: '.$user->name,
            'Title: '.($user->title ?? 'Not specified'),
            'Department: '.($user->department ?? 'Not specified'),
            'Role: '.($user->role === 'admin' ? 'Admin' : 'User'),
            'User directory URL: '.$this->urlResolver->urlFor('user', $user->id),
        ];

        return implode("\n", $lines);
    }

    private function storeChunks(string $sourceType, int $sourceId, string $document, array $metadata): int
    {
        $chunks = $this->splitIntoChunks($document);
        $stored = 0;

        foreach ($chunks as $index => $content) {
            $embedding = $this->createEmbedding($content);

            RagChunk::query()->create([
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'chunk_index' => $index,
                'content' => $content,
                'embedding' => $embedding,
                'metadata' => $metadata,
                'indexed_at' => now(),
            ]);

            $stored++;
        }

        return $stored;
    }

    /**
     * @return list<string>
     */
    private function splitIntoChunks(string $text): array
    {
        $chunkSize = (int) config('rag.chunk_size', 1500);
        $overlap = (int) config('rag.chunk_overlap', 200);
        $text = trim($text);

        if ($text === '') {
            return [];
        }

        if (strlen($text) <= $chunkSize) {
            return [$text];
        }

        $chunks = [];
        $start = 0;
        $length = strlen($text);

        while ($start < $length) {
            $chunk = substr($text, $start, $chunkSize);
            $chunks[] = trim($chunk);
            $start += max(1, $chunkSize - $overlap);
        }

        return array_values(array_filter($chunks));
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
}
