<div class="min-h-screen bg-slate-50/70 py-20 antialiased selection:bg-slate-900 selection:text-white">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

        {{-- Tracking Request Section --}}
        <div class="max-w-xl mb-20">
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
                Track Order
            </h1>
            <p class="mt-3 text-sm text-slate-500 font-normal leading-relaxed">
                Enter your order identification reference code to retrieve real-time dispatch logs and structural status events.
            </p>

            <form wire:submit.prevent="track" class="mt-8">
                <div class="flex flex-col sm:flex-row gap-2.5">
                    <div class="relative flex-grow">
                        <input
                            type="text"
                            wire:model.defer="orderNumber"
                            class="w-full rounded-xl border border-slate-200 bg-white px-5 py-3.5 text-sm font-medium text-slate-800 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900 @error('orderNumber') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            placeholder="Order Number (e.g., ORD-12345)"
                        >
                    </div>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white transition-all duration-200 hover:bg-slate-800 active:scale-[0.99] disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="track">Track Details</span>
                        <span wire:loading wire:target="track" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Searching...
                        </span>
                    </button>
                </div>
                @error('orderNumber')
                    <p class="mt-2 px-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </form>
        </div>

        {{-- Search Result Section --}}
        @if($searched)
            @if($order)

                {{-- Status Header block --}}
                <div class="mb-12 pb-6 border-b border-slate-200/80 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between animate__animated animate__fadeIn">
                    <div>
                        <p class="text-xs font-bold text-slate-400 tracking-widest uppercase font-mono">Reference: {{ $order->order_number }}</p>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900 mt-1">Fulfillment Status Overview</h2>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-1.5 text-xs font-semibold tracking-wider text-white uppercase shadow-sm">
                            <span class="h-1.5 w-1.5 rounded-full relative flex">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-slate-400"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-white"></span>
                            </span>
                            {{ $order->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-12 lg:grid-cols-3 items-start">
                    <div class="space-y-12 lg:col-span-2">

                        {{-- Status Timeline Flow --}}
                        @if($order->orderStatuses && $order->orderStatuses->count() > 0)
                            <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
                                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-8">Status Timeline</h3>
                                <div class="flow-root">
                                    <ul class="-mb-8">
                                        @foreach($order->orderStatuses as $index => $history)
                                            <li>
                                                <div class="relative pb-8">
                                                    @if($index !== $order->orderStatuses->count() - 1)
                                                        <span class="absolute top-5 left-2 -ml-px h-full w-0.5 bg-slate-100" aria-hidden="true"></span>
                                                    @endif
                                                    <div class="relative flex items-start space-x-4">
                                                        <div class="relative">
                                                            <div class="flex h-4 w-4 items-center justify-center rounded-full bg-slate-900 ring-4 ring-white">
                                                                <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                                            </div>
                                                        </div>
                                                        <div class="min-w-0 flex-1 -mt-1">
                                                            <div class="flex items-center justify-between gap-4">
                                                                <p class="text-sm font-bold text-slate-900 uppercase tracking-wide">{{ $history->status }}</p>
                                                                <p class="text-xs font-medium text-slate-400 font-mono whitespace-nowrap">{{ $history->created_at->format('M d, Y • h:i A') }}</p>
                                                            </div>
                                                            @if($history->notes)
                                                                <p class="mt-2 text-sm text-slate-500 font-normal leading-relaxed bg-slate-50/50 rounded-xl p-3 border border-slate-100">{{ $history->notes }}</p>
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

                        {{-- Product Line Items Section --}}
                        <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
                            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-6">Line Items Summary</h3>
                            <div class="divide-y divide-slate-100">
                                @foreach($order->items as $item)
                                    <div class="flex items-center gap-6 py-5 first:pt-0 last:pb-0">
                                        <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-xl border border-slate-100 bg-slate-50">
                                            @if ($item->variant && $item->variant->image_path)
                                                <img src="{{ asset('storage/' . $item->variant->image_path) }}" alt="{{ $item->product_name }}" class="h-full w-full object-cover">
                                            @elseif ($item->product && $item->product->primaryImage)
                                                <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" alt="{{ $item->product_name }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center bg-slate-50 text-slate-300">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-semibold text-slate-900 truncate leading-snug">{{ $item->product_name }}</h4>
                                            @if($item->variant_name)
                                                <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $item->variant_name }}</p>
                                            @endif
                                            <div class="flex items-center gap-3 text-xs text-slate-400 font-medium mt-1">
                                                <span>SKU: <span class="font-mono text-slate-600">{{ $item->product_sku }}</span></span>
                                                <span class="text-slate-200">•</span>
                                                <span>Qty {{ $item->quantity }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-slate-900">${{ number_format($item->subtotal, 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Metadata Split Columns (General & Shipping) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
                                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400">Order Details</h4>
                                <div class="space-y-3 text-sm">
                                    <div>
                                        <p class="text-xs text-slate-400 font-medium">Placed Timestamp</p>
                                        <p class="font-medium text-slate-800 mt-0.5">{{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 font-medium">Payment Channel</p>
                                        <p class="font-medium text-slate-800 mt-0.5">
                                            {{ $order->payment_method === 'stripe' ? 'Credit / Debit Card' : 'Cash on Delivery' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 font-medium">Payment Status</p>
                                        <span class="inline-flex items-center text-xs font-bold text-slate-900 uppercase mt-1">
                                            {{ $order->payment_status }}
                                        </span>
                                    </div>
                                    @if($order->tracking_number)
                                        <div class="pt-2 border-t border-slate-100">
                                            <p class="text-xs text-slate-400 font-medium">Tracking Identifier</p>
                                            <p class="font-mono text-sm font-bold text-slate-900 mt-0.5">{{ $order->tracking_number }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm space-y-4">
                                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400">Shipping Designation</h4>
                                <div class="text-sm space-y-2">
                                    <p class="font-bold text-slate-900">{{ $order->shipping_full_name }}</p>
                                    <p class="text-slate-500 font-medium text-xs">{{ $order->shipping_phone }}</p>
                                    <div class="text-slate-600 space-y-0.5 pt-1 border-t border-slate-100">
                                        <p class="font-medium text-slate-700">{{ $order->shipping_address_line_1 }}</p>
                                        @if($order->shipping_address_line_2)
                                            <p class="text-slate-400">{{ $order->shipping_address_line_2 }}</p>
                                        @endif
                                        <p class="font-medium text-slate-700">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                                        <p class="text-xxs font-bold uppercase tracking-widest text-slate-400 mt-1">{{ $order->shipping_country }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Right Column Sticky Summary Sidebar --}}
                    <div class="lg:col-span-1 lg:sticky lg:top-8">
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-5">Financial Summary</h3>
                            <div class="space-y-4 text-sm font-medium text-slate-600">
                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span class="text-slate-900 font-semibold">${{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                @if($order->discount_amount > 0)
                                    <div class="flex justify-between text-slate-900 font-semibold text-xs bg-slate-50 p-2 rounded-lg">
                                        <span>Discount Savings</span>
                                        <span>-${{ number_format($order->discount_amount, 2) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span>Shipping charges</span>
                                    <span>
                                        @if($order->shipping_cost > 0)
                                            <span class="text-slate-900 font-semibold">${{ number_format($order->shipping_cost, 2) }}</span>
                                        @else
                                            <span class="text-xs font-bold tracking-wide text-slate-900 uppercase">Free</span>
                                        @endif
                                    </span>
                                </div>
                                @if($order->tax_amount > 0)
                                    <div class="flex justify-between">
                                        <span>Taxation (VAT)</span>
                                        <span class="text-slate-900 font-semibold">${{ number_format($order->tax_amount, 2) }}</span>
                                    </div>
                                @endif

                                <div class="border-t border-slate-100 pt-4 mt-2">
                                    <div class="flex items-end justify-between">
                                        <span class="text-sm font-bold text-slate-900">Total Net Value</span>
                                        <span class="text-xl font-black text-slate-900 tracking-tight">
                                            ${{ number_format($order->total, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($order->customer_notes)
                                <div class="mt-6 pt-4 border-t border-slate-100">
                                    <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400">Customer Addendum</h4>
                                    <p class="mt-2 text-xs font-medium leading-relaxed text-slate-500">{{ $order->customer_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            @else
                {{-- Error/Not Found State --}}
                <div class="bg-white rounded-xl border border-slate-200 p-6 mt-8 max-w-xl animate__animated animate__headShake">
                    <div class="flex gap-4">
                        <svg class="h-5 w-5 text-slate-900 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">No Records Found</h3>
                            <p class="mt-1 text-sm text-slate-500 leading-relaxed font-normal">
                                We couldn't verify an entry for reference token "<span class="font-mono font-bold text-slate-800">{{ $orderNumber }}</span>". Check your statement blueprint configuration or customer profile dashboard parameters.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @endif

    </div>
</div>
