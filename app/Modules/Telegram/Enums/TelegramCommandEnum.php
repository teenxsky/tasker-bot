<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Enums;

/**
 * Команды Telegram бота
 */
enum TelegramCommandEnum: string
{
    case START        = '/start';
    case SELECT_BOARD = '/select_board';
    case SET_EMAIL    = '/set_email';
    case TASK         = '/task';
    case HELP         = '/help';

    /**
     * Получить команду из текста
     */
    public static function fromText(string $text): ?self
    {
        return array_find(
            self::cases(),
            fn ($command): bool => str_starts_with($text, (string) $command->value)
        );
    }
}
