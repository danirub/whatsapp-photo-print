<?php

namespace App\Filament\Resources\PrintSizes\Pages;

use App\Filament\Resources\PrintSizes\PrintSizeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrintSize extends EditRecord
{
    protected static string $resource = PrintSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
