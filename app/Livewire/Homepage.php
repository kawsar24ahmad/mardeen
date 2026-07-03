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
            ->limit(8)
            ->get();
        $newArrivals = Product::active()
            ->with(['category', 'brand', 'primaryImage'])
            ->latest()
            ->limit(8)
            ->get();
        $categories = Category::active()
            ->sorted()
            ->withCount('products')
            ->limit(6)
            ->get();

        $banners = Banner::where('is_active', true)
            ->orderBy('serial_number')
            ->get();


        return view('livewire.homepage', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'newArrivals' => $newArrivals,
            'banners' => $banners
        ])->layout('components.layouts.frontend');
    }
}
