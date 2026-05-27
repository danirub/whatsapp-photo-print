<?php

namespace App\Filament\Pages;

use App\Models\BotMessage;
use App\Models\FlowCanvas;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class FlowBuilderPage extends Page
{
    protected string $view = 'filament.pages.flow-builder-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShare;

    protected static ?string $navigationLabel = 'Bot Flow Builder';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Bot Flow Builder';

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public array $flowData = [];

    public array $botMessages = [];

    public function mount(): void
    {
        $this->flowData   = FlowCanvas::getFlowData();
        $this->botMessages = BotMessage::orderBy('key')->get(['key', 'description', 'message_text'])
            ->map(fn($m) => [
                'key'         => $m->key,
                'description' => $m->description,
                'preview'     => \Illuminate\Support\Str::limit($m->message_text, 50),
            ])->toArray();
    }

    public function saveFlow(array $data): void
    {
        FlowCanvas::updateOrCreate(['id' => 1], [
            'name'      => 'Main Flow',
            'flow_data' => $data,
        ]);

        Notification::make()
            ->title('Flow saved successfully!')
            ->success()
            ->send();
    }

    public function resetFlow(): void
    {
        FlowCanvas::truncate();
        $this->flowData = [];

        Notification::make()
            ->title('Flow reset. Page will reload.')
            ->warning()
            ->send();
    }
}
