<?php

declare(strict_types=1);

namespace App\Modules\TaskTracker\DTO;

use App\Shared\Abstracts\AbstractDTO;

/**
 * DTO для пользователя Kaiten
 */
final class UserDTO extends AbstractDTO
{
    public function __construct(
        public int     $id,
        public string  $email,
        public ?string $fullName = null,
        public ?string $username = null,
    ) {
    }
}
