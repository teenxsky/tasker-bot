<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Commands;

use App\Modules\Telegram\DTO\TelegramMessageDTO;
use App\Modules\Telegram\Enums\TelegramCommandEnum;

/**
 * Команда /help
 */
final class HelpCommand extends AbstractTelegramCommand
{
    public function getTelegramCommandType(): TelegramCommandEnum
    {
        return TelegramCommandEnum::HELP;
    }

    public function handle(TelegramMessageDTO $message): void
    {
        $helpMessage = <<<HTML
<b>Доступные команды:</b>

/start - Начать работу с ботом
/select_board - Выбрать доску для создания задач (интерактивно)
/set_email &lt;email&gt; - Установить email для задач
/task - Создать задачу (интерактивно)
/help - Показать список доступных команд

<b>Пример использования:</b>
1. /select_board (выбрать пространство и доску через кнопки)
2. /set_email user@example.com
3. /task (следовать инструкциям бота для создания задачи)

Бот работает с Kaiten API и требует настройки переменных окружения KAITEN_API_URL и KAITEN_API_TOKEN.
HTML;

        $this->sendMessage($message->chatId, $helpMessage);
    }
}
