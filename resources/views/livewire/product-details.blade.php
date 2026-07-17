<div class="bg-slate-50/50 min-h-screen py-8 md:py-12 text-slate-800 antialiased selection:bg-slate-900 selection:text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <nav class="mb-8 text-xs md:text-sm tracking-wide">
            <ol class="flex flex-wrap items-center gap-1.5 text-slate-500">
                <li><a href="{{ route('home') }}" class="transition hover:text-slate-900">Home</a></li>
                <li class="text-slate-300">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </li>
                <li><a href="{{ route('products.index') }}" class="transition hover:text-slate-900">Shop</a></li>
                <li class="text-slate-300">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </li>
                <li>
                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="transition hover:text-slate-900">
                        {{ $product->category->name }}
                    </a>
                </li>
                <li class="text-slate-300">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </li>
                <li class="text-slate-900 font-medium truncate max-w-[180px] sm:max-w-none">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 p-4 sm:p-6 lg:p-10">

                <div class="space-y-4">
                    <div x-data="{ hover:false, x:50, y:50 }"
                         @mouseenter="hover = true"
                         @mouseleave="hover = false"
                         @mousemove="
                            const rect = $el.getBoundingClientRect();
                            x = (($event.clientX - rect.left) / rect.width) * 100;
                            y = (($event.clientY - rect.top) / rect.height) * 100;
                         "
                         class="aspect-square overflow-hidden rounded-2xl cursor-zoom-in bg-slate-50 border border-slate-100 relative group">
                        <img src="{{ $selectedImage ? asset('storage/' . $selectedImage) : 'https://via.placeholder.com/600' }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover transition-transform duration-300 ease-out"
                            :style="hover ? `transform: scale(2.2); transform-origin: ${x}% ${y}%` : 'transform: scale(1)'">
                    </div>

                    @php
$gallery = $product->images->sortBy('sort_order')->values();
$variantImagePath = $selectedVariant ? $product->variants->find($selectedVariant)?->image_path : null;

if ($variantImagePath) {
    $gallery = $gallery->prepend((object) [
        'image_path' => $variantImagePath,
        'sort_order' => -1,
    ]);
}
                    @endphp

                    @if($gallery->count() > 1)
                        <div class="grid grid-cols-5 gap-3">
                            @foreach($gallery as $image)
                                <button wire:click="selectImage('{{ $image->image_path }}')"
                                    class="aspect-square rounded-xl overflow-hidden border-2 transition-all relative
                                    {{ $selectedImage === $image->image_path ? 'border-slate-900 ring-2 ring-slate-900/10' : 'border-slate-100 hover:border-slate-300' }}">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex flex-col justify-between">
                    <div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($product->is_featured)
                                <span class="bg-amber-50 text-amber-800 text-xs font-semibold px-2.5 py-1 rounded-full tracking-wide uppercase border border-amber-200/50">Featured</span>
                            @endif
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full tracking-wide uppercase border {{ $product->stock_status === 'in_stock' ? 'bg-emerald-50 text-emerald-800 border-emerald-200/50' : 'bg-rose-50 text-rose-800 border-rose-200/50' }}">
                                {{ ucfirst(str_replace('_', ' ', $product->stock_status)) }}
                            </span>
                        </div>

                        @if($product->brand)
                            <p class="text-xs md:text-sm font-medium tracking-wider text-slate-400 uppercase mb-1">{{ $product->brand->name }}</p>
                        @endif

                        <h1 class="text-2xl md:text-4xl font-bold text-slate-900 tracking-tight mb-4 leading-tight">{{ $product->name }}</h1>

                        @if($product->reviews_count > 0)
                            <div class="flex items-center gap-2.5 mb-6 pb-6 border-b border-slate-100">
                                <div class="flex items-center text-amber-400 gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= floor($product->average_rating) ? 'fill-current' : 'text-slate-200' }}" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm font-medium text-slate-600">{{ number_format($product->average_rating, 1) }}</span>
                                <span class="text-xs text-slate-400">({{ $product->reviews_count }} reviews)</span>
                            </div>
                        @endif

                        <div class="mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100 inline-flex items-center gap-4 w-full sm:w-auto">
                            @php $variant = $selectedVariant ? $product->variants->find($selectedVariant) : null; @endphp
                            @if($variant)
                                <div class="flex items-baseline gap-3">
                                    <span class="text-2xl md:text-3xl font-extbbold text-slate-900 tracking-tight">TK. {{ number_format($variant->price, 2) }}</span>
                                    @if($variant->compare_price)
                                        <span class="text-sm md:text-base text-slate-400 line-through">TK. {{ number_format($variant->compare_price, 2) }}</span>
                                        <span class="bg-rose-500 text-white px-2 py-0.5 rounded-md text-xs font-bold tracking-wider">
                                            -{{ $variant->discount_percentage }}%
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-baseline gap-3">
                                    <span class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">TK. {{ number_format($product->price, 2) }}</span>
                                    @if($product->compare_price)
                                        <span class="text-sm md:text-base text-slate-400 line-through">TK. {{ number_format($product->compare_price, 2) }}</span>
                                        <span class="bg-rose-500 text-white px-2 py-0.5 rounded-md text-xs font-bold tracking-wider">
                                            -{{ $product->discount_percentage }}%
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if($product->short_description)
                            <p class="text-slate-600 text-sm md:text-base leading-relaxed mb-6">{{ $product->short_description }}</p>
                        @endif

                        @php
