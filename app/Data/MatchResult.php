<?php

namespace App\Data;

use App\Models\Event;

/**
 * One recommended initiative and why it was recommended.
 */
final readonly class MatchResult
{
    /**
     * @param  float       $score      Final hybrid score, 0–1
     * @param  float|null  $semantic   Cosine similarity from Gemini embeddings, 0–1 (null if AI unavailable)
     * @param  array       $breakdown  ['semantic' => .., 'skills' => .., 'category' => .., 'availability' => .., 'state' => ..]
     *                                 null = the volunteer did not answer that part
     * @param  string[]    $reasons    Human-readable explanations
     */
    public function __construct(
        public Event $event,
        public float $score,
        public ?float $semantic,
        public array $breakdown,
        public array $reasons,
    ) {
    }

    public function percent(): int
    {
        return (int) round($this->score * 100);
    }
}