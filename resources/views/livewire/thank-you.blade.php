<div class="min-h-screen bg-gray-50 py-12">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

        <!-- Hero Thank You Banner -->
        <div class="mb-8 rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-sm">
            <div
                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600 ring-8 ring-green-50">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Thank you for your order!
            </h1>
            <p class="mt-2 text-base text-gray-500">
                We've received your request and are preparing it for shipping.
            </p>
            <div
                class="mt-4 inline-flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 px-4 py-1.5 text-sm font-medium text-gray-700">
                <span>Order Reference:</span>
                <span class="font-bold text-gray-900">#{{ $order->order_number }}</span>
            </div>
        </div>

        <!-- Main Order Details Card -->
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">

            <!-- Card Header & Actions -->
            <div
                class="flex flex-col gap-4 border-b border-gray-100 bg-gray-50/50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Placed On</span>
                    <p class="text-sm font-medium text-gray-800">{{ $order->created_at->format('M d, Y • h:i A') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span
                        class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold capitalize text-amber-700 ring-1 ring-inset ring-amber-600/20">
                        Payment: {{ $order->payment_status }}
                    </span>
                    <a href="{{ route('orders.invoice.download', $order->id) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Download Invoice
                    </a>
                </div>
            </div>

            <!-- Ordered Items List -->
        <ul class="divide-y divide-gray-100 px-6">
            @foreach($order->items as $item)
                <li class="flex items-center justify-between gap-4 py-5">
                    <div class="flex items-center gap-4">
                        <!-- Thumbnail -->
                        <div class="h-14 w-14 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                            @if($item->variant && $item->variant->getFirstMediaUrl('variant_images'))
                                <img src="{{ $item->variant->getFirstMediaUrl('variant_images')}}" alt="{{ $item->product_name }}"
                                    class="h-full w-full object-cover object-center">
                            @elseif($item->product && $item->product->getFirstMediaUrl('products'))
                                <img src="{{ $item->product->getFirstMediaUrl('products') }}"
                                    alt="{{ $item->product_name }}" class="h-full w-full object-cover object-center">
                            @else
                                <div
                                    class="flex h-full w-full items-center justify-center bg-gray-100 text-xs font-bold uppercase text-gray-400">
                                    {{ substr($item->product_name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <!-- Title & Badges -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">{{ $item->product_name }}</h3>

                            <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                @if($item->variant_name)
                                    <span class="inline-block rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                                        {{ $item->variant_name }}
                                    </span>
                                @endif

                                @if($item->product_size)
                                    <span
                                        class="inline-block rounded bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                        Size: {{ $item->product_size }}
                                    </span>
                                @endif
                            </div>

                            <p class="mt-1 text-xs text-gray-500">
                                Qty: <span class="font-medium text-gray-700">{{ $item->quantity }}</span> × TK.
                                {{ number_format($item->price, 2) }}
                            </p>
                        </div>
                    </div>

                    <!-- Item Subtotal -->
                    <div class="text-right font-bold text-gray-900 text-sm sm:text-base">
                        <span class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($item->subtotal, 2) }}
                    </div>
                </li>
            @endforeach
        </ul>

            <!-- Pricing Calculation Block -->
            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-5 space-y-2.5">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span class="font-medium text-gray-900"><span class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($order->subtotal, 2) }}</span>
                </div>

                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-sm text-green-600">
                        <span>Discount Savings</span>
                        <span class="font-semibold">-<span class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif

                <div class="flex justify-between text-sm text-gray-600">
                    <span>Shipping Fee</span>
                    <span class="font-medium text-gray-900"><span class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($order->shipping_cost, 2) }}</span>
                </div>

                <div class="flex justify-between border-t border-gray-200 pt-3 text-base font-bold text-gray-900">
                    <span>Total Amount Paid</span>
                    <span class="text-lg text-blue-600"><span class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Information Cards Grid -->
        <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Shipping Information Card -->
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-3 flex items-center gap-2 text-sm font-bold text-gray-900">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    Shipping Address
                </div>
                <div class="space-y-1 text-sm text-gray-600">
                    <p class="font-semibold text-gray-900">{{ $order->shipping_full_name }}</p>
                    <p>{{ $order->shipping_address_line_1 }}</p>
                    <p class="pt-1 text-xs text-gray-500">Phone: <span
                            class="font-medium text-gray-700">{{ $order->shipping_phone }}</span></p>
                </div>
            </div>

            <!-- Payment & Notes Card -->
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-3 flex items-center gap-2 text-sm font-bold text-gray-900">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                    Payment Method
                </div>
                <p class="text-sm font-semibold uppercase text-gray-800">{{ $order->payment_method }}</p>

                @if($order->customer_notes)
                    <div class="mt-4 border-t border-gray-100 pt-3">
                        <span class="text-xs font-semibold uppercase text-gray-400">Order Notes</span>
                        <p class="mt-1 text-xs italic text-gray-600">"{{ $order->customer_notes }}"</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer Call to Action -->
        <div class="text-center">
            <a href="/" wire:navigate
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-8 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100">
                Continue Shopping
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

    </div>
</div>
