<?php

namespace App\Services;

use App\Models\BotMessage;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\PrintSize;
use Illuminate\Support\Facades\Log;

class WhatsAppBotService
{
    public function __construct(
        private WhatsAppService $whatsApp,
        private PayPlusService $payPlus,
    ) {}

    public function handle(array $message, string $phone): void
    {
        $type = $message['type'] ?? 'text';
        $order = $this->getOrCreateActiveOrder($phone);

        match ($order->status) {
            'collecting'         => $this->handleCollecting($message, $type, $order),
            'selecting_size'     => $this->handleSelectingSize($message, $type, $order),
            'confirming'         => $this->handleConfirming($message, $type, $order),
            'selecting_payment'  => $this->handleSelectingPayment($message, $type, $order),
            'payment_pending'    => $this->handlePaymentPending($message, $type, $order),
            default              => $this->handleCollecting($message, $type, $order),
        };
    }

    private function handleCollecting(array $message, string $type, Order $order): void
    {
        if ($type === 'image') {
            $mediaId = $message['image']['id'] ?? null;
            if (!$mediaId) return;

            $mediaFile = $this->whatsApp->downloadMedia($mediaId);
            if ($mediaFile) {
                OrderImage::create([
                    'order_id'          => $order->id,
                    'file_path'         => $mediaFile['path'],
                    'original_filename' => $mediaFile['filename'],
                    'mime_type'         => $mediaFile['mime_type'],
                    'file_size'         => $mediaFile['size'],
                    'whatsapp_media_id' => $mediaId,
                ]);

                $order->increment('image_count');

                $this->whatsApp->sendText(
                    $order->whatsapp_phone,
                    BotMessage::get('image_received', ['count' => $order->fresh()->image_count])
                );
            }
            return;
        }

        if ($type === 'text') {
            $body = strtolower(trim($message['text']['body'] ?? ''));
            $finishKeywords = ['finish', 'done', 'סיום', 'סיימתי', 'סיים', 'גמרתי'];

            if (in_array($body, $finishKeywords)) {
                if ($order->image_count === 0) {
                    $this->whatsApp->sendText(
                        $order->whatsapp_phone,
                        BotMessage::get('no_images_yet')
                    );
                    return;
                }

                $order->update(['status' => 'selecting_size']);
                $this->sendSizeSelection($order);
                return;
            }

            // Welcome message for first contact
            $this->whatsApp->sendText(
                $order->whatsapp_phone,
                BotMessage::get('welcome')
            );
        }
    }

    private function sendSizeSelection(Order $order): void
    {
        $sizes = PrintSize::active()->get();

        if ($sizes->isEmpty()) {
            $this->whatsApp->sendText($order->whatsapp_phone, 'No print sizes configured. Please contact support.');
            return;
        }

        $sizeLines = $sizes->map(fn($s) => "{$s->label}) {$s->name}" . ($s->dimensions ? " ({$s->dimensions})" : '') . " - ₪{$s->price} per photo")->join("\n");

        $message = BotMessage::get('select_size', [
            'count' => $order->image_count,
            'sizes' => $sizeLines,
        ]);

        // Use list if ≤10 sizes, otherwise use text
        if ($sizes->count() <= 3) {
            $buttons = $sizes->map(fn($s) => [
                'id' => 'size_' . $s->id,
                'title' => "{$s->label} - ₪{$s->price}",
            ])->toArray();

            $this->whatsApp->sendButtons($order->whatsapp_phone, $message, $buttons);
        } else {
            $rows = $sizes->map(fn($s) => [
                'id' => 'size_' . $s->id,
                'title' => "{$s->label} - {$s->name}",
                'description' => "₪{$s->price} per photo",
            ])->toArray();

            $this->whatsApp->sendList(
                $order->whatsapp_phone,
                $message,
                BotMessage::get('choose_size_button'),
                [['title' => 'Print Sizes', 'rows' => $rows]]
            );
        }
    }

    private function handleSelectingSize(array $message, string $type, Order $order): void
    {
        $selectedId = null;

        if ($type === 'interactive') {
            $replyId = $message['interactive']['button_reply']['id']
                ?? $message['interactive']['list_reply']['id']
                ?? null;

            if ($replyId && str_starts_with($replyId, 'size_')) {
                $selectedId = (int) substr($replyId, 5);
            }
        } elseif ($type === 'text') {
            $body = strtoupper(trim($message['text']['body'] ?? ''));
            $size = PrintSize::active()->where('label', $body)->first();
            if ($size) {
                $selectedId = $size->id;
            }
        }

        if (!$selectedId) {
            $this->whatsApp->sendText(
                $order->whatsapp_phone,
                BotMessage::get('invalid_size_selection')
            );
            $this->sendSizeSelection($order);
            return;
        }

        $size = PrintSize::find($selectedId);
        if (!$size) return;

        $total = $order->image_count * $size->price;

        $order->update([
            'print_size_id' => $size->id,
            'total_price'   => $total,
            'status'        => 'confirming',
        ]);

        $message = BotMessage::get('order_summary', [
            'count'      => $order->image_count,
            'size_name'  => $size->name,
            'size_label' => $size->label,
            'price_each' => $size->price,
            'total'      => $total,
        ]);

        $this->whatsApp->sendButtons(
            $order->whatsapp_phone,
            $message,
            [
                ['id' => 'confirm_yes', 'title' => BotMessage::get('confirm_yes_button')],
                ['id' => 'confirm_no', 'title' => BotMessage::get('confirm_no_button')],
            ]
        );
    }

