<?php

if (! function_exists('money')) {
    function money(?float $usdAmount, ?string $currency = null): string
    {
        return app(\App\Services\CurrencyService::class)->format($usdAmount ?? 0, $currency);
    }
}

if (! function_exists('balance_money')) {
    function balance_money(?float $usdAmount, ?string $currency = null): string
    {
        $usdAmount ??= 0.0;
        $currency = strtoupper($currency ?? auth()->user()?->preferred_currency ?? 'USD');
        $formatted = money($usdAmount, $currency);

        if ($currency === 'USD') {
            return $formatted;
        }

        return $formatted . ' <span style="font-size:0.4em; font-weight:400; opacity:0.6; white-space:nowrap;">(≈ $' . number_format($usdAmount, 2) . ' USD)</span>';
    }
}
