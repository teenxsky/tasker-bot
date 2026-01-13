<?php

declare(strict_types=1);

namespace App\Modules\TaskTracker;

use App\Modules\TaskTracker\Interfaces\Services\TaskTrackerDataProviderInterface;
use App\Modules\TaskTracker\Interfaces\Services\TaskTrackerServiceInterface;
use App\Modules\TaskTracker\Services\KaitenApiClient;
use App\Modules\TaskTracker\Services\TaskTrackerService;
use App\Shared\Abstracts\AbstractModuleProvider;

final class ModuleProvider extends AbstractModuleProvider
{
    protected function registerRepositories(): void
    {
        // TODO: Implement registerRepositories() method.
    }

    protected function registerServices(): void
    {
        $this->app->singleton(KaitenApiClient::class);

        $this->app->bind(
            TaskTrackerDataProviderInterface::class,
            KaitenApiClient::class
        );

        $this->app->bind(
            TaskTrackerServiceInterface::class,
            TaskTrackerService::class
        );
    }

    protected function registerUseCases(): void
    {
        // TODO: Implement registerUseCases() method.
    }
}
