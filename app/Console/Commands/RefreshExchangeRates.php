<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class RefreshExchangeRates extends Command
{
    protected $signature = 'currency:refresh-rates';
    protected $description = 'Fetch the latest USD exchange rates and refresh the cache';

    public function handle(CurrencyService $currencyService)
    {
        $rates = $currencyService->fetchRates();

        $this->info('Exchange rates refreshed: ' . count($rates) . ' currencies cached.');
    }
}