    private function handleConfirming(array $message, string $type, Order $order): void
    {
        $reply = null;

        if ($type === 'interactive') {
            $reply = $message['interactive']['button_reply']['id'] ?? null;
        } elseif ($type === 'text') {
            $body = strtolower(trim($message['text']['body'] ?? ''));
            if (in_array($body, ['yes', 'כן', 'אישור', 'confirm', '✓'])) {
                $reply = 'confirm_yes';
            } elseif (in_array($body, ['no', 'לא', 'ביטול', 'cancel'])) {
                $reply = 'confirm_no';
            }
        }

        if ($reply === 'confirm_yes') {
            $order->update(['status' => 'selecting_payment']);
            $this->whatsApp->sendButtons(
                $order->whatsapp_phone,
                BotMessage::get('select_payment'),
                [
                    ['id' => 'pay_store', 'title' => BotMessage::get('pay_store_button')],
                    ['id' => 'pay_card', 'title' => BotMessage::get('pay_card_button')],
                ]
            );
        } elseif ($reply === 'confirm_no') {
            $order->update(['status' => 'collecting', 'print_size_id' => null, 'total_price' => null]);
            $this->whatsApp->sendText(
                $order->whatsapp_phone,
                BotMessage::get('order_cancelled_reupload')
            );
        } else {
            $this->whatsApp->sendText(
                $order->whatsapp_phone,
                BotMessage::get('confirm_prompt')
            );
        }
    }

    private function handleSelectingPayment(array $message, string $type, Order $order): void
    {
        $reply = null;

        if ($type === 'interactive') {
            $reply = $message['interactive']['button_reply']['id'] ?? null;
        } elseif ($type === 'text') {
            $body = strtolower(trim($message['text']['body'] ?? ''));
            if (in_array($body, ['store', 'חנות', '1'])) $reply = 'pay_store';
            if (in_array($body, ['card', 'כרטיס', 'credit', '2'])) $reply = 'pay_card';
        }

        if ($reply === 'pay_store') {
            $order->update([
                'payment_method' => 'store',
                'status'         => 'completed',
                'payment_status' => 'pending',
            ]);
            $this->whatsApp->sendText(
                $order->whatsapp_phone,
                BotMessage::get('pay_store_confirmation', ['order_id' => $order->id])
            );
        } elseif ($reply === 'pay_card') {
            $transaction = $this->payPlus->createPaymentLink($order);

            if (!$transaction || !$transaction->payment_url) {
                $this->whatsApp->sendText(
                    $order->whatsapp_phone,
                    BotMessage::get('payment_link_error')
                );
                return;
            }

            $order->update([
                'payment_method' => 'credit_card',
                'status'         => 'payment_pending',
            ]);

            $this->whatsApp->sendText(
                $order->whatsapp_phone,
                BotMessage::get('payment_link_sent', [
                    'total' => $order->total_price,
                    'url'   => $transaction->payment_url,
                ])
            );
        } else {
            $this->whatsApp->sendText(
                $order->whatsapp_phone,
                BotMessage::get('invalid_payment_choice')
            );
        }
    }

    private function handlePaymentPending(array $message, string $type, Order $order): void
    {
        // Refresh to check if payment came in via webhook
        $order->refresh();

        if ($order->payment_status === 'paid') {
            $this->whatsApp->sendText(
                $order->whatsapp_phone,
                BotMessage::get('payment_confirmed', ['order_id' => $order->id])
            );
            return;
        }

        // Re-send payment link
        $transaction = $order->latestTransaction;
        if ($transaction && $transaction->payment_url) {
            $this->whatsApp->sendText(
                $order->whatsapp_phone,
                BotMessage::get('payment_pending_reminder', [
                    'url' => $transaction->payment_url,
                ])
            );
        }
    }

    private function getOrCreateActiveOrder(string $phone): Order
    {
        $activeStatuses = ['collecting', 'selecting_size', 'confirming', 'selecting_payment', 'payment_pending'];

        return Order::where('whatsapp_phone', $phone)
            ->whereIn('status', $activeStatuses)
            ->latest()
            ->firstOrCreate(
                ['whatsapp_phone' => $phone, 'status' => 'collecting'],
                ['whatsapp_phone' => $phone, 'status' => 'collecting', 'image_count' => 0]
            );
    }
}
