<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('whatsapp_phone')
                    ->tel()
                    ->required(),
                TextInput::make('customer_name'),
                Select::make('status')
                    ->required()
                    ->options([
                        'collecting'        => 'Collecting Images',
                        'selecting_size'    => 'Selecting Size',
                        'confirming'        => 'Confirming',
                        'selecting_payment' => 'Selecting Payment',
                        'payment_pending'   => 'Payment Pending',
                        'completed'         => 'Completed',
                        'cancelled'         => 'Cancelled',
                    ])
                    ->default('collecting'),
                Select::make('print_size_id')
                    ->relationship('printSize', 'name'),
                TextInput::make('image_count')
                    ->numeric()
                    ->required()
                    ->default(0),
                TextInput::make('total_price')
                    ->numeric()
                    ->prefix('₪'),
                Select::make('payment_method')
                    ->options([
                        'store'       => 'Pay in Store',
                        'credit_card' => 'Credit Card',
                    ]),
                Select::make('payment_status')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'paid'    => 'Paid',
                        'failed'  => 'Failed',
                    ])
                    ->default('pending'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
