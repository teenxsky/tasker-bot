<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'kaiten' => [
        'api_token' => env('KAITEN_API_TOKEN'),
        'api_url'   => env('KAITEN_API_URL', 'https://api.kaiten.ru/api/v1'),
        'timeout'   => (int)env('KAITEN_API_TIMEOUT', 30),
    ],

    'openrouter' => [
        'api_key'     => env('OPEN_ROUTER_AI_API_KEY'),
        'api_url'     => env('OPEN_ROUTER_AI_API_URL', 'https://openrouter.ai/api/v1'),
        'model'       => env('OPEN_ROUTER_AI_MODEL', 'deepseek/deepseek-chat'),
        'temperature' => (float)env('OPEN_ROUTER_AI_TEMPERATURE', 0.2),
        'timeout'     => (int)env('OPEN_ROUTER_AI_TIMEOUT', 60),
    ],

    'telegram' => [
        'bot_token'   => env('TELEGRAM_BOT_TOKEN'),
        'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
    ],

];
