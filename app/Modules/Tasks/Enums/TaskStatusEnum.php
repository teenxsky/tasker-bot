<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Enums;

enum TaskStatusEnum: string
{
    case PENDING = 'pending';

    case PROCESSING = 'processing';

    case COMPLETED = 'completed';

    case FAILED = 'failed';
}
