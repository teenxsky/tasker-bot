#!/bin/sh

composer install --no-scripts -o
php /app/artisan key:generate

sed -i "s/9000/${APP_PORT}/g" /usr/local/etc/php-fpm.d/www.conf
sed -i "s/9000/${APP_PORT}/g" /usr/local/etc/php-fpm.d/zz-docker.conf

exec php-fpm
