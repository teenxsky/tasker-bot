<?php

declare(strict_types=1);

namespace App\Shared\Contracts;

use App\Shared\Abstracts\AbstractModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * Базовый интерфейс репозитория, определяющий стандартные операции CRUD
 *
 * @template T of AbstractModel
 */
interface RepositoryInterface
{
    /**
     * Найти все сущности
     *
     * @return array<T>
     */
    public function findAll(): array;

    /**
     * Найти сущность по ID
     *
     * @return T|null
     */
    public function findById(int $id): ?AbstractModel;

    /**
     * Сохранить сущность
     *
     * @param  T $model
     * @return T
     */
    public function save(AbstractModel $model): AbstractModel;

    /**
     * Удалить сущность
     *
     * @param T $model
     */
    public function delete(AbstractModel $model): bool;

    /**
     * Получить builder запросов для сущности
     *
     * @return Builder<T>
     */
    public function query(): Builder;
}
