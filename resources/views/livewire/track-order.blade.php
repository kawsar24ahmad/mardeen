<div class="min-h-screen bg-gray-50 py-12 antialiased">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

        <!-- Tracking Request Header & Search Form -->
        <div class="mx-auto mb-12 max-w-2xl text-center">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Track Your Order
            </h1>
            <p class="mt-2 text-sm text-gray-500">
                Enter your order reference code below to view real-time fulfillment status and delivery updates.
            </p>

            <form wire:submit.prevent="track" class="mt-8">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-grow">
                        <input
                            type="text"
                            wire:model.defer="orderNumber"
                            class="w-full rounded-xl border border-gray-200 bg-white px-5 py-3.5 text-sm font-medium text-gray-800 placeholder-gray-400 shadow-sm transition-all focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('orderNumber') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            placeholder="Order Reference (e.g., ORD-12345)"
                        >
                    </div>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700 active:scale-[0.98] disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="track" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            Track Order
                        </span>
                        <span wire:loading wire:target="track" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Searching...
                        </span>
                    </button>
                </div>
                @error('orderNumber')
                    <p class="mt-2 text-left text-xs font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </form>
        </div>

        <!-- Search Results -->
        @if($searched)
            @if($order)

                <!-- Status Banner -->
                <div class="mb-8 flex flex-col gap-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Order Reference</span>
                        <h2 class="text-xl font-bold text-gray-900">#{{ $order->order_number }}</h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-1.5 text-xs font-bold uppercase tracking-wide text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-600"></span>
                            </span>
                            {{ $order->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Left Section: Timeline, Items & Shipping -->
                    <div class="space-y-8 lg:col-span-2">

                        <!-- Status Timeline -->
                        @if($order->orderStatuses && $order->orderStatuses->count() > 0)
                            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                                <h3 class="mb-6 text-xs font-bold uppercase tracking-wider text-gray-400">Fulfillment Progress</h3>
                                <div class="flow-root">
                                    <ul class="-mb-8">
                                        @foreach($order->orderStatuses as $index => $history)
                                            <li>
                                                <div class="relative pb-8">
                                                    @if($index !== $order->orderStatuses->count() - 1)
                                                        <span class="absolute left-3 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                    @endif
                                                    <div class="relative flex items-start gap-4">
                                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-white ring-4 ring-white">
                                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                            </svg>
                                                        </div>
                                                        <div class="min-w-0 flex-1 pt-0.5">
                                                            <div class="flex flex-col justify-between gap-1 sm:flex-row sm:items-center">
                                                                <p class="text-sm font-bold uppercase tracking-wide text-gray-900">{{ $history->status }}</p>
                                                                <time class="text-xs font-medium text-gray-400">{{ $history->created_at->format('M d, Y • h:i A') }}</time>
                                                            </div>
                                                            @if($history->notes)
                                                                <p class="mt-2 rounded-xl border border-gray-100 bg-gray-50/60 p-3 text-xs leading-relaxed text-gray-600">
                                                                    {{ $history->notes }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Line Items -->
                        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                            <h3 class="mb-6 text-xs font-bold uppercase tracking-wider text-gray-400">Ordered Items Summary</h3>
                            <ul class="divide-y divide-gray-100">
                               @foreach($order->items as $item)
    <li class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                @if ($item->variant && $item->variant->image_path)
                    <img src="{{ asset('storage/' . $item->variant->image_path) }}" alt="{{ $item->product_name }}" class="h-full w-full object-cover object-center">
                @elseif ($item->product && $item->product->primaryImage)
                    <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" alt="{{ $item->product_name }}" class="h-full w-full object-cover object-center">
                @else
                    <div class="flex h-full w-full items-center justify-center bg-gray-100 text-xs font-bold uppercase text-gray-400">
                        {{ substr($item->product_name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-900">{{ $item->product_name }}</h4>

                <!-- Variant and Size Badges -->
                <div class="mt-1 flex flex-wrap items-center gap-1.5">
                    @if($item->variant_name)
                        <span class="inline-block rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                           Variant: {{ $item->variant_name }}
                        </span>
                    @endif

                    @if($item->product_size)
                        <span class="inline-block rounded bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            Size: {{ $item->product_size }}
                        </span>
                    @endif
                </div>

                <div class="mt-1 flex items-center gap-2 text-xs text-gray-500">
                    <span>SKU: <span class="font-mono text-gray-700">{{ $item->product_sku }}</span></span>
                    <span>•</span>
                    <span>Qty: <span class="font-medium text-gray-700">{{ $item->quantity }}</span></span>
                </div>
            </div>
        </div>
        <div class="text-right text-sm font-bold text-gray-900">
            TK. {{ number_format($item->subtotal, 2) }}
        </div>
    </li>
@endforeach
                            </ul>
                        </div>

                        <!-- Order & Shipping Metadata Cards -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Order Info -->
                            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                                <h4 class="mb-4 text-xs font-bold uppercase tracking-wider text-gray-400">Order Information</h4>
                                <dl class="space-y-3 text-sm">
                                    <div>
                                        <dt class="text-xs text-gray-400">Placed On</dt>
                                        <dd class="font-medium text-gray-800">{{ $order->created_at->format('M d, Y • h:i A') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-400">Payment Method</dt>
                                        <dd class="font-medium uppercase text-gray-800">{{ $order->payment_method === 'stripe' ? 'Credit / Debit Card' : $order->payment_method }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-400">Payment Status</dt>
                                        <dd class="font-semibold capitalize text-gray-900">{{ $order->payment_status }}</dd>
                                    </div>
                                    @if($order->tracking_number)
                                        <div class="border-t border-gray-100 pt-2">
                                            <dt class="text-xs text-gray-400">Carrier Tracking Code</dt>
                                            <dd class="font-mono text-sm font-bold text-blue-600">{{ $order->tracking_number }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            <!-- Shipping Address -->
                            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                                <h4 class="mb-4 text-xs font-bold uppercase tracking-wider text-gray-400">Shipping Address</h4>
                                <div class="space-y-1 text-sm text-gray-600">
                                    <p class="font-bold text-gray-900">{{ $order->shipping_full_name }}</p>
                                    <p class="text-xs font-medium text-gray-500">Phone: {{ $order->shipping_phone }}</p>
                                    <div class="border-t border-gray-100 pt-2 text-xs leading-relaxed text-gray-600">
                                        <p>{{ $order->shipping_address_line_1 }}</p>
                                        @if($order->shipping_address_line_2)
                                            <p>{{ $order->shipping_address_line_2 }}</p>
                                        @endif
                                        <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                                        <p class="font-semibold uppercase text-gray-500">{{ $order->shipping_country }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Section: Sticky Financial Summary Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-8 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                            <h3 class="mb-5 text-xs font-bold uppercase tracking-wider text-gray-400">Financial Summary</h3>
                            <div class="space-y-3 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span class="font-semibold text-gray-900">TK. {{ number_format($order->subtotal, 2) }}</span>
                                </div>

                                @if($order->discount_amount > 0)
                                    <div class="flex justify-between text-green-600">
                                        <span>Discount Savings</span>
                                        <span class="font-semibold">-TK. {{ number_format($order->discount_amount, 2) }}</span>
                                    </div>
                                @endif

                                <div class="flex justify-between">
                                    <span>Shipping</span>
                                    <span>
                                        @if($order->shipping_cost > 0)
                                            <span class="font-semibold text-gray-900">TK. {{ number_format($order->shipping_cost, 2) }}</span>
                                        @else
                                            <span class="font-bold uppercase text-green-600 text-xs">Free</span>
                                        @endif
                                    </span>
                                </div>

                                @if($order->tax_amount > 0)
                                    <div class="flex justify-between">
                                        <span>Tax / VAT</span>
                                        <span class="font-semibold text-gray-900">TK. {{ number_format($order->tax_amount, 2) }}</span>
                                    </div>
                                @endif

                                <div class="border-t border-gray-100 pt-4">
                                    <div class="flex items-baseline justify-between">
                                        <span class="font-bold text-gray-900">Total Paid</span>
                                        <span class="text-xl font-extrabold text-blue-600">
                                            TK. {{ number_format($order->total, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($order->customer_notes)
                                <div class="mt-6 border-t border-gray-100 pt-4">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Order Notes</h4>
                                    <p class="mt-1 text-xs italic text-gray-500">"{{ $order->customer_notes }}"</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            @else
                <!-- Empty / Error State -->
                <div class="mx-auto max-w-xl rounded-2xl border border-rose-100 bg-white p-6 shadow-sm">
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Order Not Found</h3>
                            <p class="mt-1 text-xs leading-relaxed text-gray-500">
                                We couldn't find any order matching reference code "<span class="font-mono font-semibold text-gray-800">{{ $orderNumber }}</span>". Please verify the code on your confirmation receipt and try again.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @endif

    </div>
</div>
