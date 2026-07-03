<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'banner_title',
        'banner_image',
        'serial_number',
        'is_active'
    ];
}
