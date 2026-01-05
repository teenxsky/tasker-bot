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
use Throwable;

/**
 * UseCase для обработки задачи через ИИ и создания в трекере
 *
 * TODO: реализовать сервисы ИИ/таск-трекера.
 */
final readonly class ProcessTaskUseCase implements ProcessTaskUseCaseInterface
{
    public function __construct(
        private TaskServiceInterface                $taskService,
        private TaskExecutionResultServiceInterface $executionResultService,
        private TaskBoardResolverInterface          $boardResolver
    ) {
    }

    public function execute(int $taskId): void
    {
        // Получаем задачу
        $taskDTO = $this->taskService->getTaskById($taskId);
        if (!$taskDTO instanceof TaskDTO) {
            throw new TaskNotFoundException($taskId);
        }

        if ($taskDTO->id === null) {
            throw new TaskProcessingException('ID задачи не может быть нулевым');
        }

        // Получаем результат выполнения
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

            // TODO: сохранить обработанные ИИ данные
            $this->executionResultService->updateAiProcessedData(
                resultId: $executionResultDTO->id,
                data: $aiProcessedData
            );

            // TODO: создать задачу в трекере
            $taskTrackerUrl = 'https://example.com/task/123'; // Заглушка

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
