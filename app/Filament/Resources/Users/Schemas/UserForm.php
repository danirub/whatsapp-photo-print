<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        $isEdit = $schema->getRecord()?->exists ?? false;

        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->required(fn() => !$isEdit)
                    ->dehydrated(fn($state) => filled($state))
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->label($isEdit ? 'New password (leave blank to keep current)' : 'Password')
                    ->minLength(8)
                    ->confirmed()
                    ->live(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->label('Confirm password')
                    ->required(fn() => !$isEdit)
                    ->dehydrated(false)
                    ->visible(fn($get) => filled($get('password'))),
            ]);
    }
}
