<?php

declare(strict_types=1);

namespace App\Modules\TaskTracker\DTO;

use App\Shared\Abstracts\AbstractDTO;

/**
 * DTO для пространства Kaiten
 */
final class SpaceDTO extends AbstractDTO
{
    public function __construct(
        public int     $id,
        public string  $title,
        public ?string $description = null,
    ) {
    }
}
