<div class="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
    <div class="grid grid-cols-2 gap-4">
        <!-- Current Balances -->
        <div>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Current GCash Balance</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">₱{{ number_format($currentGCash, 2) }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Current Cash Balance</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">₱{{ number_format($currentCash, 2) }}</p>
        </div>
    </div>

    <div class="border-t border-gray-300 pt-4 dark:border-gray-600">
        <p class="mb-2 text-center text-sm font-semibold text-gray-600 dark:text-gray-400">After Transaction</p>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">New GCash Balance</p>
                <p class="text-lg font-bold {{ $newGCash >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    ₱{{ number_format($newGCash, 2) }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">New Cash Balance</p>
                <p class="text-lg font-bold {{ $newCash >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    ₱{{ number_format($newCash, 2) }}
                </p>
            </div>
        </div>
    </div>
</div>
