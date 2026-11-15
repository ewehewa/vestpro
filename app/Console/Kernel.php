<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Register your command class here
        \App\Console\Commands\CloseExpiredTrades::class,
        \App\Console\Commands\RefreshExchangeRates::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Run the expired trades command every minute
        $schedule->command('trades:close-expired')->everyMinute();

        // Refresh cached USD exchange rates once a day
        $schedule->command('currency:refresh-rates')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
