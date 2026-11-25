<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentPayout extends Model
{
    protected $fillable = [
        'investment_id',
        'user_id',
        'day_number',
        'payout_date',
        'amount',
        'transaction_id',
    ];

    protected $casts = [
        'payout_date' => 'date',
        'amount' => 'float',
    ];

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
