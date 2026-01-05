<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Commands;

use App\Modules\Tasks\Interfaces\Services\TaskTrackerDataServiceInterface;
use App\Modules\Telegram\DTO\TelegramMessageDTO;
use App\Modules\Telegram\Enums\TelegramCommandEnum;
use App\Modules\Telegram\Interfaces\Services\TelegramBotServiceInterface;
use App\Modules\Telegram\Interfaces\Services\TelegramChatServiceInterface;
use App\Modules\Telegram\Services\ConversationManager;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Команда выбора доски
 * Управляет полным циклом диалога выбора пространства и доски
 */
final class SelectBoardCommand extends AbstractTelegramCommand
{
    public function __construct(
        private readonly TaskTrackerDataServiceInterface $taskTrackerDataService,
        private readonly TelegramChatServiceInterface    $chatService,
        ConversationManager                              $conversationManager,
        TelegramBotServiceInterface                      $botService
    ) {
        parent::__construct($conversationManager, $botService);
    }

    public function getTelegramCommandType(): TelegramCommandEnum
    {
        return TelegramCommandEnum::SELECT_BOARD;
    }

    public function handle(TelegramMessageDTO $message): void
    {
        // Если диалог не активен - начинаем новый
        if (!$this->isConversationActive($message->chatId)) {
            $this->startSelectBoardFlow($message);

            return;
        }

        // Иначе продолжаем существующий диалог
        $this->continueSelectBoardFlow($message);
    }

    /**
     * Начинает процесс выбора доски
     */
    private function startSelectBoardFlow(TelegramMessageDTO $message): void
    {
        try {
            $spaces = $this->taskTrackerDataService->getSpaces();

            if ($spaces === []) {
                $this->sendMessage(
                    $message->chatId,
                    'У вас нет доступных пространств в Kaiten. Пожалуйста, создайте пространство и попробуйте снова.'
                );

                return;
            }

            $keyboard = $this->createKeyboard(
                array_map(fn ($space): string => $space->title, $spaces)
            );

            $this->startConversation($message->chatId, [
                'step'   => 'select_space',
                'spaces' => array_map(fn ($space): array => [
                    'id'    => $space->id,
                    'title' => $space->title,
                ], $spaces),
            ]);

            $this->sendMessage(
                chatId: $message->chatId,
                text: '<b>Выберите пространство:</b>',
                replyMarkup: $keyboard
            );
        } catch (Throwable $throwable) {
            Log::error('Failed to start select board conversation', [
                'chat_id' => $message->chatId,
                'error'   => $throwable->getMessage(),
            ]);

            $this->sendMessage(
                $message->chatId,
                'Произошла ошибка при получении списка пространств. Пожалуйста, убедитесь, что настроены параметры KAITEN_API_URL и KAITEN_API_TOKEN.'
            );
        }
    }

    /**
     * Продолжает процесс выбора доски
     */
    private function continueSelectBoardFlow(TelegramMessageDTO $message): void
    {
        $conversation = $this->getConversation($message->chatId);

        if ($conversation === null) {
            return;
        }

        $step = $conversation['step'] ?? null;

        match ($step) {
            'select_space' => $this->handleSpaceSelection($message, $conversation),
            'select_board' => $this->handleBoardSelection($message, $conversation),
            default        => $this->handleInvalidStep($message),
        };
    }

