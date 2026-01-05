<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Exceptions;

use Exception;
use Throwable;

/**
 * Исключение для ошибок Telegram-бота
 */
final class TelegramBotException extends Exception
{
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            'Ошибка Telegram-бота: ' . $message,
            $code,
            $previous
        );
    }
}
