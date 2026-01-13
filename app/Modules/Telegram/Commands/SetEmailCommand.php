<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Commands;

use App\Modules\Telegram\DTO\TelegramMessageDTO;
use App\Modules\Telegram\Enums\TelegramCommandEnum;
use App\Modules\Telegram\Interfaces\Services\TelegramBotServiceInterface;
use App\Modules\Telegram\Interfaces\Services\TelegramChatServiceInterface;
use App\Modules\Telegram\Services\ConversationManager;

/**
 * Команда /set_email
 */
final class SetEmailCommand extends AbstractTelegramCommand
{
    public function __construct(
        private readonly TelegramChatServiceInterface $chatService,
        ConversationManager                           $conversationManager,
        TelegramBotServiceInterface                   $botService
    ) {
        parent::__construct($conversationManager, $botService);
    }

    public function getTelegramCommandType(): TelegramCommandEnum
    {
        return TelegramCommandEnum::SET_EMAIL;
    }

    public function handle(TelegramMessageDTO $message): void
    {
        if ($message->text === null) {
            return;
        }

        // Извлекаем email из команды
        $parts = explode(' ', $message->text, 2);

        if (count($parts) < 2 || trim($parts[1]) === '') {
            $this->sendMessage(
                $message->chatId,
                'Пожалуйста, укажите email. Пример: /set_email user@example.com'
            );

            return;
        }

        $email = trim($parts[1]);

        // Проверяем валидность email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendMessage(
                $message->chatId,
                'Пожалуйста, укажите корректный email адрес'
            );

            return;
        }

        // Сохраняем email
        $this->chatService->updateUserEmail($message->chatId, $email);

        $this->sendMessage(
            $message->chatId,
            sprintf(
                'Email <b>%s</b> успешно сохранен. Теперь вы можете создавать задачи командой /task',
                htmlspecialchars($email, ENT_QUOTES, 'UTF-8')
            )
        );
    }
}
