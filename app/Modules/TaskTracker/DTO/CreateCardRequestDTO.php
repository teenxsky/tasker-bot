<?php

declare(strict_types=1);

namespace App\Modules\TaskTracker\DTO;

use App\Shared\Abstracts\AbstractDTO;

/**
 * DTO для запроса создания карточки в таск-трекере
 */
final class CreateCardRequestDTO extends AbstractDTO
{
    /**
     * @param array<string, mixed> $processedData
     */
    public function __construct(
        public string $title,
        public string $description,
        public ?string $userEmail = null,
        public ?string $ownerEmail = null,
        public array $processedData = [],
    ) {
    }
}
