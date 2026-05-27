<?php

namespace App\Filament\Resources\PrintSizes;

use App\Filament\Resources\PrintSizes\Pages\CreatePrintSize;
use App\Filament\Resources\PrintSizes\Pages\EditPrintSize;
use App\Filament\Resources\PrintSizes\Pages\ListPrintSizes;
use App\Filament\Resources\PrintSizes\Schemas\PrintSizeForm;
use App\Filament\Resources\PrintSizes\Tables\PrintSizesTable;
use App\Models\PrintSize;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrintSizeResource extends Resource
{
    protected static ?string $model = PrintSize::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Print Sizes & Prices';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PrintSizeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrintSizesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrintSizes::route('/'),
            'create' => CreatePrintSize::route('/create'),
            'edit' => EditPrintSize::route('/{record}/edit'),
        ];
    }
}
