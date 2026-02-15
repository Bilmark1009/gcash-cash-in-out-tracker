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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('gcash_balance', 12, 2)->default(0)->after('phone_number');
            $table->decimal('cash_balance', 12, 2)->default(0)->after('gcash_balance');
            $table->decimal('default_fee_percentage', 5, 2)->default(2.0)->after('cash_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gcash_balance', 'cash_balance', 'default_fee_percentage']);
        });
    }
};
