<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Repositories;

use App\Modules\Tasks\Interfaces\Repositories\TaskRepositoryInterface;
use App\Modules\Tasks\Models\Task;
use App\Shared\Abstracts\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends AbstractRepository<Task>
 */
final class TaskRepository extends AbstractRepository implements TaskRepositoryInterface
{
    /**
     * @return Builder<Task>
     */
    public function query(): Builder
    {
        return Task::query();
    }
}
