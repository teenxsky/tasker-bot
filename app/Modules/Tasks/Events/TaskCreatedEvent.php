<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Событие создания задачи в таск-трекере
 */
final readonly class TaskCreatedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public int $taskId,
        public string $taskUrl,
        public int $chatId,
        public int $messageId,
    ) {
    }
}
