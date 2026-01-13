<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Interfaces\UseCases;

use App\Modules\Tasks\Exceptions\TaskExecutionResultNotFoundException;
use App\Modules\Tasks\Exceptions\TaskNotFoundException;
use App\Modules\Tasks\Exceptions\TaskProcessingException;
use App\Shared\Contracts\UseCaseInterface;

/**
 * Интерфейс UseCase для обработки задачи
 */
interface ProcessTaskUseCaseInterface extends UseCaseInterface
{
    /**
     * Обработать задачу через ИИ агента и создать задачу в трекере
     *
     * @throws TaskNotFoundException
     * @throws TaskProcessingException
     * @throws TaskExecutionResultNotFoundException
     */
    public function execute(int $taskId): void;
}
