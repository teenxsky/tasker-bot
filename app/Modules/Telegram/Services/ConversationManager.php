<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Сервис для управления состоянием диалогов с пользователями
 */
final class ConversationManager
{
    private const string CACHE_PREFIX = 'telegram-conversation:';

    private const int TTL = 3600; // 1 час

    /**
     * Получить состояние диалога
     *
     * @return array<string, mixed>|null
     */
    public function get(int $chatId): ?array
    {
        /** @var array<string, mixed>|null $data */
        $data = Cache::get($this->getCacheKey($chatId));

        return $data;
    }

    /**
     * Сохранить состояние диалога
     *
     * @param array<string, mixed> $data
     */
    public function set(int $chatId, array $data): void
    {
        Cache::put($this->getCacheKey($chatId), $data, self::TTL);
    }

    /**
     * Удалить состояние диалога
     */
    public function forget(int $chatId): void
    {
        Cache::forget($this->getCacheKey($chatId));
    }

    /**
     * Проверить, есть ли активный диалог
     */
    public function has(int $chatId): bool
    {
        return Cache::has($this->getCacheKey($chatId));
    }

    private function getCacheKey(int $chatId): string
    {
        return self::CACHE_PREFIX . $chatId;
    }
}
