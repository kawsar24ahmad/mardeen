<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BDCourierService
{
    public function check(string $phone): array
    {
        $response = Http::withToken(env('BD_COURIER_API_KEY'))
            ->acceptJson()
            ->post(env('BD_COURIER_BASE_URL') . '/courier-check', [
                'phone' => $phone,
            ]);

        if ($response->failed()) {
            return [
                'status' => 'error',
                'message' => 'API Request Failed'
            ];
        }

        return $response->json();
    }
}
