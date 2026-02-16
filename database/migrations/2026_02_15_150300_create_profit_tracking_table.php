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
        Schema::create('profit_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->decimal('total_profit', 12, 2)->default(0);
            $table->decimal('profit_from_cash_in', 12, 2)->default(0);
            $table->decimal('profit_from_cash_out', 12, 2)->default(0);
            $table->integer('transaction_count')->default(0);
            $table->timestamps();
            
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_trackings');
    }
};
