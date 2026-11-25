<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BonusController extends Controller
{
    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $amount = (float) $validated['amount'];

        DB::transaction(function () use ($user, $amount) {
            // record the bonus
            $bonus = $user->bonuses()->create(['amount' => $amount]);

            // credit balance; this also sends the credit alert email
            app(BalanceService::class)->credit($user, $amount, 'bonus', 'Bonus credited to your account', $bonus);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Bonus added successfully',
            'user_id' => $user->id,
            'new_balance' => $user->fresh()->balance,
        ]);
    }

    // ➖ Remove Bonus
    public function destroy(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $amount = (float) $validated['amount'];

        // Get user's current bonus balance
        $bonusBalance = $user->bonuses()->sum('amount');

        if ($bonusBalance < $amount) {
            return response()->json([
                'status' => 'error',
                'message' => 'User does not have enough bonus balance to remove',
            ], 422);
        }

        try {
            DB::transaction(function () use ($user, $amount) {
                // Record a negative bonus entry (debit)
                $bonus = $user->bonuses()->create([
                    'amount' => -$amount
                ]);

                // debit balance; this also sends the debit alert email
                app(BalanceService::class)->debit($user, $amount, 'bonus_reversal', 'Bonus removed by administrator', $bonus);
            });
        } catch (InsufficientBalanceException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User does not have enough balance to remove this bonus',
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bonus removed successfully',
            'user_id' => $user->id,
            'new_bonus_balance' => $user->fresh()->balance, // updated bonus balance
        ]);
    }

}
