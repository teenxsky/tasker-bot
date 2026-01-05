<?php

declare(strict_types=1);

namespace App\Modules\AI\Interfaces\Services;

use App\Modules\AI\Exceptions\AIProcessingException;

/**
 * Интерфейс для взаимодействия с OpenRouter API.
 */
interface OpenRouterApiServiceInterface
{
    /**
     * Отправляет сообщения в OpenRouter и возвращает сгенерированный ответ модели.
     *
     * @param array<int, array{role: string, content: string}> $messages Массив сообщений диалога в формате OpenAI (role/content)
     *
     * @return string Ответ, сгенерированный моделью
     *
     * @throws AIProcessingException В случае ошибок при обработке запроса
     */
    public function send(array $messages): string;
}
