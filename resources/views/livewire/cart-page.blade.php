<div class="bg-gray-50 min-h-screen py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb Navigation -->
        <nav class="mb-6 flex items-center space-x-2 text-sm font-medium text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Home</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900">Shopping Cart</span>
        </nav>

        <!-- Page Header -->
        <div class="mb-8 border-b border-gray-200 pb-5 flex items-baseline justify-between">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Shopping Cart</h1>
            @if(count($cart) > 0)
                <span class="text-sm text-gray-500">{{ count($cart) }} {{ count($cart) === 1 ? 'item' : 'items' }} selected</span>
            @endif
        </div>

        @if(count($cart) > 0)
            <div class="lg:grid lg:grid-cols-12 lg:items-start lg:gap-x-8">
                <!-- Cart Items Container (Left 7 Columns) -->
                <div class="lg:col-span-7 space-y-4">
                    <!-- Flash Messages -->
                    @if (session()->has('success'))
                        <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-r-md shadow-sm mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @foreach($cart as $cartKey => $item)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 transition-all hover:shadow-md">
                            <div class="flex items-start gap-4 sm:gap-6">
                                <!-- Product Image Thumbnail -->
                                <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                                    @if($item['image'])
                                        <img src="{{  $item['image']}}" alt="{{ $item['name'] }}" class="w-full h-full object-cover object-center">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300">
                                            <span class="text-2xl font-bold text-gray-400 uppercase">{{ substr($item['name'], 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Details & Mid-Row Meta -->
                                <div class="flex flex-1 flex-col justify-between self-stretch">
                                    <div>
                                        <div class="flex justify-between gap-2">
                                            <h3 class="font-semibold text-gray-900 text-base sm:text-lg hover:text-blue-600 transition-colors line-clamp-1">
                                                {{ $item['name'] }}
                                            </h3>
                                            <!-- Mobile Remove Icon Button -->
                                            <button wire:click="removeItem('{{ $cartKey }}')" class="sm:hidden text-gray-400 hover:text-red-500 transition-colors p-1" title="Remove item">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Variants Configuration Layout -->
                                        <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs sm:text-sm text-gray-500">
                                            @if(!empty($item['variant_name']))
                                                <span class="flex items-center gap-1"><span class="font-medium text-gray-700">Variant:</span> {{ $item['variant_name'] }}</span>
                                            @endif
                                            @if(!empty($item['color']))
                                                <span class="flex items-center gap-1"><span class="font-medium text-gray-700">Color:</span> {{ $item['color'] }}</span>
                                            @endif
                                            @if(!empty($item['product_size']))
                                                <span class="flex items-center gap-1"><span class="font-medium text-gray-700">Size:</span> {{ $item['product_size'] }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Unit Price Display -->
                                    <div class="mt-2 text-sm text-gray-500 font-medium">
                                        <span class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($item['price'], 2) }} <span class="text-xs font-normal text-gray-400">/ each</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Container inside Item Card (Controls & Line Pricing) -->
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <!-- Premium Custom Quantity Selector Group Capsule -->
                                    <div class="flex items-center border border-gray-300 rounded-lg bg-gray-50 p-0.5 shadow-sm">
                                        <button wire:click="updateQuantity('{{ $cartKey }}', {{ $item['quantity'] - 1 }})"
                                                class="w-8 h-8 rounded-md bg-white border border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-100 flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                                                {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <span class="w-10 text-center font-semibold text-gray-800 text-sm select-none">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateQuantity('{{ $cartKey }}', {{ $item['quantity'] + 1 }})"
                                                class="w-8 h-8 rounded-md bg-white border border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-100 flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Desktop Remove text button -->
                                    <button wire:click="removeItem('{{ $cartKey }}')" class="hidden sm:flex items-center gap-1.5 text-sm text-gray-400 hover:text-red-600 font-medium transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Remove
                                    </button>
                                </div>

                                <!-- Total Item Cost Segment -->
                                <div class="text-right">
                                    <span class="text-xs font-medium text-gray-400 block sm:inline">Subtotal:</span>
                                    <span class="text-base sm:text-lg font-bold text-gray-900 tracking-tight ml-1">
                                        <span class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($item['price'] * $item['quantity'], 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Secondary Operational Control Strip -->
                    <div class="flex justify-between items-center pt-2">
                        <button wire:click="clearCart" wire:confirm="Are you sure you want to clear the cart?"
                            class="text-sm font-medium text-red-600 hover:text-red-700 border border-transparent hover:border-red-200 px-3 py-2 rounded-lg transition-all hover:bg-red-50">
                            Clear Shopping Cart
                        </button>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 group transition-colors">
                            <span class="transform group-hover:-translate-x-1 transition-transform">←</span> Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Order Sidebar Summary Block (Right 5 Columns) -->
                <div class="mt-10 lg:mt-0 lg:col-span-5">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8 sticky top-6">
                        <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-4 mb-6">Order Summary</h2>

                        <div class="space-y-4 mb-6 text-sm">
                            <div class="flex justify-between items-center text-gray-600">
                                <span>Subtotal ({{ count($cart) }} {{ count($cart) === 1 ? 'item' : 'items' }})</span>
                                <span class="font-semibold text-gray-900"><span class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($this->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-600">
                                <span>Estimated Shipping</span>
                                <span class="font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded text-xs">Calculated at checkout</span>
                            </div>
                        </div>

                        <!-- Grand Pricing Total Node -->
                        <div class="border-t border-gray-200 pt-5 mb-6">
                            <div class="flex justify-between items-baseline">
                                <span class="text-base font-bold text-gray-900">Total Estimated</span>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-blue-600 tracking-tight">
                                        <span class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($this->subtotal, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Primary Checkout Button CTA -->
                        <a href="{{ route('checkout') }}"
                            class="block w-full bg-blue-600 text-white text-center py-3.5 px-6 rounded-xl hover:bg-blue-700 shadow-sm hover:shadow transition duration-150 font-bold tracking-wide focus:outline-none focus:ring-4 focus:ring-blue-100">
                            Proceed to Checkout
                        </a>

                        <!-- Multi-Tier Safety & Conversion Guarantee Badges -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="space-y-3.5">
                                <div class="flex items-center gap-3 text-sm text-gray-600 font-medium">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <span>100% Encrypted Secure Checkout</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-600 font-medium">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span>Free Delivery on orders above $100</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-600 font-medium">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <span>30-Day Hassle-Free Returns</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Fallback Empty State Canvas -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 sm:p-20 text-center max-w-2xl mx-auto my-8">
                <div class="w-24 h-24 bg-gray-50 border border-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight mb-2">Your shopping cart is empty</h2>
                <p class="text-gray-500 max-w-sm mx-auto mb-8 text-sm sm:text-base">Looks like you haven't made your choice yet. Explore our latest arrivals to find something special!</p>
                <a href="{{ route('products.index') }}"
                    class="inline-flex items-center justify-center bg-blue-600 text-white px-8 py-3.5 rounded-xl hover:bg-blue-700 shadow-sm font-semibold transition tracking-wide focus:outline-none focus:ring-4 focus:ring-blue-100">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
</div>
