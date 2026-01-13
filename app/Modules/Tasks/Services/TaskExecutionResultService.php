<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Services;

use App\Modules\Tasks\DTO\TaskExecutionResultDTO;
use App\Modules\Tasks\Exceptions\TaskExecutionResultNotFoundException;
use App\Modules\Tasks\Interfaces\Repositories\TaskExecutionResultRepositoryInterface;
use App\Modules\Tasks\Interfaces\Services\TaskExecutionResultServiceInterface;
use App\Modules\Tasks\Models\TaskExecutionResult;

final readonly class TaskExecutionResultService implements TaskExecutionResultServiceInterface
{
    public function __construct(
        private TaskExecutionResultRepositoryInterface $executionResultRepository
    ) {
    }

    public function createExecutionResult(int $taskId): TaskExecutionResultDTO
    {
        $taskExecutionResult = new TaskExecutionResult([
            'task_id' => $taskId,
        ]);

        $saved = $this->executionResultRepository->save($taskExecutionResult);

        return TaskExecutionResultDTO::from($saved);
    }

    public function getExecutionResultByTaskId(int $taskId): ?TaskExecutionResultDTO
    {
        $result = $this->executionResultRepository->findByTaskId($taskId);

        if (!$result instanceof TaskExecutionResult) {
            return null;
        }

        return TaskExecutionResultDTO::from($result);
    }

    public function updateAiProcessedData(int $resultId, array $data): TaskExecutionResultDTO
    {
        $result = $this->executionResultRepository->findById($resultId);

        if (!$result instanceof TaskExecutionResult) {
            throw new TaskExecutionResultNotFoundException($resultId);
        }

        $result->ai_processed_data = $data;

        $taskExecutionResult = $this->executionResultRepository->save($result);

        return TaskExecutionResultDTO::from($taskExecutionResult);
    }

    public function setExecutionError(int $resultId, string $errorMessage): TaskExecutionResultDTO
    {
        $result = $this->executionResultRepository->findById($resultId);

        if (!$result instanceof TaskExecutionResult) {
            throw new TaskExecutionResultNotFoundException($resultId);
        }

        $result->error_message = $errorMessage;

        $taskExecutionResult = $this->executionResultRepository->save($result);

        return TaskExecutionResultDTO::from($taskExecutionResult);
    }
}
