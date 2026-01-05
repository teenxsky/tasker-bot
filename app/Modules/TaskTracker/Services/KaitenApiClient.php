<?php

declare(strict_types=1);

namespace App\Modules\TaskTracker\Services;

use App\Modules\TaskTracker\DTO\BoardDTO;
use App\Modules\TaskTracker\DTO\CardDTO;
use App\Modules\TaskTracker\DTO\SpaceDTO;
use App\Modules\TaskTracker\DTO\TagDTO;
use App\Modules\TaskTracker\DTO\UserDTO;
use App\Modules\TaskTracker\Interfaces\Services\TaskTrackerDataProviderInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

/**
 * API-клиент для работы с Kaiten
 */
final readonly class KaitenApiClient implements TaskTrackerDataProviderInterface
{
    /** URL API Kaiten */
    private string $apiUrl;

    /** Токен доступа к API Kaiten */
    private string $apiToken;

    /** Таймаут HTTP-запросов (в секундах) */
    private int $timeout;

    public function __construct()
    {
        /** @var string|null $apiUrlConfig */
        $apiUrlConfig = config('services.kaiten.api_url');

        /** @var string|null $apiToken */
        $apiToken = config('services.kaiten.api_token');

        /** @var int|null $timeout */
        $timeout = config('services.kaiten.timeout');

        if (!is_string($apiUrlConfig) || $apiUrlConfig === '') {
            throw new RuntimeException(
                'URL API Kaiten не задан. Укажите services.kaiten.api_url в конфигурации.'
            );
        }

        if (!is_string($apiToken) || $apiToken === '') {
            throw new RuntimeException(
                'Токен API Kaiten не задан. Укажите services.kaiten.api_token в конфигурации.'
            );
        }

        if (!is_int($timeout)) {
            throw new RuntimeException(
                'Таймаут API Kaiten должен быть целым числом (в секундах). Проверьте services.kaiten.timeout.'
            );
        }

        $this->apiUrl   = $apiUrlConfig . '/api';
        $this->apiToken = $apiToken;
        $this->timeout  = $timeout;
    }

    /**
     * Получить список пространств
     *
     * @return array<SpaceDTO>
     */
    public function getSpaces(): array
    {
        try {
            $response = $this->request(Request::METHOD_GET, '/latest/spaces');

            if (!is_array($response)) {
                return [];
            }

            return SpaceDTO::collect($response);
        } catch (Throwable $throwable) {
            Log::error('Ошибка получения пространств Kaiten', [
                'error' => $throwable->getMessage(),
            ]);

            throw new RuntimeException('Не удалось получить список пространств: ' . $throwable->getMessage(), $throwable->getCode(), $throwable);
        }
    }

    /**
     * Получить список досок
     *
     * @return array<BoardDTO>
     */
    public function getBoards(?int $spaceId = null): array
    {
        try {
            $response = $this->request(
                Request::METHOD_GET,
                '/latest/spaces/' . $spaceId . '/boards'
            );

            if (!is_array($response)) {
                return [];
            }

            return BoardDTO::collect($response);
        } catch (Throwable $throwable) {
            Log::error('Ошибка получения досок Kaiten', [
                'space_id' => $spaceId,
                'error'    => $throwable->getMessage(),
            ]);

            throw new RuntimeException(
                'Не удалось получить список досок: ' . $throwable->getMessage(),
                $throwable->getCode(),
                $throwable
            );
        }
    }

    /**
     * Получить список пользователей
     *
     * @return array<UserDTO>
     */
    public function getUsers(): array
    {
        try {
            $response = $this->request(Request::METHOD_GET, '/latest/users');

            if (!is_array($response)) {
                return [];
            }

            return UserDTO::collect($response);
        } catch (Throwable $throwable) {
            Log::error('Ошибка получения пользователей Kaiten', [
                'error' => $throwable->getMessage(),
            ]);

            throw new RuntimeException(
                'Не удалось получить список пользователей: ' . $throwable->getMessage(),
                $throwable->getCode(),
                $throwable
            );
        }
    }

    /**
     * Найти пользователя по email
     */
    public function findUserByEmail(string $email): ?UserDTO
    {
        try {
            $users = $this->getUsers();

            return array_find($users, fn ($user): bool => $user->email === $email);
        } catch (Throwable $throwable) {
            Log::error('Ошибка поиска пользователя Kaiten по email', [
                'email' => $email,
                'error' => $throwable->getMessage(),
            ]);

            throw new RuntimeException(
                'Не удалось найти пользователя по email: ' . $throwable->getMessage(),
                $throwable->getCode(),
                $throwable
            );
        }
    }

    /**
     * Получить список тегов доски
     *
     * @return array<TagDTO>
     */
    public function getTags(int $boardId): array
    {
        try {
            $response = $this->request(
                Request::METHOD_GET,
                sprintf('/latest/boards/%d/tags', $boardId)
            );

            if (!is_array($response)) {
                return [];
            }

            return TagDTO::collect($response);
        } catch (Throwable $throwable) {
            Log::error('Ошибка получения тегов Kaiten', [
                'board_id' => $boardId,
                'error'    => $throwable->getMessage(),
            ]);

            throw new RuntimeException(
                'Не удалось получить теги доски: ' . $throwable->getMessage(),
                $throwable->getCode(),
                $throwable
            );
        }
    }

    /**
     * Создать карточку (задачу)
     *
     * @param array<string, mixed> $data
     */
    public function createCard(array $data): CardDTO
    {
        try {
            $response = $this->request(Request::METHOD_POST, '/latest/cards', $data);

            if (!is_array($response)) {
                throw new RuntimeException('Получен некорректный формат ответа от API Kaiten');
            }

            return CardDTO::from($response);
        } catch (Throwable $throwable) {
            Log::error('Ошибка создания карточки Kaiten', [
                'data'  => $data,
                'error' => $throwable->getMessage(),
            ]);

            throw new RuntimeException('Не удалось создать карточку: ' . $throwable->getMessage(), $throwable->getCode(), $throwable);
        }
    }

    /**
     * Выполнить HTTP-запрос к API Kaiten
     *
     * @param  array<string, mixed> $data
     * @throws ConnectionException
     */
    private function request(string $method, string $endpoint, array $data = []): mixed
    {
        $url = $this->apiUrl . $endpoint;

        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])
            ->send($method, $url, [
                'json' => $data,
            ]);

        if (!$response->successful()) {
            throw new RuntimeException(sprintf(
                'Запрос к API Kaiten завершился ошибкой (%d): %s',
                $response->status(),
                $response->body()
            ));
        }

        return $response->json();
    }
}
