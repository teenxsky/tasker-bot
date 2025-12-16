#!/bin/sh

php /var/www/artisan key:generate --force
php /var/www/artisan migrate --force
php /var/www/artisan optimize

sed -i "s/9000/${APP_PORT}/g" /usr/local/etc/php-fpm.d/www.conf
sed -i "s/9000/${APP_PORT}/g" /usr/local/etc/php-fpm.d/zz-docker.conf

exec php-fpm
