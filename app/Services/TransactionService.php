<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * Process a new transaction and update balances.
     */
    public function processTransaction(User $user, array $data): Transaction
    {
        return DB::transaction(function () use ($user, $data) {
            // Prepare transaction data
            $transactionData = [
                'user_id' => $user->id,
                'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
                'type' => $data['type'],
                'amount' => $data['amount'],
                'fee_percentage' => $data['fee_percentage'] ?? $user->default_fee_percentage,
                'notes' => $data['notes'] ?? null,
            ];

            // Calculate fee amount
            $transactionData['fee_amount'] = round(
                $transactionData['amount'] * ($transactionData['fee_percentage'] / 100),
                2
            );

            // Store current balances before transaction
            $transactionData['gcash_balance_before'] = $user->gcash_balance;
            $transactionData['cash_balance_before'] = $user->cash_balance;

            // Calculate new balances based on transaction type
            if ($transactionData['type'] === 'cash-in') {
                $transactionData['gcash_balance_after'] = $user->gcash_balance - $transactionData['amount'];
                $transactionData['cash_balance_after'] = $user->cash_balance + $transactionData['fee_amount'];
            } else {
                // cash-out
                $transactionData['gcash_balance_after'] = $user->gcash_balance + $transactionData['amount'];
                $transactionData['cash_balance_after'] = $user->cash_balance - ($transactionData['amount'] - $transactionData['fee_amount']);
            }

            // Validate balances
            if ($transactionData['type'] === 'cash-in') {
                if ($transactionData['gcash_balance_after'] < 0) {
                    throw new \Exception('Insufficient GCash balance for this transaction.');
                }
            } else {
                // cash-out
                if ($transactionData['cash_balance_after'] < 0) {
                    throw new \Exception('Insufficient cash balance for this transaction.');
                }
            }

            // Create the transaction
            $transaction = Transaction::create($transactionData);

            // Update user balances
            $this->updateUserBalances($user, $transactionData);

            // Add profit
            $this->addProfit($user, $transaction, $transactionData['fee_amount'], $transactionData['type']);

            return $transaction;
        });
    }

    /**
     * Update user balances after transaction.
     */
    private function updateUserBalances(User $user, array $balanceData): void
    {
        $user->update([
            'gcash_balance' => $balanceData['gcash_balance_after'],
            'cash_balance' => $balanceData['cash_balance_after'],
        ]);
    }

    /**
     * Add profit tracking.
     */
    private function addProfit(User $user, Transaction $transaction, float $feeAmount, string $type): void
    {
        $user->addProfit($feeAmount, $type, $transaction);
    }

    /**
     * Update an existing transaction and recalculate balances.
     */
    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $user = $transaction->user;

            // Revert the original transaction's balance changes
            $this->revertTransaction($transaction);

            // Revert profit
            $this->revertProfit($user, $transaction);

            // Update transaction data
            $transaction->update([
                'amount' => $data['amount'] ?? $transaction->amount,
                'fee_percentage' => $data['fee_percentage'] ?? $transaction->fee_percentage,
                'type' => $data['type'] ?? $transaction->type,
                'notes' => $data['notes'] ?? $transaction->notes,
            ]);

            // Recalculate fee amount
            $feeAmount = round(
                $transaction->amount * ($transaction->fee_percentage / 100),
                2
            );

            // Calculate new balances
            if ($transaction->type === 'cash-in') {
                $newGCashBalance = $transaction->gcash_balance_before - $transaction->amount;
                $newCashBalance = $transaction->cash_balance_before + $feeAmount;
            } else {
                $newGCashBalance = $transaction->gcash_balance_before + $transaction->amount;
                $newCashBalance = $transaction->cash_balance_before - ($transaction->amount - $feeAmount);
            }

            // Update transaction balances
            $transaction->update([
                'fee_amount' => $feeAmount,
                'gcash_balance_after' => $newGCashBalance,
                'cash_balance_after' => $newCashBalance,
            ]);

            // Update user balances
            $user->update([
                'gcash_balance' => $newGCashBalance,
                'cash_balance' => $newCashBalance,
            ]);

            // Re-add profit
            $this->addProfit($user, $transaction, $feeAmount, $transaction->type);

            return $transaction->refresh();
        });
    }

    /**
     * Delete a transaction and revert its balance changes.
     */
    public function deleteTransaction(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            $user = $transaction->user;

            // Revert balances
            $this->revertTransaction($transaction);

            // Revert profit
            $this->revertProfit($user, $transaction);

            return $transaction->delete();
        });
    }

    /**
     * Revert a transaction's balance changes.
     */
    private function revertTransaction(Transaction $transaction): void
    {
        $user = $transaction->user;

        // Restore previous balances
        $user->update([
            'gcash_balance' => $transaction->gcash_balance_before,
            'cash_balance' => $transaction->cash_balance_before,
        ]);

        // Remove balance history
        $user->balanceHistory()
            ->where('transaction_id', $transaction->id)
            ->delete();
    }

    /**
     * Revert profit for a transaction.
     */
    private function revertProfit(User $user, Transaction $transaction): void
    {
        $profitTracking = $user->profitTracking;

        if ($profitTracking) {
            $profitTracking->update([
                'total_profit' => $profitTracking->total_profit - $transaction->fee_amount,
                'profit_from_' . $transaction->type => $profitTracking->{'profit_from_' . $transaction->type} - $transaction->fee_amount,
                'transaction_count' => max(0, $profitTracking->transaction_count - 1),
            ]);
        }
    }
}
