<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Commands;

use App\Modules\Telegram\DTO\TelegramMessageDTO;
use App\Modules\Telegram\Enums\TelegramCommandEnum;

/**
 * Команда /start
 */
final class StartCommand extends AbstractTelegramCommand
{
    public function getTelegramCommandType(): TelegramCommandEnum
    {
        return TelegramCommandEnum::START;
    }

    public function handle(TelegramMessageDTO $message): void
    {
        $welcomeMessage = <<<HTML
<b>Добро пожаловать в Task Tracker Bot!</b>

Я помогу вам создавать задачи в Kaiten используя ИИ агента.

Для начала работы:
1. Выберите пространство и доску командой /select_board
2. Укажите ваш email командой /set_email
3. Создавайте задачи командой /task

Доступные команды:
/start - Начать работу с ботом
/select_board - Выбрать доску для создания задач (интерактивно)
/set_email &lt;email&gt; - Установить email для задач
/task - Создать задачу (интерактивно)
/help - Показать список доступных команд
HTML;

        $this->sendMessage($message->chatId, $welcomeMessage);
    }
}
