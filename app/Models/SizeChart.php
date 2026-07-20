<?php

namespace App\Models;

use App\Models\Product;
use App\Models\SizeChartSize;
use App\Models\SizeChartMeasurement;
use Illuminate\Database\Eloquent\Model;

class SizeChart extends Model
{
    protected $guarded = [];
    public function sizes()
    {
        return $this->hasMany(SizeChartSize::class)
            ->with('values')
            ->orderBy('sort_order');
    }

    public function measurements()
    {
        return $this->belongsToMany(
            SizeChartMeasurement::class,
            'measurement_size_chart'
        )->orderBy('sort_order');
    }



    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
