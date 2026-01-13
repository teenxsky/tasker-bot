<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Interfaces\Services;

use App\Modules\Tasks\Exceptions\BoardNotSelectedException;
use App\Modules\Tasks\Exceptions\TaskProcessingException;

/**
 * Интерфейс для получения ID доски для задачи
 */
interface TaskBoardResolverInterface
{
    /**
     * Получить ID доски для задачи
     *
     * @throws BoardNotSelectedException
     * @throws TaskProcessingException
     */
    public function resolveBoardId(int $taskId): string;

    /**
     * Получить chat_id и message_id для задачи
     *
     * @return array{chat_id: int, message_id: int}|null
     */
    public function getTaskMessageInfo(int $taskId): ?array;
}
