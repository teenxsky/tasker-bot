<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Repositories;

use App\Modules\Telegram\Interfaces\Repositories\TelegramChatRepositoryInterface;
use App\Modules\Telegram\Models\TelegramChat;
use App\Shared\Abstracts\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Репозиторий для работы с Telegram чатами
 *
 * @extends AbstractRepository<TelegramChat>
 */
final class TelegramChatRepository extends AbstractRepository implements TelegramChatRepositoryInterface
{
    public function query(): Builder
    {
        return TelegramChat::query();
    }

    public function findByChatId(int $chatId): ?TelegramChat
    {
        return $this->query()->where('chat_id', $chatId)->first();
    }
}
