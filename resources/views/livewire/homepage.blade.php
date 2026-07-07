<div>

    @if($banners->count())
        <section class="relative overflow-hidden" wire:ignore>
            <div class="swiper heroSwiper">
                <div class="swiper-wrapper">

                    @foreach ($banners as $banner)
                        <div class="swiper-slide">
                            <img src="{{ asset('storage/' . $banner->banner_image) }}" alt="{{ $banner->banner_title }}"
                                class="w-full h-[220px] sm:h-[350px] md:h-[500px] lg:h-[650px] object-cover">
                        </div>
                    @endforeach

                </div>

                <!-- Pagination -->
                <div class="swiper-pagination"></div>

                <!-- Navigation -->
                {{-- <div class="swiper-button-prev hidden md:flex"></div>
                <div class="swiper-button-next hidden md:flex"></div> --}}
            </div>
        </section>
    @endif
    <!-- Categories Section -->

    <section class="py-16 bg-white relative group/section">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Shop by Category</h2>

            {{-- বামের অ্যারো বাটন (ক্লিক করলে বামে স্ক্রোল হবে) --}}
            <button onclick="scrollCategories('left')"
                class="absolute top-[50%] transform -translate-y-1/2 left-2 z-20 md:hidden opacity-80 active:scale-95 bg-white p-2 rounded-full shadow-lg border border-gray-100 text-gray-700 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- ডানের অ্যারো বাটন (ক্লিক করলে ডানে স্ক্রোল হবে) --}}
            <button onclick="scrollCategories('right')"
                class="absolute top-[50%] transform -translate-y-1/2 right-2 z-20 md:hidden opacity-80 active:scale-95 bg-white p-2 rounded-full shadow-lg border border-gray-100 text-gray-700 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div id="categoryContainer"
                class="flex overflow-x-auto snap-x snap-mandatory gap-6 pb-4 scrollbar-none md:grid md:grid-cols-3 lg:grid-cols-6 md:pb-0 scroll-smooth">
                @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                        class="group min-w-[140px] w-[40%] shrink-0 snap-start md:w-auto md:min-w-0">

                        <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 mb-3">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-600">
                                    <span
                                        class="text-4xl font-bold text-white uppercase">{{ substr($category->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <h3
                            class="text-center font-medium text-gray-900 group-hover:text-blue-600 truncate text-sm md:text-base">
                            {{ $category->name }}
                        </h3>
                        <p class="text-center text-xs md:text-sm text-gray-500">{{ $category->products_count }} items</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @script
    <script>
        // এই কোডটি শুধুমাত্র এই কম্পোনেন্টের জন্যই এক্সিকিউট হবে
        window.scrollCategories = function (direction) {
            const container = document.getElementById('categoryContainer');
            if (direction === 'left') {
                container.scrollLeft -= 200;
            } else {
                container.scrollLeft += 200;
            }
        }
    </script>
    @endscript
    <!-- Featured Products -->
    <section class="py-16 bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Featured Products</h2>
                <a href="{{ route('products.index', ['featured' => 1]) }}"
                    class="text-blue-600 hover:text-indigo-700 font-medium">
                    View All →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <livewire:product-card :product="$product" :key="$product->id" />
                @endforeach
            </div>
        </div>
    </section>


    <!-- New Arrivals -->
    <section class="py-16 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-900">New Arrivals</h2>
                <a href="{{ route('products.index', ['sort' => 'newest']) }}"
                    class="text-blue-600 hover:text-indigo-700 font-medium">
                    View All →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($newArrivals as $product)
                    <livewire:product-card :product="$product" :key="'new-' . $product->id" />
                @endforeach
            </div>
        </div>
    </section>
    <!-- New t-shirt -->
    {{-- <section class="py-16 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-900">T-Shirt</h2>
                <a href="{{ route('products.index', ['category' => 't-shirt']) }}"
                    class="text-blue-600 hover:text-indigo-700 font-medium">
                    View All →
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($tshirts as $product)
                <livewire:product-card :product="$product" :key="'new-' . $product->id" />
                @endforeach
            </div>
        </div>
    </section> --}}
    <!-- All Categories with 4 products -->
    @foreach ($categories as $category)
        @if ($category->products->isNotEmpty())
            <section class="py-16 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-3xl font-bold text-gray-900">
                            {{ $category->name }}
                        </h2>

                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                            class="text-indigo-600 hover:text-indigo-700 font-semibold transition">
                            View All →
                        </a>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                        @foreach ($category->products as $product)
                            <livewire:product-card :product="$product" :key="'category-' . $category->id . '-product-' . $product->id" />
                        @endforeach
                    </div>

                </div>
            </section>
        @endif
    @endforeach


    <!-- Benefits Section -->
    <section class="py-16 bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 text-blue-600 rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Quality Guarantee</h3>
                    <p class="text-gray-600">All products are carefully selected and quality tested</p>
                </div>
                <div class="text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 text-blue-600 rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Fast Shipping</h3>
                    <p class="text-gray-600">Quick delivery right to your doorstep</p>
                </div>
                <div class="text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 text-blue-600 rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Secure Payment</h3>
                    <p class="text-gray-600">Your payment information is safe with us</p>
                </div>
            </div>
        </div>
    </section>
</div>