<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Repositories;

use App\Modules\Tasks\Interfaces\Repositories\TaskExecutionResultRepositoryInterface;
use App\Modules\Tasks\Models\TaskExecutionResult;
use App\Shared\Abstracts\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends AbstractRepository<TaskExecutionResult>
 */
final class TaskExecutionResultRepository extends AbstractRepository implements TaskExecutionResultRepositoryInterface
{
    /**
     * @return Builder<TaskExecutionResult>
     */
    public function query(): Builder
    {
        return TaskExecutionResult::query();
    }

    public function findByTaskId(int $taskId): ?TaskExecutionResult
    {
        return $this->query()->where('task_id', $taskId)->first();
    }
}
