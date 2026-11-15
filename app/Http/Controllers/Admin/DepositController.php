<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DepositDeclinedMail;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DepositController extends Controller
{
    public function decline($id)
    {
        try {
            $deposit = Deposit::find($id);

            if (!$deposit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deposit record not found.'
                ], 404);
            }

            if (strtolower($deposit->status) !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This deposit has already been processed (' . ucfirst($deposit->status) . ').'
                ], 400);
            }

            $deposit->update(['status' => 'declined']);

            try {
                Mail::to($deposit->user->email)->send(new DepositDeclinedMail($deposit));
            } catch (\Throwable $e) {
                Log::warning('Failed to send deposit declined email: ' . $e->getMessage(), ['deposit_id' => $id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Deposit declined successfully.'
            ]);

        } catch (\Throwable $e) {
            Log::error('Deposit decline error: ' . $e->getMessage(), [
                'deposit_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while declining the deposit: ' . $e->getMessage()
            ], 500);
        }
    }
}
