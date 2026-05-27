<?php

namespace App\Services;

use App\Models\BotMessage;
use App\Models\FlowCanvas;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\PrintSize;
use Illuminate\Support\Facades\Log;

class FlowEngineService
{
    private array $flow = [];

    public function __construct(
        private WhatsAppService $whatsApp,
        private PayPlusService $payPlus,
    ) {}

    public function handle(array $message, string $phone): void
    {
        $this->flow = FlowCanvas::getFlowData();

        if (empty($this->flow['drawflow']['Home']['data'] ?? [])) {
            // No flow configured — fall back to hardcoded bot
            app(WhatsAppBotService::class)->handle($message, $phone);
            return;
        }

        $order = $this->getOrCreateOrder($phone);

        try {
            $this->processMessage($message, $order);
        } catch (\Throwable $e) {
            Log::error('FlowEngine error', ['phone' => $phone, 'error' => $e->getMessage()]);
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // Message processing
    // ──────────────────────────────────────────────────────────────────────

    private function processMessage(array $message, Order $order): void
    {
        $nodes = $this->flow['drawflow']['Home']['data'];

        // Bootstrap: find start node and enter it
        if (!$order->current_node_id) {
            $startNode = collect($nodes)->first(fn($n) => ($n['data']['node_type'] ?? '') === 'start');
            if (!$startNode) return;
            $order->update(['current_node_id' => (string) $startNode['id']]);
            $this->enterNode((string) $startNode['id'], $order);
            return;
        }

        $currentNodeId = $order->current_node_id;
        $currentNode   = $nodes[$currentNodeId] ?? null;
        if (!$currentNode) return;

        $nodeType    = $currentNode['data']['node_type'] ?? '';
        $messageType = $message['type'] ?? 'text';

        // ── wait_image: images are handled internally (save + ack + stay)
        if ($nodeType === 'wait_image' && $messageType === 'image') {
            $this->saveImage($message, $order);
            return;
        }

        // Find matching outgoing edge and follow it
        $nextId = $this->resolveNextNode($currentNode, $message, $order);
        if ($nextId) {
            $order->update(['current_node_id' => $nextId]);
            $this->enterNode($nextId, $order, $message);
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // Entering a node
    // ──────────────────────────────────────────────────────────────────────

    private function enterNode(string $nodeId, Order $order, array $message = []): void
    {
        $nodes = $this->flow['drawflow']['Home']['data'];
        $node  = $nodes[$nodeId] ?? null;
        if (!$node) return;

        $data     = $node['data'];
        $nodeType = $data['node_type'] ?? '';
        $phone    = $order->whatsapp_phone;

        switch ($nodeType) {
            case 'start':
                $next = $this->getFirstOutput($node);
                if ($next) {
                    $order->update(['current_node_id' => $next]);
                    $this->enterNode($next, $order, $message);
                }
                break;

            case 'send_message':
                if (!empty($data['message_key'])) {
                    $this->whatsApp->sendText($phone, BotMessage::get($data['message_key']));
                }
                $next = $this->getFirstOutput($node);
                if ($next) {
                    $order->update(['current_node_id' => $next]);
                    $this->enterNode($next, $order, $message);
                }
                break;

            case 'wait_image':
                // Show welcome/instruction message on first entry
                if (!empty($data['enter_message'])) {
                    $this->whatsApp->sendText($phone, BotMessage::get($data['enter_message']));
                }
                // Stay here — wait for images or "finish"
                break;

            case 'send_buttons':
                $msgText = !empty($data['message_key']) ? BotMessage::get($data['message_key']) : ($data['label'] ?? '');
                $buttons = $data['buttons'] ?? [];
                if (!empty($buttons) && !empty($msgText)) {
                    $this->whatsApp->sendButtons($phone, $msgText, $buttons);
                }
                // Stay here — wait for button reply
                break;

            case 'select_size':
                $this->sendSizeSelection($order, $data);
                // Stay here
                break;

            case 'show_order_total':
                $this->sendOrderSummary($order, $data);
                // Stay here
                break;

            case 'action':
                $this->executeAction($data['action'] ?? '', $nodeId, $node, $order, $message);
                break;

            case 'end':
                if (!empty($data['message_key'])) {
                    $this->whatsApp->sendText($phone, BotMessage::get($data['message_key']));
                }
                break;
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // Resolve next node from edge triggers
    // ──────────────────────────────────────────────────────────────────────

    private function resolveNextNode(array $currentNode, array $message, Order $order): ?string
    {
        $outputLabels = $currentNode['data']['output_labels'] ?? [];
        $outputs      = $currentNode['outputs'] ?? [];
        $messageType  = $message['type'] ?? 'text';
        $textBody     = strtolower(trim($message['text']['body'] ?? ''));
        $buttonId     = $message['interactive']['button_reply']['id']
            ?? $message['interactive']['list_reply']['id']
            ?? null;

        $finishKeywords = ['finish', 'done', 'סיום', 'סיימתי', 'סיים', 'גמרתי'];

        foreach ($outputLabels as $outputName => $config) {
            $trigger = $config['trigger'] ?? 'any';
            $matched = false;

            if ($trigger === 'any') {
                $matched = true;
            } elseif ($trigger === 'image') {
                $matched = $messageType === 'image';
            } elseif ($trigger === 'text:finish') {
                $matched = $messageType === 'text' && in_array($textBody, $finishKeywords);
            } elseif ($trigger === 'text:*') {
                $matched = $messageType === 'text';
            } elseif (str_starts_with($trigger, 'text:')) {
                $expected = strtolower(substr($trigger, 5));
                $matched  = $messageType === 'text' && $textBody === $expected;
            } elseif (str_starts_with($trigger, 'button:')) {
                $btnId   = substr($trigger, 7);
                $matched = $messageType === 'interactive' && $buttonId === $btnId;
            } elseif ($trigger === 'button:confirm_yes') {
                $matched = $messageType === 'interactive' && $buttonId === 'confirm_yes';
            } elseif ($trigger === 'button:confirm_no') {
                $matched = $messageType === 'interactive' && $buttonId === 'confirm_no';
            } elseif ($trigger === 'size_selected') {
                // Any size button (size_1, size_2, etc.)
                $matched = $messageType === 'interactive' && str_starts_with($buttonId ?? '', 'size_');
                if ($matched && $buttonId) {
                    // Store the selected size on the order
                    $sizeId = (int) substr($buttonId, 5);
                    $size   = PrintSize::find($sizeId);
                    if ($size) {
                        $total = $order->image_count * $size->price;
                        $order->update(['print_size_id' => $size->id, 'total_price' => $total]);
                    }
                }
            }

            if ($matched) {
                $connections = $outputs[$outputName]['connections'] ?? [];
                if (!empty($connections)) {
                    return (string) $connections[0]['node'];
                }
            }
        }

        return null;
    }

    // ──────────────────────────────────────────────────────────────────────
    // Special node actions
    // ──────────────────────────────────────────────────────────────────────

    private function saveImage(array $message, Order $order): void
    {
        $mediaId = $message['image']['id'] ?? null;
        if (!$mediaId) return;

        $file = $this->whatsApp->downloadMedia($mediaId);
        if ($file) {
            OrderImage::create([
                'order_id'          => $order->id,
                'file_path'         => $file['path'],
                'original_filename' => $file['filename'],
                'mime_type'         => $file['mime_type'],
                'file_size'         => $file['size'],
                'whatsapp_media_id' => $mediaId,
            ]);
            $order->increment('image_count');
            $this->whatsApp->sendText(
                $order->whatsapp_phone,
                BotMessage::get('image_received', ['count' => $order->fresh()->image_count])
            );
        }
    }

    private function sendSizeSelection(Order $order, array $data): void
    {
        $sizes     = PrintSize::active()->get();
        $sizeLines = $sizes->map(fn($s) =>
            "{$s->label}) {$s->name}" . ($s->dimensions ? " ({$s->dimensions})" : '') . " - ₪{$s->price}"
        )->join("\n");

        $messageText = BotMessage::get('select_size', [
            'count' => $order->image_count,
            'sizes' => $sizeLines,
        ]);

        if ($sizes->count() <= 3) {
            $buttons = $sizes->map(fn($s) => ['id' => 'size_' . $s->id, 'title' => "{$s->label} - ₪{$s->price}"])->toArray();
            $this->whatsApp->sendButtons($order->whatsapp_phone, $messageText, $buttons);
        } else {
            $rows = $sizes->map(fn($s) => [
                'id'          => 'size_' . $s->id,
                'title'       => "{$s->label} - {$s->name}",
                'description' => "₪{$s->price} per photo",
            ])->toArray();
            $this->whatsApp->sendList(
                $order->whatsapp_phone,
                $messageText,
                BotMessage::get('choose_size_button'),
                [['title' => 'Print Sizes', 'rows' => $rows]]
            );
        }
    }

    private function sendOrderSummary(Order $order, array $data): void
    {
        $size        = $order->printSize;
        $messageText = BotMessage::get('order_summary', [
            'count'      => $order->image_count,
            'size_name'  => $size?->name ?? '—',
            'size_label' => $size?->label ?? '—',
            'price_each' => $size?->price ?? '0',
            'total'      => $order->total_price,
        ]);

        $this->whatsApp->sendButtons($order->whatsapp_phone, $messageText, [
            ['id' => 'confirm_yes', 'title' => BotMessage::get('confirm_yes_button')],
            ['id' => 'confirm_no',  'title' => BotMessage::get('confirm_no_button')],
        ]);
    }

    private function executeAction(string $action, string $nodeId, array $node, Order $order, array $message): void
    {
        $phone = $order->whatsapp_phone;

        switch ($action) {
            case 'create_payment_link':
                $order->update(['payment_method' => 'credit_card', 'status' => 'payment_pending']);
                $transaction = $this->payPlus->createPaymentLink($order);
                if ($transaction && $transaction->payment_url) {
                    $this->whatsApp->sendText($phone, BotMessage::get('payment_link_sent', [
                        'total' => $order->total_price,
                        'url'   => $transaction->payment_url,
                    ]));
                } else {
                    $this->whatsApp->sendText($phone, BotMessage::get('payment_link_error'));
                }
                break;

            case 'mark_store_payment':
                $order->update(['payment_method' => 'store', 'status' => 'completed', 'payment_status' => 'pending']);
                $this->whatsApp->sendText($phone, BotMessage::get('pay_store_confirmation', ['order_id' => $order->id]));
                break;

            case 'mark_completed':
                $order->update(['status' => 'completed', 'payment_status' => 'paid']);
                break;

            case 'reset_order':
                $order->update([
                    'status'          => 'collecting',
                    'print_size_id'   => null,
                    'total_price'     => null,
                    'current_node_id' => null,
                ]);
                $this->whatsApp->sendText($phone, BotMessage::get('order_cancelled_reupload'));
                // Re-enter from start
                $this->processMessage(['type' => 'text', 'text' => ['body' => '__reset__']], $order);
                return;
        }

        // Auto-advance after action
        $next = $this->getFirstOutput($node);
        if ($next) {
            $order->update(['current_node_id' => $next]);
            $this->enterNode($next, $order, $message);
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────

    private function getFirstOutput(array $node): ?string
    {
        $outputs = $node['outputs'] ?? [];
        foreach ($outputs as $output) {
            $connections = $output['connections'] ?? [];
            if (!empty($connections)) {
                return (string) $connections[0]['node'];
            }
        }
        return null;
    }

    private function getOrCreateOrder(string $phone): Order
    {
        $active = ['collecting', 'selecting_size', 'confirming', 'selecting_payment', 'payment_pending'];
        return Order::where('whatsapp_phone', $phone)
            ->whereIn('status', $active)
            ->latest()
            ->firstOrCreate(
                ['whatsapp_phone' => $phone, 'status' => 'collecting'],
                ['whatsapp_phone' => $phone, 'status' => 'collecting', 'image_count' => 0]
            );
    }
}
