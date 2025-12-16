<?php

declare(strict_types=1);

namespace App\Framework\Http;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Формирование успешного JSON-ответа (HTTP 200)
     *
     * @param array<string, mixed>      $data Данные для возврата в ответе
     * @param array<string, mixed>|null $meta Дополнительные метаданные (опционально)
     */
    public function successResponse(
        array  $data,
        ?array $meta = null
    ): JsonResponse {
        return new JsonResponse(
            $this->responseTemplate(
                data: $data,
                meta: $meta
            ),
            Response::HTTP_OK
        );
    }

    /**
     * Формирование JSON-ответа "Ресурс не найден" (HTTP 404)
     *
     * @param string                    $errorMessage Сообщение об ошибке
     * @param array<string, mixed>|null $errorDetails Детали ошибки (опционально)
     * @param array<string, mixed>|null $meta         Дополнительные метаданные (опционально)
     */
    public function notFoundResponse(
        string $errorMessage,
        ?array $errorDetails = null,
        ?array $meta = null
    ): JsonResponse {
        return new JsonResponse(
            $this->responseTemplate(
                meta: $meta,
                errorMessage: $errorMessage,
                errorDetails: $errorDetails
            ),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Формирование JSON-ответа "Неверный запрос" (HTTP 400)
     *
     * @param string                    $errorMessage Сообщение об ошибке
     * @param array<string, mixed>|null $errorDetails Детали ошибки (опционально)
     * @param array<string, mixed>|null $meta         Дополнительные метаданные (опционально)
     */
    public function badRequestResponse(
        string $errorMessage,
        ?array $errorDetails = null,
        ?array $meta = null
    ): JsonResponse {
        return new JsonResponse(
            $this->responseTemplate(
                meta: $meta,
                errorMessage: $errorMessage,
                errorDetails: $errorDetails
            ),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * Формирование JSON-ответа "Слишком много запросов" (HTTP 429)
     *
     * @param string                    $errorMessage Сообщение об ошибке
     * @param array<string, mixed>|null $errorDetails Детали ошибки (опционально)
     * @param array<string, mixed>|null $meta         Дополнительные метаданные (опционально)
     */
    public function tooManyRequestsResponse(
        string $errorMessage,
        ?array $errorDetails = null,
        ?array $meta = null
    ): JsonResponse {
        return new JsonResponse(
            $this->responseTemplate(
                meta: $meta,
                errorMessage: $errorMessage,
                errorDetails: $errorDetails
            ),
            Response::HTTP_TOO_MANY_REQUESTS
        );
    }

    /**
     * Формирование JSON-ответа "Внутренняя ошибка сервера" (HTTP 500)
     *
     * @param string                    $errorMessage Сообщение об ошибке
     * @param array<string, mixed>|null $errorDetails Детали ошибки (опционально)
     * @param array<string, mixed>|null $meta         Дополнительные метаданные (опционально)
     */
    public function internalServerErrorResponse(
        string $errorMessage,
        ?array $errorDetails = null,
        ?array $meta = null
    ): JsonResponse {
        return new JsonResponse(
            $this->responseTemplate(
                meta: $meta,
                errorMessage: $errorMessage,
                errorDetails: $errorDetails
            ),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    /**
     * Формирование унифицированного шаблона JSON-ответа
     *
     * @param array<string, mixed>|null $data         Данные ответа (для успешных запросов)
     * @param array<string, mixed>|null $meta         Метаданные ответа
     * @param string|null               $errorMessage Сообщение об ошибке (для ошибочных запросов)
     * @param array<string, mixed>|null $errorDetails Детали ошибки
     *
     * @return array<string, mixed>
     */
    private function responseTemplate(
        ?array  $data = null,
        ?array  $meta = null,
        ?string $errorMessage = null,
        ?array  $errorDetails = null
    ): array {
        if ($errorMessage !== null) {
            return [
                'data'   => null,
                'errors' => [
                    [
                        'message' => $errorMessage,
                        'details' => $errorDetails,
                    ]
                ],
                'meta' => $meta,
            ];
        }

        return [
            'data' => $data,
            'meta' => $meta,
        ];
    }
}
