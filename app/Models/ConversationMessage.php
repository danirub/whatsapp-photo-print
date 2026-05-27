<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationMessage extends Model
{
    protected $fillable = ['order_id', 'whatsapp_phone', 'direction', 'message_type', 'content', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function log(
        string $phone,
        string $direction,
        string $content,
        string $type = 'text',
        ?int $orderId = null,
        ?array $metadata = null,
    ): self {
        return static::create([
            'whatsapp_phone' => $phone,
            'direction'      => $direction,
            'message_type'   => $type,
            'content'        => $content,
            'order_id'       => $orderId,
            'metadata'       => $metadata,
        ]);
    }
}
