<?php

declare(strict_types=1);

namespace App\Modules\Telegram\DTO;

use App\Shared\Abstracts\AbstractDTO;

/**
 * DTO для Telegram чата
 */
final class TelegramChatDTO extends AbstractDTO
{
    public function __construct(
        public readonly ?int    $id,
        public readonly int     $chatId,
        public readonly string  $chatType,
        public readonly ?string $title = null,
        public readonly ?string $boardId = null,
        public readonly ?string $userEmail = null,
        public readonly bool    $isActive = true,
    ) {
    }
}
