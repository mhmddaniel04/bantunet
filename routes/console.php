<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
|
| Run locally with:   php artisan schedule:work   (keep the terminal open)
| On a server, a cron job runs "php artisan schedule:run" every minute.
|
*/

// Close initiatives whose date has passed.
Schedule::command('events:complete-past')->dailyAt('00:10');

// Retry AI embeddings that failed while an NPO was saving (e.g. Gemini was down).
Schedule::command('events:embed')->hourly()->withoutOverlapping();