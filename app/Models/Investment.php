<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'investment_plan_id',
        'amount',
        'status',
        'roi',
        'duration_days',
        'daily_return',
        'starts_at',
        'ends_at',
        'days_paid',
        'total_earned',
        'last_paid_at',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'roi' => 'float',
        'daily_return' => 'float',
        'total_earned' => 'float',
        'days_paid' => 'integer',
        'duration_days' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_paid_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(InvestmentPlan::class, 'investment_plan_id');
    }

    public function payouts()
    {
        return $this->hasMany(InvestmentPayout::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
