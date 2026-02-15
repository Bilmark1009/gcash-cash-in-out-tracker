<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
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
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@peraly.com',
            'business_name' => 'My Business',
            'phone_number' => '09123456789',
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

        // Create sample transactions for the past 30 days
        $now = Carbon::now();
        $cashInCategories = Category::where('type', 'cash-in')->pluck('id')->toArray();
        $cashOutCategories = Category::where('type', 'cash-out')->pluck('id')->toArray();

        // Cash In transactions
        for ($i = 0; $i < 15; $i++) {
            $amount = rand(5000, 50000);
            $date = $now->copy()->subDays(rand(0, 29));
            $fee = Transaction::calculateFee($amount, 'cash-in');

            Transaction::create([
                'transaction_date' => $date,
                'type' => 'cash-in',
                'amount' => $amount,
                'fee' => $fee,
                'category_id' => $cashInCategories[array_rand($cashInCategories)],
                'notes' => 'Sample transaction',
            ]);
        }

        // Cash Out transactions
        for ($i = 0; $i < 12; $i++) {
            $amount = rand(3000, 30000);
            $date = $now->copy()->subDays(rand(0, 29));
            $fee = Transaction::calculateFee($amount, 'cash-out');

            Transaction::create([
                'transaction_date' => $date,
                'type' => 'cash-out',
                'amount' => $amount,
                'fee' => $fee,
                'category_id' => $cashOutCategories[array_rand($cashOutCategories)],
                'notes' => 'Sample transaction',
            ]);
        }
    }
}

