<?php

declare(strict_types=1);

namespace App\Controllers\V1;

use App\Framework\Http\ApiController;
use App\Modules\Telegram\Interfaces\UseCases\ProcessTelegramUpdateUseCaseInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Контроллер для обработки webhook от Telegram
 */
final class TelegramWebhookController extends ApiController
{
    public function __construct(
        private readonly ProcessTelegramUpdateUseCaseInterface $processTelegramUpdateUseCase,
    ) {
    }

    /**
     * Обработать webhook от Telegram
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $update = $request->all();

            $this->processTelegramUpdateUseCase->execute($update);

            return $this->successResponse([
                'message' => 'Webhook обработан успешно',
            ]);
        } catch (Throwable $throwable) {
            Log::error('Ошибка обработки Telegram webhook', [
                'error' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);

            return $this->internalServerErrorResponse(
                errorMessage: 'Ошибка обработки webhook',
            );
        }
    }
}
