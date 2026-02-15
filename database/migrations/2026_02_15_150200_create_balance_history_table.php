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
        Schema::create('balance_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('balance_type', ['gcash', 'cash', 'profit']);
            $table->decimal('amount_before', 12, 2);
            $table->decimal('amount_after', 12, 2);
            $table->decimal('change_amount', 12, 2);
            $table->string('reason')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_history');
    }
};
