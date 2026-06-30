<?php

namespace App\Courier\DTO;

class CourierResponseData
{
    public function __construct(
        public bool $success,
        public ?string $trackingCode = null,
        public ?string $consignmentId = null,
        public ?string $status = null,
        public ?string $message = null,
        public array $raw = [],
    ) {}
}
