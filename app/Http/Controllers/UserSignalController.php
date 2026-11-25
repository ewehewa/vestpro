<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Signal;
use App\Models\UserSignal;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserSignalController extends Controller
{
    public function index()
    {
        $signals = Signal::where('status', 1)->get();

        return view('dashboard.user.signals_index', compact('signals'));
    }

    public function subscribe(Signal $signal)
    {
        $user = Auth::user();

        // Check balance
        if ($user->balance < $signal->price) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance.'
            ]);
        }

        try {
            DB::transaction(function () use ($user, $signal) {
                // Save subscription
                $subscription = UserSignal::create([
                    'user_id' => $user->id,
                    'signal_id' => $signal->id,
                    'status' => 'active',
                    'expires_at' => now()->addWeeks($signal->duration ?? 1)
                ]);

                // Deduct balance; this also sends the debit alert email
                app(BalanceService::class)->debit(
                    $user,
                    (float) $signal->price,
                    'signal_subscription',
                    "Signal subscription: {$signal->name}",
                    $subscription,
                );
            });
        } catch (InsufficientBalanceException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscription successful!',
        ]);
    }

    public function myPlans()
    {
        $subscriptions = UserSignal::where('user_id', Auth::id())
            ->with('signal')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.user.my_signal', compact('subscriptions'));
    }
}

