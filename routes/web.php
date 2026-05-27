<?php

use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\PayPlusWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// WhatsApp Meta Cloud API webhook
Route::get('/webhook/whatsapp', [WhatsAppWebhookController::class, 'verify'])->name('webhook.whatsapp.verify');
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'receive'])->name('webhook.whatsapp');

// PayPlus payment callback
Route::post('/webhook/payplus', [PayPlusWebhookController::class, 'handle'])->name('webhook.payplus');

// Payment result pages
Route::get('/payment/success', fn() => view('payment.success'))->name('payment.success');
Route::get('/payment/fail', fn() => view('payment.fail'))->name('payment.fail');
