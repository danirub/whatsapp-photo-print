<?php

namespace App\Filament\Resources\PrintSizes\Pages;

use App\Filament\Resources\PrintSizes\PrintSizeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrintSize extends CreateRecord
{
    protected static string $resource = PrintSizeResource::class;
}
