<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    protected const RATES_CACHE_KEY = 'exchange_rates';
    protected const LAST_GOOD_CACHE_KEY = 'exchange_rates_last_good';
    protected const RATES_ENDPOINT = 'https://open.er-api.com/v6/latest/USD';

    protected ?array $ratesCacheForRequest = null;

    public function getRates(): array
    {
        if ($this->ratesCacheForRequest !== null) {
            return $this->ratesCacheForRequest;
        }

        return $this->ratesCacheForRequest = Cache::remember(
            self::RATES_CACHE_KEY,
            now()->addDay(),
            fn () => $this->fetchRates()
        );
    }

    public function fetchRates(): array
    {
        try {
            $response = Http::timeout(10)->get(self::RATES_ENDPOINT);

            if ($response->successful() && is_array($response->json('rates'))) {
                $rates = $response->json('rates');
                Cache::forever(self::LAST_GOOD_CACHE_KEY, $rates);
                Cache::put(self::RATES_CACHE_KEY, $rates, now()->addDay());

                return $rates;
            }
        } catch (\Throwable $e) {
            Log::warning('CurrencyService: failed to fetch exchange rates.', ['error' => $e->getMessage()]);
        }

        return Cache::get(self::LAST_GOOD_CACHE_KEY, ['USD' => 1.0]);
    }

    public function convert(float $usdAmount, string $toCurrency): float
    {
        $rates = $this->getRates();
        $rate = $rates[strtoupper($toCurrency)] ?? 1.0;

        return $usdAmount * $rate;
    }

    public function format(?float $usdAmount, ?string $currency = null): string
    {
        $usdAmount ??= 0.0;
        $currency = strtoupper($currency ?? Auth::user()?->preferred_currency ?? 'USD');

        $converted = $this->convert($usdAmount, $currency);
        $symbol = $this->symbolFor($currency);

        return $symbol !== null
            ? $symbol . number_format($converted, 2)
            : $currency . ' ' . number_format($converted, 2);
    }

    public function supportedCurrencies(): array
    {
        $codes = array_keys($this->getRates());
        sort($codes);

        return $codes;
    }

    public function symbolFor(string $code): ?string
    {
        return config('currencies.symbols.' . strtoupper($code));
    }
}
