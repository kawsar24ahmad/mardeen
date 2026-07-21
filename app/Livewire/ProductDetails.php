<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Component;

class ProductDetails extends Component
{
    public Product $product;

    public ?string $selectedImage = null;
    public ?int $selectedVariant = null;
    public ?int $selectedSize = null;
    public ?int $selectedProductSize = null;
    public int $quantity = 1;

    public function mount(string $slug): void
    {
        $this->product = Product::active()
            ->where('slug', $slug)
            ->with([
                'category',
                'brand',
                'media',
                'approvedReviews.customer',
                'variants.color',
                'variants.size',
                'variants.media',
                'sizeChart',
                'availableSizes',
            ])
            ->firstOrFail();

        $this->product->increment('views_count');

        // Initial image select using Spatie Media Library
        $this->selectedImage = $this->product->getFirstMediaUrl('products')
            ?: $this->product->getFirstMediaUrl();
    }

    public function selectImage(string $imageUrl): void
    {
        $this->selectedImage = $imageUrl;
    }

    public function selectVariant(int $variantId): void
    {
        $variant = $this->product->variants->find($variantId);
        if (! $variant) {
            return;
        }

        $this->selectedVariant = $variant->id;

        // Spatie: Get variant specific image or fallback to main product image
        $variantImage = $variant->getFirstMediaUrl('variant_images')
            ?: $variant->getFirstMediaUrl();

        $this->selectedImage = $variantImage
            ?: ($this->product->getFirstMediaUrl('products') ?: $this->product->getFirstMediaUrl());
    }

    public function selectProductSize(int $sizeId): void
    {
        $this->selectedProductSize = $sizeId;
        $size = $this->product->availableSizes->firstWhere('id', $sizeId);
        if ($size) {
            $this->selectedSize = $size->id;
        }
    }

    public function incrementQuantity(): void
    {
        $this->quantity++;
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    private function handleCartLogic(): bool
    {
        // Require size selection if available
        if ($this->product->availableSizes->isNotEmpty() && ! $this->selectedProductSize) {
            session()->flash('error', 'Please select a Size.');
            return false;
        }

        $cart = session()->get('cart', []);

        $cartKey = $this->selectedVariant
            ? 'variant_' . $this->selectedVariant . '_size_' . ($this->selectedProductSize ?? 0)
            : 'product_' . $this->product->id . '_size_' . ($this->selectedProductSize ?? 0);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $this->quantity;
        } else {
            $productSize = $this->selectedProductSize
                ? $this->product->availableSizes->firstWhere('id', $this->selectedProductSize)
                : null;

            $base = [
                'product_id'      => $this->product->id,
                'quantity'        => $this->quantity,
                'image'           => $this->selectedImage,
                'product_size_id' => $productSize?->id,
                'product_size'    => $productSize?->name,
            ];

            if ($this->selectedVariant) {
                $variant = ProductVariant::with('color')->findOrFail($this->selectedVariant);

                $cart[$cartKey] = $base + [
                    'variant_id'   => $variant->id,
                    'name'         => $this->product->name,
                    'variant_name' => $variant->display_label,
                    'color'        => $variant->color?->name,
                    'price'        => (float) $variant->price,
                ];
            } else {
                $cart[$cartKey] = $base + [
                    'variant_id'   => null,
                    'name'         => $this->product->name,
                    'variant_name' => null,
                    'color'        => null,
                    'price'        => (float) $this->product->price,
                ];
            }
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');

        return true;
    }

    public function addToCart(): void
    {
        if ($this->handleCartLogic()) {
            session()->flash('success', 'Product added to cart.');
        }
    }

    public function buyNow(): void
    {
        if ($this->handleCartLogic()) {
            $this->redirect(route('cart.index'), navigate: true);
        }
    }

    public function render()
    {
        $relatedProducts = Product::active()
            ->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->with(['media']) // Spatie Media Eager Loading
            ->limit(4)
            ->get();

        return view('livewire.product-details', [
            'relatedProducts' => $relatedProducts,
        ])->layout('components.layouts.frontend');
    }
}
