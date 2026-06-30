<?php

namespace App\Courier\Contracts;

use App\Courier\DTO\CourierOrderData;
use App\Courier\DTO\CourierResponseData;


interface CourierInterface
{
    public function createOrder(CourierOrderData $order): CourierResponseData;

    public function cancelOrder(string $tracking);

    public function trackOrder(string $tracking);

    public function printLabel(string $tracking);
}
