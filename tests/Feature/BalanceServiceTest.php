<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientBalanceException;
use App\Mail\AccountTransactionMail;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private function user(float $balance = 0): User
    {
        return User::factory()->create(['username' => 'user' . uniqid(), 'balance' => $balance]);
    }

    public function test_credit_updates_balance_writes_ledger_and_sends_alert(): void
    {
        Mail::fake();
        $user = $this->user(100);

        $tx = app(BalanceService::class)->credit($user, 50.25, 'deposit', 'Deposit approved (USDT)');

        $this->assertSame(150.25, (float) $user->fresh()->balance);
        $this->assertSame(150.25, (float) $user->balance, 'caller model is kept in sync');
        $this->assertSame(100.0, $tx->balance_before);
        $this->assertSame(150.25, $tx->balance_after);
        $this->assertSame(Transaction::CREDIT, $tx->type);

        Mail::assertSent(AccountTransactionMail::class, fn ($mail) => $mail->hasTo($user->email) && $mail->transaction->is($tx));
        Mail::assertSentCount(1);
    }

    public function test_debit_with_insufficient_balance_changes_nothing(): void
    {
        Mail::fake();
        $user = $this->user(20);

        try {
            app(BalanceService::class)->debit($user, 50, 'withdrawal', 'Withdrawal');
            $this->fail('Expected InsufficientBalanceException');
        } catch (InsufficientBalanceException) {
        }

        $this->assertSame(20.0, (float) $user->fresh()->balance);
        $this->assertSame(0, Transaction::count());
        Mail::assertNothingSent();
    }

    public function test_no_alert_is_sent_when_the_surrounding_transaction_rolls_back(): void
    {
        Mail::fake();
        $user = $this->user(100);

        try {
            DB::transaction(function () use ($user) {
                app(BalanceService::class)->credit($user, 10, 'bonus', 'Bonus');
                throw new \RuntimeException('something later failed');
            });
        } catch (\RuntimeException) {
        }

        $this->assertSame(100.0, (float) $user->fresh()->balance);
        $this->assertSame(0, Transaction::count());
        Mail::assertNothingSent();
    }

    public function test_alert_email_shows_amount_and_available_balance(): void
    {
        $user = $this->user(1000);
        $tx = app(BalanceService::class)->debit($user, 250, 'withdrawal', 'Withdrawal request via USDT (pending approval)', notify: false);

        $html = (new AccountTransactionMail($tx->fresh()))->render();

        $this->assertStringContainsString('Debit Alert', $html);
        $this->assertStringContainsString('-$250.00', $html);
        $this->assertStringContainsString('Available Balance', $html);
        $this->assertStringContainsString('$750.00', $html);
        $this->assertStringContainsString($tx->referenceCode(), $html);
    }

    public function test_stale_model_save_does_not_overwrite_new_balance(): void
    {
        Mail::fake();
        $user = $this->user(500);

        app(BalanceService::class)->debit($user, 100, 'withdrawal', 'Withdrawal');
        $user->otp = null;
        $user->save();

        $this->assertSame(400.0, (float) $user->fresh()->balance);
    }
}
