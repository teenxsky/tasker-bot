<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Commands;

use App\Modules\Tasks\DTO\CreateTaskDTO;
use App\Modules\Tasks\Interfaces\Services\TaskTrackerDataServiceInterface;
use App\Modules\Tasks\Interfaces\UseCases\CreateTaskUseCaseInterface;
use App\Modules\Telegram\DTO\TelegramMessageDTO;
use App\Modules\Telegram\Enums\TelegramCommandEnum;
use App\Modules\Telegram\Interfaces\Services\TelegramBotServiceInterface;
use App\Modules\Telegram\Interfaces\Services\TelegramChatServiceInterface;
use App\Modules\Telegram\Services\ConversationManager;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Команда создания задачи
 * Управляет полным циклом диалога создания задачи
 */
final class TaskCommand extends AbstractTelegramCommand
{
    public function __construct(
        private readonly TelegramChatServiceInterface    $chatService,
        private readonly TaskTrackerDataServiceInterface $taskTrackerDataService,
        private readonly CreateTaskUseCaseInterface      $createTaskUseCase,
        ConversationManager                              $conversationManager,
        TelegramBotServiceInterface                      $botService
    ) {
        parent::__construct($conversationManager, $botService);
    }

    public function getTelegramCommandType(): TelegramCommandEnum
    {
        return TelegramCommandEnum::TASK;
    }

    public function handle(TelegramMessageDTO $message): void
    {
        // Если диалог не активен - начинаем новый
        if (!$this->isConversationActive($message->chatId)) {
            $this->startCreateTaskFlow($message);

            return;
        }

        // Иначе продолжаем существующий диалог
        $this->continueCreateTaskFlow($message);
    }

    /**
     * Начинает процесс создания задачи
     */
    private function startCreateTaskFlow(TelegramMessageDTO $message): void
    {
        // Проверяем предусловия
        if (!$this->chatService->hasBoard($message->chatId)) {
            $this->sendMessage(
                $message->chatId,
                'Сначала выберите доску командой /select_board'
            );

            return;
        }

        if (!$this->chatService->hasEmail($message->chatId)) {
            $this->sendMessage(
                $message->chatId,
                'Сначала укажите ваш email командой /set_email &lt;email&gt;'
            );

            return;
        }

        // Начинаем диалог
        $this->startConversation($message->chatId, [
            'step' => 'enter_description',
        ]);

        $text = <<<HTML
<b>Создание задачи</b>

Опишите задачу подробно. Чем больше деталей вы укажете, тем лучше ИИ сможет обработать задачу.
HTML;

        $this->sendMessage($message->chatId, $text);
    }

    /**
     * Продолжает процесс создания задачи
     */
    private function continueCreateTaskFlow(TelegramMessageDTO $message): void
    {
        $conversation = $this->getConversation($message->chatId);

        if ($conversation === null) {
            return;
        }

        $step = $conversation['step'] ?? null;

        match ($step) {
            'enter_description' => $this->handleDescriptionInput($message),
            'select_owner'      => $this->handleOwnerSelection($message, $conversation),
            'confirm'           => $this->handleTaskConfirmation($message, $conversation),
            default             => $this->handleInvalidStep($message),
        };
    }

    /**
     * Обрабатывает ввод описания задачи
     */
    private function handleDescriptionInput(TelegramMessageDTO $message): void
    {
        $description = $message->text;

        if ($description === null || mb_strlen($description) < 10) {
            $this->sendMessage(
                $message->chatId,
                'Описание слишком короткое. Пожалуйста, опишите задачу подробнее (минимум 10 символов).'
            );

            return;
        }

        if (mb_strlen($description) > 2000) {
            $this->sendMessage(
                $message->chatId,
                'Описание слишком длинное. Пожалуйста, сократите описание (максимум 2000 символов).'
            );

            return;
        }

        try {
            $users = $this->taskTrackerDataService->getUsers();

            if ($users === []) {
                $this->finishConversation($message->chatId);
                $this->sendMessage(
                    $message->chatId,
                    'Не найдено пользователей в Kaiten. Пожалуйста, убедитесь, что у вас есть доступ к пользователям.'
                );

                return;
            }

            $keyboard = $this->createKeyboard(
                array_map(fn ($user): string => $user->email, $users)
            );

            $this->updateConversation($message->chatId, [
                'step'        => 'select_owner',
                'description' => $description,
                'users'       => array_map(fn ($user): array => [
                    'id'    => $user->id,
                    'email' => $user->email,
                ], $users),
            ]);

            $this->sendMessage(
                chatId: $message->chatId,
                text: '<b>Выберите ответственного за задачу:</b>',
                replyMarkup: $keyboard
            );
        } catch (Throwable $throwable) {
            Log::error('Failed to get users', [
                'chat_id' => $message->chatId,
                'error'   => $throwable->getMessage(),
            ]);

            $this->finishConversation($message->chatId);
            $this->sendMessage(
                $message->chatId,
                'Произошла ошибка при получении списка пользователей. Пожалуйста, попробуйте позже.'
            );
        }
    }

