<?php

namespace App\Filament\Resources\BotMessages\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BotMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Internal identifier — do not change after creation'),
                TextInput::make('description')
                    ->required()
                    ->helperText('What this message is for'),
                Textarea::make('message_text')
                    ->required()
                    ->rows(6)
                    ->helperText('Use {variable} for dynamic values. Available: {count}, {sizes}, {size_name}, {size_label}, {price_each}, {total}, {order_id}, {url}')
                    ->columnSpanFull(),
            ]);
    }
}
