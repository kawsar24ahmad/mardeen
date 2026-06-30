<?php

namespace App\Courier;

use Exception;
use App\Models\Courier;
use App\Courier\Drivers\PathaoCourier;
use App\Courier\Drivers\SteadfastCourier;

class CourierFactory
{
    public static function make(string $slug)
    {
        $courier = Courier::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return match ($slug) {

            'steadfast' => app(SteadfastCourier::class, [
                'courier' => $courier,
            ]),

            'pathao' => app(PathaoCourier::class, [
                'courier' => $courier,
            ]),


            default => throw new Exception("Courier [$slug] not found."),
        };
    }
}
