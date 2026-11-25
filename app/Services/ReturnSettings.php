<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Admin-editable schedule for daily investment returns (Admin → Settings → Investment Returns).
 *
 * The server cron runs `schedule:run` every minute; the `investments:credit-returns`
 * command then asks isDue() whether the admin's payout time has been reached today.
 */
class ReturnSettings
{
    public const DEFAULT_TIME = '00:05';
    public const DEFAULT_TIMEZONE = 'UTC';

    public function enabled(): bool
    {
        return Setting::get('returns.enabled', '1') === '1';
    }

    /** Payout time as "HH:MM" (24-hour) in timezone(). */
    public function time(): string
    {
        return Setting::get('returns.time', self::DEFAULT_TIME);
    }

    public function timezone(): string
    {
        return Setting::get('returns.timezone', self::DEFAULT_TIMEZONE);
    }

    public function update(bool $enabled, string $time, string $timezone): void
    {
        Setting::set('returns.enabled', $enabled ? '1' : '0');
        Setting::set('returns.time', $time);
        Setting::set('returns.timezone', $timezone);
    }

    public function now(): Carbon
    {
        return now()->setTimezone($this->timezone());
    }

    /** True once per day, at or after the configured time, while payouts are enabled. */
    public function isDue(?CarbonInterface $now = null): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        $now = ($now ?? now())->copy()->setTimezone($this->timezone());

        return $now->format('H:i') >= $this->time()
            && Setting::get('returns.last_run_date') !== $now->toDateString();
    }

    public function markRun(array $stats, string $trigger, ?CarbonInterface $now = null): void
    {
        $now = ($now ?? now())->copy()->setTimezone($this->timezone());

        Setting::set('returns.last_run_date', $now->toDateString());
        Setting::setJson('returns.last_run', $stats + [
            'trigger' => $trigger,
            'ran_at' => now()->toIso8601String(),
        ]);
    }

    public function lastRun(): array
    {
        return Setting::getJson('returns.last_run');
    }

    /** Called every minute by the scheduler, so the admin page can show whether the cron is working. */
    public function heartbeat(): void
    {
        Setting::set('scheduler.last_seen_at', now()->toIso8601String());
    }

    public function schedulerLastSeen(): ?Carbon
    {
        $value = Setting::get('scheduler.last_seen_at');

        return $value ? Carbon::parse($value) : null;
    }
}
