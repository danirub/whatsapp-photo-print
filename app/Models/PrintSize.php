<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrintSize extends Model
{
    protected $fillable = [
        'label',
        'name',
        'dimensions',
        'price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getDisplayLabelAttribute(): string
    {
        return "{$this->label} - {$this->name}" . ($this->dimensions ? " ({$this->dimensions})" : '') . " - ₪{$this->price}";
    }
}
