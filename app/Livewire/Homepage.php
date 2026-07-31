<?php

namespace App\Livewire;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class Homepage extends Component
{
    public function render()
    {
        $featuredProducts = Product::active()
            ->featured()
            ->with(['category', 'brand', 'primaryImage'])
            ->limit(4)
            ->get();
        $newArrivals = Product::active()
            ->with(['category', 'brand', 'primaryImage'])
            ->latest()
            ->limit(4)
            ->get();
        $tshirts = Product::active()
            ->with(['category', 'brand', 'primaryImage'])
            ->where('category_id', 11)
            ->latest()
            ->limit(4)
            ->get();
        $categories = Category::active()
            ->with([
                'products' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('sort_order', 'asc')
                        ->take(4);
                }
            ])
            ->sorted()
            ->withCount('products')
            ->get();

        $banners = Banner::with('media')->where('is_active', true)
            ->orderBy('serial_number')
            ->get();


        return view('livewire.homepage', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'newArrivals' => $newArrivals,
            'banners' => $banners,
            'tshirts' => $tshirts,
        ])->layout('components.layouts.frontend');
    }
}
