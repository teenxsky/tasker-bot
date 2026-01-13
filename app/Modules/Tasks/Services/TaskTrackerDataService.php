<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Services;

use App\Modules\Tasks\Interfaces\Services\TaskTrackerDataServiceInterface;
use App\Modules\TaskTracker\DTO\BoardDTO;
use App\Modules\TaskTracker\DTO\SpaceDTO;
use App\Modules\TaskTracker\DTO\UserDTO;
use App\Modules\TaskTracker\Interfaces\Services\TaskTrackerDataProviderInterface;

final readonly class TaskTrackerDataService implements TaskTrackerDataServiceInterface
{
    public function __construct(
        private TaskTrackerDataProviderInterface $taskTrackerProvider
    ) {
    }

    /**
     * Получить список пространств
     *
     * @return array<SpaceDTO>
     */
    public function getSpaces(): array
    {
        return $this->taskTrackerProvider->getSpaces();
    }

    /**
     * Получить список досок
     *
     * @return array<BoardDTO>
     */
    public function getBoards(?int $spaceId = null): array
    {
        return $this->taskTrackerProvider->getBoards($spaceId);
    }

    /**
     * Получить список пользователей
     *
     * @return array<UserDTO>
     */
    public function getUsers(): array
    {
        return $this->taskTrackerProvider->getUsers();
    }
}
