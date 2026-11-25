<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Investment;
use App\Models\InvestmentPlan;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvestmentController extends Controller
{
    public function showInvestments()
    {
        $user = request()->user();

        $investments = $user->investments()
            ->with('plan')
            ->latest()
            ->get();

        return view('dashboard.user.investments', compact('investments'));
    }

    public function showPlans()
    {
        $plans = InvestmentPlan::latest()->get();
        return view('dashboard.user.plans', compact('plans'));
    }

    public function invest(Request $request)
    {
        try {
            $validated = $request->validate([
                'plan_id' => 'required|exists:investment_plans,id',
                'amount' => 'required|numeric|min:1',
            ]);

            $user = Auth::user();

            $plan = InvestmentPlan::findOrFail($validated['plan_id']);
            $amount = $validated['amount'];

            // Validate amount range
            if ($amount < $plan->min_amount || $amount > $plan->max_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Investment amount must be between ' . money($plan->min_amount) . ' and ' . money($plan->max_amount),
                ], 422);
            }

            // Check balance
            if ($user->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance to invest.',
                ], 422);
            }

            DB::transaction(function () use ($user, $plan, $amount) {
                // Create investment; ROI and duration are copied so later plan edits don't affect it
                $investment = Investment::create([
                    'user_id' => $user->id,
                    'investment_plan_id' => $plan->id,
                    'amount' => $amount,
                    'status' => Investment::STATUS_ACTIVE,
                    'roi' => $plan->roi,
                    'duration_days' => $plan->duration,
                    'daily_return' => round($amount * $plan->roi / 100, 2),
                    'starts_at' => now(),
                    'ends_at' => now()->addDays((int) $plan->duration),
                ]);

                // Deduct balance; this also sends the debit alert email
                app(BalanceService::class)->debit(
                    $user,
                    (float) $amount,
                    'investment',
                    "Investment in {$plan->name} ({$plan->roi}% daily for {$plan->duration} days)",
                    $investment,
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Investment successful. A confirmation email has been sent.',
            ], 201);

        } catch (InsufficientBalanceException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance to invest.',
            ], 422);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
