<?php

return [
    # Framework Providers
    \App\Framework\Providers\AppServiceProvider::class,

    # Module Providers
    \App\Modules\AI\ModuleProvider::class,
    \App\Modules\Tasks\ModuleProvider::class,
    \App\Modules\TaskTracker\ModuleProvider::class,
    \App\Modules\Telegram\ModuleProvider::class,
];
