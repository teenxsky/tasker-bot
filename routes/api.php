<?php

declare(strict_types=1);

use App\Controllers\V1\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('telegram')->group(function (): void {
        Route::post('webhook', [TelegramWebhookController::class, 'handle']);
    });
});
