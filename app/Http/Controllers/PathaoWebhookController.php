<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PathaoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // $secret    = env('PATHAO_WEBHOOK_SECRET');
        $secret    = 'pathao-webhook-secret';
        $signature = $request->header('x-pathao-signature');

        // if (!$secret || !$signature) {
        //     return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        // }

        if ($secret != $signature) {
            Log::warning('Pathao webhook: invalid signature');
            return response()->json(['error' => 'Invalid signature'], Response::HTTP_FORBIDDEN);
        }

        $payload = $request->all();

        Log::info('Pathao webhook received', $payload);

        // TODO: Update your order by consignment_id / merchant_order_id

        return response()->json(
            ['success' => true],
            202,
            [
                'X-Pathao-Merchant-Webhook-Integration-Secret' => 'f3992ecc-59da-4cbe-a049-a13da2018d51', // Pathao Merchant Dashboard এর Webhook Integration section থেকে এই secret token পাওয়া যাবে
            ]
        );
    }
}
