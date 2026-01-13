<?php

declare(strict_types=1);

namespace App\Modules\TaskTracker\DTO;

use App\Shared\Abstracts\AbstractDTO;
use RuntimeException;

/**
 * DTO для карточки (задачи) Kaiten
 */
final class CardDTO extends AbstractDTO
{
    /**
     * @param array<int> $tags
     */
    public function __construct(
        public int     $id,
        public string  $title,
        public string  $description,
        public int     $boardId,
        public ?int    $columnId = null,
        public ?int    $laneId = null,
        public ?int    $ownerId = null,
        public ?string $externalLink = null,
        public array   $tags = [],
    ) {
    }

    /**
     * Получить URL карточки
     */
    public function getUrl(): string
    {
        $baseUrl = config('services.kaiten.api_url');

        if (!is_string($baseUrl)) {
            throw new RuntimeException('Базовый url не заполнен!');
        }

        return sprintf('%s/space/%d/card/%d', $baseUrl, $this->boardId, $this->id);
    }
}
