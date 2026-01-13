<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Interfaces\Repositories;

use App\Modules\Tasks\Models\Task;
use App\Shared\Contracts\RepositoryInterface;

/**
 * Интерфейс репозитория для работы с задачами
 *
 * @extends RepositoryInterface<Task>
 */
interface TaskRepositoryInterface extends RepositoryInterface
{
}
