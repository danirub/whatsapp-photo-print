<?php

namespace App\Services;

use App\Models\ConversationMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsAppService
{
    private string $apiUrl;
    private string $token;
    private string $phoneNumberId;

    public function __construct()
    {
        $this->apiUrl = 'https://graph.facebook.com/v21.0';
        $this->token = config('services.whatsapp.token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    public function sendText(string $to, string $message, ?int $orderId = null): bool
    {
        $response = Http::withToken($this->token)
            ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

        if (!$response->successful()) {
            Log::error('WhatsApp send failed', ['to' => $to, 'response' => $response->json()]);
            return false;
        }

        ConversationMessage::log($to, 'outbound', $message, 'text', $orderId);
        return true;
    }

    public function sendButtons(string $to, string $body, array $buttons, ?int $orderId = null): bool
    {
        $buttonRows = array_map(fn($btn) => [
            'type' => 'reply',
            'reply' => [
                'id' => $btn['id'],
                'title' => $btn['title'],
            ],
        ], $buttons);

        $response = Http::withToken($this->token)
            ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'button',
                    'body' => ['text' => $body],
                    'action' => ['buttons' => $buttonRows],
                ],
            ]);

        if (!$response->successful()) {
            Log::error('WhatsApp send buttons failed', ['to' => $to, 'response' => $response->json()]);
            return false;
        }

        $labels = implode(' | ', array_column($buttons, 'title'));
        ConversationMessage::log($to, 'outbound', $body . "\n[Buttons: {$labels}]", 'buttons', $orderId, ['buttons' => $buttons]);
        return true;
    }

    public function sendList(string $to, string $body, string $buttonLabel, array $sections, ?int $orderId = null): bool
    {
        $response = Http::withToken($this->token)
            ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'list',
                    'body' => ['text' => $body],
                    'action' => [
                        'button' => $buttonLabel,
                        'sections' => $sections,
                    ],
                ],
            ]);

        if (!$response->successful()) {
            Log::error('WhatsApp send list failed', ['to' => $to, 'response' => $response->json()]);
            return false;
        }

        ConversationMessage::log($to, 'outbound', $body . "\n[List: {$buttonLabel}]", 'list', $orderId, ['sections' => $sections]);
        return true;
    }

    public function markAsRead(string $messageId): void
    {
        Http::withToken($this->token)
            ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'status' => 'read',
                'message_id' => $messageId,
            ]);
    }

    public function downloadMedia(string $mediaId): ?array
    {
        // Get media URL
        $urlResponse = Http::withToken($this->token)
            ->get("{$this->apiUrl}/{$mediaId}");

        if (!$urlResponse->successful()) {
            Log::error('WhatsApp media URL fetch failed', ['media_id' => $mediaId]);
            return null;
        }

        $mediaData = $urlResponse->json();
        $mediaUrl = $mediaData['url'] ?? null;
        $mimeType = $mediaData['mime_type'] ?? 'image/jpeg';

        if (!$mediaUrl) {
            return null;
        }

        // Download the media
        $mediaResponse = Http::withToken($this->token)->get($mediaUrl);

        if (!$mediaResponse->successful()) {
            Log::error('WhatsApp media download failed', ['url' => $mediaUrl]);
            return null;
        }

        $extension = match (explode('/', $mimeType)[1] ?? 'jpeg') {
            'png' => 'png',
            'gif' => 'gif',
            'webp' => 'webp',
            default => 'jpg',
        };

        $filename = 'whatsapp_' . uniqid() . '.' . $extension;
        $path = 'order_images/' . $filename;

        Storage::put($path, $mediaResponse->body());

        return [
            'path' => $path,
            'filename' => $filename,
            'mime_type' => $mimeType,
            'size' => strlen($mediaResponse->body()),
        ];
    }
}
