<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Interfaces\Services;

use App\Modules\Telegram\DTO\SendMessageDTO;
use App\Modules\Telegram\Exceptions\TelegramBotException;
use TelegramBot\Api\Types\WebhookInfo;

/**
 * Интерфейс сервиса для работы с Telegram Bot API
 */
interface TelegramBotServiceInterface
{
    /**
     * Отправить сообщение в чат
     * @throws TelegramBotException
     */
    public function sendMessage(SendMessageDTO $dto): void;

    /**
     * Установить webhook
     * @throws TelegramBotException
     */
    public function setWebhook(string $url): void;

    /**
     * Получить информацию о webhook
     * @throws TelegramBotException
     */
    public function getWebhookInfo(): WebhookInfo;
}
