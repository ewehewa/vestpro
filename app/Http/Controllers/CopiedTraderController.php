<?php

namespace App\Http\Controllers;

use App\Mail\TraderCopiedMail;
use App\Models\CopiedTrader;
use App\Models\Trader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CopiedTraderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'trader_id' => 'required|exists:traders,id',
        ]);

        $user = Auth::user();

        // ✅ Check if user has funds
        if ($user->balance <= 0) {   // assuming you store balance in `users` table
            return response()->json([
                'success' => false,
                'message' => 'You need to fund your account before copying a trader.',
            ], 403);
        }

        // Check if already copied
        $exists = CopiedTrader::where('user_id', $user->id)
                    ->where('trader_id', $request->trader_id)
                    ->first();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'You are already copying this trader.',
            ], 400);
        }

        $trader = Trader::findOrFail($request->trader_id);

        CopiedTrader::create([
            'user_id'   => $user->id,
            'trader_id' => $trader->id,
        ]);

        try {
            Mail::to($user->email)->send(new TraderCopiedMail($user, $trader));
        } catch (\Throwable $e) {
            Log::warning('Failed to send trader copied email: ' . $e->getMessage(), ['user_id' => $user->id, 'trader_id' => $trader->id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'You are now copying this trader!',
        ]);
    }


    // Stop copying a trader
    public function destroy($id)
    {
        $user = Auth::user();

        $copied = CopiedTrader::where('user_id', $user->id)
                    ->where('trader_id', $id)
                    ->first();

        if (!$copied) {
            return response()->json([
                'success' => false,
                'message' => 'You are not copying this trader.',
            ], 404);
        }

        $copied->delete();

        return response()->json([
            'success' => true,
            'message' => 'You have stopped copying this trader.',
        ]);
    }

    public function history()
    {
        $user = Auth::user()->load([
            'copiedTraders' => function ($q) {
                $q->latest();
            },
            'copiedTraders.trader'
        ]);

        return view('dashboard.user.copiedTradeHistory', compact('user'));
    }
}
