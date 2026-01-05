<?php

declare(strict_types=1);

namespace App\Modules\Telegram;

use App\Modules\Tasks\Events\TaskCreatedEvent;
use App\Modules\Tasks\Interfaces\Services\TaskBoardResolverInterface;
use App\Modules\Telegram\Commands\HelpCommand;
use App\Modules\Telegram\Commands\SelectBoardCommand;
use App\Modules\Telegram\Commands\SetEmailCommand;
use App\Modules\Telegram\Commands\StartCommand;
use App\Modules\Telegram\Commands\TaskCommand;
use App\Modules\Telegram\Interfaces\Repositories\TelegramChatRepositoryInterface;
use App\Modules\Telegram\Interfaces\Services\TelegramBotServiceInterface;
use App\Modules\Telegram\Interfaces\Services\TelegramChatServiceInterface;
use App\Modules\Telegram\Interfaces\UseCases\ProcessTelegramUpdateUseCaseInterface;
use App\Modules\Telegram\Listeners\SendTaskCreatedNotificationListener;
use App\Modules\Telegram\Repositories\TelegramChatRepository;
use App\Modules\Telegram\Services\ConversationManager;
use App\Modules\Telegram\Services\TelegramBotService;
use App\Modules\Telegram\Services\TelegramChatService;
use App\Modules\Telegram\Services\TelegramTaskBoardResolver;
use App\Modules\Telegram\UseCases\ProcessTelegramUpdateUseCase;
use App\Shared\Abstracts\AbstractModuleProvider;
use Illuminate\Support\Facades\Event;

final class ModuleProvider extends AbstractModuleProvider
{
    public function boot(): void
    {
        Event::listen(
            TaskCreatedEvent::class,
            SendTaskCreatedNotificationListener::class
        );
    }

    protected function registerRepositories(): void
    {
        $this->app->singleton(
            TelegramChatRepositoryInterface::class,
            TelegramChatRepository::class
        );
    }

    protected function registerServices(): void
    {
        $this->app->singleton(
            TelegramBotServiceInterface::class,
            TelegramBotService::class
        );

        $this->app->singleton(
            TelegramChatServiceInterface::class,
            TelegramChatService::class
        );

        $this->app->singleton(ConversationManager::class);

        $this->app->singleton(
            TaskBoardResolverInterface::class,
            TelegramTaskBoardResolver::class
        );
    }

    protected function registerUseCases(): void
    {
        $this->app->bind(
            ProcessTelegramUpdateUseCaseInterface::class,
            ProcessTelegramUpdateUseCase::class
        );

        $this->app->bind(StartCommand::class);
        $this->app->bind(HelpCommand::class);
        $this->app->bind(SetEmailCommand::class);
        $this->app->bind(SelectBoardCommand::class);
        $this->app->bind(TaskCommand::class);

        $this->app->when(ProcessTelegramUpdateUseCase::class)
            ->needs('$commands')
            ->give(fn ($app): array => [
                $app->make(StartCommand::class),
                $app->make(HelpCommand::class),
                $app->make(SetEmailCommand::class),
                $app->make(SelectBoardCommand::class),
                $app->make(TaskCommand::class),
            ]);
    }
}
