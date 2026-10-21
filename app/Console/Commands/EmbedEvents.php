<?php

namespace App\Console\Commands;

use App\Exceptions\GeminiException;
use App\Models\Event;
use App\Services\EventEmbeddingService;
use Illuminate\Console\Command;

/**
 * php artisan events:embed        → initiatives without a vector
 * php artisan events:embed --all  → re-generate every vector
 */
class EmbedEvents extends Command
{
    protected $signature = 'events:embed {--all : Re-generate embeddings for every initiative}';

    protected $description = 'Generate Gemini embeddings for initiatives (used by Smart AI Matching)';

    public function handle(EventEmbeddingService $embeddings): int
    {
        $all = (bool) $this->option('all');

        $pending = Event::query()->when(! $all, fn ($q) => $q->whereNull('embedded_at'))->count();

        if ($pending === 0) {
            $this->info('All initiatives already have embeddings. Use --all to regenerate.');

            return self::SUCCESS;
        }

        $this->info("Embedding {$pending} initiative(s) with Gemini...");

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
        $this->info('Done! Smart Match can now use these initiatives.');

        return self::SUCCESS;
    }
}