<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('fee_percentage', 5, 2)->default(2.0)->after('amount');
            $table->decimal('fee_amount', 12, 2)->default(0)->after('fee_percentage');
            $table->decimal('gcash_balance_before', 12, 2)->nullable()->after('fee_amount');
            $table->decimal('gcash_balance_after', 12, 2)->nullable()->after('gcash_balance_before');
            $table->decimal('cash_balance_before', 12, 2)->nullable()->after('gcash_balance_after');
            $table->decimal('cash_balance_after', 12, 2)->nullable()->after('cash_balance_before');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'fee_percentage',
                'fee_amount',
                'gcash_balance_before',
                'gcash_balance_after',
                'cash_balance_before',
                'cash_balance_after',
            ]);
        });
    }
};
