<?php

namespace App\Models;

use App\Models\SizeChartSize;
use App\Models\SizeChartMeasurement;
use Illuminate\Database\Eloquent\Model;

class SizeChartValue extends Model
{
    protected $guarded = [];

    public function size()
    {
        return $this->belongsTo(SizeChartSize::class, 'size_chart_size_id');
    }

    public function measurement()
    {
        return $this->belongsTo(SizeChartMeasurement::class, 'size_chart_measurement_id');
    }
}
