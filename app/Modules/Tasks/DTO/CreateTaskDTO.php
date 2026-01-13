<?php

declare(strict_types=1);

namespace App\Modules\Tasks\DTO;

use App\Shared\Abstracts\AbstractDTO;

/**
 * DTO для создания задачи
 */
final class CreateTaskDTO extends AbstractDTO
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly ?string $userEmail = null,
        public readonly ?string $ownerEmail = null,
        public readonly array $metadata = [],
    ) {
    }
}
