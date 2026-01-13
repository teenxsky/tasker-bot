<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Interfaces\Services;

use App\Modules\Telegram\DTO\TelegramChatDTO;
use App\Modules\Telegram\Exceptions\TelegramChatNotFoundException;

/**
 * Интерфейс сервиса для работы с Telegram чатами
 */
interface TelegramChatServiceInterface
{
    /**
     * Найти или создать чат
     */
    public function findOrCreate(int $chatId, string $chatType, ?string $title = null): TelegramChatDTO;

    /**
     * Получить чат по chat_id
     *
     * @throws TelegramChatNotFoundException
     */
    public function getByChatId(int $chatId): TelegramChatDTO;

    /**
     * Обновить board_id для чата
     *
     * @throws TelegramChatNotFoundException
     */
    public function updateBoardId(int $chatId, string $boardId): TelegramChatDTO;

    /**
     * Проверить, что у чата выбрана доска
     *
     * @throws TelegramChatNotFoundException
     */
    public function hasBoard(int $chatId): bool;

    /**
     * Обновить user_email для чата
     *
     * @throws TelegramChatNotFoundException
     */
    public function updateUserEmail(int $chatId, string $userEmail): TelegramChatDTO;

    /**
     * Проверить, что у чата указан email
     *
     * @throws TelegramChatNotFoundException
     */
    public function hasEmail(int $chatId): bool;
}
