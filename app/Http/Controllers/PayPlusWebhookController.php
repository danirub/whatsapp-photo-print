<?php

namespace App\Http\Controllers;

use App\Models\BotMessage;
use App\Services\PayPlusService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPlusWebhookController extends Controller
{
    public function __construct(
        private PayPlusService $payPlus,
        private WhatsAppService $whatsApp,
    ) {}

    public function handle(Request $request): \Illuminate\Http\Response
    {
        $payload = $request->all();
        Log::debug('PayPlus webhook received', $payload);

        $success = $this->payPlus->handleWebhook($payload);

        if ($success) {
            // Notify customer on WhatsApp
            $orderId = $payload['more_info'] ?? null;
            $phone   = $payload['more_info_1'] ?? null;
            $status  = $payload['status_code'] ?? null;

            if ($phone) {
                if ($status === '000') {
                    $this->whatsApp->sendText(
                        $phone,
                        BotMessage::get('payment_confirmed', ['order_id' => $orderId ?? ''])
                    );
                } else {
                    $this->whatsApp->sendText(
                        $phone,
                        BotMessage::get('payment_failed')
                    );
                }
            }
        }

        return response('OK', 200);
    }
}
