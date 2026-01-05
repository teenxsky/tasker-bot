<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Services;

use App\Modules\Tasks\DTO\CreateTaskDTO;
use App\Modules\Tasks\DTO\TaskDTO;
use App\Modules\Tasks\Enums\TaskStatusEnum;
use App\Modules\Tasks\Exceptions\TaskNotFoundException;
use App\Modules\Tasks\Interfaces\Repositories\TaskRepositoryInterface;
use App\Modules\Tasks\Interfaces\Services\TaskServiceInterface;
use App\Modules\Tasks\Models\Task;

final readonly class TaskService implements TaskServiceInterface
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    public function create(CreateTaskDTO $dto): TaskDTO
    {
        $task = new Task([
            'title'       => $dto->title,
            'description' => $dto->description,
            'user_email'  => $dto->userEmail,
            'status'      => TaskStatusEnum::PENDING->value,
            'metadata'    => $dto->metadata,
        ]);

        $savedTask = $this->taskRepository->save($task);

        return TaskDTO::from($savedTask);
    }

    public function getTaskById(int $id): ?TaskDTO
    {
        $task = $this->taskRepository->findById($id);

        if (!$task instanceof Task) {
            return null;
        }

        return TaskDTO::from($task);
    }

    public function updateTaskStatus(int $taskId, string $status): TaskDTO
    {
        $task = $this->taskRepository->findById($taskId);

        if (!$task instanceof Task) {
            throw new TaskNotFoundException($taskId);
        }

        $task->status = $status;

        $savedTask = $this->taskRepository->save($task);

        return TaskDTO::from($savedTask);
    }

    public function updateTaskTrackerUrl(int $taskId, string $url): TaskDTO
    {
        $task = $this->taskRepository->findById($taskId);

        if (!$task instanceof Task) {
            throw new TaskNotFoundException($taskId);
        }

        $task->task_tracker_url = $url;

        $savedTask = $this->taskRepository->save($task);

        return TaskDTO::from($savedTask);
    }
}
