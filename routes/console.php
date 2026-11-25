<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
| Laravel 12 reads the schedule from here (app/Console/Kernel.php is no longer used).
| The server needs ONE cron entry that runs every minute:
|   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
*/

// Checks every minute; pays out once a day at the time the admin sets in
// Admin → Settings → Investment Returns.
Schedule::command('investments:credit-returns')->everyMinute()->withoutOverlapping(30);

Schedule::command('trades:close-expired')->everyMinute()->withoutOverlapping();

Schedule::command('currency:refresh-rates')->daily();

// Sends queued emails (alerts). cPanel can't keep a permanent worker running, so the
// worker starts every minute, sends whatever is waiting, and exits.
// Runs in the foreground: shared hosts often kill background processes started by cron,
// which also left a stale overlap lock. The lock now expires after 5 minutes.
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping(5);
