<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per paid day of an investment. The unique (investment_id, day_number)
     * index makes it impossible to pay the same day twice, even if the job runs twice.
     */
    public function up(): void
    {
        Schema::create('investment_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('day_number');
            $table->date('payout_date');
            $table->decimal('amount', 12, 2);
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['investment_id', 'day_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_payouts');
    }
};
