<?php

declare(strict_types=1);

namespace App\Modules\TaskTracker\Services;

use App\Modules\TaskTracker\DTO\CreateCardRequestDTO;
use App\Modules\TaskTracker\Interfaces\Services\TaskTrackerServiceInterface;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

final readonly class TaskTrackerService implements TaskTrackerServiceInterface
{
    public function __construct(
        private KaitenApiClient $kaitenClient,
    ) {
    }

    public function createTask(CreateCardRequestDTO $request, string $boardId): string
    {
        // Проверяем, что идентификатор доски является числом
        if (!is_numeric($boardId)) {
            throw new RuntimeException(
                'Идентификатор доски должен быть числом, получено значение: ' . $boardId
            );
        }

        $boardIdInt = (int)$boardId;

        try {
            // Подготавливаем данные для создания карточки
            $cardData = $this->prepareCardData($request, $boardIdInt);

            // Создаём карточку в Kaiten
            $card = $this->kaitenClient->createCard($cardData);

            return $card->getUrl();
        } catch (Throwable $throwable) {
            Log::error('Ошибка создания задачи в Kaiten', [
                'title'    => $request->title,
                'board_id' => $boardId,
                'error'    => $throwable->getMessage(),
            ]);

            throw new RuntimeException(
                'Не удалось создать задачу в таск-трекере: ' . $throwable->getMessage(),
                $throwable->getCode(),
                $throwable
            );
        }
    }

    /**
     * Подготовить данные для создания карточки в Kaiten
     *
     * @return array<string, mixed>
     */
    private function prepareCardData(CreateCardRequestDTO $request, int $boardId): array
    {
        // Базовые данные карточки
        $cardData = [
            'board_id' => $boardId,
            'title'    => $request->processedData['title']
                ?? $request->title,
            'description' => $request->processedData['processed_description']
                ?? $request->description,
        ];

        // Назначаем владельца карточки по email владельца, если он указан
        if ($request->ownerEmail !== null) {
            try {
                $user = $this->kaitenClient->findUserByEmail($request->ownerEmail);
                if ($user instanceof \App\Modules\TaskTracker\DTO\UserDTO) {
                    $cardData['owner_id'] = $user->id;
                }
            } catch (Throwable $e) {
                Log::warning('Не удалось определить владельца карточки в Kaiten', [
                    'email' => $request->ownerEmail,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Назначаем владельца по email пользователя, если владелец не задан
        if ($request->ownerEmail === null && $request->userEmail !== null) {
            try {
                $user = $this->kaitenClient->findUserByEmail($request->userEmail);
                if ($user instanceof \App\Modules\TaskTracker\DTO\UserDTO) {
                    $cardData['owner_id'] = $user->id;
                }
            } catch (Throwable $e) {
                Log::warning('Не удалось определить пользователя в Kaiten', [
                    'email' => $request->userEmail,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Добавляем теги, если они указаны
        if (
            isset($request->processedData['tags'])
            && is_array($request->processedData['tags'])
            && $request->processedData['tags'] !== []
        ) {
            try {
                $availableTags = $this->kaitenClient->getTags($boardId);
                $tagIds        = [];

                // Сопоставляем названия тегов с их идентификаторами
                foreach ($request->processedData['tags'] as $tagName) {
                    if (!is_string($tagName) && !is_int($tagName)) {
                        continue;
                    }

                    $tagNameStr = (string)$tagName;

                    foreach ($availableTags as $availableTag) {
                        if (strcasecmp($availableTag->name, $tagNameStr) === 0) {
                            $tagIds[] = $availableTag->id;
                            break;
                        }
                    }
                }

                if ($tagIds !== []) {
                    $cardData['tags'] = $tagIds;
                }
            } catch (Throwable $e) {
                Log::warning('Не удалось добавить теги к карточке', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $cardData;
    }
}
