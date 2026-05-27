<?php

namespace App\Filament\Resources\BotMessages\Pages;

use App\Filament\Resources\BotMessages\BotMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBotMessage extends EditRecord
{
    protected static string $resource = BotMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
