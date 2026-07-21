<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;

    public bool $showVariantModal = false;
    public bool $buyNowAction = false;

    public ?int $selectedVariant = null;
    public ?int $selectedProductSize = null;
    public int $quantity = 1;

    public function mount(): void
    {
        $this->product->loadMissing([
            'category',
            'brand',
            'media',
            'approvedReviews.customer',
            'variants.color',
            'variants.media',
            'sizeChart',
            'availableSizes',
        ]);
    }

    public function addToCartClicked(): void
    {
        $this->openModal(buyNow: false);
    }

    public function buyNowClicked(): void
    {
        $this->openModal(buyNow: true);
    }

    public function updatedQuantity($value): void
    {
        $this->quantity = max(1, (int) $value);
    }

    protected function openModal(bool $buyNow): void
    {
        $this->buyNowAction = $buyNow;
        $this->quantity = 1;

        // Simple Product (No variants & No available sizes) -> Direct Add to Cart
        if (! $this->product->has_variants && $this->product->availableSizes->isEmpty()) {
            if ($this->handleCartLogic()) {
                if ($buyNow) {
                    $this->redirect(route('cart.index'), navigate: true);
                }
            }
            return;
        }

        $this->showVariantModal = true;
    }

    public function closeModal(): void
    {
        $this->reset([
            'showVariantModal',
            'buyNowAction',
            'selectedVariant',
            'selectedProductSize',
        ]);

        $this->quantity = 1;
    }

    public function selectVariant(int $variantId): void
    {
        $variant = $this->product->variants->firstWhere('id', $variantId);

        if (! $variant || ! $variant->is_active) {
            return;
        }

        $this->selectedVariant = $variant->id;
    }

    public function selectProductSize(int $sizeId): void
    {
        $this->selectedProductSize = $sizeId;
    }

    public function incrementQuantity(): void
    {
        if ($this->quantity < 99) {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function confirmVariant(): void
    {
        $rules = [
            'quantity' => 'required|integer|min:1|max:99',
        ];

        if ($this->product->has_variants) {
            $rules['selectedVariant'] = 'nullable|integer'; // Variant বাছাই করা বাধ্যতামূলক করতে চাইলে
        }

        if ($this->product->availableSizes->isNotEmpty()) {
            $rules['selectedProductSize'] = 'required|integer';
        }

        $this->validate($rules);

        if ($this->handleCartLogic()) {
            $shouldRedirect = $this->buyNowAction;

            $this->closeModal();

            if ($shouldRedirect) {
                $this->redirect(route('cart.index'), navigate: true);
            }
        }
    }

    private function handleCartLogic(): bool
    {
        if ($this->product->stock_status !== 'in_stock') {
            session()->flash('error', $this->product->name . ' is out of stock.');
            return false;
        }

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

            $currentVariant = $this->selectedVariant
                ? $this->product->variants->firstWhere('id', $this->selectedVariant)
                : null;

            // Spatie Media URL Selection
            $selectedImage = null;
            if ($currentVariant) {
                $selectedImage = $currentVariant->getFirstMediaUrl('variant_images')
                    ?: $currentVariant->getFirstMediaUrl();
            }

            if (! $selectedImage) {
                $selectedImage = $this->product->getFirstMediaUrl('products')
                    ?: $this->product->getFirstMediaUrl();
            }

            $base = [
                'product_id'      => $this->product->id,
                'quantity'        => $this->quantity,
                'image'           => $selectedImage,
                'product_size_id' => $productSize?->id,
                'product_size'    => $productSize?->name,
            ];

            if ($currentVariant) {
                $cart[$cartKey] = $base + [
                    'variant_id'   => $currentVariant->id,
                    'name'         => $this->product->name,
                    'variant_name' => $currentVariant->display_label ?? $currentVariant->name,
                    'color'        => $currentVariant->color?->name,
                    'price'        => (float) $currentVariant->price,
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
        session()->flash('success', $this->product->name . ' added to cart.');

        return true;
    }

    public function render()
    {
        return view('livewire.product-card');
    }
}
