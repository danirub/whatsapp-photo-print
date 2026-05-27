<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use ZipStream\ZipStream;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Order #')
                    ->sortable(),
                TextColumn::make('whatsapp_phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('customer_name')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'collecting'        => 'info',
                        'selecting_size'    => 'warning',
                        'confirming'        => 'warning',
                        'selecting_payment' => 'warning',
                        'payment_pending'   => 'danger',
                        'completed'         => 'success',
                        'cancelled'         => 'gray',
                        default             => 'gray',
                    }),
                TextColumn::make('printSize.label')
                    ->label('Size')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('image_count')
                    ->label('Images')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('ILS')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'credit_card' => 'info',
                        'store'       => 'success',
                        default       => 'gray',
                    })
                    ->placeholder('—'),
                TextColumn::make('payment_status')
                    ->label('Pay Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid'    => 'success',
                        'failed'  => 'danger',
                        'pending' => 'warning',
                        default   => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'collecting'        => 'Collecting Images',
                        'selecting_size'    => 'Selecting Size',
                        'confirming'        => 'Confirming',
                        'selecting_payment' => 'Selecting Payment',
                        'payment_pending'   => 'Payment Pending',
                        'completed'         => 'Completed',
                        'cancelled'         => 'Cancelled',
                    ]),
                SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid'    => 'Paid',
                        'failed'  => 'Failed',
                    ]),
            ])
            ->recordActions([
                Action::make('download_zip')
                    ->label('Download ZIP')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Order $record) {
                        $images = $record->images;
                        if ($images->isEmpty()) {
                            \Filament\Notifications\Notification::make()
                                ->title('No images for this order')
                                ->warning()
                                ->send();
                            return null;
                        }

                        $filename = "order_{$record->id}_{$record->whatsapp_phone}.zip";

                        return response()->streamDownload(function () use ($images) {
                            $zip = new ZipStream(outputStream: fopen('php://output', 'w'));
                            foreach ($images as $image) {
                                $fullPath = storage_path('app/' . $image->file_path);
                                if (file_exists($fullPath)) {
                                    $zip->addFileFromPath($image->original_filename ?? basename($image->file_path), $fullPath);
                                }
                            }
                            $zip->finish();
                        }, $filename, ['Content-Type' => 'application/zip']);
                    }),
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
