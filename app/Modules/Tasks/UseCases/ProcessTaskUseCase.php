<?php

declare(strict_types=1);

namespace App\Modules\Tasks\UseCases;

use App\Modules\Tasks\DTO\TaskDTO;
use App\Modules\Tasks\DTO\TaskExecutionResultDTO;
use App\Modules\Tasks\Enums\TaskStatusEnum;
use App\Modules\Tasks\Events\TaskCreatedEvent;
use App\Modules\Tasks\Exceptions\TaskNotFoundException;
use App\Modules\Tasks\Exceptions\TaskProcessingException;
use App\Modules\Tasks\Interfaces\Services\TaskBoardResolverInterface;
use App\Modules\Tasks\Interfaces\Services\TaskExecutionResultServiceInterface;
use App\Modules\Tasks\Interfaces\Services\TaskServiceInterface;
use App\Modules\Tasks\Interfaces\UseCases\ProcessTaskUseCaseInterface;
use App\Modules\TaskTracker\DTO\CreateCardRequestDTO;
use App\Modules\TaskTracker\Interfaces\Services\TaskTrackerServiceInterface;
use Throwable;

/**
 * UseCase для обработки задачи через ИИ и создания в трекере
 */
final readonly class ProcessTaskUseCase implements ProcessTaskUseCaseInterface
{
    public function __construct(
        private TaskServiceInterface                $taskService,
        private TaskExecutionResultServiceInterface $executionResultService,
        private TaskTrackerServiceInterface         $taskTrackerService,
        private TaskBoardResolverInterface          $boardResolver,
    ) {
    }

    public function execute(int $taskId): void
    {
        $taskDTO = $this->taskService->getTaskById($taskId);

        if (!$taskDTO instanceof TaskDTO) {
            throw new TaskNotFoundException($taskId);
        }

        if ($taskDTO->id === null) {
            throw new TaskProcessingException('ID задачи не может быть нулевым');
        }

        $executionResultDTO = $this->executionResultService->getExecutionResultByTaskId($taskId);

        if (!$executionResultDTO instanceof TaskExecutionResultDTO) {
            throw new TaskProcessingException('Результат выполнения не найден для задачи ' . $taskId);
        }

        if ($executionResultDTO->id === null) {
            throw new TaskProcessingException('ID результата выполнения не может быть нулевым.');
        }

        try {
            // TODO: здесь будет обработка через ИИ
            $aiProcessedData = [
                'title'               => 'Заглушка: заголовок задачи',
                'description'         => 'Заглушка: описание задачи',
                'acceptance_criteria' => 'Заглушка: критерии приемки',
                'technical_notes'     => 'Заглушка: технические заметки',
                'estimated_time'      => 0,
                'priority'            => 'medium',
                'tags'                => [],
            ];

            // Сохраняем обработанные ИИ данные
            $this->executionResultService->updateAiProcessedData(
                resultId: $executionResultDTO->id,
                data: $aiProcessedData
            );

            // Создаём задачу в таск-трекере
            $boardId = $this->boardResolver->resolveBoardId($taskId);

            $createCardRequestDTO = new CreateCardRequestDTO(
                title: $aiProcessedData['title'],
                description: $aiProcessedData['description'],
                userEmail: $taskDTO->userEmail,
                ownerEmail: $taskDTO->ownerEmail,
                processedData: $aiProcessedData
            );

            $taskTrackerUrl = $this->taskTrackerService->createTask(
                request: $createCardRequestDTO,
                boardId: $boardId
            );

            // Обновляем URL в задаче
            $this->taskService->updateTaskTrackerUrl(
                taskId: $taskDTO->id,
                url: $taskTrackerUrl
            );

            // Обновляем статус - задача выполнена
            $this->taskService->updateTaskStatus(
                taskId: $taskDTO->id,
                status: TaskStatusEnum::COMPLETED->value
            );

            // Получаем информацию о сообщении для уведомления
            $taskMessageInfo = $this->boardResolver->getTaskMessageInfo($taskId);

            // Отправляем событие о создании задачи
            if ($taskMessageInfo !== null) {
                event(new TaskCreatedEvent(
                    $taskDTO->id,
                    $taskTrackerUrl,
                    $taskMessageInfo['chat_id'],
                    $taskMessageInfo['message_id']
                ));
            }
        } catch (Throwable $throwable) {
            $this->executionResultService->setExecutionError(
                resultId: $executionResultDTO->id,
                errorMessage: $throwable->getMessage()
            );

            $this->taskService->updateTaskStatus(
                taskId: $taskDTO->id,
                status: TaskStatusEnum::FAILED->value
            );

            throw new TaskProcessingException($throwable->getMessage());
        }
    }
}
