<?php

namespace App\Services;

use App\Data\MatchReport;
use App\Data\MatchResult;
use App\Exceptions\GeminiException;
use App\Models\Category;
use App\Models\Event;
use App\Models\MatchLog;
use App\Models\Skill;
use Illuminate\Support\Collection;

/**
 * Smart AI Matching: ranks open initiatives for a volunteer.
 *
 *  1. Volunteer answers → profile text → Gemini query vector
 *  2. PostgreSQL/pgvector finds the 50 nearest initiatives by cosine distance
 *  3. Each one gets a hybrid score (semantic + skills + cause + availability + state)
 *  4. Top results are returned with reasons, and the search is logged anonymously
 */
class MatchingService
{
    private const CANDIDATE_POOL = 50;

    public function __construct(private GeminiEmbeddingService $gemini)
    {
    }

    /**
     * @param  array{skill_ids: int[], category_id: ?int, availability: string, state: ?string, about: string}  $profile
     */
    public function recommend(array $profile, bool $log = true): MatchReport
    {
        $startedAt = hrtime(true);

        $skills = Skill::whereIn('skill_id', $profile['skill_ids'])->orderBy('name')->get();
        $category = $profile['category_id'] ? Category::find($profile['category_id']) : null;
        $queryText = $this->profileText($profile, $skills, $category);

        // 1. Ask Gemini for the volunteer's vector. If it fails, fall back to rules only.
        try {
            $vector = GeminiEmbeddingService::toVector($this->gemini->embedQuery($queryText));
        } catch (GeminiException $e) {
            report($e);
            $vector = null;
        }

        // 2–3. Nearest initiatives, scored and ranked.
        $weights = config('bantunet.matching.weights');

        $results = $this->candidates($vector)
            ->reject(fn (Event $event) => $event->isFull())
            ->map(fn (Event $event) => $this->score($event, $profile, $skills, $category, $weights, $vector !== null))
            ->sortByDesc(fn (MatchResult $result) => $result->score)
            ->take(config('bantunet.matching.limit'))
            ->values()
            ->all();

        $responseMs = (int) ((hrtime(true) - $startedAt) / 1_000_000);

        // 4. Anonymous log for evaluation (Chapter 4).
        if ($log) {
            $this->log($profile, $queryText, $results, $responseMs);
        }

        return new MatchReport($results, $queryText, $vector !== null, $responseMs, $weights);
    }

    /** The text that represents the volunteer for the AI (mirrors the initiative profile). */
    public function profileText(array $profile, Collection $skills, ?Category $category): string
    {
        return implode("\n", array_filter([
            'Volunteer looking for a volunteer initiative.',
            $skills->isNotEmpty() ? 'Skills: '.$skills->pluck('name')->implode(', ').'.' : null,
            $category ? 'Cause I care about: '.$category->name.' — '.$category->description : null,
            'Availability: '.(config('bantunet.availability')[$profile['availability']] ?? $profile['availability']).'.',
            $profile['state'] ? 'Preferred location: '.$profile['state'].', Malaysia.' : null,
            'About me: '.$profile['about'],
        ]));
    }

    /* ------------------------------------------------------------------ */

    /** Open initiatives, nearest first by cosine distance (<=>) when AI is available. */
    private function candidates(?string $vector): Collection
    {
        $query = Event::query()
            ->openForApplications()
            ->select('events.*');

        if ($vector) {
            $query->whereNotNull('embedding')
                ->selectRaw('1 - (embedding <=> ?::vector) AS semantic_score', [$vector])
                ->orderByRaw('embedding <=> ?::vector', [$vector]);
        } else {
            $query->orderBy('event_date');
        }

        return $query
            ->with(['category', 'organization', 'skills'])
            ->withCount('approvedSubmissions')
            ->limit(self::CANDIDATE_POOL)
            ->get();
    }

    private function score(Event $event, array $profile, Collection $skills, ?Category $category, array $weights, bool $usedAi): MatchResult
    {
        $matchedSkills = $event->skills->whereIn('skill_id', $skills->pluck('skill_id'));

        // Each part is 0–1, or null when the volunteer gave no preference.
        $parts = [
            'semantic'     => $usedAi ? max(0.0, (float) $event->semantic_score) : null,
            'skills'       => $skills->isEmpty() || $event->skills->isEmpty()
                                ? null
                                : $matchedSkills->count() / $event->skills->count(),
            'category'     => $category ? (float) ($event->category_id === $category->category_id) : null,
            'availability' => $profile['availability'] === 'Any'
                                ? null
                                : (float) ($profile['availability'] === $event->dayType()),
            'state'        => $profile['state'] ? (float) ($event->state === $profile['state']) : null,
        ];

        // Weighted average of the answered parts only.
        $total = 0.0;
        $weightSum = 0.0;

        foreach ($parts as $name => $value) {
            if ($value !== null) {
                $total += $weights[$name] * $value;
                $weightSum += $weights[$name];
            }
        }

        return new MatchResult(
            event: $event,
            score: $weightSum > 0 ? $total / $weightSum : 0.0,
            semantic: $parts['semantic'],
            breakdown: $parts,
            reasons: $this->reasons($event, $parts, $matchedSkills),
        );
    }

    private function reasons(Event $event, array $parts, Collection $matchedSkills): array
    {
        $thresholds = config('bantunet.matching.reasons');
        $reasons = [];

        if ($parts['semantic'] !== null && $parts['semantic'] >= $thresholds['strong']) {
            $reasons[] = 'Closely matches what you described';
        } elseif ($parts['semantic'] !== null && $parts['semantic'] >= $thresholds['related']) {
            $reasons[] = 'Related to what you described';
        }

        if ($matchedSkills->isNotEmpty()) {
            $reasons[] = 'Uses your skills: '.$matchedSkills->pluck('name')->implode(', ');
        }

        if ($parts['category'] === 1.0) {
            $reasons[] = 'In your chosen cause: '.$event->category->name;
        }

        if ($parts['availability'] === 1.0) {
            $reasons[] = 'Held on a '.($event->dayType() === 'Weekends' ? 'weekend' : 'weekday').' — fits your availability';
        }

        if ($parts['state'] === 1.0) {
            $reasons[] = 'Located in '.$event->state;
        }

        return $reasons;
    }

    /** @param MatchResult[] $results */
    private function log(array $profile, string $queryText, array $results, int $responseMs): void
    {
        rescue(fn () => MatchLog::create([
            'query_text'   => $queryText,
            'skill_ids'    => $profile['skill_ids'],
            'category_id'  => $profile['category_id'],
            'availability' => $profile['availability'],
            'state'        => $profile['state'],
            'results'      => array_map(fn (MatchResult $r) => [
                'event_id' => $r->event->event_id,
                'semantic' => $r->semantic === null ? null : round($r->semantic, 4),
                'final'    => round($r->score, 4),
            ], $results),
            'response_ms'  => $responseMs,
        ]));
    }
}