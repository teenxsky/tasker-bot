<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Services;

use App\Modules\Tasks\DTO\TaskDTO;
use App\Modules\Tasks\Exceptions\BoardNotSelectedException;
use App\Modules\Tasks\Exceptions\TaskProcessingException;
use App\Modules\Tasks\Interfaces\Services\TaskBoardResolverInterface;
use App\Modules\Tasks\Interfaces\Services\TaskServiceInterface;
use App\Modules\Telegram\Interfaces\Services\TelegramChatServiceInterface;
use Throwable;

/**
 * Резолвер ID доски для задачи через Telegram-чат
 */
final readonly class TelegramTaskBoardResolver implements TaskBoardResolverInterface
{
    public function __construct(
        private TaskServiceInterface         $taskService,
        private TelegramChatServiceInterface $chatService,
    ) {
    }

    public function resolveBoardId(int $taskId): string
    {
        // Получаем задачу с метаданными
        $taskDTO = $this->taskService->getTaskById($taskId);

        if (!$taskDTO instanceof TaskDTO) {
            throw new TaskProcessingException(
                sprintf('Задача с ID %d не найдена', $taskId)
            );
        }

        // Проверяем, что задача создана из Telegram
        $metadata = $taskDTO->metadata ?? [];
        if (!isset($metadata['source']) || $metadata['source'] !== 'telegram') {
            throw new TaskProcessingException(
                sprintf('Задача с ID %d не относится к Telegram', $taskId)
            );
        }

        if (!isset($metadata['chat_id'])) {
            throw new TaskProcessingException(
                sprintf('В метаданных задачи с ID %d отсутствует chat_id', $taskId)
            );
        }

        $chatIdValue = $metadata['chat_id'];
        if (!is_int($chatIdValue) && !is_numeric($chatIdValue)) {
            throw new TaskProcessingException(
                sprintf('Значение chat_id в метаданных задачи с ID %d не является числом', $taskId)
            );
        }

        $chatId = (int)$chatIdValue;

        // Получаем информацию о Telegram-чате
        try {
            $telegramChatDTO = $this->chatService->getByChatId($chatId);
        } catch (Throwable $throwable) {
            throw new TaskProcessingException($throwable->getMessage());
        }

        if ($telegramChatDTO->boardId === null || $telegramChatDTO->boardId === '') {
            throw new BoardNotSelectedException();
        }

        return $telegramChatDTO->boardId;
    }

    public function getTaskMessageInfo(int $taskId): ?array
    {
        // Получаем задачу с метаданными
        $taskDTO = $this->taskService->getTaskById($taskId);

        if (!$taskDTO instanceof TaskDTO) {
            return null;
        }

        // Проверяем, что задача создана из Telegram
        $metadata = $taskDTO->metadata ?? [];
        if (!isset($metadata['source']) || $metadata['source'] !== 'telegram') {
            return null;
        }

        if (!isset($metadata['chat_id'], $metadata['message_id'])) {
            return null;
        }

        $chatIdValue    = $metadata['chat_id'];
        $messageIdValue = $metadata['message_id'];

        if (
            (!is_int($chatIdValue) && !is_numeric($chatIdValue))
            || (!is_int($messageIdValue) && !is_numeric($messageIdValue))
        ) {
            return null;
        }

        return [
            'chat_id'    => (int)$chatIdValue,
            'message_id' => (int)$messageIdValue,
        ];
    }
}
