<?php

namespace App\Models;

use App\Models\SizeChart;
use Illuminate\Database\Eloquent\Model;

class SizeChartMeasurement extends Model
{
    protected $guarded = [];


    public function sizeCharts()
    {
        return $this->belongsToMany(
            SizeChart::class,
            'measurement_size_chart'
        );
    }
    public function values()
    {
        return $this->hasMany(SizeChartValue::class, 'measurement_id');
    }
}
