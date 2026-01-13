<?php

declare(strict_types=1);

namespace App\Modules\AI;

use App\Modules\AI\Interfaces\Services\OpenRouterApiServiceInterface;
use App\Modules\AI\Interfaces\UseCases\ProcessTaskDescriptionUseCaseInterface;
use App\Modules\AI\Services\OpenRouterApiService;
use App\Modules\AI\UseCases\ProcessTaskDescriptionUseCase;
use App\Shared\Abstracts\AbstractModuleProvider;

final class ModuleProvider extends AbstractModuleProvider
{
    protected function registerRepositories(): void
    {
        // TODO: Implement registerRepositories() method.
    }

    protected function registerServices(): void
    {
        $this->app->singleton(
            OpenRouterApiServiceInterface::class,
            OpenRouterApiService::class
        );
    }

    protected function registerUseCases(): void
    {
        $this->app->bind(
            ProcessTaskDescriptionUseCaseInterface::class,
            ProcessTaskDescriptionUseCase::class
        );
    }
}
