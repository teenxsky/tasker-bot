<?php

declare(strict_types=1);

namespace App\Modules\Telegram\UseCases;

use App\Modules\Telegram\Commands\AbstractTelegramCommand;
use App\Modules\Telegram\DTO\TelegramMessageDTO;
use App\Modules\Telegram\Enums\TelegramCommandEnum;
use App\Modules\Telegram\Interfaces\Services\TelegramChatServiceInterface;
use App\Modules\Telegram\Interfaces\UseCases\ProcessTelegramUpdateUseCaseInterface;
use App\Modules\Telegram\Services\ConversationManager;

final readonly class ProcessTelegramUpdateUseCase implements ProcessTelegramUpdateUseCaseInterface
{
    /**
     * @param iterable<AbstractTelegramCommand> $commands
     */
    public function __construct(
        private TelegramChatServiceInterface $chatService,
        private ConversationManager          $conversationManager,
        private iterable                     $commands,
    ) {
    }

    public function execute(array $update): void
    {
        $telegramMessageDTO = $this->extractMessage($update);

        if (!$telegramMessageDTO instanceof TelegramMessageDTO) {
            return;
        }

        // Регистрируем чат, если он ещё не существует
        $this->chatService->findOrCreate(
            chatId: $telegramMessageDTO->chatId,
            chatType: $telegramMessageDTO->chatType,
            title: $telegramMessageDTO->chatTitle
        );

        // Маршрутизируем сообщение на соответствующую команду
        $this->routeMessage($telegramMessageDTO);
    }

    /**
     * Извлечь сообщение из update Telegram
     *
     * @param array<string, mixed> $update
     */
    private function extractMessage(array $update): ?TelegramMessageDTO
    {
        if (!isset($update['message']) || !is_array($update['message'])) {
            return null;
        }

        /** @var array<string, mixed> $messageData */
        $messageData        = $update['message'];
        $telegramMessageDTO = $this->parseMessage($messageData);

        if ($telegramMessageDTO->text === null || $telegramMessageDTO->text === '') {
            return null;
        }

        return $telegramMessageDTO;
    }

    /**
     * Преобразовать данные сообщения Telegram в DTO
     *
     * @param array<string, mixed> $messageData
     */
    private function parseMessage(array $messageData): TelegramMessageDTO
    {
        $chat = $messageData['chat'] ?? [];
        $from = $messageData['from'] ?? [];

        $chatId = is_array($chat) && isset($chat['id']) && is_int($chat['id'])
            ? $chat['id']
            : 0;

        $chatType = is_array($chat) && isset($chat['type']) && is_string($chat['type'])
            ? $chat['type']
            : 'private';

        $chatTitle = is_array($chat) && isset($chat['title']) && is_string($chat['title'])
            ? $chat['title']
            : null;

        $text = isset($messageData['text']) && is_string($messageData['text'])
            ? $messageData['text']
            : null;

        $messageId = isset($messageData['message_id']) && is_int($messageData['message_id'])
            ? $messageData['message_id']
            : null;

        $username = is_array($from) && isset($from['username']) && is_string($from['username'])
            ? $from['username']
            : null;

        return new TelegramMessageDTO(
            chatId: $chatId,
            chatType: $chatType,
            chatTitle: $chatTitle,
            text: $text,
            messageId: $messageId,
            username: $username,
        );
    }

    /**
     * Определить способ обработки сообщения
     */
    private function routeMessage(TelegramMessageDTO $message): void
    {
        // Если есть активный диалог — продолжаем его
        if ($this->conversationManager->has($message->chatId)) {
            $this->handleConversation($message);

            return;
        }

        // Иначе пытаемся обработать сообщение как команду
        $this->handleCommand($message);
    }

    /**
     * Обработать сообщение в рамках активного диалога
     */
    private function handleConversation(TelegramMessageDTO $message): void
    {
        $conversation = $this->conversationManager->get($message->chatId);

        if ($conversation === null) {
            return;
        }

        $commandType = $conversation['command_type'] ?? null;

        if (!$commandType instanceof TelegramCommandEnum) {
            $this->conversationManager->forget($message->chatId);

            return;
        }

        // Находим команду, которая инициировала диалог
        $command = $this->findCommand($commandType);

        if (!$command instanceof AbstractTelegramCommand) {
            $this->conversationManager->forget($message->chatId);

            return;
        }

        // Команда самостоятельно обрабатывает продолжение диалога
        $command->handle($message);
    }

    /**
     * Обработать сообщение как команду
     */
    private function handleCommand(TelegramMessageDTO $message): void
    {
        $commandType = TelegramCommandEnum::fromText($message->text ?? '');

        if (!$commandType instanceof TelegramCommandEnum) {
            return;
        }

        $command = $this->findCommand($commandType);

        if (!$command instanceof AbstractTelegramCommand) {
            return;
        }

        $command->handle($message);
    }

    /**
     * Найти обработчик команды по её типу
     */
    private function findCommand(TelegramCommandEnum $commandType): ?AbstractTelegramCommand
    {
        foreach ($this->commands as $command) {
            if ($command->getTelegramCommandType() === $commandType) {
                return $command;
            }
        }

        return null;
    }
}