    /**
     * Обрабатывает выбор ответственного
     *
     * @param array<string, mixed> $conversation
     */
    private function handleOwnerSelection(TelegramMessageDTO $message, array $conversation): void
    {
        $users         = $conversation['users'] ?? [];
        $selectedEmail = $message->text;

        if (!is_array($users)) {
            $this->finishConversation($message->chatId);
            $this->sendMessage(
                $message->chatId,
                'Произошла ошибка. Пожалуйста, начните снова.'
            );

            return;
        }

        $selectedUser = array_find(
            $users,
            fn ($user): bool => is_array($user) && isset($user['email']) && $user['email'] === $selectedEmail
        );

        if ($selectedUser === null) {
            $this->sendMessage(
                $message->chatId,
                'Пользователь не найден. Пожалуйста, выберите из предложенных вариантов.'
            );

            return;
        }

        $descriptionValue = $conversation['description'] ?? '';
        if (!is_string($descriptionValue)) {
            $this->finishConversation($message->chatId);
            $this->sendMessage(
                $message->chatId,
                'Ошибка получения описания задачи. Пожалуйста, начните снова.'
            );

            return;
        }

        $this->updateConversation($message->chatId, [
            'step'        => 'confirm',
            'description' => $descriptionValue,
            'owner_id'    => $selectedUser['id'] ?? null,
            'owner_email' => $selectedEmail,
        ]);

        $confirmMessage = <<<HTML
<b>Подтверждение создания задачи</b>

<b>Описание:</b>
{$descriptionValue}

<b>Ответственный:</b> {$selectedEmail}

Подтвердите создание задачи или отмените.
HTML;

        $this->sendMessage(
            chatId: $message->chatId,
            text: $confirmMessage,
            replyMarkup: $this->createKeyboard(['Подтвердить', 'Отменить'])
        );
    }

    /**
     * Обрабатывает подтверждение создания задачи
     *
     * @param array<string, mixed> $conversation
     */
    private function handleTaskConfirmation(TelegramMessageDTO $message, array $conversation): void
    {
        $action = $message->text;

        if ($action === 'Отменить') {
            $this->finishConversation($message->chatId);
            $this->sendMessage(
                chatId: $message->chatId,
                text: 'Создание задачи отменено. Вы можете начать снова командой /task',
                replyMarkup: $this->removeKeyboard()
            );

            return;
        }

        if ($action !== 'Подтвердить') {
            $this->sendMessage(
                $message->chatId,
                'Пожалуйста, используйте кнопки: "Подтвердить" или "Отменить".'
            );

            return;
        }

        $this->createTask($message, $conversation);
    }

    /**
     * Создает задачу
     *
     * @param array<string, mixed> $conversation
     */
    private function createTask(TelegramMessageDTO $message, array $conversation): void
    {
        $descriptionValue = $conversation['description'] ?? '';
        $ownerEmailValue  = $conversation['owner_email'] ?? '';

        if (!is_string($descriptionValue) || !is_string($ownerEmailValue)) {
            $this->finishConversation($message->chatId);
            $this->sendMessage(
                chatId: $message->chatId,
                text: 'Ошибка получения данных задачи. Пожалуйста, начните снова.',
                replyMarkup: $this->removeKeyboard()
            );

            return;
        }

        $telegramChatDTO = $this->chatService->getByChatId($message->chatId);
        $this->finishConversation($message->chatId);

        $this->sendMessage(
            chatId: $message->chatId,
            text: 'Задача обрабатывается... Пожалуйста, подождите.',
            replyMarkup: $this->removeKeyboard()
        );

        try {
            $taskDTO = $this->createTaskUseCase->execute(new CreateTaskDTO(
                title: 'Задача из Telegram',
                description: $descriptionValue,
                userEmail: $telegramChatDTO->userEmail,
                ownerEmail: $ownerEmailValue,
                metadata: [
                    'source'     => 'telegram',
                    'chat_id'    => $message->chatId,
                    'message_id' => $message->messageId,
                ],
            ));

            Log::info('Task created from Telegram', [
                'chat_id'     => $message->chatId,
                'task_id'     => $taskDTO->id,
                'message_id'  => $message->messageId,
                'user_email'  => $telegramChatDTO->userEmail,
                'owner_email' => $ownerEmailValue,
            ]);
        } catch (Throwable $throwable) {
            Log::error('Failed to create task from Telegram', [
                'chat_id' => $message->chatId,
                'error'   => $throwable->getMessage(),
            ]);

            $this->sendMessage(
                $message->chatId,
                'Произошла ошибка при создании задачи. Пожалуйста, попробуйте позже.'
            );
        }
    }

    /**
     * Обрабатывает неизвестный шаг диалога
     */
    private function handleInvalidStep(TelegramMessageDTO $message): void
    {
        Log::warning('Invalid conversation step in TaskCommand', [
            'chat_id'      => $message->chatId,
            'conversation' => $this->getConversation($message->chatId),
        ]);

        $this->finishConversation($message->chatId);
        $this->sendMessage(
            chatId: $message->chatId,
            text: 'Произошла ошибка в процессе создания задачи. Пожалуйста, начните снова командой /task',
            replyMarkup: $this->removeKeyboard()
        );
    }
}
