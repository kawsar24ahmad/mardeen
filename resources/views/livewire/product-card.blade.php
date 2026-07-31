<div
    class="group relative bg-white rounded-xl border border-gray-100 hover:border-transparent hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden p-2 md:p-3">
    <a href="{{ route('products.show', $product->slug) }}" class="block flex-1">
        {{-- Product Image Container --}}
        <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-50">
            @php
$imageUrl = $product->getFirstMediaUrl('products') ?: $product->getFirstMediaUrl();
            @endphp

            @if($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500 ease-out">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                    <span class="text-4xl font-bold text-gray-400 uppercase">{{ substr($product->name, 0, 1) }}</span>
                </div>
            @endif

            {{-- Badges --}}
            <div class="absolute top-2 left-2 flex flex-col gap-1.5 z-10">
                @if($product->is_featured)
                    <span
                        class="bg-amber-500 text-white text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-md shadow-sm">Featured</span>
                @endif
                @if($product->discount_percentage > 0)
                    <span
                        class="bg-rose-500 text-white text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-md shadow-sm">
                        -{{ $product->discount_percentage }}%
                    </span>
                @endif
                @if($product->stock_status === 'out_of_stock')
                    <span
                        class="bg-gray-900 text-white text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-md shadow-sm">
                        Sold Out
                    </span>
                @endif
            </div>
        </div>

        {{-- Product Information Details --}}
        <div class="pt-3 pb-2 px-1">
            <p class="text-[11px] font-medium text-blue-600 uppercase tracking-wider mb-1">
                {{ $product->category->name ?? 'Store' }}
            </p>
            <h3
                class="font-medium text-gray-800 text-sm md:text-base line-clamp-2 group-hover:text-blue-600 transition duration-200">
                {{ $product->name }}
            </h3>

            {{-- Reviews --}}
            @if($product->reviews_count > 0)
                <div class="flex items-center gap-1 mt-1.5 mb-2">
                    <div class="flex text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-3.5 h-3.5 {{ $i <= floor($product->average_rating) ? 'fill-current' : 'text-gray-200 fill-current' }}"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                            </svg>
                        @endfor
                    </div>
                    <span class="text-xs text-gray-400 font-medium">({{ $product->reviews_count }})</span>
                </div>
            @else
                <div class="h-1"></div>
            @endif

            {{-- Pricing --}}
            <div class="flex flex-wrap items-baseline gap-1.5 mt-auto">
                <span class="text-base md:text-lg font-bold text-gray-900">
                    <span
                        class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($product->price, 0) }}
                </span>
                @if($product->compare_price)
                    <span class="text-xs text-gray-400 line-through">
                        <span
                            class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($product->compare_price, 0) }}
                    </span>
                @endif
            </div>
        </div>
    </a>

    {{-- Call to Action Elements --}}
    <div class="mt-2 px-1 pb-1">
        @if($product->stock_status === 'in_stock')
            <div class="grid grid-cols-[auto_1fr] md:grid-cols-2 gap-2.5 w-full items-stretch">
                <button wire:click="addToCartClicked" type="button"
                    class="group/btn flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-800 transition-all active:scale-95 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-slate-600 transition-transform group-hover/btn:scale-110" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1 5h12m-9 0a1 1 0 102 0m4 0a1 1 0 102 0" />
                    </svg>
                </button>

                <button wire:click="buyNowClicked" type="button"
                    class="flex-1 flex items-center justify-center py-3 rounded-xl text-xs font-semibold transition-all active:scale-95 text-white bg-blue-600 hover:bg-indigo-700 shadow-sm">
                    Order Now
                </button>
            </div>
        @else
            <button disabled
                class="w-full bg-gray-100 text-gray-400 py-2.5 px-4 rounded-xl cursor-not-allowed font-medium text-xs uppercase tracking-wider">
                Out of Stock
            </button>
        @endif
    </div>

    {{-- Variant Selection Modal --}}
    @if($showVariantModal)
        <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-950/40 backdrop-blur-sm p-0 sm:p-4 sm:items-center"
            wire:click.self="closeModal">
            <div
                class="bg-white rounded-t-2xl w-full max-w-2xl max-h-[92vh] overflow-hidden flex flex-col shadow-2xl sm:rounded-2xl animate-in slide-in-from-bottom-8 sm:slide-in-from-bottom-0 sm:zoom-in-95 duration-200">

                {{-- Header --}}
                <div class="flex justify-between items-center border-b border-slate-100 px-4 py-3.5 sm:px-6 sm:py-4">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">
                        {{ $buyNowAction ? 'Quick Buy Checkout' : 'Configure Product Options' }}
                    </h2>
                    <button wire:click="closeModal"
                        class="h-8 w-8 inline-flex items-center justify-center rounded-full text-slate-400 hover:bg-slate-50 hover:text-slate-700 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="overflow-y-auto p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    @php
    $currentVariant = $selectedVariant ? $product->variants->firstWhere('id', $selectedVariant) : null;

    // Fix for Spatie Media Library URL in Modal
    $displayImage = $currentVariant?->getFirstMediaUrl('variant_images')
        ?: ($currentVariant?->getFirstMediaUrl()
            ?: ($product->getFirstMediaUrl('products') ?: $product->getFirstMediaUrl()));

    $displayPrice = $currentVariant?->price ?? $product->price;
    $displayCompare = $currentVariant?->compare_price ?? $product->compare_price;
                    @endphp

                    {{-- Product Preview --}}
                    <div class="flex gap-4 items-center md:block md:items-start">
                        <div
                            class="w-24 h-24 sm:w-32 sm:h-32 md:w-full md:h-auto md:aspect-square shrink-0 rounded-xl overflow-hidden bg-slate-50 border border-slate-100">
                            @if($displayImage)
                                <img src="{{ $displayImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-100">
                                    <span
                                        class="text-2xl md:text-4xl text-slate-400 font-bold uppercase">{{ substr($product->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0 md:mt-3">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-0.5">
                                {{ $product->category->name ?? 'Store General' }}
                            </p>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 mb-1 truncate md:whitespace-normal">
                                {{ $product->name }}
                            </h3>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-base sm:text-xl font-black text-slate-900 tracking-tight">
                                    <span
                                        class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($displayPrice, 0) }}
                                </span>
                                @if($displayCompare && $displayCompare > $displayPrice)
                                    <span class="text-xs text-slate-400 line-through font-medium">
                                        <span
                                            class="text-xs md:text-sm font-semibold text-blue-600 uppercase tracking-wide">TK.</span>{{ number_format($displayCompare, 0) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Controls / Selections --}}
                    <div class="flex flex-col justify-between gap-4 md:gap-0">
                        <div class="space-y-4">
                            {{-- Color / Product Variants --}}
                            @if($product->has_variants)
                                <div>
                                    <h4 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">
                                        Available Variants / Colors
                                    </h4>
                                    <div class="grid grid-cols-2 gap-2 max-h-[120px] overflow-y-auto pr-1">
                                        @foreach($product->variants->where('is_active', true) as $item)
                                            @php
            $variantTitle = collect([$item->color?->name, $item->size?->name])->filter()->implode(' • ') ?: $item->name;
                                            @endphp
                                            <button type="button" wire:click="selectVariant({{ $item->id }})"
                                                class="p-2 border text-left rounded-xl transition-all text-xs flex items-center gap-2 min-w-0 {{ $selectedVariant == $item->id ? 'border-slate-900 bg-slate-950 text-white font-semibold' : 'border-slate-200 bg-white text-slate-800' }}">
                                                @if($item->color)
                                                    <span class="w-3 h-3 rounded-full shrink-0 border border-black/10"
                                                        style="background-color: {{ $item->color->hex_code ?? '#ddd' }}"></span>
                                                @endif
                                                <span class="truncate">{{ $variantTitle }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Product Available Sizes --}}
                            @if($product->availableSizes->isNotEmpty())
                                <div>
                                    <h4 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">
                                        Select Size
                                    </h4>
                                    <div class="flex flex-wrap gap-2 max-h-[100px] overflow-y-auto">
                                        @foreach($product->availableSizes as $size)
                                            <button type="button" wire:click="selectProductSize({{ $size->id }})"
                                                class="px-3 py-1.5 border rounded-lg text-xs font-medium transition-all {{ $selectedProductSize == $size->id ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300' }}">
                                                {{ $size->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Quantity Counter --}}
                            <div>
                                <label
                                    class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">
                                    Select Quantity
                                </label>
                                <div class="inline-flex items-center border border-slate-200 rounded-xl bg-slate-50 p-1">
                                    <button wire:click="decrementQuantity" type="button"
                                        class="w-8 h-8 rounded-lg hover:bg-white flex items-center justify-center text-slate-600 transition shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="w-12 text-center font-bold text-sm text-slate-800">{{ $quantity }}</span>
                                    <button wire:click="incrementQuantity" type="button"
                                        class="w-8 h-8 rounded-lg hover:bg-white flex items-center justify-center text-slate-600 transition shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Validation & Action Buttons --}}
                        <div class="space-y-3 pt-3">
                            @if(session('error'))
                                <div
                                    class="bg-rose-50 border border-rose-100 text-rose-600 px-3 py-2 rounded-xl text-xs font-semibold">
                                    {{ session('error') }}
                                </div>
                            @endif
                            @if ($errors->any())
                                <div
                                    class="bg-rose-50 border border-rose-100 text-rose-600 px-3 py-2 rounded-xl text-xs font-medium space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <p>• {{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex gap-2">
                                <button wire:click="closeModal" type="button"
                                    class="flex-1 py-3 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 transition">
                                    Cancel
                                </button>
                                <button wire:click="confirmVariant" type="button"
                                    class="flex-1 py-3 text-xs font-bold rounded-xl transition text-center bg-slate-950 hover:bg-slate-800 text-white shadow-sm">
                                    {{ $buyNowAction ? 'Proceed to Buy' : 'Add To Cart' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
