<?php

namespace App\Courier\Services;

use Exception;
use App\Models\Order;
use App\Courier\CourierFactory;
use App\Courier\DTO\CourierOrderData;

class CourierService
{
    public function send(Order $order)
    {

        $order->load('courier');

        $driver = CourierFactory::make(
            $order->courier->slug
        );

        $response = $driver->createOrder(
            new CourierOrderData(
                customer_name: $order->shipping_full_name,
                phone: $order->shipping_phone,
                address: $order->shipping_address_line_1,
                cod_amount: $order->total,
                weight: 1,
                invoice: $order->order_number,
            )
        );

        if (! $response->success) {
            throw new Exception($response->message);
        }
        $order->update([
            'tracking_code' => $response->trackingCode,
            'consignment_id' => $response->consignmentId,
            'courier_status' => $response->status,
            'status'         => 'PROCESSING',
        ]);

        return $response;
    }
}
