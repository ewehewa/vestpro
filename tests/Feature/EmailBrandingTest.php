<?php

namespace Tests\Feature;

use App\Mail;
use App\Models\Deposit;
use App\Models\Investment;
use App\Models\InvestmentPlan;
use App\Models\Trader;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\BalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Every email the app sends must carry the Strategic Invest Pro logo. */
class EmailBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_mailable_shows_the_logo(): void
    {
        $user = User::factory()->create(['username' => 'jane', 'balance' => 1000]);
        $plan = InvestmentPlan::create(['name' => 'Gold Plan', 'roi' => 2, 'min_amount' => 100, 'max_amount' => 10000, 'duration' => 30]);
        $investment = Investment::create(['user_id' => $user->id, 'investment_plan_id' => $plan->id, 'amount' => 500]);
        $deposit = Deposit::create(['user_id' => $user->id, 'amount' => 250, 'payment_mode' => 'USDT', 'status' => 'pending']);
        $withdrawal = Withdrawal::create(['user_id' => $user->id, 'amount' => 100, 'receiving_mode' => 'USDT', 'address' => 'TXabc']);
        $trader = Trader::create(['name' => 'Alex', 'picture' => 'a.png', 'average_return' => 12, 'followers' => 10, 'profit_share' => 20, 'win_rate' => 80, 'total_profit' => 1000]);
        $transaction = app(BalanceService::class)->credit($user, 25, 'bonus', 'Bonus', notify: false);

        $mailables = [
            new Mail\AccountTransactionMail($transaction),
            new Mail\AdminToUserMail('Hello', 'A message from the admin'),
            new Mail\BalanceCreditedMail($user, 25),
            new Mail\BonusAddedMail($user, 25),
            new Mail\DailyReturnsMail($user, [['plan' => 'Gold Plan', 'day' => 1, 'days' => 30, 'amount' => 10.0, 'kind' => 'return']], 10.0, 1010.0),
            new Mail\DepositApprovedMail($deposit),
            new Mail\DepositDeclinedMail($deposit),
            new Mail\DepositReceived($deposit),
            new Mail\EmailVerificationCode(123456),
            new Mail\InvestmentSuccessfulMail($investment),
            new Mail\OtpGeneratedMail(654321),
            new Mail\ProfitToppedUpMail($user, 25),
            new Mail\TraderCopiedMail($user, $trader),
            new Mail\UserRegistered($user),
            new Mail\WithdrawalApprovedMail($withdrawal),
            new Mail\WithdrawalDeclinedMail($withdrawal),
        ];

        // Fails if a new mailable is added to app/Mail without being listed here
        $this->assertCount(count(glob(app_path('Mail/*.php'))), $mailables, 'Add the new mailable to this test.');

        foreach ($mailables as $mailable) {
            $html = $mailable->render();
            $name = class_basename($mailable);

            $this->assertMatchesRegularExpression('/<img[^>]+(logo-icon\.png|data:image\/png;base64,)/', $html, "$name has no logo image");
            $this->assertStringContainsString('STRATEGIC', $html, "$name has no wordmark");
        }
    }
}
