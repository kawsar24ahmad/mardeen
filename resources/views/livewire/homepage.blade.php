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
    <section class="py-16 bg-white relative group/section" wire:ignore>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Shop by Category</h2>

            <!-- বামের অ্যারো বাটন -->
            <button
                class="category-prev absolute top-[50%] transform -translate-y-1/2 left-2 z-20 opacity-100 active:scale-95 bg-white p-2.5 rounded-full shadow-lg border border-gray-100 text-gray-700 transition-all duration-300 cursor-pointer hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- ডানের অ্যারো বাটন -->
            <button
                class="category-next absolute top-[50%] transform -translate-y-1/2 right-2 z-20 opacity-100 active:scale-95 bg-white p-2.5 rounded-full shadow-lg border border-gray-100 text-gray-700 transition-all duration-300 cursor-pointer hover:bg-gray-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Swiper Container (এখানে overflow-hidden এবং flex-nowrap যুক্ত করা হয়েছে) -->
            <div class="swiper categorySwiper !overflow-hidden">
                <!-- flex এবং flex-nowrap ব্যবহারের ফলে JS লোড হওয়ার আগেই আইটেমগুলো এক লাইনে পাশাপাশি থাকবে, নিচে নামবে না -->
                <div class="swiper-wrapper pb-4 flex flex-nowrap">
                    @foreach($categories as $category)
                        <!-- initial width ফিক্স করার জন্য shrink-0 এবং নির্দিষ্ট width ক্লাস যুক্ত করা হয়েছে -->
                        <div class="swiper-slide shrink-0 w-[40%] sm:w-[28%] md:w-[25%] lg:w-[16.666%]">
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                                class="group block text-center">
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
                                    class="font-medium text-gray-900 group-hover:text-blue-600 truncate text-sm md:text-base px-1">
                                    {{ $category->name }}
                                </h3>
                                <p class="text-xs md:text-sm text-gray-500 mt-1">{{ $category->products_count }} items</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>



    <!-- Featured Products -->
    <section class="py-16 bg-gray-50">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl ps-4  font-bold text-gray-900">Featured Products</h2>
                {{-- <a href="{{ route('products.index', ['featured' => 1]) }}"
                    class="text-blue-600 hover:text-indigo-700 font-medium">
                    View All →
                </a> --}}
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4  gap-1 gap-y-4 md:gap-6">
                @foreach($featuredProducts as $product)
                    <livewire:product-card :product="$product" :key="$product->id" />
                @endforeach
            </div>
            <div class="flex justify-center mt-8 px-4">
                <a href="{{ route('products.index', ['featured' => 1]) }}"
                    class="group inline-flex items-center justify-center gap-2 sm:gap-3 rounded-full bg-slate-900 px-5 sm:px-7 py-3 text-sm sm:text-base font-semibold text-white shadow-md transition-all duration-300 hover:bg-indigo-600 hover:shadow-xl hover:scale-105">
                    <span>View More</span>

                    <span
                        class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full bg-white/20 transition-transform duration-300 group-hover:translate-x-1">
                        →
                    </span>
                </a>
            </div>
        </div>
    </section>


    <!-- New Arrivals -->
    <section class="py-16 bg-white">
        <div class="mx-auto max-w-7xl  sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl ps-4 font-bold text-gray-900">New Arrivals</h2>
                {{-- <a href="{{ route('products.index', ['sort' => 'newest']) }}"
                    class="text-blue-600 hover:text-indigo-700 font-medium">
                    View All →
                </a> --}}
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-1 gap-y-4 md:gap-6">
                @foreach($newArrivals as $product)
                    <livewire:product-card :product="$product" :key="'new-' . $product->id" />
                @endforeach
            </div>
            <div class="flex justify-center mt-8 px-4">
                <a href="{{ route('products.index', ['sort' => 'newest']) }}"
                    class="group inline-flex items-center justify-center gap-2 sm:gap-3 rounded-full bg-slate-900 px-5 sm:px-7 py-3 text-sm sm:text-base font-semibold text-white shadow-md transition-all duration-300 hover:bg-indigo-600 hover:shadow-xl hover:scale-105">
                    <span>View More</span>

                    <span
                        class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full bg-white/20 transition-transform duration-300 group-hover:translate-x-1">
                        →
                    </span>
                </a>
            </div>
        </div>
    </section>

    <!-- All Categories with 4 products -->
    @foreach ($categories as $category)
        @if ($category->products->isNotEmpty())
            <section class="py-16 {{ $loop->even ? 'bg-gray-100' : 'bg-white' }}">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-3xl ps-4 font-bold text-gray-900">
                            {{ $category->name }}
                        </h2>

                        {{-- <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                            class="text-indigo-600 hover:text-indigo-700 font-semibold transition">
                            View All →
                        </a> --}}
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-1 gap-y-4 md:gap-6">
                        @foreach ($category->products as $product)
                            <livewire:product-card :product="$product" :key="'category-' . $category->id . '-product-' . $product->id" />
                        @endforeach

                    </div>
                    <div class="flex justify-center mt-8 px-4">
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                            class="group inline-flex items-center justify-center gap-2 sm:gap-3 rounded-full bg-slate-900 px-5 sm:px-7 py-3 text-sm sm:text-base font-semibold text-white shadow-md transition-all duration-300 hover:bg-indigo-600 hover:shadow-xl hover:scale-105">

                            <span>View More</span>

                            <span class="flex h-7 w-7 sm:h-8 sm:w-8 items-center justify-center rounded-full bg-white/20">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6" />
                                </svg>
                            </span>

                        </a>
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