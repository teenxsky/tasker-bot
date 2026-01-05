<?php

declare(strict_types=1);

namespace App\Modules\Telegram\DTO;

use App\Shared\Abstracts\AbstractDTO;
use TelegramBot\Api\Types\ForceReply;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardRemove;

/**
 * DTO для отправки сообщения в Telegram
 */
final class SendMessageDTO extends AbstractDTO
{
    public function __construct(
        public readonly int $chatId,
        public readonly string $text,
        public readonly ?int $replyToMessageId = null,
        public readonly ?string $parseMode = 'HTML',
        public readonly ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|ForceReply|null $replyMarkup = null,
    ) {
    }
}
