<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Listeners;

use App\Modules\Tasks\Events\TaskCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Слушатель события создания задачи для отправки уведомления в Telegram
 */
final class SendTaskCreatedNotificationListener implements ShouldQueue
{
    public function handle(TaskCreatedEvent $event): void
    {
        dispatch(new \App\Modules\Telegram\Jobs\SendTaskCreatedNotificationJob(
            taskUrl: $event->taskUrl,
            chatId: $event->chatId,
            messageId: $event->messageId
        ));
    }
}
