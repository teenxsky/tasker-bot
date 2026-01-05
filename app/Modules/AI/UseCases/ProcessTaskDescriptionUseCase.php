<?php

declare(strict_types=1);

namespace App\Modules\AI\UseCases;

use App\Modules\AI\DTO\ProcessedTaskDataDTO;
use App\Modules\AI\DTO\TaskDescriptionDTO;
use App\Modules\AI\Exceptions\AIProcessingException;
use App\Modules\AI\Interfaces\Services\OpenRouterApiServiceInterface;
use App\Modules\AI\Interfaces\UseCases\ProcessTaskDescriptionUseCaseInterface;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ProcessTaskDescriptionUseCase implements ProcessTaskDescriptionUseCaseInterface
{
    private string $systemPrompt = <<<'PROMPT'
Ты - ассистент для систематизации задач в таск-трекере.

Твоя задача - преобразовать объемное и обширное описание задачи от менеджера/аналитика в структурированный формат для разработчика.

Верни ответ СТРОГО в формате JSON со следующими полями:
{
    "title": "Краткое название задачи (максимум 100 символов)",
    "description": "Подробное описание задачи, структурированное и понятное для разработчика",
    "acceptance_criteria": "Критерии приемки (что должно быть выполнено)",
    "technical_notes": "Технические заметки и рекомендации (если есть)",
    "estimated_time": "Примерная оценка времени выполнения в часах (число или null)",
    "priority": "Приоритет задачи: low, medium, high, critical",
    "tags": ["массив", "тегов", "для", "категоризации"]
}

Анализируй описание, выделяй главное, структурируй информацию и возвращай только JSON без дополнительных комментариев.
PROMPT;

    public function __construct(
        private readonly OpenRouterApiServiceInterface $openRouterApiService,
    ) {
    }

    public function execute(TaskDescriptionDTO $taskDescription): ProcessedTaskDataDTO
    {
        try {
            $userPrompt = $this->buildUserPrompt($taskDescription);

            $messages = [
                ['role' => 'system', 'content' => $this->systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ];

            $response = $this->openRouterApiService->send($messages);

            return $this->parseResponse($response);
        } catch (AIProcessingException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Непредвиденная ошибка при обработке описания задачи', [
                'exception' => $e,
            ]);

            throw new AIProcessingException('Непредвиденная ошибка: ' . $e->getMessage());
        }
    }

    /**
     * Построить промпт для пользователя.
     */
    private function buildUserPrompt(TaskDescriptionDTO $taskDescription): string
    {
        $prompt = 'Описание задачи от пользователя:' . "\n\n" . $taskDescription->description;

        if ($taskDescription->userEmail !== null) {
            $prompt .= "\n\nEmail исполнителя: " . $taskDescription->userEmail;
        }

        return $prompt;
    }

    /**
     * Распарсить ответ от AI и вернуть ProcessedTaskDataDTO.
     *
     * @throws AIProcessingException
     */
    private function parseResponse(string $response): ProcessedTaskDataDTO
    {
        // Удаляем возможные markdown блоки кода
        $cleanedResponse = preg_replace('/```json\s*|\s*```/', '', $response);

        if ($cleanedResponse === null) {
            throw new AIProcessingException('Failed to clean response from markdown');
        }

        $cleanedResponse = trim($cleanedResponse);

        try {
            $data = json_decode($cleanedResponse, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $throwable) {
            throw new AIProcessingException(
                'Не удалось проанализировать ответ AI: ' . $throwable->getMessage()
            );
        }

        if (!is_array($data)) {
            throw new AIProcessingException('Ответ ИИ — это не массив');
        }

        try {
            return ProcessedTaskDataDTO::from($data);
        } catch (Throwable $throwable) {
            throw new AIProcessingException($throwable->getMessage());
        }
    }
}
