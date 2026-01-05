<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Models;

use App\Shared\Abstracts\AbstractModel;
use Illuminate\Support\Carbon;

/**
 * Модель задачи
 *
 * @property int                 $id
 * @property string              $title
 * @property string              $description
 * @property string|null         $user_email
 * @property string              $status
 * @property string|null         $task_tracker_url
 * @property array<string,mixed> $metadata
 * @property Carbon              $created_at
 * @property Carbon              $updated_at
 *
 * @extends AbstractModel<Task>
 */
final class Task extends AbstractModel
{
    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'description',
        'user_email',
        'status',
        'task_tracker_url',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata'   => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
