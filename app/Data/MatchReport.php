<?php

namespace App\Data;

/**
 * Everything the results page needs about one Smart Match search.
 */
final readonly class MatchReport
{
    /**
     * @param  MatchResult[]  $results
     * @param  array          $weights  The weights that were used
     */
    public function __construct(
        public array $results,
        public string $queryText,
        public bool $usedAi,
        public int $responseMs,
        public array $weights,
    ) {
    }

    public function isEmpty(): bool
    {
        return $this->results === [];
    }
}