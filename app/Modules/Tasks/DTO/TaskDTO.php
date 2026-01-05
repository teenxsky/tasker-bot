<?php

declare(strict_types=1);

namespace App\Modules\Tasks\DTO;

use App\Shared\Abstracts\AbstractDTO;

/**
 * DTO для задачи
 */
final class TaskDTO extends AbstractDTO
{
    /**
     * @param array<string, mixed>|array{
     *     source: string|null,
     *     chat_id: int|null,
     *     message_id: int|null
     * } $metadata
     */
    public function __construct(
        public readonly ?int    $id,
        public readonly string  $title,
        public readonly string  $description,
        public readonly ?string $userEmail,
        public readonly string  $status,
        public readonly ?string $taskTrackerUrl,
        public readonly array   $metadata = [],
        public readonly ?string $ownerEmail = null,
    ) {
    }
}
