<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Services;

use App\Modules\Telegram\DTO\SendMessageDTO;
use App\Modules\Telegram\Exceptions\TelegramBotException;
use App\Modules\Telegram\Interfaces\Services\TelegramBotServiceInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\WebhookInfo;
use Throwable;

final class TelegramBotService implements TelegramBotServiceInterface
{
    private BotApi $bot;

    /**
     * @throws TelegramBotException
     */
    public function __construct()
    {
        $token = config('services.telegram.bot_token');

        if (!is_string($token) || $token === '') {
            throw new TelegramBotException(
                'Токен Telegram-бота не задан или не сконфигурирован'
            );
        }

        try {
            $this->bot = new BotApi($token);
        } catch (Throwable $throwable) {
            throw new TelegramBotException(
                'Не удалось инициализировать Telegram-бота: ' . $throwable->getMessage(),
                0,
                $throwable
            );
        }
    }

    public function sendMessage(SendMessageDTO $dto): void
    {
        try {
            $this->bot->sendMessage(
                chatId: $dto->chatId,
                text: $dto->text,
                parseMode: $dto->parseMode,
                replyToMessageId: $dto->replyToMessageId,
                replyMarkup: $dto->replyMarkup
            );
        } catch (Throwable $throwable) {
            throw new TelegramBotException(
                'Ошибка при отправке сообщения в Telegram: ' . $throwable->getMessage(),
                0,
                $throwable
            );
        }
    }

    public function setWebhook(string $url): void
    {
        try {
            $this->bot->setWebhook($url);
        } catch (Throwable $throwable) {
            throw new TelegramBotException(
                'Не удалось установить webhook для Telegram-бота: ' . $throwable->getMessage(),
                0,
                $throwable
            );
        }
    }

    public function getWebhookInfo(): WebhookInfo
    {
        try {
            return $this->bot->getWebhookInfo();
        } catch (Throwable $throwable) {
            throw new TelegramBotException(
                'Не удалось получить информацию о webhook Telegram-бота: ' . $throwable->getMessage(),
                0,
                $throwable
            );
        }
    }
}
