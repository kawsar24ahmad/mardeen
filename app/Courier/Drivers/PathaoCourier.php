<?php

namespace App\Courier\Drivers;

use App\Models\Courier;
use App\Courier\DTO\CourierOrderData;
use App\Services\PathaoCourierService;
use App\Courier\DTO\CourierResponseData;
use App\Courier\Contracts\CourierInterface;

class PathaoCourier implements CourierInterface
{
    public function __construct(
        protected Courier $courier,
    ) {}
    public function createOrder(CourierOrderData $order): CourierResponseData
    {

        $pathao = new PathaoCourierService($this->courier);

        $response = $pathao->placeOrder([
            'store_id'                  => 150445,        // required  | integer  | Your merchant store ID (sets pickup location)
            'merchant_order_id'         =>  $order->invoice,    // nullable  | string   | Your own order tracking ID
            'recipient_name'            =>  $order->customer_name,   // required  | string   | 3–100 characters
            'recipient_phone'           => $order->phone, // required  | string   | Must be 11 characters
            'recipient_address'         => $order->address, // required | string | 10–220 characters
            'delivery_type'             => 48,           // required  | integer  | 48 = Normal Delivery, 12 = On Demand Delivery
            'item_type'                 => 2,            // required  | integer  | 1 = Document, 2 = Parcel
            'item_quantity'             => 1,            // required  | integer  | Number of parcels
            'item_weight'               => 0.5,          // required  | float    | Min: 0.5 kg — Max: 10 kg
            'amount_to_collect'         => (int) $order->cod_amount,          // required  | integer  | COD amount; use 0 for non-COD orders
        ]);

        // array:4 [▼ // app\Courier\Drivers\PathaoCourier.php:30
        //     "message" => "Order Created Successfully"
        //     "type" => "success"
        //     "code" => 200
        //     "data" => array:4 [▼
        //         "consignment_id" => "DT290626U5SNJ4"
        //         "merchant_order_id" => "ORD-6A42949358363"
        //         "order_status" => "Pending"
        //         "delivery_fee" => 110
        //     ]
        //     ]

        return new CourierResponseData(
            success: true,
            trackingCode: null,
            consignmentId: (string) $response['data']['consignment_id'],
            status: $response['data']['order_status'],
            message: $response['message'],
            raw: $response,
        );
    }

    public function cancelOrder($tracking) {}

    public function trackOrder($tracking) {}

    public function printLabel($tracking) {}
}
