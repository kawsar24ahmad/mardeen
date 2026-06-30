<?php

namespace App\Courier\Drivers;

use App\Models\Courier;
use Illuminate\Support\Facades\Http;
use App\Courier\DTO\CourierOrderData;
use App\Courier\DTO\CourierResponseData;
use App\Courier\Contracts\CourierInterface;

class SteadfastCourier implements CourierInterface
{
    public function __construct(
        protected Courier $courier,
    ) {}
    public function createOrder(CourierOrderData $order): CourierResponseData
    {
        $payload =  [
            'invoice' => $order->invoice,
            'recipient_name' => $order->customer_name,
            'recipient_phone' => $order->phone,
            'recipient_address' => $order->address,
            'cod_amount' => $order->cod_amount,
            'parcel_weight' => 1.0,
        ];



        $response = Http::withHeaders([
            'API-KEY' => $this->courier->api_key,
            'Secret-Key' => $this->courier->secret_key,
            'Accept' => 'application/json',
        ])->post($this->courier->base_url . '/create_order', $payload);

        // $response = Http::withHeader([
        //     'Api-Key'      => $this->courier->api_key,
        //     'Secret-Key'   => $this->courier->secret_key,
        //     'Content-Type' => 'application/json',
        // ])
        //     ->post($this->courier->base_url . '/create_order', $payload);


        // dd(
        //     $response->status(),
        //     $response->body(),
        //     $response->headers()
        // );

        if (! $response->successful()) {
            throw new \Exception($response->body());
        }

        $response = $response->json();

        return new CourierResponseData(
            success: true,
            trackingCode: $response['consignment']['tracking_code'],
            consignmentId: (string) $response['consignment']['consignment_id'],
            status: $response['consignment']['status'],
            message: $response['message'],
            raw: $response,
        );
    }

    public function cancelOrder($tracking) {}

    public function trackOrder($tracking) {}

    public function printLabel($tracking) {}
}
