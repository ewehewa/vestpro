<?php

namespace Tests\Feature;

use App\Mail\AccountTransactionMail;
use App\Models\Admin;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/** Every money-moving endpoint goes through BalanceService and sends exactly one alert. */
class BalanceAlertsWiringTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->user = User::factory()->create(['username' => 'jane', 'balance' => 1000, 'otp' => '123456']);
        $this->admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('secret')]);
    }

    private function assertOneAlert(string $type, string $category, float $balanceAfter): void
    {
        Mail::assertSentCount(1);
        Mail::assertSent(AccountTransactionMail::class, fn ($m) => $m->hasTo($this->user->email)
            && $m->transaction->type === $type
            && $m->transaction->category === $category
            && $m->transaction->balance_after === $balanceAfter);
    }

    public function test_deposit_approval_credits_once_even_if_clicked_twice(): void
    {
        $deposit = Deposit::create(['user_id' => $this->user->id, 'amount' => 250, 'payment_mode' => 'USDT', 'status' => 'pending']);

        $this->actingAs($this->admin, 'admin')->postJson(route('admin.deposits.approve', $deposit->id))->assertOk();
        $this->actingAs($this->admin, 'admin')->postJson(route('admin.deposits.approve', $deposit->id))->assertStatus(400);

        $this->assertSame(1250.0, (float) $this->user->fresh()->balance);
        $this->assertOneAlert(Transaction::CREDIT, 'deposit', 1250.0);
    }

    public function test_withdrawal_request_debits_and_alerts(): void
    {
        $this->actingAs($this->user)->postJson(route('withdraw.submit'), [
            'amount' => 300, 'method' => 'USDT', 'address' => 'TXabc', 'otp' => '123456',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertSame(700.0, (float) $this->user->fresh()->balance);
        $this->assertNull($this->user->fresh()->otp, 'OTP is cleared');
        $this->assertOneAlert(Transaction::DEBIT, 'withdrawal', 700.0);
    }

    public function test_declined_withdrawal_refunds_once(): void
    {
        $withdrawal = Withdrawal::create(['user_id' => $this->user->id, 'amount' => 300, 'receiving_mode' => 'USDT', 'address' => 'TXabc']);

        $this->actingAs($this->admin, 'admin')->postJson(route('admin.withdrawals.decline', $withdrawal->id))->assertJson(['success' => true]);
        $this->actingAs($this->admin, 'admin')->postJson(route('admin.withdrawals.decline', $withdrawal->id))->assertJson(['success' => false]);

        $this->assertSame(1300.0, (float) $this->user->fresh()->balance);
        $this->assertOneAlert(Transaction::CREDIT, 'withdrawal_refund', 1300.0);
    }

    public function test_admin_balance_debit_is_refused_when_insufficient(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.users.update.balance', $this->user), ['type' => 'debit', 'amount' => 5000])
            ->assertStatus(400);

        $this->assertSame(1000.0, (float) $this->user->fresh()->balance);
        Mail::assertNothingSent();
    }

    public function test_admin_balance_credit_alerts(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.users.update.balance', $this->user), ['type' => 'add', 'amount' => 75.5])
            ->assertOk();

        $this->assertOneAlert(Transaction::CREDIT, 'admin_credit', 1075.5);
    }

    public function test_profit_top_up_and_bonus_alert(): void
    {
        $this->actingAs($this->admin, 'admin')->postJson(route('admin.topup.profit', $this->user), ['amount' => 40])->assertOk();
        $this->actingAs($this->admin, 'admin')->postJson("/admin/users/{$this->user->id}/bonus", ['amount' => 10])->assertOk();

        $this->assertSame(1050.0, (float) $this->user->fresh()->balance);
        Mail::assertSentCount(2);
        Mail::assertSent(AccountTransactionMail::class, fn ($m) => $m->transaction->category === 'profit' && $m->transaction->balance_after === 1040.0);
        Mail::assertSent(AccountTransactionMail::class, fn ($m) => $m->transaction->category === 'bonus' && $m->transaction->balance_after === 1050.0);
    }
}
