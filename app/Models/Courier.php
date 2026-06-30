<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'base_url',
        'api_key',
        'secret_key',
        'username',
        'password',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
