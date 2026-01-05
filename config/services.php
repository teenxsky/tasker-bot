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

];
