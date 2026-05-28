<?php

use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\PayPlusWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/privacy', fn() => view('privacy'))->name('privacy');

// Secure order image viewer (admin only)
Route::get('/admin/image/{orderImage}', function (\App\Models\OrderImage $orderImage) {
    abort_unless(auth()->check(), 403);
    $path = storage_path('app/' . $orderImage->file_path);
    abort_unless(file_exists($path), 404);
    return response()->file($path);
})->name('admin.order-image');

// WhatsApp Meta Cloud API webhook
Route::get('/webhook/whatsapp', [WhatsAppWebhookController::class, 'verify'])->name('webhook.whatsapp.verify');
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'receive'])->name('webhook.whatsapp');

// PayPlus payment callback
Route::post('/webhook/payplus', [PayPlusWebhookController::class, 'handle'])->name('webhook.payplus');

// Payment result pages
Route::get('/payment/success', fn() => view('payment.success'))->name('payment.success');
Route::get('/payment/fail', fn() => view('payment.fail'))->name('payment.fail');
