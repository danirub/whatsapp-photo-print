<?php

namespace App\Filament\Resources\BotMessages;

use App\Filament\Resources\BotMessages\Pages\CreateBotMessage;
use App\Filament\Resources\BotMessages\Pages\EditBotMessage;
use App\Filament\Resources\BotMessages\Pages\ListBotMessages;
use App\Filament\Resources\BotMessages\Schemas\BotMessageForm;
use App\Filament\Resources\BotMessages\Tables\BotMessagesTable;
use App\Models\BotMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BotMessageResource extends Resource
{
    protected static ?string $model = BotMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Bot Messages';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return BotMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BotMessagesTable::configure($table);
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
            'index' => ListBotMessages::route('/'),
            'create' => CreateBotMessage::route('/create'),
            'edit' => EditBotMessage::route('/{record}/edit'),
        ];
    }
}
