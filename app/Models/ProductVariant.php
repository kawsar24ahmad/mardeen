<?php

namespace App\Models;

use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductVariant extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public function registerMediaConversions(?Media $media = null): void
    {
        // ১. কার্ড/গ্রিড ভিউয়ের জন্য (326x419px এর জন্য ৫০০x৬০০ WebP যথেষ্ট)
        $this->addMediaConversion('thumb')
            ->width(500)
            ->height(600)
            ->format('webp')
            ->quality(80)
            ->nonQueued();

        // ২. প্রোডাক্ট ডিটেইলস পেজ (Zoom/Large View)-এর জন্য
        $this->addMediaConversion('medium')
            ->width(1000)
            ->height(1200)
            ->format('webp')
            ->quality(85)
            ->nonQueued();
    }
    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'name',
        'sku',
        'price',
        'compare_price',
        'image_path',
        'stock_quantity',
        'stock_status',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    #[Scope]
    protected function active(Builder $builder): void
    {
        $builder->where('is_active', true);
    }

    #[Scope]
    protected function inStock(Builder $builder): void
    {
        $builder->where('stock_status', 'in_stock')
            ->where('stock_quantity', '>', 0);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getDisplayLabelAttribute(): string
    {
        return $this->name;
        // $parts = array_filter([$this->color?->name, $this->size?->name]);
        // return $parts ? implode(' / ', $parts) : ($this->name ?? 'Variant');
    }

    public function getDiscountPercentageAttribute(): int
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
        }
        return 0;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $variant->sku = 'VAR-' . Str::random(8);
            }
        });
        // static::saving(function ($variant) {
        //     // Auto-generate a human-friendly name from color + size
        //     // $variant->name = $variant->display_label;
        // });
    }
}
