<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Exceptions;

use Exception;

/**
 * Исключение для случая, когда Telegram-чат не найден
 */
final class TelegramChatNotFoundException extends Exception
{
    public function __construct(int $chatId)
    {
        parent::__construct(
            sprintf(
                'Telegram-чат с ID %d не найден',
                $chatId
            )
        );
    }
}
