<x-filament-panels::page>
    <x-filament::section>
        <div class="mx-auto max-w-2xl space-y-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Welcome to GCash Tracker</h1>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                    Let's set up your account with your current balances to get started tracking your transactions.
                </p>
            </div>

            <form wire:submit="submit" class="space-y-6">
                {{ $this->form }}

                <div class="flex justify-center gap-3">
                    <x-filament::button
                        type="submit"
                        size="lg"
                        color="primary"
                    >
                        Complete Setup
                    </x-filament::button>
                </div>
            </form>
        </div>
    </x-filament::section>
</x-filament-panels::page>
