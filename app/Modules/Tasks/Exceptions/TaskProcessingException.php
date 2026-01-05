<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Exceptions;

use Exception;

/**
 * Исключение для ошибок при обработке задачи
 */
final class TaskProcessingException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct('Ошибка обработки задачи: ' . $message);
    }
}
