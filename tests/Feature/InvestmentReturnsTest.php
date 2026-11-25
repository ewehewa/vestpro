<?php

namespace Tests\Feature;

use App\Mail\AccountTransactionMail;
use App\Mail\DailyReturnsMail;
use App\Models\Admin;
use App\Models\Investment;
use App\Models\InvestmentPayout;
use App\Models\InvestmentPlan;
use App\Models\Setting;
use App\Models\User;
use App\Services\InvestmentReturnService;
use App\Services\ReturnSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvestmentReturnsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private InvestmentPlan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->travelTo(Carbon::parse('2026-01-10 15:00:00', 'UTC'));

        $this->user = User::factory()->create(['username' => 'investor', 'balance' => 1000]);
        $this->plan = InvestmentPlan::create([
            'name' => 'Gold Plan', 'roi' => 2, 'min_amount' => 100, 'max_amount' => 10000, 'duration' => 3,
        ]);
    }

    private function invest(float $amount = 500): Investment
    {
        $this->actingAs($this->user)
            ->postJson(route('user.invest'), ['plan_id' => $this->plan->id, 'amount' => $amount])
            ->assertCreated();

        return Investment::latest('id')->first();
    }

    private function runPayouts(): array
    {
        return app(InvestmentReturnService::class)->run();
    }

    public function test_investing_debits_balance_snapshots_the_plan_and_sends_debit_alert(): void
    {
        $investment = $this->invest(500);

        $this->assertSame(500.0, (float) $this->user->fresh()->balance);
        $this->assertSame(Investment::STATUS_ACTIVE, $investment->status);
        $this->assertSame(10.0, $investment->daily_return);
        $this->assertSame(3, $investment->duration_days);

        Mail::assertSent(AccountTransactionMail::class, fn ($m) => $m->transaction->category === 'investment'
            && $m->transaction->balance_after === 500.0);
    }

    public function test_nothing_is_paid_on_the_day_of_investing(): void
    {
        $this->invest();
        $stats = $this->runPayouts();

        $this->assertSame(0, $stats['payouts']);
        Mail::assertNotSent(DailyReturnsMail::class);
    }

    public function test_daily_return_is_paid_once_per_day_with_one_summary_email(): void
    {
        $investment = $this->invest(500);

        $this->travelTo(Carbon::parse('2026-01-11 00:05:00', 'UTC'));
        $stats = $this->runPayouts();

        $this->assertSame(1, $stats['payouts']);
        $this->assertSame(510.0, (float) $this->user->fresh()->balance);
        $this->assertSame(1, $investment->fresh()->days_paid);
        Mail::assertSent(DailyReturnsMail::class, fn ($m) => $m->total === 10.0 && $m->balance === 510.0);

        // Running again the same day pays nothing more
        $again = $this->runPayouts();
        $this->assertSame(0, $again['payouts']);
        $this->assertSame(510.0, (float) $this->user->fresh()->balance);
        $this->assertSame(1, InvestmentPayout::count());
        Mail::assertSentCount(2); // the investment debit alert + one returns email
    }

    public function test_missed_days_are_caught_up_and_capital_is_returned_on_completion(): void
    {
        $investment = $this->invest(500);

        // Server was down for days 1-2; run on day 3 (the last day)
        $this->travelTo(Carbon::parse('2026-01-13 00:05:00', 'UTC'));
        $stats = $this->runPayouts();

        $investment->refresh();
        $this->assertSame(3, $stats['payouts']);
        $this->assertSame(1, $stats['completed']);
        $this->assertSame(Investment::STATUS_COMPLETED, $investment->status);
        $this->assertSame(30.0, $investment->total_earned);
        // 500 left after investing + 3 x 10 returns + 500 capital
        $this->assertSame(1030.0, (float) $this->user->fresh()->balance);

        Mail::assertSent(DailyReturnsMail::class, fn ($m) => count($m->items) === 4 && $m->total === 530.0 && $m->balance === 1030.0);

        // Completed investments are never paid again
        $this->travelTo(Carbon::parse('2026-01-20 00:05:00', 'UTC'));
        $this->assertSame(0, $this->runPayouts()['payouts']);
        $this->assertSame(1030.0, (float) $this->user->fresh()->balance);
    }

    public function test_user_with_several_investments_gets_one_email(): void
    {
        $this->invest(200);
        $this->invest(300);

        $this->travelTo(Carbon::parse('2026-01-11 00:05:00', 'UTC'));
        $stats = $this->runPayouts();

        $this->assertSame(2, $stats['payouts']);
        $this->assertSame(1, $stats['users']);
        Mail::assertSent(DailyReturnsMail::class, 1);
    }

    public function test_daily_returns_email_shows_each_line_and_available_balance(): void
    {
        $html = (new DailyReturnsMail($this->user, [
            ['plan' => 'Gold Plan', 'day' => 2, 'days' => 30, 'amount' => 10.0, 'kind' => 'return'],
        ], 10.0, 510.0))->render();

        $this->assertStringContainsString('Gold Plan', $html);
        $this->assertStringContainsString('day 2 of 30', $html);
        $this->assertStringContainsString('+$10.00', $html);
        $this->assertStringContainsString('Available Balance', $html);
        $this->assertStringContainsString('$510.00', $html);
    }

    public function test_command_waits_for_the_admin_payout_time_and_runs_once_a_day(): void
    {
        $this->invest(500);
        app(ReturnSettings::class)->update(true, '09:30', 'Africa/Lagos'); // 08:30 UTC

        // 08:00 UTC = 09:00 Lagos: not yet
        $this->travelTo(Carbon::parse('2026-01-11 08:00:00', 'UTC'));
        $this->artisan('investments:credit-returns')->assertSuccessful();
        $this->assertSame(500.0, (float) $this->user->fresh()->balance);
        $this->assertNotNull(Setting::get('scheduler.last_seen_at'), 'heartbeat recorded');

        // 08:31 UTC = 09:31 Lagos: due
        $this->travelTo(Carbon::parse('2026-01-11 08:31:00', 'UTC'));
        $this->artisan('investments:credit-returns')->assertSuccessful();
        $this->assertSame(510.0, (float) $this->user->fresh()->balance);

        // Later the same day: already ran
        $this->travelTo(Carbon::parse('2026-01-11 20:00:00', 'UTC'));
        $this->artisan('investments:credit-returns')->assertSuccessful();
        $this->assertSame(510.0, (float) $this->user->fresh()->balance);
    }

    public function test_paused_payouts_do_not_run_on_schedule(): void
    {
        $this->invest(500);
        app(ReturnSettings::class)->update(false, '00:05', 'UTC');

        $this->travelTo(Carbon::parse('2026-01-11 12:00:00', 'UTC'));
        $this->artisan('investments:credit-returns')->assertSuccessful();

        $this->assertSame(500.0, (float) $this->user->fresh()->balance);
    }

    public function test_cron_warning_shows_only_while_the_scheduler_is_not_running(): void
    {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('secret')]);
        $warning = "Automatic payouts aren't running";

        // Never seen: warning with the command
        $this->actingAs($admin, 'admin')->get(route('admin.settings.returns'))
            ->assertOk()->assertSee($warning, false)->assertSee('is missing', false)->assertSee('artisan schedule:run');

        // Scheduler checks in: warning gone
        $this->artisan('investments:credit-returns')->assertSuccessful();
        $this->actingAs($admin, 'admin')->get(route('admin.settings.returns'))
            ->assertOk()->assertDontSee($warning, false)->assertDontSee('artisan schedule:run')->assertSee('Running');

        // Scheduler stops for 10 minutes: warning is back, saying when it was last seen
        $this->travel(10)->minutes();
        $this->actingAs($admin, 'admin')->get(route('admin.settings.returns'))
            ->assertOk()->assertSee($warning, false)->assertSee('no longer running', false);
    }

    public function test_admin_can_view_save_and_run_the_returns_settings(): void
    {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('secret')]);
        $this->invest(500);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.settings.returns'))
            ->assertOk()
            ->assertSee('Investment Returns')
            ->assertSee('Run payouts now');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.settings.returns.update'), ['enabled' => '1', 'time' => '07:15', 'timezone' => 'Africa/Lagos'])
            ->assertRedirect(route('admin.settings.returns'));

        $settings = app(ReturnSettings::class);
        $this->assertSame('07:15', $settings->time());
        $this->assertSame('Africa/Lagos', $settings->timezone());

        $this->actingAs($admin, 'admin')
            ->post(route('admin.settings.returns.update'), ['time' => '25:99', 'timezone' => 'Nowhere/Fake'])
            ->assertSessionHasErrors(['time', 'timezone']);

        $this->travelTo(Carbon::parse('2026-01-11 12:00:00', 'UTC'));
        $this->actingAs($admin, 'admin')
            ->post(route('admin.settings.returns.run'))
            ->assertRedirect(route('admin.settings.returns'))
            ->assertSessionHas('success');

        $this->assertSame(510.0, (float) $this->user->fresh()->balance);
    }
}
