<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Interfaces\Repositories;

use App\Modules\Tasks\Models\TaskExecutionResult;
use App\Shared\Contracts\RepositoryInterface;

/**
 * Интерфейс репозитория для работы с результатами выполнения задач
 *
 * @extends RepositoryInterface<TaskExecutionResult>
 */
interface TaskExecutionResultRepositoryInterface extends RepositoryInterface
{
    /**
     * Найти результат выполнения по ID задачи
     */
    public function findByTaskId(int $taskId): ?TaskExecutionResult;
}
