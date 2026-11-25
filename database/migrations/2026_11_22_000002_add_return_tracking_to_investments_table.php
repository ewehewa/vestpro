<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Track daily returns on each investment. ROI, duration and the daily amount are
     * copied from the plan when the investment is made, so editing a plan later does
     * not change investments that are already running.
     */
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->index()->after('amount');
            $table->decimal('roi', 5, 2)->nullable()->after('status');
            $table->unsignedInteger('duration_days')->nullable()->after('roi');
            $table->decimal('daily_return', 12, 2)->nullable()->after('duration_days');
            $table->timestamp('starts_at')->nullable()->after('daily_return');
            $table->timestamp('ends_at')->nullable()->after('starts_at');
            $table->unsignedInteger('days_paid')->default(0)->after('ends_at');
            $table->decimal('total_earned', 15, 2)->default(0)->after('days_paid');
            $table->timestamp('last_paid_at')->nullable()->after('total_earned');
            $table->timestamp('completed_at')->nullable()->after('last_paid_at');
        });

        // Existing investments start earning from today (no back-pay for past days).
        $now = now();

        DB::table('investments')
            ->join('investment_plans', 'investments.investment_plan_id', '=', 'investment_plans.id')
            ->select('investments.id', 'investments.amount', 'investment_plans.roi', 'investment_plans.duration')
            ->orderBy('investments.id')
            ->chunk(200, function ($rows) use ($now) {
                foreach ($rows as $row) {
                    DB::table('investments')->where('id', $row->id)->update([
                        'status' => 'active',
                        'roi' => $row->roi,
                        'duration_days' => $row->duration,
                        'daily_return' => round($row->amount * $row->roi / 100, 2),
                        'starts_at' => $now,
                        'ends_at' => $now->copy()->addDays((int) $row->duration),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn([
                'status', 'roi', 'duration_days', 'daily_return', 'starts_at', 'ends_at',
                'days_paid', 'total_earned', 'last_paid_at', 'completed_at',
            ]);
        });
    }
};
