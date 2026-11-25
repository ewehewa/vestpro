<?php

namespace App\Console\Commands;

use App\Services\InvestmentReturnService;
use App\Services\ReturnSettings;
use Illuminate\Console\Command;

class CreditInvestmentReturns extends Command
{
    protected $signature = 'investments:credit-returns {--now : Run immediately, ignoring the payout time set by the admin}';

    protected $description = 'Credit daily investment returns at the payout time set in Admin → Settings → Investment Returns';

    public function handle(ReturnSettings $settings, InvestmentReturnService $returns): int
    {
        // Runs every minute via the scheduler; record that the cron is alive.
        $settings->heartbeat();

        if (! $this->option('now') && ! $settings->isDue()) {
            return self::SUCCESS;
        }

        $stats = $returns->run();
        $settings->markRun($stats, $this->option('now') ? 'manual (console)' : 'schedule');

        $this->info(sprintf(
            'Paid %d daily returns and %d completed plans ($%s) to %d users. Errors: %d.',
            $stats['payouts'], $stats['completed'], number_format($stats['amount'], 2), $stats['users'], $stats['errors'],
        ));

        return $stats['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
