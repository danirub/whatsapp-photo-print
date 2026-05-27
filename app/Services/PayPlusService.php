<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPlusService
{
    private string $apiUrl = 'https://restapi.payplus.co.il/api/v1.0';
    private string $apiKey;
    private string $secretKey;

    public function __construct()
    {
        $this->apiKey = config('services.payplus.api_key');
        $this->secretKey = config('services.payplus.secret_key');
    }

    public function createPaymentLink(Order $order): ?PaymentTransaction
    {
        $callbackUrl = route('webhook.payplus');
        $successUrl = config('app.url') . '/payment/success';
        $failUrl = config('app.url') . '/payment/fail';

        $payload = [
            'payment_page_uid' => config('services.payplus.page_uid'),
            'charge_method' => 1,
            'amount' => (float) $order->total_price,
            'currency_code' => 'ILS',
            'order_id' => (string) $order->id,
            'more_info' => "WhatsApp Order #{$order->id} - {$order->whatsapp_phone}",
            'more_info_1' => $order->whatsapp_phone,
            'more_info_2' => (string) $order->image_count,
            'webhook_url' => $callbackUrl,
            'success_url' => $successUrl,
            'fail_url' => $failUrl,
            'language' => 'HE',
        ];

        $response = Http::withHeaders([
            'Authorization' => $this->buildAuthHeader($payload),
            'Content-Type' => 'application/json',
        ])->post("{$this->apiUrl}/PaymentPages/generateLink", $payload);

        if (!$response->successful()) {
            Log::error('PayPlus createPaymentLink failed', [
                'order_id' => $order->id,
                'response' => $response->json(),
            ]);
            return null;
        }

        $data = $response->json();

        $transaction = PaymentTransaction::create([
            'order_id' => $order->id,
            'payplus_page_request_uid' => $data['data']['page_request_uid'] ?? null,
            'payment_url' => $data['data']['payment_page_link'] ?? null,
            'amount' => $order->total_price,
            'status' => 'pending',
            'response_data' => $data,
        ]);

        return $transaction;
    }

    public function handleWebhook(array $payload): bool
    {
        $pageRequestUid = $payload['page_request_uid'] ?? null;
        $statusCode = $payload['status_code'] ?? null;

        if (!$pageRequestUid) {
            return false;
        }

        $transaction = PaymentTransaction::where('payplus_page_request_uid', $pageRequestUid)->first();

        if (!$transaction) {
            Log::warning('PayPlus webhook: transaction not found', ['uid' => $pageRequestUid]);
            return false;
        }

        if ($statusCode === '000') {
            $transaction->update(['status' => 'paid', 'response_data' => $payload]);
            $transaction->order->update(['payment_status' => 'paid', 'status' => 'completed']);
        } else {
            $transaction->update(['status' => 'failed', 'response_data' => $payload]);
        }

        return true;
    }

    private function buildAuthHeader(array $payload): string
    {
        $body = json_encode($payload);
        $hash = hash_hmac('sha256', $body, $this->secretKey);
        return base64_encode($this->apiKey . ':' . $hash);
    }
}
