<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Exceptions;

use Exception;

/**
 * Исключение для случая, когда результат выполнения задачи не найден
 */
final class TaskExecutionResultNotFoundException extends Exception
{
    public function __construct(int $resultId)
    {
        parent::__construct(sprintf('Результат выполнения задачи с ID %d не найден', $resultId));
    }
}
