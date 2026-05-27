<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use ZipStream\ZipStream;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_zip')
                ->label('Download Images ZIP')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    /** @var Order $order */
                    $order = $this->record;
                    $images = $order->images;

                    if ($images->isEmpty()) {
                        \Filament\Notifications\Notification::make()
                            ->title('No images for this order')
                            ->warning()
                            ->send();
                        return null;
                    }

                    $filename = "order_{$order->id}_{$order->whatsapp_phone}.zip";

                    return response()->streamDownload(function () use ($images) {
                        $zip = new ZipStream(outputStream: fopen('php://output', 'w'));
                        foreach ($images as $image) {
                            $fullPath = storage_path('app/' . $image->file_path);
                            if (file_exists($fullPath)) {
                                $zip->addFileFromPath(
                                    $image->original_filename ?? basename($image->file_path),
                                    $fullPath
                                );
                            }
                        }
                        $zip->finish();
                    }, $filename, ['Content-Type' => 'application/zip']);
                }),
            EditAction::make(),
        ];
    }
}
