<?php

declare(strict_types=1);

namespace App\Modules\Telegram\Models;

use App\Shared\Abstracts\AbstractModel;

/**
 * Модель Telegram чата
 *
 * @property int                        $id
 * @property int                        $chat_id
 * @property string                     $chat_type
 * @property string|null                $title
 * @property string|null                $board_id
 * @property string|null                $user_email
 * @property bool                       $is_active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @extends AbstractModel<TelegramChat>
 */
final class TelegramChat extends AbstractModel
{
    protected $table = 'telegram_chats';

    protected $fillable = [
        'chat_id',
        'chat_type',
        'title',
        'board_id',
        'user_email',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'chat_id'   => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
