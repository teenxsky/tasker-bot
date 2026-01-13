<?php

declare(strict_types=1);

namespace App\Modules\AI\Interfaces\UseCases;

use App\Modules\AI\DTO\ProcessedTaskDataDTO;
use App\Modules\AI\DTO\TaskDescriptionDTO;
use App\Modules\AI\Exceptions\AIProcessingException;
use App\Shared\Contracts\UseCaseInterface;

/**
 * Интерфейс для обработки описания задачи через AI.
 */
interface ProcessTaskDescriptionUseCaseInterface extends UseCaseInterface
{
    /**
     * Обработать описание задачи через AI и вернуть структурированные данные.
     *
     * @throws AIProcessingException
     */
    public function execute(TaskDescriptionDTO $taskDescription): ProcessedTaskDataDTO;
}
