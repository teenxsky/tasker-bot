<?php

declare(strict_types=1);

namespace App\Modules\TaskTracker\Interfaces\Services;

use App\Modules\TaskTracker\DTO\BoardDTO;
use App\Modules\TaskTracker\DTO\SpaceDTO;
use App\Modules\TaskTracker\DTO\UserDTO;

/**
 * Интерфейс для получения данных из таск-трекера
 */
interface TaskTrackerDataProviderInterface
{
    /**
     * Получить список пространств
     *
     * @return array<SpaceDTO>
     */
    public function getSpaces(): array;

    /**
     * Получить список досок
     *
     * @return array<BoardDTO>
     */
    public function getBoards(?int $spaceId = null): array;

    /**
     * Получить список пользователей
     *
     * @return array<UserDTO>
     */
    public function getUsers(): array;
}
