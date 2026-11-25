<?php

namespace App\Services;

use App\Exceptions\InsufficientBalanceException;
use App\Mail\AccountTransactionMail;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

/**
 * The only place a user's balance should change.
 *
 * Every credit/debit locks the user's row, updates the balance, writes a ledger row
 * to `transactions`, and (unless told not to) sends a credit/debit alert email.
 * The email is sent only after the database transaction commits, so a mail failure
 * can never undo or block a balance change.
 */
class BalanceService
{
    public function credit(User $user, float $amount, string $category, string $description, ?Model $reference = null, bool $notify = true): Transaction
    {
        return $this->apply($user, Transaction::CREDIT, $amount, $category, $description, $reference, $notify);
    }

    /**
     * @throws InsufficientBalanceException when the user's balance is lower than $amount
     */
    public function debit(User $user, float $amount, string $category, string $description, ?Model $reference = null, bool $notify = true): Transaction
    {
        return $this->apply($user, Transaction::DEBIT, $amount, $category, $description, $reference, $notify);
    }

    private function apply(User $user, string $type, float $amount, string $category, string $description, ?Model $reference, bool $notify): Transaction
    {
        $amount = round($amount, 2);

        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than zero.');
        }

        return DB::transaction(function () use ($user, $type, $amount, $category, $description, $reference, $notify) {
            $locked = User::whereKey($user->getKey())->lockForUpdate()->firstOrFail();

            $before = round((float) $locked->balance, 2);

            if ($type === Transaction::DEBIT && $before < $amount) {
                throw new InsufficientBalanceException();
            }

            $after = $type === Transaction::CREDIT ? $before + $amount : $before - $amount;
            $after = round($after, 2);

            $locked->balance = $after;
            $locked->save();

            // Keep the caller's model in step, so a later $user->save() can't write back a stale balance.
            $user->balance = $after;
            $user->syncOriginalAttribute('balance');

            $transaction = Transaction::create([
                'user_id' => $locked->id,
                'type' => $type,
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'description' => $description,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
            ]);

            if ($notify) {
                DB::afterCommit(fn () => $this->sendAlert($transaction));
            }

            return $transaction;
        });
    }

    /**
     * Sent immediately (not queued): shared hosting can't reliably run a queue worker.
     * This runs after the balance change is committed, so a mail failure only gets logged.
     */
    private function sendAlert(Transaction $transaction): void
    {
        try {
            $transaction->loadMissing('user');
            Mail::to($transaction->user->email)->send(new AccountTransactionMail($transaction));
        } catch (\Throwable $e) {
            Log::warning('Failed to send transaction alert email', [
                'transaction_id' => $transaction->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
