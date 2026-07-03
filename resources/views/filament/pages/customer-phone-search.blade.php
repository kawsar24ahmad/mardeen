<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Search by Phone Number</x-slot>
            <x-slot name="description">
                Enter the customer's phone number (any format is accepted — spaces, dashes, country code, etc.).
            </x-slot>

            <form wire:submit.prevent="search" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="phone" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">
                        Phone number
                    </label>
                    <input
                        id="phone"
                        type="tel"
                        wire:model.defer="phone"
                        placeholder="e.g. +880 1712-345678 or 01712345678"
                        autocomplete="off"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    />
                </div>

                <div class="flex gap-2">
                    <x-filament::button type="submit" icon="heroicon-m-magnifying-glass">
                        Search
                    </x-filament::button>

                    @if ($searchedPhone)
                        <x-filament::button
                            color="gray"
                            type="button"
                            wire:click="clearSearch"
                            icon="heroicon-m-x-mark"
                        >
                            Clear
                        </x-filament::button>
                    @endif
                </div>
            </form>
        </x-filament::section>

        @if ($searchedPhone !== null)
            <x-filament::section>
                <x-slot name="heading">
                    Results for "{{ $searchedPhone }}"
                </x-slot>
                <x-slot name="description">
                    {{ $results->count() }} customer(s) matched.
                </x-slot>

                @if ($results->isEmpty())
                    <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        No customers found with that phone number.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($results as $customer)
                            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <div class="text-base font-semibold text-gray-900 dark:text-white">
                                            {{ $customer->name ?? 'Unnamed customer' }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $customer->email }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            📞 {{ $customer->phone }}
                                        </div>
                                    </div>

                                    <div class="text-right text-sm">
                                        <div>
                                            <span class="font-semibold">{{ $customer->order_count ?? $customer->orders->count() }}</span>
                                            <span class="text-gray-500">orders</span>
                                        </div>
                                        <div class="text-gray-600 dark:text-gray-400">
                                            Total spent:
                                            <span class="font-semibold">
                                                {{ $formatOrderTotal($customer->total_spent ?? $customer->orders->where('payment_status', 'paid')->sum('total')) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if ($customer->orders->isNotEmpty())
                                    <div class="mt-4 overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-800">
                                                <tr>
                                                    <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Order #</th>
                                                    <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Date</th>
                                                    <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Total</th>
                                                    <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Payment</th>
                                                    <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-300">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                                @foreach ($customer->orders as $order)
                                                    <tr>
                                                        <td class="px-3 py-2 font-mono text-xs">{{ $order->order_number }}</td>
                                                        <td class="px-3 py-2">{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                                                        <td class="px-3 py-2">{{ $formatOrderTotal($order->total) }}</td>
                                                        <td class="px-3 py-2">
                                                            <span class="rounded-full px-2 py-0.5 text-xs
                                                                @switch($order->payment_status)
                                                                    @case('paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                                                    @case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @break
                                                                    @default bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200
                                                                @endswitch">
                                                                {{ ucfirst($order->payment_status ?? 'unknown') }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <span class="rounded-full px-2 py-0.5 text-xs
                                                                @switch($order->status)
                                                                    @case('delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                                                    @case('shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @break
                                                                    @case('processing') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 @break
                                                                    @case('cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                                                                    @default bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                                @endswitch">
                                                                {{ ucfirst($order->status ?? 'pending') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>