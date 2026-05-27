<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderImage extends Model
{
    protected $fillable = [
        'order_id',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'whatsapp_media_id',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getFullPathAttribute(): string
    {
        return storage_path('app/' . $this->file_path);
    }
}
