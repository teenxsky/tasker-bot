<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Interfaces\Services;

use App\Modules\Tasks\DTO\TaskExecutionResultDTO;
use App\Modules\Tasks\Exceptions\TaskExecutionResultNotFoundException;

/**
 * Интерфейс сервиса для работы с результатами выполнения задач
 */
interface TaskExecutionResultServiceInterface
{
    /**
     * Создать результат выполнения задачи
     */
    public function createExecutionResult(int $taskId): TaskExecutionResultDTO;

    /**
     * Получить результат выполнения по ID задачи
     */
    public function getExecutionResultByTaskId(int $taskId): ?TaskExecutionResultDTO;

    /**
     * Обновить обработанные ИИ данные
     * @param  array<string, mixed>                 $data
     * @throws TaskExecutionResultNotFoundException
     */
    public function updateAiProcessedData(int $resultId, array $data): TaskExecutionResultDTO;

    /**
     * Установить ошибку выполнения
     * @throws TaskExecutionResultNotFoundException
     */
    public function setExecutionError(int $resultId, string $errorMessage): TaskExecutionResultDTO;
}
