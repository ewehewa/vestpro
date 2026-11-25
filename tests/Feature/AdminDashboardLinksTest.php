<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardLinksTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('secret')]);
        $this->user = User::factory()->create(['username' => 'jane']);
    }

    public function test_every_dashboard_card_links_to_its_page(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.dashboard'))->assertOk();

        foreach ([
            route('admin.users'),
            route('admin.deposits.all'),
            route('admin.withdrawals'),
            route('admin.deposits.all', ['status' => 'pending']),
            route('admin.withdrawals', ['status' => 'pending']),
            route('admin.show.plans'),
        ] as $url) {
            $response->assertSee('href="' . $url . '"', false);
            $this->actingAs($this->admin, 'admin')->get($url)->assertOk();
        }
    }

    public function test_pending_filter_shows_only_pending_deposits(): void
    {
        Deposit::create(['user_id' => $this->user->id, 'amount' => 111, 'payment_mode' => 'USDT', 'status' => 'pending']);
        Deposit::create(['user_id' => $this->user->id, 'amount' => 999, 'payment_mode' => 'USDT', 'status' => 'approved']);

        $this->actingAs($this->admin, 'admin')->get(route('admin.deposits.all', ['status' => 'pending']))
            ->assertOk()->assertSee('Pending Deposits')->assertSee('Show all deposits')
            ->assertSee('111')->assertDontSee('999');

        $this->actingAs($this->admin, 'admin')->get(route('admin.deposits.all'))
            ->assertOk()->assertSee('All Deposit History')->assertSee('111')->assertSee('999');
    }

    public function test_pending_filter_shows_only_pending_withdrawals(): void
    {
        Withdrawal::create(['user_id' => $this->user->id, 'amount' => 222, 'receiving_mode' => 'USDT', 'address' => 'TXa', 'status' => 'pending']);
        Withdrawal::create(['user_id' => $this->user->id, 'amount' => 888, 'receiving_mode' => 'USDT', 'address' => 'TXb', 'status' => 'approved']);

        $this->actingAs($this->admin, 'admin')->get(route('admin.withdrawals', ['status' => 'pending']))
            ->assertOk()->assertSee('Pending Withdrawals')->assertSee('Show all withdrawals')
            ->assertSee('222')->assertDontSee('888');
    }

    public function test_unknown_status_shows_everything(): void
    {
        Deposit::create(['user_id' => $this->user->id, 'amount' => 111, 'payment_mode' => 'USDT', 'status' => 'pending']);

        $this->actingAs($this->admin, 'admin')->get(route('admin.deposits.all', ['status' => 'nonsense']))
            ->assertOk()->assertSee('All Deposit History')->assertSee('111');
    }
}
