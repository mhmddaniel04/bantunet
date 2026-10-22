<?php

namespace App\Services;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Turns an applicant's "skills & experience" into a Gemini vector,
 * so NPOs can rank applicants by AI fit.
 * Applications without a meaningful description are skipped (no fit score).
 */
class SubmissionEmbeddingService
{
    private const BATCH_SIZE = 50;
    private const MIN_SUMMARY_LENGTH = 15;

    public function __construct(private GeminiEmbeddingService $gemini)
    {
    }

    /** The text that represents an applicant for the AI. Uses no name, age or gender. */
    public function profileText(Submission $submission): string
    {
        $availability = config('bantunet.availability')[$submission->availability] ?? $submission->availability;

        return implode("\n", [
            'Volunteer applicant.',
            'Skills and experience: '.trim((string) $submission->skills_summary),
            'Availability: '.$availability.'.',
        ]);
    }

    public function hasEnoughText(Submission $submission): bool
    {
        return mb_strlen(trim((string) $submission->skills_summary)) >= self::MIN_SUMMARY_LENGTH;
    }

    /** Used right after a volunteer applies: never blocks the application. */
    public function embedSafely(Submission $submission): void
    {
        if (! $this->hasEnoughText($submission)) {
            return;
        }

        try {
            $vector = $this->gemini->embed($this->profileText($submission), GeminiEmbeddingService::TASK_QUERY);
            $this->store($submission, $vector);
        } catch (Throwable $e) {
            report($e);   // "php artisan submissions:embed" retries later
        }
    }

    /** Applications that can be embedded (and still need it, unless $all). */
    public function pendingQuery(bool $all = false): Builder
    {
        return Submission::query()
            ->whereRaw('char_length(trim(skills_summary)) >= ?', [self::MIN_SUMMARY_LENGTH])
            ->when(! $all, fn (Builder $q) => $q->whereNull('embedded_at'));
    }

    /**
     * @param  callable(int):void|null  $progress
     */
    public function embedPending(bool $all = false, ?callable $progress = null): int
    {
        $done = 0;

        $this->pendingQuery($all)->chunkById(self::BATCH_SIZE, function ($submissions) use (&$done, $progress) {
            $texts = $submissions->map(fn (Submission $s) => $this->profileText($s))->all();
            $vectors = $this->gemini->embedMany($texts, GeminiEmbeddingService::TASK_QUERY);

            foreach ($submissions->values() as $i => $submission) {
                $this->store($submission, $vectors[$i]);
            }

            $done += $submissions->count();

            if ($progress) {
                $progress($submissions->count());
            }
        }, 'submission_id');

        return $done;
    }

    private function store(Submission $submission, array $vector): void
    {
        DB::update(
            'UPDATE submissions SET embedding = ?::vector, embedded_at = ? WHERE submission_id = ?',
            [GeminiEmbeddingService::toVector($vector), now(), $submission->submission_id]
        );
    }
}