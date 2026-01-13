<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Interfaces\Services;

use App\Modules\Tasks\DTO\CreateTaskDTO;
use App\Modules\Tasks\DTO\TaskDTO;
use App\Modules\Tasks\Exceptions\TaskNotFoundException;

/**
 * Интерфейс сервиса для работы с задачами
 */
interface TaskServiceInterface
{
    /**
     * Создать новую задачу
     */
    public function create(CreateTaskDTO $dto): TaskDTO;

    /**
     * Получить задачу по ID
     */
    public function getTaskById(int $id): ?TaskDTO;

    /**
     * Обновить статус задачи
     *
     * @throws TaskNotFoundException
     */
    public function updateTaskStatus(int $taskId, string $status): TaskDTO;

    /**
     * Обновить URL задачи в трекере
     * @throws TaskNotFoundException
     */
    public function updateTaskTrackerUrl(int $taskId, string $url): TaskDTO;
}
