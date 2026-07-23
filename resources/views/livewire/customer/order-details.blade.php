<div class="min-h-screen bg-slate-50/50 py-12 antialiased">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="mb-10">
            <nav class="mb-4 text-xs font-medium tracking-wide uppercase text-slate-400">
                <ol class="flex items-center gap-2">
                    <li>
                        <a href="{{ route('customer.dashboard') }}"
                            class="transition-colors hover:text-indigo-600">Account</a>
                    </li>
                    <li>
                        <svg class="h-3 w-3 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </li>
                    <li>
                        <a href="{{ route('customer.orders') }}"
                            class="transition-colors hover:text-indigo-600">Orders</a>
                    </li>
                    <li>
                        <svg class="h-3 w-3 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </li>
                    <li class="font-semibold text-slate-600">{{ $order->order_number }}</li>
                </ol>
            </nav>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Order Details</h1>
                    <p class="mt-2 text-sm text-slate-500">Manage and review your processed transaction history.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('orders.invoice.download', $order->id) }}"
                        class="group inline-flex items-center justify-center gap-2 rounded-xl border border-indigo-100 bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-indigo-600/10 transition-all duration-200 hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 active:scale-[0.98]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor"
                            class="h-4 w-4 text-indigo-200 transition-colors group-hover:text-white">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Download Invoice
                    </a>
                    <span class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2.5 text-sm font-bold tracking-wide uppercase {{
    $order->status === 'delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' :
    ($order->status === 'cancelled' ? 'bg-rose-50 text-rose-700 border border-rose-200/60' :
        ($order->status === 'shipped' ? 'bg-sky-50 text-sky-700 border border-sky-200/60' :
            'bg-amber-50 text-amber-700 border border-amber-200/60'))
                    }}">
                        <span class="h-2 w-2 rounded-full currentColor {{
    $order->status === 'delivered' ? 'bg-emerald-500' :
    ($order->status === 'cancelled' ? 'bg-rose-500' :
        ($order->status === 'shipped' ? 'bg-sky-500' : 'bg-amber-500'))
                        }}"></span>
                        {{ $order->status }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Main Grid Content Layout --}}
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="space-y-8 lg:col-span-2">

                {{-- Order Meta Information Card --}}
                <div
                    class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-shadow hover:shadow-md">
                    <div class="border-b border-slate-50 bg-slate-50/50 px-6 py-4">
                        <h2 class="text-lg font-bold text-slate-900">General Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div class="space-y-1">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Order Reference
                                </p>
                                <p class="font-mono text-base font-bold text-slate-800">{{ $order->order_number }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Placed
                                    Timestamp</p>
                                <p class="text-base font-medium text-slate-800">
                                    {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                            </div>
                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Payment Status
                                </p>
                                <div>
                                    <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold uppercase tracking-wider {{
    $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100'
                                    }}">
                                        {{ $order->payment_status }}
                                    </span>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Payment Channel
                                </p>
                                <p class="text-base font-medium text-slate-800">
                                    {{ $order->payment_method === 'stripe' ? 'Credit / Debit Card' : 'Cash on Delivery' }}
                                </p>
                            </div>
                            @if($order->tracking_number)
                                <div class="sm:col-span-2 space-y-1 rounded-xl bg-slate-50 p-4 border border-slate-100">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tracking Identifier
                                    </p>
                                    <p class="font-mono text-base font-bold text-indigo-600 tracking-normal">
                                        {{ $order->tracking_number }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Order Line Items Table Card --}}
                <div
                    class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-shadow hover:shadow-md">
                    <div class="border-b border-slate-50 bg-slate-50/50 px-6 py-4">
                        <h2 class="text-lg font-bold text-slate-900">Line Items Summary</h2>
                    </div>
                    <div class="divide-y divide-slate-100 p-6">
                        @foreach($order->items as $item)
                            <div class="flex flex-col gap-4 py-5 first:pt-0 last:pb-0 sm:flex-row sm:items-center">
                                <div
                                    class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-xl border border-slate-100 bg-slate-50 shadow-inner">
                                    @if ($item->variant && $item->variant->getFirstMediaUrl('variant_images'))
                                        <img src="{{ $item->variant->getFirstMediaUrl('variant_images') }}"
                                            alt="{{ $item->product_name }}"
                                            class="h-full w-full object-cover transition duration-300 hover:scale-105">
                                    @elseif ($item->product && $item->product->getFirstMediaUrl('products'))
                                        <img src="{{ $item->product->getFirstMediaUrl('products') }}"
                                            alt="{{ $item->product_name }}"
                                            class="h-full w-full object-cover transition duration-300 hover:scale-105">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-slate-100 text-slate-400">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            <div class="flex-1 space-y-1.5">
                                <!-- Product Title -->
                                <h3 class="font-semibold leading-snug text-slate-900 transition-colors hover:text-indigo-600">
                                    {{ $item->product_name }}
                                </h3>

                                <!-- Badges Container (Variant & Size) -->
                                @if($item->variant_name || $item->product_size)
                                    <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                        @if($item->variant_name)
                                            <span class="inline-block rounded bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                                                Variant: {{ $item->variant_name }}
                                            </span>
                                        @endif

                                        @if($item->product_size)
                                            <span
                                                class="inline-block rounded bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                Size: {{ $item->product_size }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Product Meta Details (SKU, Qty, Price) -->
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 pt-1 text-xs font-medium text-slate-400">
                                    <span>SKU: <span class="font-mono text-slate-600">{{ $item->product_sku }}</span></span>
                                    <span class="hidden text-slate-300 sm:inline">•</span>
                                    <span>Qty: <span class="text-slate-600">{{ $item->quantity }}</span> ×
                                        ${{ number_format($item->price, 2) }}</span>
                                </div>
                            </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-base font-bold text-slate-900">${{ number_format($item->subtotal, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Delivery Destination Card --}}
                <div
                    class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-shadow hover:shadow-md">
                    <div class="border-b border-slate-50 bg-slate-50/50 px-6 py-4">
                        <h2 class="text-lg font-bold text-slate-900">Shipping Designation</h2>
                    </div>
                    <div class="p-6 text-sm leading-relaxed text-slate-600">
                        <p class="text-base font-bold text-slate-900">{{ $order->shipping_full_name }}</p>
                        <p class="font-medium inline-flex items-center gap-1.5 mt-1 text-slate-500">
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            {{ $order->shipping_phone }}
                        </p>
                        <div
                            class="mt-4 rounded-xl bg-slate-50/60 p-4 border border-slate-100 text-slate-700 space-y-0.5">
                            <p class="font-medium">{{ $order->shipping_address_line_1 }}</p>
                            @if($order->shipping_address_line_2)
                                <p class="text-slate-500">{{ $order->shipping_address_line_2 }}</p>
                            @endif
                            <p class="font-medium">{{ $order->shipping_city }}, {{ $order->shipping_state }}
                                {{ $order->shipping_postal_code }}</p>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 pt-1">
                                {{ $order->shipping_country }}</p>
                        </div>
                    </div>
                </div>

                {{-- Audit/Order History Workflow Timeline --}}
                @if($order->orderStatuses->count() > 0)
                    <div
                        class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-shadow hover:shadow-md">
                        <div class="border-b border-slate-50 bg-slate-50/50 px-6 py-4">
                            <h2 class="text-lg font-bold text-slate-900">Status Timeline</h2>
                        </div>
                        <div class="p-6">
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    @foreach($order->orderStatuses as $index => $history)
                                        <li>
                                            <div class="relative pb-8">
                                                @if($index !== $order->orderStatuses->count() - 1)
                                                    <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-slate-200"
                                                        aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex items-start space-x-4">
                                                    <div class="relative">
                                                        <div
                                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 ring-8 ring-white">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5">
                                                        <div
                                                            class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                                            <p class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                                                                {{ $history->status }}</p>
                                                            <p class="text-xs font-medium text-slate-400">
                                                                {{ $history->created_at->format('M d, Y • h:i A') }}</p>
                                                        </div>
                                                        @if($history->notes)
                                                            <p
                                                                class="mt-2 text-sm text-slate-600 bg-slate-50 rounded-xl p-3 border border-slate-100/70">
                                                                {{ $history->notes }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Checkout Financial Balance Sticky Sidebar --}}
            <div class="lg:col-span-1">
                <div class="sticky top-8 space-y-6">
                    <div
                        class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-shadow hover:shadow-md">
                        <div class="border-b border-slate-50 bg-slate-50/50 px-6 py-4">
                            <h2 class="text-lg font-bold text-slate-900">Financial Summary</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3.5 text-sm font-medium text-slate-600">
                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span class="text-slate-900">${{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                @if($order->discount_amount > 0)
                                    <div
                                        class="flex justify-between text-emerald-600 bg-emerald-50/50 px-2 py-1 rounded-lg border border-emerald-100/50">
                                        <span>Discount Savings</span>
                                        <span class="font-bold">-${{ number_format($order->discount_amount, 2) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span>Shipping charges</span>
                                    <span>
                                        @if($order->shipping_cost > 0)
                                            <span
                                                class="text-slate-900">${{ number_format($order->shipping_cost, 2) }}</span>
                                        @else
                                            <span
                                                class="text-xs font-extrabold tracking-wider text-emerald-600 uppercase bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">Free</span>
                                        @endif
                                    </span>
                                </div>
                                @if($order->tax_amount > 0)
                                    <div class="flex justify-between">
                                        <span>Taxation (VAT)</span>
                                        <span class="text-slate-900">${{ number_format($order->tax_amount, 2) }}</span>
                                    </div>
                                @endif

                                <div class="border-t border-slate-100 pt-4 mt-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-base font-bold text-slate-900">Total Net Value</span>
                                        <span class="text-2xl font-black tracking-tight text-indigo-600">
                                            ${{ number_format($order->total, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($order->customer_notes)
                                <div class="mt-6 rounded-xl border border-amber-100 bg-amber-50/30 p-4">
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800">Customer Addendum
                                    </h3>
                                    <p class="mt-1.5 text-sm font-medium leading-relaxed text-amber-900/80">
                                        {{ $order->customer_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
