<?php

declare(strict_types=1);

namespace App\Modules\Tasks;

use App\Modules\Tasks\Interfaces\Repositories\TaskExecutionResultRepositoryInterface;
use App\Modules\Tasks\Interfaces\Repositories\TaskRepositoryInterface;
use App\Modules\Tasks\Interfaces\Services\TaskExecutionResultServiceInterface;
use App\Modules\Tasks\Interfaces\Services\TaskServiceInterface;
use App\Modules\Tasks\Interfaces\UseCases\CreateTaskUseCaseInterface;
use App\Modules\Tasks\Interfaces\UseCases\ProcessTaskUseCaseInterface;
use App\Modules\Tasks\Repositories\TaskExecutionResultRepository;
use App\Modules\Tasks\Repositories\TaskRepository;
use App\Modules\Tasks\Services\TaskExecutionResultService;
use App\Modules\Tasks\Services\TaskService;
use App\Modules\Tasks\UseCases\CreateTaskUseCase;
use App\Modules\Tasks\UseCases\ProcessTaskUseCase;
use App\Shared\Abstracts\AbstractModuleProvider;

final class ModuleProvider extends AbstractModuleProvider
{
    protected function registerRepositories(): void
    {
        $this->app->singleton(
            TaskRepositoryInterface::class,
            TaskRepository::class
        );

        $this->app->singleton(
            TaskExecutionResultRepositoryInterface::class,
            TaskExecutionResultRepository::class
        );
    }

    protected function registerServices(): void
    {
        $this->app->bind(
            TaskServiceInterface::class,
            TaskService::class
        );

        $this->app->bind(
            TaskExecutionResultServiceInterface::class,
            TaskExecutionResultService::class
        );
    }

    protected function registerUseCases(): void
    {
        $this->app->bind(
            CreateTaskUseCaseInterface::class,
            CreateTaskUseCase::class
        );

        $this->app->bind(
            ProcessTaskUseCaseInterface::class,
            ProcessTaskUseCase::class
        );
    }
}
