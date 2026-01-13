<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Interfaces\Repositories;

use App\Modules\Telegram\Models\TelegramChat;
use App\Shared\Contracts\RepositoryInterface;

/**
 * Интерфейс репозитория для работы с Telegram чатами
 *
 * @extends RepositoryInterface<TelegramChat>
 */
interface TelegramChatRepositoryInterface extends RepositoryInterface
{
    /**
     * Найти чат по chat_id
     */
    public function findByChatId(int $chatId): ?TelegramChat;
}
