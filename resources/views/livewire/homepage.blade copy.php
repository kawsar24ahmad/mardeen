<div>
    {{-- ১. হিরো ব্যানার সেকশন (ডার্ক থিম) --}}
    @if($banners->count())
    <section class="relative overflow-hidden bg-gray-950 group/swiper" wire:ignore>
        <div class="swiper heroSwiper">
            <div class="swiper-wrapper">
                @foreach ($banners as $banner)
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent z-10"></div>
                    <img src="{{ asset('storage/' . $banner->banner_image) }}" alt="{{ $banner->banner_title }}"
                        class="w-full h-[200px] sm:h-[350px] md:h-[500px] lg:h-[600px] object-cover">
                </div>
                @endforeach
            </div>

            <div class="swiper-pagination !bottom-4"></div>

            <div class="swiper-button-prev !hidden md:!flex !w-10 !h-10 !bg-white/80 backdrop-blur-sm !text-gray-800 rounded-full shadow-md opacity-0 group-hover/swiper:opacity-100 !left-6 transition-all duration-300 after:!text-sm"></div>
            <div class="swiper-button-next !hidden md:!flex !w-10 !h-10 !bg-white/80 backdrop-blur-sm !text-gray-800 rounded-full shadow-md opacity-0 group-hover/swiper:opacity-100 !right-6 transition-all duration-300 after:!text-sm"></div>
        </div>
    </section>
    @endif

    {{-- ২. ক্যাটাগরি সেকশন (পিওর হোয়াইট ব্যাকগ্রাউন্ড) --}}
    <section class="py-12 md:py-16 bg-white relative group/section">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative">
            <h2 class="text-xl md:text-3xl font-bold text-gray-900 mb-6 md:mb-8">Shop by Category</h2>

            <button onclick="scrollCategories('left')"
                class="absolute top-[45%] -left-2 z-20 md:opacity-0 md:group-hover/section:opacity-100 active:scale-95 bg-white p-2.5 rounded-full shadow-xl border border-gray-100 text-gray-700 transition-all duration-300 hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <button onclick="scrollCategories('right')"
                class="absolute top-[45%] -right-2 z-20 md:opacity-0 md:group-hover/section:opacity-100 active:scale-95 bg-white p-2.5 rounded-full shadow-xl border border-gray-100 text-gray-700 transition-all duration-300 hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div id="categoryContainer"
                class="flex overflow-x-auto snap-x snap-mandatory gap-4 md:gap-6 pb-4 scrollbar-none md:grid md:grid-cols-4 lg:grid-cols-6 md:pb-0 scroll-smooth">
                @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                    class="group min-w-[110px] w-[28%] shrink-0 snap-start md:w-auto md:min-w-0 text-center">

                    <div class="aspect-square rounded-full overflow-hidden bg-white border border-gray-100 shadow-sm p-1 mb-3 transition duration-300 group-hover:shadow-md group-hover:border-blue-200">
                        @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                            class="w-full h-full object-cover rounded-full group-hover:scale-105 transition duration-500 ease-out">
                        @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full shadow-inner">
                            <span class="text-2xl font-bold text-white uppercase tracking-wider">{{ substr($category->name, 0, 1) }}</span>
                        </div>
                        @endif
                    </div>

                    <h3 class="font-semibold text-gray-800 group-hover:text-blue-600 truncate text-xs md:text-sm px-1 transition duration-200">
                        {{ $category->name }}
                    </h3>
                    <p class="text-[10px] md:text-xs text-gray-400 mt-0.5">{{ $category->products_count }} items</p>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ৩. ফিচারড প্রোডাক্টস (হালকা অফ-হোয়াইট/গ্রে ব্যাকগ্রাউন্ড) --}}
    <section class="py-12 md:py-16 bg-gray-50 border-t border-b border-gray-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6 md:mb-8">
                <h2 class="text-xl md:text-3xl font-bold text-gray-900">Featured Products</h2>
                <a href="{{ route('products.index', ['featured' => 1]) }}"
                    class="text-sm md:text-base text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1 transition">
                    View All <span>&rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
                @foreach($featuredProducts as $product)
                <livewire:product-card :product="$product" :key="'featured-'.$product->id" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ৪. নিউ অ্যারাইভালস (সফট ব্লু বা নীলচে ব্যাকগ্রাউন্ড) --}}
    <section class="py-12 md:py-16 bg-blue-50/40">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6 md:mb-8">
                <h2 class="text-xl md:text-3xl font-bold text-gray-900">New Arrivals</h2>
                <a href="{{ route('products.index', ['sort' => 'newest']) }}"
                    class="text-sm md:text-base text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1 transition">
                    View All <span>&rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
                @foreach($newArrivals as $product)
                <livewire:product-card :product="$product" :key="'new-' . $product->id" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ৫. লুপ ভিত্তিক ক্যাটাগরি প্রোডাক্টস (সফট পার্পল/ইনডিগো ব্যাকগ্রাউন্ড) --}}
    @foreach ($categories as $category)
    @if ($category->products->isNotEmpty())
    <section class="py-12 md:py-16 bg-indigo-50/30 border-t border-gray-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6 md:mb-8">
                <h2 class="text-xl md:text-3xl font-bold text-gray-900">{{ $category->name }}</h2>
                <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                    class="text-sm md:text-base text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1 transition">
                    View All <span>&rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-6">
                @foreach ($category->products->take(4) as $product)
                <livewire:product-card :product="$product" :key="'category-' . $category->id . '-product-' . $product->id" />
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @endforeach

    {{-- ৬. বেনিফিটস সেকশন (সফট গ্রিন বা এমারেল্ড ব্যাকগ্রাউন্ড) --}}
    <section class="py-12 bg-emerald-50/30 border-t border-b border-emerald-100/50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">

                <div class="flex items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-50 text-blue-600 rounded-xl shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm md:text-base">Quality Guarantee</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Carefully selected and tested products</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-center w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm md:text-base">Fast Shipping</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Quick delivery right to your doorstep</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-center w-12 h-12 bg-amber-50 text-amber-600 rounded-xl shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm md:text-base">Secure Payment</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Your financial data is completely safe</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- জাভাস্ক্রিপ্ট স্ক্রোল ফাংশন --}}
    @script
    <script>
        window.scrollCategories = function(direction) {
            const container = document.getElementById('categoryContainer');
            const scrollAmount = window.innerWidth < 768 ? 160 : 300;
            if (direction === 'left') {
                container.scrollLeft -= scrollAmount;
            } else {
                container.scrollLeft += scrollAmount;
            }
        }
    </script>
    @endscript
</div>