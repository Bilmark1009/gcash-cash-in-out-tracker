<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\ProfitTracking;
use App\Services\TransactionService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create or get admin user without initial balances (user must onboard)
        $user = User::firstOrCreate(
            ['email' => 'admin@peraly.com'],
            [
                'name' => 'Admin User',
                'business_name' => 'My Business',
                'phone_number' => '09123456789',
                'gcash_balance' => 0,
                'cash_balance' => 0,
                'default_fee_percentage' => 2.0,
                'onboarded' => false,
                'password' => bcrypt('password'),
            ]
        );

        // Create or get profit tracking for user
        ProfitTracking::firstOrCreate(
            ['user_id' => $user->id],
            [
                'total_profit' => 0,
                'profit_from_cash_in' => 0,
                'profit_from_cash_out' => 0,
                'transaction_count' => 0,
            ]
        );

        // Create sample categories
        $categories = [
            // Cash In Categories
            ['name' => 'Sales', 'type' => 'cash-in'],
            ['name' => 'Refund', 'type' => 'cash-in'],
            ['name' => 'Payment Received', 'type' => 'cash-in'],
            ['name' => 'Deposit', 'type' => 'cash-in'],
            
            // Cash Out Categories
            ['name' => 'Inventory', 'type' => 'cash-out'],
            ['name' => 'Utilities', 'type' => 'cash-out'],
            ['name' => 'Rent', 'type' => 'cash-out'],
            ['name' => 'Employee Salary', 'type' => 'cash-out'],
            ['name' => 'Supplies', 'type' => 'cash-out'],
            ['name' => 'Withdrawal', 'type' => 'cash-out'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
