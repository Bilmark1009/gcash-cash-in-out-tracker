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
        // user_id is now added directly in create_transactions_table migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // user_id is now added directly in create_transactions_table migration
    }
};
