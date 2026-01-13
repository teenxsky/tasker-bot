<?php

declare(strict_types=1);

namespace App\Modules\Telegram\DTO;

use App\Shared\Abstracts\AbstractDTO;

/**
 * DTO для входящего Telegram сообщения
 */
final class TelegramMessageDTO extends AbstractDTO
{
    public function __construct(
        public readonly int $chatId,
        public readonly string $chatType,
        public readonly ?string $chatTitle,
        public readonly ?string $text,
        public readonly ?int $messageId,
        public readonly ?string $username = null,
    ) {
    }
}
