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
        // Create admin user with initial balances
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@peraly.com',
            'business_name' => 'My Business',
            'phone_number' => '09123456789',
            'gcash_balance' => 50000,
            'cash_balance' => 10000,
            'default_fee_percentage' => 2.0,
        ]);

        // Create profit tracking for user
        ProfitTracking::create([
            'user_id' => $user->id,
            'total_profit' => 0,
            'profit_from_cash_in' => 0,
            'profit_from_cash_out' => 0,
            'transaction_count' => 0,
        ]);

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
            Category::create($category);
        }

        // Create sample transactions using TransactionService
        $transactionService = new TransactionService();
        $now = Carbon::now();
        $cashInCategories = Category::where('type', 'cash-in')->pluck('id')->toArray();
        $cashOutCategories = Category::where('type', 'cash-out')->pluck('id')->toArray();

        // Cash In transactions
        for ($i = 0; $i < 15; $i++) {
            $amount = rand(5000, 50000);
            $date = $now->copy()->subDays(rand(0, 29));
            $feePercentage = rand(1, 3); // Random fee percentage between 1-3%

            try {
                $transactionService->processTransaction($user, [
                    'transaction_date' => $date->toDateString(),
                    'type' => 'cash-in',
                    'amount' => $amount,
                    'fee_percentage' => $feePercentage,
                    'category_id' => $cashInCategories[array_rand($cashInCategories)],
                    'notes' => 'Sample cash-in transaction',
                ]);
            } catch (\Exception $e) {
                // Log error but continue seeding
                \Log::error('Error seeding transaction: ' . $e->getMessage());
            }
        }

        // Cash Out transactions
        for ($i = 0; $i < 12; $i++) {
            $amount = rand(3000, 30000);
            $date = $now->copy()->subDays(rand(0, 29));
            $feePercentage = rand(1, 3); // Random fee percentage between 1-3%

            try {
                $transactionService->processTransaction($user, [
                    'transaction_date' => $date->toDateString(),
                    'type' => 'cash-out',
                    'amount' => $amount,
                    'fee_percentage' => $feePercentage,
                    'category_id' => $cashOutCategories[array_rand($cashOutCategories)],
                    'notes' => 'Sample cash-out transaction',
                ]);
            } catch (\Exception $e) {
                // Log error but continue seeding
                \Log::error('Error seeding transaction: ' . $e->getMessage());
            }
        }
    }
}
