<?php

namespace App\Services;

use App\Mail\DailyReturnsMail;
use App\Models\Investment;
use App\Models\InvestmentPayout;
use App\Models\Profit;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Credits daily returns on active investments.
 *
 * Day N of an investment is payable from the Nth calendar day after it started (in the
 * admin's payout timezone). Missed days are caught up on the next run, each day can only
 * be paid once, and when the last day is paid the capital is returned and the investment
 * is marked completed. Each user gets one summary email per run.
 */
class InvestmentReturnService
{
    public function __construct(
        private BalanceService $balance,
        private ReturnSettings $settings,
    ) {
    }

    /**
     * @return array{investments: int, payouts: int, amount: float, completed: int, users: int, errors: int}
     */
    public function run(?CarbonInterface $now = null): array
    {
        $timezone = $this->settings->timezone();
        $today = ($now ?? now())->copy()->setTimezone($timezone)->startOfDay();

        $stats = ['investments' => 0, 'payouts' => 0, 'amount' => 0.0, 'completed' => 0, 'users' => 0, 'errors' => 0];
        $itemsByUser = [];

        Investment::active()
            ->whereNotNull('starts_at')
            ->whereNotNull('duration_days')
            ->chunkById(100, function ($investments) use ($today, $timezone, &$stats, &$itemsByUser) {
                foreach ($investments as $investment) {
                    try {
                        $items = $this->processInvestment($investment->id, $today, $timezone);
                    } catch (\Throwable $e) {
                        $stats['errors']++;
                        Log::error('Investment return failed', ['investment_id' => $investment->id, 'message' => $e->getMessage()]);
                        continue;
                    }

                    if (empty($items)) {
                        continue;
                    }

                    $stats['investments']++;
                    foreach ($items as $item) {
                        $stats['amount'] += $item['amount'];
                        $item['kind'] === 'principal' ? $stats['completed']++ : $stats['payouts']++;
                    }

                    $itemsByUser[$investment->user_id] = array_merge($itemsByUser[$investment->user_id] ?? [], $items);
                }
            });

        foreach ($itemsByUser as $userId => $items) {
            $this->notify($userId, $items);
            $stats['users']++;
        }

        $stats['amount'] = round($stats['amount'], 2);

        return $stats;
    }

    /**
     * Pay every due day of one investment inside a single locked transaction.
     *
     * @return array<int, array{plan: string, day: int, days: int, amount: float, kind: string}>
     */
    private function processInvestment(int $investmentId, CarbonInterface $today, string $timezone): array
    {
        return DB::transaction(function () use ($investmentId, $today, $timezone) {
            $investment = Investment::with(['plan', 'user'])->lockForUpdate()->find($investmentId);

            if (! $investment || $investment->status !== Investment::STATUS_ACTIVE || ! $investment->user) {
                return [];
            }

            $planName = $investment->plan->name ?? 'Investment';
            $days = (int) $investment->duration_days;
            $start = $investment->starts_at->copy()->setTimezone($timezone)->startOfDay();
            $items = [];

            while ($investment->days_paid < $days) {
                $day = $investment->days_paid + 1;
                $payoutDate = $start->copy()->addDays($day);

                if ($payoutDate->gt($today)) {
                    break;
                }

                $amount = round((float) $investment->daily_return, 2);
                $transaction = null;

                if ($amount > 0) {
                    $transaction = $this->balance->credit(
                        $investment->user,
                        $amount,
                        'investment_return',
                        "Daily return: {$planName}, day {$day} of {$days}",
                        $investment,
                        notify: false,
                    );

                    Profit::create(['user_id' => $investment->user_id, 'amount' => $amount]);
                }

                InvestmentPayout::create([
                    'investment_id' => $investment->id,
                    'user_id' => $investment->user_id,
                    'day_number' => $day,
                    'payout_date' => $payoutDate->toDateString(),
                    'amount' => $amount,
                    'transaction_id' => $transaction?->id,
                ]);

                $investment->days_paid = $day;
                $investment->total_earned = round($investment->total_earned + $amount, 2);
                $investment->last_paid_at = now();

                $items[] = ['plan' => $planName, 'day' => $day, 'days' => $days, 'amount' => $amount, 'kind' => 'return'];
            }

            if ($investment->days_paid >= $days) {
                $this->balance->credit(
                    $investment->user,
                    (float) $investment->amount,
                    'principal_return',
                    "Capital returned: {$planName} completed",
                    $investment,
                    notify: false,
                );

                $investment->status = Investment::STATUS_COMPLETED;
                $investment->completed_at = now();

                $items[] = ['plan' => $planName, 'day' => $days, 'days' => $days, 'amount' => (float) $investment->amount, 'kind' => 'principal'];
            }

            $investment->save();

            return $items;
        });
    }

    private function notify(int $userId, array $items): void
    {
        try {
            $user = User::find($userId);
            if (! $user) {
                return;
            }

            $total = round(array_sum(array_column($items, 'amount')), 2);

            // Sent directly from the payout run (no queue worker needed on shared hosting)
            Mail::to($user->email)->send(new DailyReturnsMail($user, $items, $total, (float) $user->balance));
        } catch (\Throwable $e) {
            Log::warning('Failed to send daily returns email', ['user_id' => $userId, 'message' => $e->getMessage()]);
        }
    }
}
