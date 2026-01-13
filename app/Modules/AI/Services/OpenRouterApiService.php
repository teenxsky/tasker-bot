<?php

declare(strict_types=1);

namespace App\Modules\AI\Services;

use App\Modules\AI\Exceptions\AIProcessingException;
use App\Modules\AI\Interfaces\Services\OpenRouterApiServiceInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Override;
use RuntimeException;
use Throwable;

/**
 * Сервис для взаимодействия с API OpenRouter.
 */
final readonly class OpenRouterApiService implements OpenRouterApiServiceInterface
{
    private string $apiKey;

    private string $apiUrl;

    private string $model;

    private float $temperature;

    private int $timeout;

    public function __construct()
    {
        /** @var string|null $apiKey */
        $apiKey = config('services.openrouter.api_key');
        /** @var string|null $apiUrl */
        $apiUrl = config('services.openrouter.api_url');
        /** @var string|null $model */
        $model = config('services.openrouter.model');
        /** @var float|int|null $temperature */
        $temperature = config('services.openrouter.temperature');
        /** @var int|null $timeout */
        $timeout = config('services.openrouter.timeout');

        if (!is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException('API-ключ OpenRouter не задан');
        }

        if (!is_string($apiUrl) || $apiUrl === '') {
            throw new RuntimeException('URL OpenRouter API не задан');
        }

        if (!is_string($model) || $model === '') {
            throw new RuntimeException('Модель OpenRouter не задана');
        }

        if (!is_float($temperature) && !is_int($temperature)) {
            throw new RuntimeException('Параметр temperature должен быть числом (float)');
        }

        if (!is_int($timeout)) {
            throw new RuntimeException('Таймаут OpenRouter API должен быть целым числом (в секундах)');
        }

        $this->apiKey      = $apiKey;
        $this->apiUrl      = $apiUrl;
        $this->model       = $model;
        $this->temperature = (float)$temperature;
        $this->timeout     = $timeout;
    }

    #[Override]
    public function send(array $messages): string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->apiUrl . '/chat/completions', [
                    'model'       => $this->model,
                    'messages'    => $messages,
                    'temperature' => $this->temperature,
                ]);

            if (!$response->successful()) {
                throw new AIProcessingException(
                    'OpenRouter API вернул HTTP-статус: ' . $response->status()
                );
            }

            /** @var array<string, mixed> $data */
            $data = $response->json();

            if (
                !isset($data['choices'])
                || !is_array($data['choices'])
                || !isset($data['choices'][0])
                || !is_array($data['choices'][0])
                || !isset($data['choices'][0]['message'])
                || !is_array($data['choices'][0]['message'])
                || !isset($data['choices'][0]['message']['content'])
                || !is_string($data['choices'][0]['message']['content'])
            ) {
                throw new AIProcessingException(
                    'Неожиданный формат ответа от API OpenRouter'
                );
            }

            return $data['choices'][0]['message']['content'];
        } catch (ConnectionException $e) {
            throw new AIProcessingException(
                'Ошибка соединения с OpenRouter API: ' . $e->getMessage()
            );
        } catch (AIProcessingException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Неожиданная ошибка при обращении к OpenRouter API', [
                'exception' => $e,
            ]);

            throw new AIProcessingException(
                'Неожиданная ошибка при работе с OpenRouter API: ' . $e->getMessage()
            );
        }
    }
}
