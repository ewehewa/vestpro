<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WithdrawalApprovedMail;
use App\Mail\WithdrawalDeclinedMail;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class WithdrawalController extends Controller
{
    public function approve($id)
    {
        try {
            $withdrawal = Withdrawal::find($id);

            if (!$withdrawal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Withdrawal not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            if ($withdrawal->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Withdrawal already approved.',
                ], Response::HTTP_BAD_REQUEST);
            }

            $withdrawal->update(['status' => 'approved']);

            try {
                Mail::to($withdrawal->user->email)->send(new WithdrawalApprovedMail($withdrawal));
            } catch (\Throwable $e) {
                Log::warning('Failed to send withdrawal approved email: ' . $e->getMessage(), ['withdrawal_id' => $id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal approved successfully.',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function declineWithdrawal($id)
    {
        try {
            $withdrawal = Withdrawal::findOrFail($id);
            if ($withdrawal->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Withdrawal already processed.']);
            }

            $user = $withdrawal->user;

            // Reverse the amount
            $user->balance += $withdrawal->amount;
            $user->save();

            $withdrawal->status = 'declined';
            $withdrawal->save();

            try {
                Mail::to($user->email)->send(new WithdrawalDeclinedMail($withdrawal));
            } catch (\Throwable $e) {
                Log::warning('Failed to send withdrawal declined email: ' . $e->getMessage(), ['withdrawal_id' => $id]);
            }

            return response()->json(['success' => true, 'message' => 'Withdrawal declined successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while declining.']);
        }
    }
}
