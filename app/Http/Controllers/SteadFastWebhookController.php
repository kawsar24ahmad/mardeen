<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SteadFastWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        // Verify Bearer token
        $token = $request->header('Authorization');


        if ($token !== 'Bearer ' . env('STEADFAST_BEARER_TOKEN')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        // Validate required fields
        $required = ['consignment_id', 'invoice', 'status', 'cod_amount', 'updated_at'];
        $missing  = array_diff($required, array_keys($payload));



        if (!empty($missing)) {
            return response()->json([
                'error' => 'Missing fields: ' . implode(', ', $missing),
            ], 400);
        }

        $this->processStatusUpdate($payload);

        Log::info('SteadFast webhook received', $payload);

        return response()->json(['status' => 'success'], 200);
    }

    private function processStatusUpdate(array $payload): void
    {
        $invoice = $payload['invoice'];
        $status        = $payload['status'];
        $tracking_message        = $payload['tracking_message'];

        // Example: update your orders table
        Order::where('order_number', $invoice)->update([
            'courier_status' => $status,
            'admin_notes' => $tracking_message,
        ]);

        // Add your own business logic here
    }
}
