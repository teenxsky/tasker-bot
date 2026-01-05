<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Exceptions;

use Exception;

/**
 * Исключение для случая, когда для чата не выбрана доска
 */
final class BoardNotSelectedException extends Exception
{
    public function __construct(int $chatId)
    {
        parent::__construct(
            sprintf(
                'Для чата с ID %d не выбрана доска. Пожалуйста, сначала используйте команду /select_board.',
                $chatId
            )
        );
    }
}
