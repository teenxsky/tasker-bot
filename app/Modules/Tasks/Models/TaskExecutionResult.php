<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Models;

use App\Shared\Abstracts\AbstractModel;
use Illuminate\Support\Carbon;

/**
 * Модель результата выполнения задачи
 *
 * @property int                       $id
 * @property int                       $task_id
 * @property array<string, mixed>|null $ai_processed_data
 * @property string|null               $error_message
 * @property Carbon                    $created_at
 * @property Carbon                    $updated_at
 *
 * @extends AbstractModel<TaskExecutionResult>
 */
final class TaskExecutionResult extends AbstractModel
{
    protected $table = 'task_execution_results';

    protected $fillable = [
        'task_id',
        'ai_processed_data',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'ai_processed_data' => 'array',
            'created_at'        => 'datetime',
            'updated_at'        => 'datetime',
        ];
    }
}
