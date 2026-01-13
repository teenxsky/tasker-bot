<?php

declare(strict_types=1);

namespace App\Shared\Abstracts;

use App\Shared\Contracts\RepositoryInterface;

/**
 * @template T of AbstractModel
 * @implements RepositoryInterface<T>
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @return array<T>
     */
    public function findAll(): array
    {
        /** @var array<T> */
        return $this->query()->get()->all();
    }

    /**
     * @return T|null
     */
    public function findById(int $id): ?AbstractModel
    {
        /** @var T|null */
        return $this->query()->find($id);
    }

    /**
     * @param  T $model
     * @return T
     */
    public function save(AbstractModel $model): AbstractModel
    {
        $model->save();
        return $model;
    }

    /**
     * @param T $model
     */
    public function delete(AbstractModel $model): bool
    {
        return (bool)$model->delete();
    }
}
