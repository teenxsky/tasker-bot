<?php

declare(strict_types=1);

namespace App\Modules\TaskTracker\DTO;

use App\Shared\Abstracts\AbstractDTO;

/**
 * DTO для тега Kaiten
 */
final class TagDTO extends AbstractDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $color = null,
    ) {
    }
}
