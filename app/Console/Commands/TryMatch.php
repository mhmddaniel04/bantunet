<?php

namespace App\Console\Commands;

use App\Models\Skill;
use App\Services\MatchingService;
use Illuminate\Console\Command;

/**
 * Quick way to test matching scenarios (useful for Chapter 4):
 *
 * php artisan match:try "I love teaching kids" --availability=Weekends --skills=teaching-tutoring
 */
class TryMatch extends Command
{
    protected $signature = 'match:try
                            {about : What the volunteer writes about themselves}
                            {--availability=Any : Weekdays, Weekends or Any}
                            {--state= : e.g. Selangor}
                            {--skills=* : Skill slugs, e.g. --skills=teaching-tutoring --skills=driving}';

    protected $description = 'Run Smart Match from the terminal and show the ranking (not logged)';

    public function handle(MatchingService $matching): int
    {
        $report = $matching->recommend([
            'skill_ids'    => Skill::whereIn('slug', $this->option('skills'))->pluck('skill_id')->all(),
            'category_id'  => null,
            'availability' => $this->option('availability'),
            'state'        => $this->option('state') ?: null,
            'about'        => $this->argument('about'),
        ], log: false);

        $this->line('<comment>Query text sent to Gemini:</comment>');
        $this->line($report->queryText);
        $this->newLine();

        if (! $report->usedAi) {
            $this->warn('Gemini unavailable: ranking uses rules only.');
        }

        $this->table(
            ['#', 'Initiative', 'Semantic', 'Final'],
            collect($report->results)->map(fn ($r, $i) => [
                $i + 1,
                $r->event->title,
                $r->semantic === null ? '—' : number_format($r->semantic * 100, 1).'%',
                $r->percent().'%',
            ])->all()
        );

        $this->info("Response time: {$report->responseMs} ms");

        return self::SUCCESS;
    }
}