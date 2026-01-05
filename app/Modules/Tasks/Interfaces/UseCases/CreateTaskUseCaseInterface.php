<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Interfaces\UseCases;

use App\Modules\Tasks\DTO\CreateTaskDTO;
use App\Modules\Tasks\DTO\TaskDTO;
use App\Modules\Tasks\Exceptions\TaskNotFoundException;
use App\Shared\Contracts\UseCaseInterface;

/**
 * Интерфейс UseCase для создания задачи
 */
interface CreateTaskUseCaseInterface extends UseCaseInterface
{
    /**
     * Создать задачу и поставить её в очередь на обработку
     *
     * @throws TaskNotFoundException
     */
    public function execute(CreateTaskDTO $dto): TaskDTO;
}
