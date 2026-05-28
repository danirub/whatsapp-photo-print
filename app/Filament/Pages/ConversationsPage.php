<?php

namespace App\Filament\Pages;

use App\Models\ConversationMessage;
use App\Models\Order;
use App\Models\OrderImage;
use App\Services\WhatsAppService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ConversationsPage extends Page
{
    protected string $view = 'filament.pages.conversations-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Conversations';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Conversations';

    public ?string $selectedPhone = null;
    public string  $replyText     = '';

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function mount(): void
    {
        $latest = ConversationMessage::select('whatsapp_phone')
            ->orderByDesc('created_at')
            ->value('whatsapp_phone');

        $this->selectedPhone = $latest;
    }

    public function selectPhone(string $phone): void
    {
        $this->selectedPhone = $phone;
        $this->replyText     = '';
    }

    public function sendReply(): void
    {
        $text = trim($this->replyText);
        if (!$this->selectedPhone || !$text) return;

        $order = Order::where('whatsapp_phone', $this->selectedPhone)->latest()->first();

        app(WhatsAppService::class)->sendText($this->selectedPhone, $text, $order?->id);

        $this->replyText = '';

        Notification::make()->title('Message sent!')->success()->send();
    }

    public function getConversationList(): array
    {
        return ConversationMessage::selectRaw('whatsapp_phone, MAX(created_at) as last_at, COUNT(*) as msg_count')
            ->groupBy('whatsapp_phone')
            ->orderByDesc('last_at')
            ->get()
            ->map(function ($row) {
                $last = ConversationMessage::where('whatsapp_phone', $row->whatsapp_phone)
                    ->orderByDesc('created_at')
                    ->first();

                $order = Order::where('whatsapp_phone', $row->whatsapp_phone)
                    ->latest()
                    ->first();

                return [
                    'phone'     => $row->whatsapp_phone,
                    'last_msg'  => \Illuminate\Support\Str::limit($last?->content ?? '', 40),
                    'last_dir'  => $last?->direction,
                    'last_at'   => $last?->created_at?->diffForHumans(),
                    'msg_count' => $row->msg_count,
                    'order_id'  => $order?->id,
                    'status'    => $order?->status,
                ];
            })
            ->toArray();
    }

    public function getMessages(): array
    {
        if (!$this->selectedPhone) return [];

        // Pre-load order images for this phone grouped by order
        $order = Order::where('whatsapp_phone', $this->selectedPhone)->latest()->first();
        $orderImages = $order
            ? OrderImage::where('order_id', $order->id)->orderBy('created_at')->get()
            : collect();

        $imageIndex = 0;

        return ConversationMessage::where('whatsapp_phone', $this->selectedPhone)
            ->orderBy('created_at')
            ->get()
            ->map(function ($m) use ($orderImages, &$imageIndex) {
                $imageUrl = null;

                if ($m->message_type === 'image' && $m->direction === 'inbound') {
                    // Match image messages to saved OrderImages in sequence
                    if (isset($orderImages[$imageIndex])) {
                        $img      = $orderImages[$imageIndex];
                        $imageUrl = route('admin.order-image', $img->id);
                        $imageIndex++;
                    }
                }

                return [
                    'id'        => $m->id,
                    'direction' => $m->direction,
                    'type'      => $m->message_type,
                    'content'   => $m->content,
                    'time'      => $m->created_at->format('H:i'),
                    'date'      => $m->created_at->format('d/m/Y'),
                    'image_url' => $imageUrl,
                ];
            })
            ->toArray();
    }

    public function getSelectedOrder(): ?array
    {
        if (!$this->selectedPhone) return null;
        $order = Order::where('whatsapp_phone', $this->selectedPhone)->latest()->first();
        if (!$order) return null;
        return [
            'id'          => $order->id,
            'status'      => $order->status,
            'image_count' => $order->image_count,
            'total_price' => number_format($order->total_price, 2),
            'payment'     => $order->payment_status,
            'size'        => $order->printSize?->display_label ?? '—',
            'name'        => $order->customer_name ?? 'Unknown',
        ];
    }
}
