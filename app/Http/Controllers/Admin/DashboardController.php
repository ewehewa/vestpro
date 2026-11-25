<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Mail\AdminToUserMail;
use App\Mail\OtpGeneratedMail;
use App\Models\Deposit;
use App\Models\Investment;
use App\Models\Profit;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    public function __construct(private BalanceService $balance)
    {
    }

    public function index()
    {
         $admin = Auth::guard('admin')->user();

        $totalUsers = User::count();
        $totalDeposits = Deposit::sum('amount');
        $totalWithdrawals = Withdrawal::sum('amount');
        $pendingDeposits = Deposit::where('status', 'pending')->count();
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        $activePlansCount = Investment::count();

        return view('dashboard.admin.index', compact(
            'totalUsers',
            'totalDeposits',
            'totalWithdrawals',
            'pendingDeposits',
            'pendingWithdrawals',
            'activePlansCount'
        ));
    }

    public function allUsers()
    {
        $users = User::latest()->paginate(10); // 10 users per page
        return view('dashboard.admin.users', compact('users'));
    }

    public function showUser($id)
    {
        $user = User::with([
            'deposits' => function ($q) {
                $q->latest();
            },
            'withdrawals' => function ($q) {
                $q->latest();
            },
            'investments' => function ($q) {
                $q->latest();
            },
            'investments.plan',
            'copiedTraders' => function ($q) {   // <-- add this
                $q->latest();
            },
            'copiedTraders.trader', // eager load trader details
        ])->findOrFail($id);

        // calculate totals
    $totalProfits = $user->profits()->sum('amount');
    $totalBonuses = $user->bonuses()->sum('amount');

        return view('dashboard.admin.show_user', compact('user', 'totalProfits', 'totalBonuses'));
    }


    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function approveDeposit($id)
    {
        $approved = DB::transaction(function () use ($id) {
            // Lock the row so a double click can't credit twice
            $deposit = Deposit::lockForUpdate()->findOrFail($id);

            if ($deposit->status !== 'pending') {
                return false;
            }

            // Approve deposit and credit the user; this also sends the credit alert email
            $deposit->update(['status' => 'approved']);

            $this->balance->credit(
                $deposit->user,
                (float) $deposit->amount,
                'deposit',
                "Deposit approved ({$deposit->payment_mode})",
                $deposit,
            );

            return true;
        });

        if (! $approved) {
            return response()->json(['success' => false, 'message' => 'Deposit already processed.'], 400);
        }

        return response()->json(['success' => true, 'message' => 'Deposit approved successfully!']);
    }

    public function allDeposits(Request $request)
    {
        // Optional ?status=pending|approved|declined (e.g. from the dashboard's "Pending Deposits" card)
        $status = in_array($request->query('status'), ['pending', 'approved', 'declined'], true)
            ? $request->query('status')
            : null;

        $deposits = Deposit::with('user')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.admin.deposit_history', compact('deposits', 'status'));
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            Mail::to($request->email)->send(new AdminToUserMail($request->subject, $request->message));

            return response()->json([
                'success' => true,
                'message' => 'Mail sent successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send mail.',
            ]);
        }
    }

    public function createMail()
    {
        return view('dashboard.admin.send_mail');
    }

    public function topUpProfit(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amount = (float) $request->amount;

        DB::transaction(function () use ($user, $amount) {
            $profit = Profit::create([
                'user_id' => $user->id,
                'amount' => $amount,
            ]);

            // Credit balance; this also sends the credit alert email
            $this->balance->credit($user, $amount, 'profit', 'Profit credited to your account', $profit);
        });

        return response()->json([
            'success' => true,
            'message' => 'Profit topped up successfully.',
            'new_balance' => $user->balance,
        ]);
    }

    public function debitProfit(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amount = $request->amount;

        // Calculate total profit balance for user
        $profitBalance = Profit::where('user_id', $user->id)->sum('amount');

        if ($profitBalance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient profit balance to debit.',
            ], 400);
        }

        try {
            DB::transaction(function () use ($user, $amount) {
                // Record the debit as a negative profit entry
                $profit = Profit::create([
                    'user_id' => $user->id,
                    'amount' => -$amount,
                ]);

                // Also reduce the user's overall balance; this sends the debit alert email
                $this->balance->debit($user, (float) $amount, 'profit_debit', 'Profit reversed by administrator', $profit);
            });
        } catch (InsufficientBalanceException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User has insufficient balance.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profit debited successfully.',
            'new_profit_balance' => Profit::where('user_id', $user->id)->sum('amount'),
            'new_user_balance' => $user->balance,
        ]);
    }

    public function generateOtp(Request $request, User $user)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        $user->otp = $request->otp;
        $user->save();

        Mail::to($user->email)->send(new OtpGeneratedMail($user->otp));

        return response()->json([
            'success' => true,
            'message' => 'OTP has been generated successfully.'
        ]);
    }

    public function showWalletForm()
    {
       return view('dashboard.admin.wallet');
    }

    public function saveWallet(Request $request)
    {
        $request->validate([
            'wallet_name' => 'required|string|max:255',
            'wallet_address' => 'required|string|max:255',
        ]);

        Wallet::create([
            'wallet_name' => $request->wallet_name,
            'wallet_address' => $request->wallet_address,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Wallet saved successfully.'
        ]);
    }

    public function showWallets()
    {
        $wallets = Wallet::latest()->get();
        return view('dashboard.admin.show_wallets', compact('wallets'));
    }

    public function deleteWallet($id)
    {
        $wallet = Wallet::find($id);
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => 'Wallet not found.'], 404);
        }

        $wallet->delete();
        return response()->json(['success' => true]);
    }

    public function debitBalance(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        try {
            // This also sends the debit alert email
            $this->balance->debit($user, (float) $request->amount, 'admin_debit', 'Account debited by administrator');
        } catch (InsufficientBalanceException $e) {
            return response()->json(['success' => false, 'message' => 'User has insufficient balance.'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'User balance debited successfully.',
            'new_balance' => $user->balance
        ]);
    }

    public function updateUserBalance(Request $request, User $user)
    {
        $request->validate([
            'type' => 'required|in:add,debit',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $amount = (float) $request->amount;

            // Both branches send a credit/debit alert email
            if ($request->type === 'debit') {
                $this->balance->debit($user, $amount, 'admin_debit', 'Account debited by administrator');
                $message = 'User balance debited successfully.';
            } else {
                $this->balance->credit($user, $amount, 'admin_credit', 'Account credited by administrator');
                $message = 'User balance added successfully.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'new_balance' => $user->balance,
            ]);

        } catch (InsufficientBalanceException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User has insufficient balance.'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user balance. Please try again later.',
            ], 500);
        }
    }

}
