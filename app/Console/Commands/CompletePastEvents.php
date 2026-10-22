<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

/**
 * Published initiatives whose date has passed become "Completed".
 * Runs daily via the scheduler (routes/console.php).
 */
class CompletePastEvents extends Command
{
    protected $signature = 'events:complete-past';

    protected $description = 'Mark published initiatives whose date has passed as Completed';

    public function handle(): int
    {
        $count = Event::where('status', Event::STATUS_PUBLISHED)
            ->whereDate('event_date', '<', today())
            ->update(['status' => Event::STATUS_COMPLETED, 'updated_at' => now()]);

        $this->info("{$count} initiative(s) marked as completed.");

        return self::SUCCESS;
    }
}