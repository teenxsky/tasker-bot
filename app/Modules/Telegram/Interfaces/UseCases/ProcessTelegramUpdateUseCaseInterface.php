<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Interfaces\UseCases;

use App\Shared\Contracts\UseCaseInterface;

/**
 * UseCase для обработки обновлений из Telegram
 * Отвечает только за маршрутизацию запросов к соответствующим командам
 */
interface ProcessTelegramUpdateUseCaseInterface extends UseCaseInterface
{
    /**
     * Обработать обновление из Telegram
     *
     * @param array<string, mixed> $update
     */
    public function execute(array $update): void;
}
