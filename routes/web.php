<?php

use Illuminate\Support\Facades\Route;
use Qlixea\PaymentHub\Http\Controllers\LocalWebhookController;

// Esta ruta debe coincidir con lo que tu ProvisioningController genera: /qlixea/webhook
Route::post('/qlixea/webhook', [LocalWebhookController::class, 'handle'])->name('qlixea.webhook');