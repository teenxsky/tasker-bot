<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Jobs;

use App\Modules\Tasks\Interfaces\UseCases\ProcessTaskUseCaseInterface;
use App\Shared\Abstracts\AbstractJob;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Job для обработки задачи через RabbitMQ
 */
final class ProcessTaskJob extends AbstractJob
{
    /**
     * @param int $taskId ID задачи для обработки
     */
    public function __construct(
        private readonly int $taskId
    ) {
        parent::__construct();
    }

    /**
     * Выполнить обработку задачи
     */
    public function handle(ProcessTaskUseCaseInterface $processTaskUseCase): void
    {
        $processTaskUseCase->execute($this->taskId);
    }

    /**
     * Обработка неудачного выполнения задания
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Не удалось выполнить Job по обработке задачи', [
            'task_id' => $this->taskId,
            'error'   => $exception->getMessage(),
            'trace'   => $exception->getTraceAsString(),
        ]);
    }
}
