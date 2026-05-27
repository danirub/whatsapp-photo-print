<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlowCanvas extends Model
{
    protected $table = 'flow_canvas';

    protected $fillable = ['name', 'flow_data'];

    protected $casts = ['flow_data' => 'array'];

    public static function getFlowData(): array
    {
        return static::first()?->flow_data ?? [];
    }

    public static function hasFlow(): bool
    {
        $data = static::getFlowData();
        return !empty($data['drawflow']['Home']['data'] ?? []);
    }
}
