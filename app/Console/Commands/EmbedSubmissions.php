<?php

namespace App\Console\Commands;

use App\Exceptions\GeminiException;
use App\Services\SubmissionEmbeddingService;
use Illuminate\Console\Command;

/**
 * php artisan submissions:embed        → applications without a vector
 * php artisan submissions:embed --all  → re-generate every vector
 */
class EmbedSubmissions extends Command
{
    protected $signature = 'submissions:embed {--all : Re-generate embeddings for every application}';

    protected $description = 'Generate Gemini embeddings for applications (used for AI fit ranking)';

    public function handle(SubmissionEmbeddingService $embeddings): int
    {
        $all = (bool) $this->option('all');
        $pending = $embeddings->pendingQuery($all)->count();

        if ($pending === 0) {
            $this->info('All applications with a skills description already have embeddings.');

            return self::SUCCESS;
        }

        $this->info("Embedding {$pending} application(s) with Gemini...");

        $bar = $this->output->createProgressBar($pending);
        $bar->start();

        try {
            $embeddings->embedPending($all, fn (int $done) => $bar->advance($done));
        } catch (GeminiException $e) {
            $bar->clear();
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Done! NPOs can now sort applicants by AI fit.');

        return self::SUCCESS;
    }
}