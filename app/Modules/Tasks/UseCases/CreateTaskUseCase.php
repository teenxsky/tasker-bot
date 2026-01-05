<?php

declare(strict_types=1);

namespace App\Modules\Tasks\UseCases;

use App\Modules\Tasks\DTO\CreateTaskDTO;
use App\Modules\Tasks\DTO\TaskDTO;
use App\Modules\Tasks\Enums\TaskStatusEnum;
use App\Modules\Tasks\Interfaces\Services\TaskExecutionResultServiceInterface;
use App\Modules\Tasks\Interfaces\Services\TaskServiceInterface;
use App\Modules\Tasks\Interfaces\UseCases\CreateTaskUseCaseInterface;
use RuntimeException;

final readonly class CreateTaskUseCase implements CreateTaskUseCaseInterface
{
    public function __construct(
        private TaskServiceInterface                $taskService,
        private TaskExecutionResultServiceInterface $executionResultService,
    ) {
    }

    public function execute(CreateTaskDTO $dto): TaskDTO
    {
        // Создаём задачу
        $taskDTO = $this->taskService->create($dto);

        if ($taskDTO->id === null) {
            throw new RuntimeException('ID задачи не может быть нулевым после создания');
        }

        // Создаём запись о выполнении задачи
        $this->executionResultService->createExecutionResult(
            taskId: $taskDTO->id
        );

        // Обновляем статус задачи
        $taskDTO = $this->taskService->updateTaskStatus(
            taskId: $taskDTO->id,
            status: TaskStatusEnum::PROCESSING->value
        );

        if ($taskDTO->id === null) {
            throw new RuntimeException('ID задачи не может быть нулевым после обновления статуса');
        }

        // Отправляем задачу в очередь
        dispatch(new \App\Modules\Tasks\Jobs\ProcessTaskJob($taskDTO->id));

        return $taskDTO;
    }
}
