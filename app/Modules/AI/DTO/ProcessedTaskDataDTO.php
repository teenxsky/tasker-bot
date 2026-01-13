<?php

declare(strict_types=1);

namespace App\Modules\AI\DTO;

use App\Shared\Abstracts\AbstractDTO;

/**
 * DTO для систематизированных данных задачи после обработки AI.
 */
final class ProcessedTaskDataDTO extends AbstractDTO
{
    /**
     * @param array<int, string> $tags
     */
    public function __construct(
        public string $title,
        public string $description,
        public string $acceptanceCriteria,
        public ?string $technicalNotes = null,
        public ?int $estimatedTime = null,
        public string $priority = 'medium',
        public array $tags = [],
    ) {
    }
}
