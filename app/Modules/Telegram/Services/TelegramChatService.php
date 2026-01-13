<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Services;

use App\Modules\Telegram\DTO\TelegramChatDTO;
use App\Modules\Telegram\Exceptions\TelegramChatNotFoundException;
use App\Modules\Telegram\Interfaces\Repositories\TelegramChatRepositoryInterface;
use App\Modules\Telegram\Interfaces\Services\TelegramChatServiceInterface;
use App\Modules\Telegram\Models\TelegramChat;

final readonly class TelegramChatService implements TelegramChatServiceInterface
{
    public function __construct(
        private TelegramChatRepositoryInterface $repository
    ) {
    }

    public function findOrCreate(int $chatId, string $chatType, ?string $title = null): TelegramChatDTO
    {
        $chat = $this->repository->findByChatId($chatId);

        if ($chat instanceof TelegramChat) {
            return TelegramChatDTO::from($chat);
        }

        $chat = new TelegramChat([
            'chat_id'   => $chatId,
            'chat_type' => $chatType,
            'title'     => $title,
            'board_id'  => null,
            'is_active' => true,
        ]);

        $this->repository->save($chat);

        return TelegramChatDTO::from($chat);
    }

    public function getByChatId(int $chatId): TelegramChatDTO
    {
        $chat = $this->repository->findByChatId($chatId);

        if (!$chat instanceof TelegramChat) {
            throw new TelegramChatNotFoundException($chatId);
        }

        return TelegramChatDTO::from($chat);
    }

    public function updateBoardId(int $chatId, string $boardId): TelegramChatDTO
    {
        $chat = $this->repository->findByChatId($chatId);

        if (!$chat instanceof TelegramChat) {
            throw new TelegramChatNotFoundException($chatId);
        }

        $chat->update(['board_id' => $boardId]);

        return TelegramChatDTO::from($chat);
    }

    public function hasBoard(int $chatId): bool
    {
        $chat = $this->repository->findByChatId($chatId);

        if (!$chat instanceof TelegramChat) {
            throw new TelegramChatNotFoundException($chatId);
        }

        return $chat->board_id !== null && $chat->board_id !== '';
    }

    public function updateUserEmail(int $chatId, string $userEmail): TelegramChatDTO
    {
        $chat = $this->repository->findByChatId($chatId);

        if (!$chat instanceof TelegramChat) {
            throw new TelegramChatNotFoundException($chatId);
        }

        $chat->update(['user_email' => $userEmail]);

        return TelegramChatDTO::from($chat);
    }

    public function hasEmail(int $chatId): bool
    {
        $chat = $this->repository->findByChatId($chatId);

        if (!$chat instanceof TelegramChat) {
            throw new TelegramChatNotFoundException($chatId);
        }

        return $chat->user_email !== null && $chat->user_email !== '';
    }
}
