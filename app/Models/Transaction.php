<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public const CREDIT = 'credit';
    public const DEBIT = 'debit';

    /** Human-readable names for each category, used in alert emails. */
    public const CATEGORY_LABELS = [
        'deposit' => 'Deposit',
        'withdrawal' => 'Withdrawal',
        'withdrawal_refund' => 'Withdrawal Refund',
        'investment' => 'Investment',
        'investment_return' => 'Investment Return',
        'principal_return' => 'Capital Returned',
        'profit' => 'Profit',
        'profit_debit' => 'Profit Reversal',
        'bonus' => 'Bonus',
        'bonus_reversal' => 'Bonus Reversal',
        'admin_credit' => 'Account Credit',
        'admin_debit' => 'Account Debit',
        'trade' => 'Trade',
        'nft_purchase' => 'NFT Purchase',
        'nft_sale' => 'NFT Sale',
        'signal_subscription' => 'Signal Subscription',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'category',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'balance_before' => 'float',
        'balance_after' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function isCredit(): bool
    {
        return $this->type === self::CREDIT;
    }

    public function categoryLabel(): string
    {
        return self::CATEGORY_LABELS[$this->category] ?? ucwords(str_replace('_', ' ', $this->category));
    }

    /** Short reference shown to the user, e.g. "TXN-000123". */
    public function referenceCode(): string
    {
        return 'TXN-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
