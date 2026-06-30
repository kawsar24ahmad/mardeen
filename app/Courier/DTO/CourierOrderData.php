<?php

namespace App\Courier\DTO;

class CourierOrderData
{
    public function __construct(
        public string $customer_name,
        public string $phone,
        public string $address,
        public float $cod_amount,
        public int $weight,
        public string $invoice
    ) {}
}
