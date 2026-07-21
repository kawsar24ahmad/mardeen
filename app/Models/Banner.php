<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Banner extends Model implements HasMedia
{
    use InteractsWithMedia;
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('optimized')
            ->format('webp')        // WebP ফরম্যাটে রূপান্তর
            ->quality(80)           // ইমেজ কোয়ালিটি ৮০% (ফাইল সাইজ অনেক কমে যাবে)
            ->width(1200)           // ম্যাক্স উইডথ ১২০০ পিক্সেল (প্রয়োজন অনুযায়ী চেঞ্জ করুন)
            ->optimize();
    }
    protected $fillable = [
        'banner_title',
        'banner_image',
        'serial_number',
        'is_active'
    ];
}
