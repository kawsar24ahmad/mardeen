<div
    class="group relative bg-white rounded-xl border border-gray-100 hover:border-transparent hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden p-2 md:p-3">
    <a href="{{ route('products.show', $product->slug) }}" class="block flex-1">
        {{-- Product Image Container --}}
        <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-50">
            @if($product->primaryImage)
                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500 ease-out">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                    <span class="text-4xl font-bold text-gray-400 uppercase">{{ substr($product->name, 0, 1) }}</span>
                </div>
            @endif

            {{-- Dynamic Badges over Image --}}
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
                        class="bg-gray-900 text-white text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-md shadow-sm">Sold
                        Out</span>
                @endif
            </div>
        </div>

        {{-- Product Information Details --}}
        <div class="pt-3 pb-2 px-1">
            <p class="text-[11px] font-medium text-blue-600 uppercase tracking-wider mb-1">
                {{ $product->category->name }}</p>
            <h3
                class="font-medium text-gray-800 text-sm md:text-base line-clamp-2  group-hover:text-blue-600 transition duration-200">
                {{ $product->name }}
            </h3>

            {{-- Reviews and Star Ratings --}}
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
                <div class="h-1"></div> {{-- Visual layout spacer --}}
            @endif

            {{-- Base and Comparison Pricing --}}
            <div class="flex flex-wrap items-baseline gap-1.5 mt-auto">
                <span class="text-base md:text-lg font-bold text-gray-900">
                    TK. {{ number_format($product->price, 0) }}
                </span>
                @if($product->compare_price)
                    <span class="text-xs text-gray-400 line-through">
                        TK. {{ number_format($product->compare_price, 0) }}
                    </span>
                @endif
            </div>
        </div>
    </a>

    {{-- Interactive Call to Action Elements --}}
    <div class="mt-2 px-1 pb-1">
        @if($product->stock_status === 'in_stock')
            <div class="grid grid-cols-2 gap-2">
                <button wire:click="addToCartClicked"
                    class="group/btn flex items-center justify-center gap-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 px-2 py-2.5 text-sm font-semibold text-gray-700 transition-all active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-gray-600 transition-transform group-hover/btn:scale-110" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1 5h12m-9 0a1 1 0 102 0m4 0a1 1 0 102 0" />
                    </svg>
                    <span class="hidden md:inline text-xs">Add to Cart</span>
                </button>

                <button wire:click="buyNowClicked"
                    class="group/btn flex items-center justify-center gap-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 px-2 py-2.5 text-sm font-semibold text-white shadow-sm transition-all active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="hidden md:inline h-4 w-4 transition-transform group-hover/btn:translate-x-0.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="text-xs">Buy <span class="hidden md:inline">Now</span></span>
                </button>
            </div>
        @else
            <button disabled
                class="w-full bg-gray-100 text-gray-400 py-2.5 px-4 rounded-xl cursor-not-allowed font-medium text-xs uppercase tracking-wider">
                Out of Stock
            </button>
        @endif
    </div>

    {{-- ====================================================== --}}
    {{-- Refactored Variant Selection Modal Component --}}
    {{-- ====================================================== --}}
    @if($showVariantModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4"
            wire:click.self="closeModal">
            <div
                class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl animate-in fade-in zoom-in-95 duration-200">

                {{-- Modal Header Area --}}
                <div class="flex justify-between items-center border-b border-gray-100 px-6 py-4">
                    <h2 class="text-base font-bold text-gray-900">
                        {{ $buyNowAction ? 'Quick Buy Checkout' : 'Configure Product Options' }}
                    </h2>
                    <button wire:click="closeModal"
                        class="h-8 w-8 inline-flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition">
                        ✕
                    </button>
                </div>

                {{-- Modal Main Core Content Area --}}
                <div class="overflow-y-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
    $currentVariant = $selectedVariant ? $product->variants->find($selectedVariant) : null;
    $displayImage = $currentVariant?->image_path ?: $product->primaryImage?->image_path;
    $displayPrice = $currentVariant?->price ?? $product->price;
    $displayCompare = $currentVariant?->compare_price ?? $product->compare_price;
    $variantTitle = $currentVariant?->display_label;
                    @endphp

                    {{-- Left View Column: Context Media Presentation --}}
                    <div>
                        <div class="aspect-square rounded-xl overflow-hidden bg-gray-50 border border-gray-100 mb-3">
                            @if($displayImage)
                                <img src="{{ asset('storage/' . $displayImage) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <span
                                        class="text-4xl text-gray-400 font-bold uppercase">{{ substr($product->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider mb-1">
                            {{ $product->category->name ?? 'Store General' }}
                        </p>
                        <h3 class="text-base font-bold text-gray-900 mb-1">
                            {{ $product->name }}
                        </h3>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-xl font-extrabold text-gray-900">
                                TK. {{ number_format($displayPrice, 0) }}
                            </span>
                            @if($displayCompare && $displayCompare > $displayPrice)
                                <span class="text-xs text-gray-400 line-through">
                                    TK. {{ number_format($displayCompare, 0) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Right View Column: Action Selections & Quantity Parameters --}}
                    <div class="flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Available Variants
                            </h4>
                            <div class="grid grid-cols-2 gap-2 mb-4 max-h-[180px] overflow-y-auto pr-1">
                                @foreach($product->variants->where('is_active', true) as $item)
                                    @php
        $variantTitle = collect([$item->color?->name, $item->size?->name])->filter()->implode(' • ') ?: $item->name;
                                    @endphp
                                    <button type="button" wire:click="selectVariant({{ $item->id }})"
                                        class="p-2 border text-left rounded-xl transition-all text-xs flex items-center gap-2
                                                {{ $selectedVariant == $item->id ? 'border-blue-600 bg-blue-50/50 ring-1 ring-blue-600 font-semibold' : 'border-gray-200 hover:border-gray-300 bg-white' }}">
                                        @if($item->color)
                                            <span class="w-3 h-3 rounded-full shrink-0 border border-black/10"
                                                style="background-color: {{ $item->color->hex_code ?? '#ddd' }}"></span>
                                        @endif
                                        <span class="truncate text-gray-800">{{ $variantTitle }}</span>
                                    </button>
                                @endforeach
                            </div>

                            {{-- Quantity Counter Inputs --}}
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Select
                                    Quantity</label>
                                <div class="inline-flex items-center border border-gray-200 rounded-xl bg-gray-50 p-1">
                                    <button wire:click="decrementQuantity" type="button"
                                        class="w-8 h-8 rounded-lg hover:bg-white flex items-center justify-center text-gray-600 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <input type="number" wire:model="quantity" min="1"
                                        class="w-12 text-center bg-transparent border-0 focus:ring-0 font-semibold text-sm text-gray-800 p-0">
                                    <button wire:click="incrementQuantity" type="button"
                                        class="w-8 h-8 rounded-lg hover:bg-white flex items-center justify-center text-gray-600 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if(session('error'))
                            <div
                                class="bg-rose-50 border border-rose-100 text-rose-600 px-3 py-2 rounded-xl mb-3 text-xs font-medium">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{-- Finalize Modal Actions Footer --}}
                        <div class="flex gap-2 pt-4 border-t border-gray-100">
                            <button wire:click="closeModal" type="button"
                                class="flex-1 py-2.5 border border-gray-200 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button wire:click="confirmVariant" type="button" @disabled(!$selectedVariant)
                                class="flex-1 py-2.5 text-xs font-semibold rounded-xl transition text-center
                                    {{ $selectedVariant ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-200' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                                {{ $buyNowAction ? 'Proceed to Buy' : 'Add To Cart' }}
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
