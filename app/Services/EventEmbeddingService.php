<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Builds each initiative's "profile text" and stores its Gemini vector
 * in events.embedding (pgvector).
 */
class EventEmbeddingService
{
    private const BATCH_SIZE = 50;

    public function __construct(private GeminiEmbeddingService $gemini)
    {
    }

    /** The text that represents an initiative for the AI. */
    public function profileText(Event $event): string
    {
        $event->loadMissing(['category', 'skills']);

        return implode("\n", array_filter([
            'Volunteer initiative: '.$event->title.'.',
            'Cause: '.$event->category->name.'.',
            $event->skills->isNotEmpty() ? 'Skills needed: '.$event->skills->pluck('name')->implode(', ').'.' : null,
            'Schedule: '.($event->dayType() === 'Weekends' ? 'weekend' : 'weekday').', '.$event->event_date->format('l j F Y').'.',
            'Location: '.$event->location.', '.$event->state.', Malaysia.',
            'Description: '.$event->description,
            $event->requirements ? 'Requirements: '.$event->requirements : null,
        ]));
    }

    public function embed(Event $event): void
    {
        $vector = $this->gemini->embed($this->profileText($event), GeminiEmbeddingService::TASK_DOCUMENT);

        $this->store($event, $vector);
    }

    /**
     * Used when an NPO saves an initiative: never blocks saving.
     * If Gemini fails, embedded_at becomes null so "php artisan events:embed" retries it later.
     */
    public function embedSafely(Event $event): bool
    {
        try {
            $this->embed($event);

            return true;
        } catch (Throwable $e) {
            report($e);
            DB::table('events')->where('event_id', $event->event_id)->update(['embedded_at' => null]);

            return false;
        }
    }

    /**
     * Embed every initiative without a vector (or all of them), 50 per API request.
     *
     * @param  callable(int):void|null  $progress  called with how many were just done
     */
    public function embedPending(bool $all = false, ?callable $progress = null): int
    {
        $done = 0;

        Event::query()
            ->when(! $all, fn ($q) => $q->whereNull('embedded_at'))
            ->with(['category', 'skills'])
            ->chunkById(self::BATCH_SIZE, function ($events) use (&$done, $progress) {
                $texts = $events->map(fn (Event $event) => $this->profileText($event))->all();
                $vectors = $this->gemini->embedMany($texts, GeminiEmbeddingService::TASK_DOCUMENT);

                foreach ($events->values() as $i => $event) {
                    $this->store($event, $vectors[$i]);
                }

                $done += $events->count();

                if ($progress) {
                    $progress($events->count());
                }
            }, 'event_id');

        return $done;
    }

    private function store(Event $event, array $vector): void
    {
        DB::update(
            'UPDATE events SET embedding = ?::vector, embedded_at = ? WHERE event_id = ?',
            [GeminiEmbeddingService::toVector($vector), now(), $event->event_id]
        );
    }
}