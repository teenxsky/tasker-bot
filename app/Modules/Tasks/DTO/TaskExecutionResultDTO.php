<?php

declare(strict_types=1);

namespace App\Modules\Tasks\DTO;

use App\Shared\Abstracts\AbstractDTO;

/**
 * DTO для результата выполнения задачи
 */
final class TaskExecutionResultDTO extends AbstractDTO
{
    /**
     * @param array<string, mixed>|null $aiProcessedData
     */
    public function __construct(
        public readonly ?int    $id,
        public readonly int     $taskId,
        public readonly ?array  $aiProcessedData,
        public readonly ?string $errorMessage,
    ) {
    }
}
