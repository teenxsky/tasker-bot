<?php

declare(strict_types=1);

namespace App\Shared\Abstracts;

use Illuminate\Support\ServiceProvider;
use Override;

/**
 * Базовый класс для провайдеров модулей.
 *
 * Унифицирует регистрацию зависимостей модуля, разделяя их на три слоя:
 * - Repositories (Infrastructure Layer) - работа с данными
 * - Services (Domain Layer) - бизнес-логика
 * - UseCases (Application Layer) - сценарии использования
 *
 * Каждый модуль должен наследоваться от этого класса и реализовать
 * методы регистрации своих зависимостей.
 */
abstract class AbstractModuleProvider extends ServiceProvider
{
    /**
     * Регистрирует сервисы модуля в контейнере.
     */
    #[Override]
    public function register(): void
    {
        $this->registerRepositories();
        $this->registerServices();
        $this->registerUseCases();
    }

    /**
     * Регистрирует репозитории модуля.
     *
     * ```
     * protected function registerRepositories(): void
     * {
     *     $this->app->singleton(
     *         ExampleRepositoryInterface::class,
     *         ExampleRepository::class
     *     );
     * }
     * ```
     */
    abstract protected function registerRepositories(): void;

    /**
     * Регистрирует сервисы модуля.
     *
     * ```
     * protected function registerServices(): void
     * {
     *     $this->app->singleton(
     *         ExampleServiceInterface::class,
     *         ExampleService::class
     *     );
     * }
     * ```
     */
    abstract protected function registerServices(): void;

    /**
     * Регистрирует сценарии использования (use cases) модуля.
     *
     * ```
     * protected function registerUseCases(): void
     * {
     *     $this->app->singleton(
     *         ExampleUseCaseInterface::class,
     *         ExampleUseCase::class
     *     );
     * }
     * ```
     */
    abstract protected function registerUseCases(): void;
}
