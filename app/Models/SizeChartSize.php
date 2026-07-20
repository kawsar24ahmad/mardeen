<?php

namespace App\Models;

use App\Models\Product;
use App\Models\SizeChart;
use Illuminate\Database\Eloquent\Model;

class SizeChartSize extends Model
{
    protected $guarded = [];

    public function sizeChart()
    {
        return $this->belongsTo(SizeChart::class);
    }
    public function values()
    {
        return $this->hasMany(SizeChartValue::class, 'size_chart_size_id');
    }
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_size_chart_size'
        )->withTimestamps();
    }
}
