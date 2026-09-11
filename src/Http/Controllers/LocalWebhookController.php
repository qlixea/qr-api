<?php

namespace Qlixea\PaymentHub\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class LocalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header('X-Qlixea-Signature');
        $payload = $request->getContent();
        $secret = config('qlixea.api_secret');

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Qlixea Webhook: Firma inválida', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Firma inválida'], 401);
        }

        $data = $request->all();
        Log::info('Qlixea Webhook recibido', ['data' => $data]);

        // Aquí puedes disparar un evento de Laravel:
        // event(new \Qlixea\PaymentHub\Events\PaymentStatusChanged($data));

        return response()->json(['received' => true], 200);
    }
}