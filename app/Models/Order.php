<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'whatsapp_phone',
        'customer_name',
        'status',
        'print_size_id',
        'image_count',
        'total_price',
        'payment_method',
        'payment_status',
        'notes',
    ];

    // Statuses: collecting, selecting_size, confirming, selecting_payment, payment_pending, completed, cancelled
    // Payment methods: store, credit_card

    public function images(): HasMany
    {
        return $this->hasMany(OrderImage::class);
    }

    public function printSize(): BelongsTo
    {
        return $this->belongsTo(PrintSize::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function conversationMessages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class)->orderBy('created_at');
    }

    public function latestTransaction()
    {
        return $this->hasOne(PaymentTransaction::class)->latestOfMany();
    }

    public function recalculateTotal(): void
    {
        if ($this->printSize) {
            $this->total_price = $this->image_count * $this->printSize->price;
            $this->save();
        }
    }
}
