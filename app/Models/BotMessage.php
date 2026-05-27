<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BotMessage extends Model
{
    protected $fillable = [
        'key',
        'description',
        'message_text',
    ];

    public static function get(string $key, array $replacements = []): string
    {
        $message = Cache::remember("bot_message_{$key}", 300, function () use ($key) {
            return static::where('key', $key)->value('message_text') ?? '';
        });

        foreach ($replacements as $search => $replace) {
            $message = str_replace("{{$search}}", $replace, $message);
        }

        return $message;
    }

    public static function clearCache(string $key): void
    {
        Cache::forget("bot_message_{$key}");
    }
}
