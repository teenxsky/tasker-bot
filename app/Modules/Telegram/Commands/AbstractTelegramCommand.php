<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Commands;

use App\Modules\Telegram\DTO\SendMessageDTO;
use App\Modules\Telegram\DTO\TelegramMessageDTO;
use App\Modules\Telegram\Enums\TelegramCommandEnum;
use App\Modules\Telegram\Interfaces\Services\TelegramBotServiceInterface;
use App\Modules\Telegram\Services\ConversationManager;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardRemove;

/**
 * Базовый класс команды Telegram
 * Инкапсулирует всю логику работы с Telegram API
 */
abstract class AbstractTelegramCommand
{
    public function __construct(
        protected readonly ConversationManager       $conversationManager,
        private readonly TelegramBotServiceInterface $botService
    ) {
    }

    /**
     * Возвращает тип команды
     */
    abstract public function getTelegramCommandType(): TelegramCommandEnum;

    /**
     * Обрабатывает команду или продолжение диалога
     */
    abstract public function handle(TelegramMessageDTO $message): void;

    /**
     * Отправляет сообщение пользователю
     */
    protected function sendMessage(
        int                                                               $chatId,
        string                                                            $text,
        ?int                                                              $replyToMessageId = null,
        ReplyKeyboardMarkup|InlineKeyboardMarkup|ReplyKeyboardRemove|null $replyMarkup = null
    ): void {
        $this->botService->sendMessage(new SendMessageDTO(
            chatId: $chatId,
            text: $text,
            replyToMessageId: $replyToMessageId,
            parseMode: 'HTML',
            replyMarkup: $replyMarkup
        ));
    }

    /**
     * Создает клавиатуру из списка элементов
     *
     * @param array<string> $items
     */
    protected function createKeyboard(array $items): ReplyKeyboardMarkup
    {
        $buttons = [];
        foreach ($items as $item) {
            $buttons[] = [$item];
        }

        return new ReplyKeyboardMarkup($buttons, true, true);
    }

    /**
     * Удаляет клавиатуру
     */
    protected function removeKeyboard(): ReplyKeyboardRemove
    {
        return new ReplyKeyboardRemove();
    }

    /**
     * Начинает новый диалог
     *
     * @param array<string, mixed> $data
     */
    protected function startConversation(int $chatId, array $data): void
    {
        $data['command_type'] = $this->getTelegramCommandType();
        $this->conversationManager->set($chatId, $data);
    }

    /**
     * Обновляет данные активного диалога
     *
     * @param array<string, mixed> $data
     */
    protected function updateConversation(int $chatId, array $data): void
    {
        $currentData                 = $this->conversationManager->get($chatId) ?? [];
        $currentData['command_type'] = $this->getTelegramCommandType();

        $this->conversationManager->set($chatId, array_merge($currentData, $data));
    }

    /**
     * Получает данные активного диалога
     *
     * @return array<string, mixed>|null
     */
    protected function getConversation(int $chatId): ?array
    {
        return $this->conversationManager->get($chatId);
    }

    /**
     * Завершает диалог
     */
    protected function finishConversation(int $chatId): void
    {
        $this->conversationManager->forget($chatId);
    }

    /**
     * Проверяет, является ли сообщение продолжением диалога
     */
    protected function isConversationActive(int $chatId): bool
    {
        return $this->conversationManager->has($chatId);
    }
}
