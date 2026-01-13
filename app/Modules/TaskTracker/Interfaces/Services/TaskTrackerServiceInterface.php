<?php

declare(strict_types=1);

namespace App\Modules\TaskTracker\Interfaces\Services;

use App\Modules\TaskTracker\DTO\CreateCardRequestDTO;

/**
 * Интерфейс сервиса для работы с таск-трекером
 */
interface TaskTrackerServiceInterface
{
    /**
     * Создать задачу в таск-трекере
     *
     * @return string URL созданной задачи в таск-трекере
     */
    public function createTask(CreateCardRequestDTO $request, string $boardId): string;
}
