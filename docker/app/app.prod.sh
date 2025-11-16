#!/bin/sh

php /app/artisan key:generate --force
php /app/artisan migrate --force
php /app/artisan optimize

php /app/artisan octane:swoole --max-requests=2000
