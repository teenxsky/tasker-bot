<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Exceptions;

use Exception;

/**
 * Исключение для случая, когда задача не найдена
 */
final class TaskNotFoundException extends Exception
{
    public function __construct(int $taskId)
    {
        parent::__construct(sprintf('Задача с ID %d не найдена', $taskId));
    }
}
