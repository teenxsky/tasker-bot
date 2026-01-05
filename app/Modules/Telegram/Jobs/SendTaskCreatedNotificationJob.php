<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Jobs;

use App\Modules\Telegram\DTO\SendMessageDTO;
use App\Modules\Telegram\Exceptions\TelegramBotException;
use App\Modules\Telegram\Interfaces\Services\TelegramBotServiceInterface;
use App\Shared\Abstracts\AbstractJob;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Job для отправки уведомления о создании задачи в Telegram
 */
final class SendTaskCreatedNotificationJob extends AbstractJob
{
    private string $messageTemplate = <<<HTML
<b>Задача успешно создана!</b>

Задача была успешно обработана и создана в таск-трекере.

🔗 <a href="%s">Перейти к задаче</a>
HTML;

    /**
     * @param string $taskUrl   URL задачи в таск-трекере
     * @param int    $chatId    ID Telegram-чата
     * @param int    $messageId ID сообщения в Telegram
     */
    public function __construct(
        private readonly string $taskUrl,
        private readonly int    $chatId,
        private readonly int    $messageId
    ) {
        parent::__construct();
    }

    /**
     * Выполнить отправку уведомления
     * @throws TelegramBotException
     */
    public function handle(
        TelegramBotServiceInterface $botService
    ): void {
        $botService->sendMessage(new SendMessageDTO(
            chatId: $this->chatId,
            text: sprintf($this->messageTemplate, $this->taskUrl),
            replyToMessageId: $this->messageId,
            parseMode: 'HTML'
        ));
    }

    /**
     * Обработка ошибки при выполнении задания
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Не удалось отправить уведомление о создании задачи', [
            'chat_id' => $this->chatId,
            'error'   => $exception->getMessage(),
            'trace'   => $exception->getTraceAsString(),
        ]);
    }
}
