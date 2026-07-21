<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;

    public function registerMediaConversions(?Media $media = null): void
    {
        // ক্যাটাগরি কার্ডের জন্য ৪০০x৪০০ পিক্সেলের লাইটওয়েট WebP কনভার্সন
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->format('webp')
            ->quality(80)
            ->nonQueued();
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];
    #[Scope()]
    protected  function active(Builder $builder)
    {
        $builder->where('is_active', true);
    }
    #[Scope()]
    protected  function sorted(Builder $builder)
    {
        $builder->orderBy('sort_order', 'asc');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
