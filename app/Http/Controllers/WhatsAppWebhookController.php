<?php

namespace App\Http\Controllers;

use App\Models\ConversationMessage;
use App\Models\FlowCanvas;
use App\Services\FlowEngineService;
use App\Services\WhatsAppBotService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        private WhatsAppBotService $bot,
        private FlowEngineService $flowEngine,
    ) {}

    // Meta webhook verification
    public function verify(Request $request): Response
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('services.whatsapp.verify_token')) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    // Incoming messages
    public function receive(Request $request): Response
    {
        $payload = $request->all();
        Log::debug('WhatsApp webhook received', $payload);

        $entry = $payload['entry'][0] ?? null;
        $changes = $entry['changes'][0] ?? null;
        $value = $changes['value'] ?? null;
        $messages = $value['messages'] ?? [];

        foreach ($messages as $message) {
            $phone = $message['from'] ?? null;
            if (!$phone) continue;

            // Log inbound message
            $type = $message['type'] ?? 'text';
            $content = match ($type) {
                'text'        => $message['text']['body'] ?? '',
                'image'       => '[Image received]',
                'interactive' => $message['interactive']['button_reply']['title']
                                 ?? $message['interactive']['list_reply']['title']
                                 ?? '[Interactive]',
                default       => '[' . strtoupper($type) . ']',
            };
            ConversationMessage::log($phone, 'inbound', $content, $type);

            try {
                // Use visual flow engine if a flow has been saved, otherwise fall back to hardcoded bot
                if (FlowCanvas::hasFlow()) {
                    $this->flowEngine->handle($message, $phone);
                } else {
                    $this->bot->handle($message, $phone);
                }
            } catch (\Throwable $e) {
                Log::error('Bot error', [
                    'phone'   => $phone,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                ]);
            }
        }

        return response('OK', 200);
    }
}
