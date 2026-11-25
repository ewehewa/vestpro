<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\InvestmentPayout;
use App\Services\InvestmentReturnService;
use App\Services\ReturnSettings;
use Illuminate\Http\Request;

/** Admin → Settings → Investment Returns: when daily returns are paid. */
class ReturnSettingsController extends Controller
{
    public function __construct(private ReturnSettings $settings)
    {
    }

    public function edit()
    {
        $timezone = $this->settings->timezone();
        $todayStart = $this->settings->now()->startOfDay()->utc();
        $schedulerLastSeen = $this->settings->schedulerLastSeen();

        return view('dashboard.admin.return_settings', [
            'enabled' => $this->settings->enabled(),
            'time' => $this->settings->time(),
            'timezone' => $timezone,
            'timezones' => \DateTimeZone::listIdentifiers(),
            'lastRun' => $this->settings->lastRun(),
            'schedulerLastSeen' => $schedulerLastSeen,
            // The scheduler checks in every minute; allow a little slack before warning
            'cronRunning' => $schedulerLastSeen && $schedulerLastSeen->gt(now()->subMinutes(3)),
            'nextRun' => $this->nextRun(),
            'activeCount' => Investment::active()->count(),
            'paidTodayCount' => InvestmentPayout::where('created_at', '>=', $todayStart)->count(),
            'paidTodayAmount' => (float) InvestmentPayout::where('created_at', '>=', $todayStart)->sum('amount'),
            // cPanel's Cron Jobs form takes the timing separately, so this is the command part only
            'cronCommand' => 'cd ' . base_path() . ' && /opt/alt/php84/usr/bin/php artisan schedule:run >> /dev/null 2>&1',
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'time' => ['required', 'date_format:H:i'],
            'timezone' => ['required', 'timezone:all'],
        ]);

        $this->settings->update(
            (bool) ($validated['enabled'] ?? false),
            $validated['time'],
            $validated['timezone'],
        );

        return redirect()->route('admin.settings.returns')->with('success', 'Investment return settings saved.');
    }

    public function runNow(InvestmentReturnService $returns)
    {
        $stats = $returns->run();
        $this->settings->markRun($stats, 'manual (admin)');

        $message = sprintf(
            'Payout run complete: %d daily returns and %d completed plans paid ($%s) to %d users.',
            $stats['payouts'], $stats['completed'], number_format($stats['amount'], 2), $stats['users'],
        );

        if ($stats['errors'] > 0) {
            return redirect()->route('admin.settings.returns')
                ->with('error', $message . " {$stats['errors']} investments failed; check storage/logs/laravel.log.");
        }

        return redirect()->route('admin.settings.returns')->with('success', $message);
    }

    /** When the next scheduled payout will happen, in the admin's timezone. */
    private function nextRun(): ?\Illuminate\Support\Carbon
    {
        if (! $this->settings->enabled()) {
            return null;
        }

        $now = $this->settings->now();
        [$hour, $minute] = array_map('intval', explode(':', $this->settings->time()));
        $next = $now->copy()->setTime($hour, $minute);

        if (! $this->settings->isDue() && $next->lte($now)) {
            $next->addDay();
        }

        return $next;
    }
}
