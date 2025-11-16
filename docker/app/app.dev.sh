#!/bin/sh

composer install --no-scripts -o
php /app/artisan key:generate

php /app/artisan octane:swoole \
    --watch \
    --workers=4 \
    --max-requests=10