$sizeGroups = $product->variants->where('is_active', true)->filter(fn($v) => $v->size_id)->groupBy('size_id');
$offeredSizes = $sizeGroups->map(fn($g) => $g->first()->size);
                        @endphp
                        @if($offeredSizes->whereNotNull('chest')->isNotEmpty() || $offeredSizes->whereNotNull('length')->isNotEmpty())
                            <div class="my-6 max-w-md">
                                <div class="rounded-xl border border-slate-200/80 overflow-hidden bg-white">
                                    <div class="bg-slate-900 text-white text-xs tracking-wider uppercase text-center py-2.5 font-semibold">Size Reference Table</div>
                                    <table class="w-full text-xs text-center border-collapse">
                                        <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                                            <tr>
                                                <th class="p-2.5 text-left font-medium text-slate-400 bg-slate-50/50 pl-4">SIZE</th>
                                                @foreach($offeredSizes as $size)
                                                    <th class="p-2.5 font-semibold text-slate-700">{{ $size->name }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-slate-600">
                                            <tr>
                                                <th class="p-2.5 text-left font-medium text-slate-400 bg-slate-50/50 pl-4">CHEST</th>
                                                @foreach($offeredSizes as $size)
                                                    <td class="p-2.5">{{ $size->chest ?? '-' }}</td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th class="p-2.5 text-left font-medium text-slate-400 bg-slate-50/50 pl-4">LENGTH</th>
                                                @foreach($offeredSizes as $size)
                                                    <td class="p-2.5">{{ $size->length ?? '-' }}</td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @if($product->has_variants && $product->variants->where('is_active', true)->count())
                            <div class="mb-8">
                                <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wider mb-3">Available Variants</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($product->variants->where('is_active', true) as $item)
                                        @php
        $variantTitle = collect([$item->color?->name, $item->size?->name])->filter()->implode(' / ');
        $variantTitle = $variantTitle ?: $item->name;
                                        @endphp

                                        <button type="button" wire:click="selectVariant({{ $item->id }})"
                                            class="group relative border rounded-xl overflow-hidden bg-white text-left transition-all duration-200 hover:shadow-md
                                            {{ $selectedVariant == $item->id ? 'border-slate-900 ring-1 ring-slate-900 shadow-sm' : 'border-slate-200 hover:border-slate-400' }}">

                                            @if($item->image_path)
                                                <div class="aspect-[4/3] bg-slate-50 overflow-hidden border-b border-slate-100">
                                                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $variantTitle }}"
                                                         class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                                                </div>
                                            @endif

                                            <div class="p-2.5">
                                                <div class="flex items-center gap-2">
                                                    @if($item->color)
                                                        <span class="w-3.5 h-3.5 rounded-full border border-slate-200 shrink-0" style="background-color: {{ $item->color->hex_code ?? '#ddd' }}"></span>
                                                    @endif
                                                    <span class="font-medium text-slate-900 text-xs truncate">{{ $variantTitle }}</span>
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="my-6">
                            <label class="block text-xs font-semibold text-slate-900 uppercase tracking-wider mb-2">Select Quantity</label>
                            <div class="inline-flex items-center border border-slate-200 rounded-xl bg-white p-1 shadow-sm">
                                <button wire:click="decrementQuantity" type="button" class="w-9 h-9 rounded-lg hover:bg-slate-50 flex items-center justify-center text-slate-500 hover:text-slate-900 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4" /></svg>
                                </button>
                                <input type="number" wire:model="quantity" min="1" class="w-12 text-center border-0 focus:ring-0 text-sm font-semibold text-slate-900 p-0">
                                <button wire:click="incrementQuantity" type="button" class="w-9 h-9 rounded-lg hover:bg-slate-50 flex items-center justify-center text-slate-500 hover:text-slate-900 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-sm px-4 py-3 rounded-xl mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-3 mt-4">
                        <button wire:click="addToCart" @disabled($product->stock_status !== 'in_stock')
                            class="w-full py-3.5 px-6 rounded-xl font-semibold text-sm tracking-wide transition-all shadow-sm flex items-center justify-center gap-2
                            {{ $product->stock_status === 'in_stock' ? 'bg-slate-100 hover:bg-slate-200 text-slate-900 border border-slate-200' : 'bg-slate-100 text-slate-400 cursor-not-allowed border-0' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            {{ $product->stock_status === 'in_stock' ? 'Add to Cart' : 'Out of Stock' }}
                        </button>

                        <button wire:click="buyNow" @disabled($product->stock_status !== 'in_stock')
                            class="w-full py-3.5 px-6 rounded-xl font-semibold text-sm tracking-wide transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2
                            {{ $product->stock_status === 'in_stock' ? 'bg-slate-900 hover:bg-slate-800 text-white' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                            <span>Buy It Now</span>
                        </button>
                    </div>

                    <div class="mt-8 border-t border-slate-100 pt-5 space-y-2.5 text-xs md:text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">SKU:</span>
                            <span class="font-semibold text-slate-700">{{ $variant?->sku ?? $product->sku }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Category:</span>
                            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="font-semibold text-slate-900 hover:underline">
                                {{ $product->category->name }}
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-12" x-data="{ activeTab: 'description' }">
            <div class="border-b border-slate-100 bg-slate-50/50 px-4 sm:px-6">
                <nav class="flex gap-6">
                    <button @click="activeTab = 'description'"
                        :class="activeTab === 'description' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-400 hover:text-slate-600'"
                        class="px-2 py-4 border-b-2 text-sm uppercase tracking-wider transition">DESCRIPTION</button>
                    <button @click="activeTab = 'delevery'"
                        :class="activeTab === 'delevery' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-400 hover:text-slate-600'"
                        class="px-2 py-4 border-b-2 text-sm uppercase tracking-wider transition">DELIVERY OPTIONS</button>
                    <button @click="activeTab = 'reviews'"
                        :class="activeTab === 'reviews' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-400 hover:text-slate-600'"
                        class="px-2 py-4 border-b-2 text-sm uppercase tracking-wider transition">
                        REVIEWS ({{ $product->reviews_count }})
                    </button>
                </nav>
            </div>
            <div class="p-4 sm:p-8">
                <div x-show="activeTab === 'description'" x-cloak>
                    <div class="prose prose-slate max-w-none prose-sm sm:prose-base">{!! $product->description !!}</div>
                </div>
                <div x-show="activeTab === 'delevery'" x-cloak>
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">
                             Delivery Information
                        </h2>
                        <p class="text-sm text-blue-100 mt-1">
                            দ্রুত, নিরাপদ ও নির্ভরযোগ্য ডেলিভারি সারাদেশে।
                        </p>
                    </div>

                    <div class="p-1 sm:p-6 space-y-6">

                        <!-- Dhaka -->
                        <div class="rounded-xl border border-green-200 bg-green-50 p-5">

                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-bold text-green-700">
                                    ঢাকা সিটি
                                </h3>

                                <span class="rounded-full bg-green-600 px-4 py-1 text-sm font-semibold text-white">
                                    ৳ ৭০
                                </span>
                            </div>

                            <ul class="space-y-2 text-gray-700 leading-7 list-disc pl-5">
                                <li>ঢাকা সিটি কর্পোরেশনের সকল এলাকায় হোম ডেলিভারি।</li>
                                <li>১০০% ক্যাশ অন ডেলিভারি সুবিধা।</li>
                                <li>পণ্য হাতে পাওয়ার পর মূল্য পরিশোধ করতে পারবেন।</li>
                            </ul>

                        </div>

                        <!-- Outside Dhaka -->
                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">

                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-bold text-blue-700">
                                    ঢাকার বাইরে (সারা বাংলাদেশ)
                                </h3>

                                <span class="rounded-full bg-blue-600 px-4 py-1 text-sm font-semibold text-white">
                                    ৳১৫০
                                </span>
                            </div>

                            <ul class="space-y-2 text-gray-700 leading-7 list-disc pl-5">
                                <li>হোম ডেলিভারি অথবা কুরিয়ার সার্ভিসে ডেলিভারি।</li>
                                <li>অর্ডার নিশ্চিত করতে শুধুমাত্র ডেলিভারি চার্জ অগ্রিম প্রদান করতে হবে।</li>
                                <li>পণ্য গ্রহণের সময় শুধু পণ্যের মূল্য পরিশোধ করবেন।</li>
                                <li>থানা সদর এলাকায় সাধারণত হোম ডেলিভারি পাওয়া যায়।</li>
                                <li>ডেলিভারি সময়: <strong>৩–৫ কার্যদিবস</strong>।</li>
                            </ul>

                        </div>

                        <!-- Notice -->
                        <div class="rounded-xl border-l-4 border-amber-500 bg-amber-50 p-5">

                            <div class="flex gap-3">

                                <div class="text-2xl">
                                    ⚠️
                                </div>

                                <div>

                                    <h4 class="font-semibold text-amber-700 mb-2">
                                        গুরুত্বপূর্ণ তথ্য
                                    </h4>

                                    <ul class="space-y-2 text-gray-700 leading-7 list-disc pl-5">
                                        <li>ঢাকার বাইরে ডেলিভারির ক্ষেত্রে শুধুমাত্র ডেলিভারি চার্জ অগ্রিম প্রদান করতে হবে।</li>
                                        <li>বাকি অর্থ পণ্য গ্রহণের সময় ক্যাশ অন ডেলিভারিতে পরিশোধ করবেন।</li>
                                    </ul>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                </div>
                <div x-show="activeTab === 'reviews'" x-cloak>
                    @if($product->approvedReviews->count() > 0)
                        <div class="space-y-6">
                            @foreach($product->approvedReviews as $review)
                                <div class="border-b border-slate-100 pb-6 last:border-b-0 last:pb-0">
                                    <div class="flex items-start gap-4">
                                        <div class="w-10 h-10 bg-slate-100 text-slate-800 rounded-full flex items-center justify-center font-bold text-sm shrink-0 uppercase border border-slate-200">
                                            {{ substr($review->customer->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                                <h4 class="font-semibold text-slate-900 text-sm sm:text-base">{{ $review->customer->name }}</h4>
                                                @if($review->is_verified_purchase)
                                                    <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider border border-emerald-100">Verified</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 mb-3">
                                                <div class="flex text-amber-400 gap-0.5">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-3.5 h-3.5 fill-current {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}" viewBox="0 0 20 20">
                                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-slate-400 font-medium">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if($review->title)
                                                <h5 class="font-semibold text-slate-900 text-sm mb-1">{{ $review->title }}</h5>
                                            @endif
                                            @if($review->comment)
                                                <p class="text-slate-600 text-sm leading-relaxed">{{ $review->comment }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <p class="text-sm text-slate-400 font-medium">No reviews yet. Be the first to review this product!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($relatedProducts->count() > 0)
            <section class="mt-16">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl md:text-2xl font-bold tracking-tight text-slate-900">You May Also Like</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <livewire:product-card :product="$relatedProduct" :key="'related-' . $relatedProduct->id" />
                    @endforeach
                </div>
            </section>
        @endif

    </div>
</div>