    /**
     * Обрабатывает выбор пространства
     *
     * @param array<string, mixed> $conversation
     */
    private function handleSpaceSelection(TelegramMessageDTO $message, array $conversation): void
    {
        $spaces             = $conversation['spaces'] ?? [];
        $selectedSpaceTitle = $message->text;

        if (!is_array($spaces)) {
            $this->finishConversation($message->chatId);
            $this->sendMessage(
                $message->chatId,
                'Произошла ошибка. Пожалуйста, начните снова.'
            );

            return;
        }

        $selectedSpace = array_find(
            $spaces,
            fn ($space): bool => is_array($space) && isset($space['title']) && $space['title'] === $selectedSpaceTitle
        );

        if ($selectedSpace === null) {
            $this->sendMessage(
                $message->chatId,
                'Пространство не найдено. Пожалуйста, выберите из предложенных вариантов.'
            );

            return;
        }

        try {
            $spaceId = $selectedSpace['id'] ?? null;
            if (!is_int($spaceId)) {
                throw new RuntimeException('Space ID is not an integer');
            }

            $boards = $this->taskTrackerDataService->getBoards($spaceId);

            if ($boards === []) {
                $this->finishConversation($message->chatId);
                $this->sendMessage(
                    chatId: $message->chatId,
                    text: 'В выбранном пространстве нет досок. Пожалуйста, создайте доску и попробуйте снова.',
                    replyMarkup: $this->removeKeyboard()
                );

                return;
            }

            $keyboard = $this->createKeyboard(
                array_map(fn ($board): string => $board->title, $boards)
            );

            $this->updateConversation($message->chatId, [
                'step'     => 'select_board',
                'space_id' => $spaceId,
                'boards'   => array_map(fn ($board): array => [
                    'id'    => $board->id,
                    'title' => $board->title,
                ], $boards),
            ]);

            $this->sendMessage(
                chatId: $message->chatId,
                text: '<b>Выберите доску:</b>',
                replyMarkup: $keyboard
            );
        } catch (Throwable $throwable) {
            Log::error('Failed to get boards', [
                'chat_id'  => $message->chatId,
                'space_id' => $selectedSpace['id'] ?? null,
                'error'    => $throwable->getMessage(),
            ]);

            $this->finishConversation($message->chatId);
            $this->sendMessage(
                chatId: $message->chatId,
                text: 'Произошла ошибка при получении списка досок. Пожалуйста, попробуйте позже.',
                replyMarkup: $this->removeKeyboard()
            );
        }
    }

    /**
     * Обрабатывает выбор доски
     *
     * @param array<string, mixed> $conversation
     */
    private function handleBoardSelection(TelegramMessageDTO $message, array $conversation): void
    {
        $boards             = $conversation['boards'] ?? [];
        $selectedBoardTitle = $message->text;

        if (!is_array($boards)) {
            $this->finishConversation($message->chatId);
            $this->sendMessage(
                $message->chatId,
                'Произошла ошибка. Пожалуйста, начните снова.'
            );

            return;
        }

        $selectedBoard = array_find(
            $boards,
            fn ($board): bool => is_array($board) && isset($board['title']) && $board['title'] === $selectedBoardTitle
        );

        if ($selectedBoard === null) {
            $this->sendMessage(
                $message->chatId,
                'Доска не найдена. Пожалуйста, выберите из предложенных вариантов.'
            );

            return;
        }

        $boardIdValue = $selectedBoard['id'] ?? null;
        if (!is_int($boardIdValue)) {
            $this->finishConversation($message->chatId);
            $this->sendMessage(
                $message->chatId,
                'Ошибка получения ID доски. Пожалуйста, начните снова.'
            );

            return;
        }

        $this->chatService->updateBoardId($message->chatId, (string)$boardIdValue);
        $this->finishConversation($message->chatId);

        $this->sendMessage(
            chatId: $message->chatId,
            text: "Доска <b>{$selectedBoardTitle}</b> успешно выбрана!\n\nТеперь вы можете создавать задачи командой /task",
            replyMarkup: $this->removeKeyboard()
        );
    }

    /**
     * Обрабатывает неизвестный шаг диалога
     */
    private function handleInvalidStep(TelegramMessageDTO $message): void
    {
        Log::warning('Invalid conversation step in SelectBoardCommand', [
            'chat_id'      => $message->chatId,
            'conversation' => $this->getConversation($message->chatId),
        ]);

        $this->finishConversation($message->chatId);
        $this->sendMessage(
            chatId: $message->chatId,
            text: 'Произошла ошибка в процессе выбора доски. Пожалуйста, начните снова командой /select_board',
            replyMarkup: $this->removeKeyboard()
        );
    }
}
