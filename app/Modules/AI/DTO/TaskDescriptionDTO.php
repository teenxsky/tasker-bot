<?php

declare(strict_types=1);

namespace App\Modules\AI\DTO;

use App\Shared\Abstracts\AbstractDTO;

/**
 * DTO для описания задачи от пользователя.
 */
final class TaskDescriptionDTO extends AbstractDTO
{
    public function __construct(
        public string $description,
        public ?string $userEmail = null,
    ) {
    }
}
