ARG PHP_EXTS="pcntl pdo_pgsql pgsql zip sockets"

# BASE-IMAGES
############################################################################
FROM php:8.4.15-fpm-alpine AS base-app

ARG PHP_EXTS
ENV TZ=Asia/Vladivostok

# Установка необходимых библиотек и Composer
RUN apk add --no-cache \
        bash \
        curl \
        unzip \
        libpq \
        postgresql-libs \
        liburing \
    && curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
        -o /usr/local/bin/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions ${PHP_EXTS} \
    && curl -sSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/cache/apk/* /tmp/*


FROM base-app AS prod-builder

ARG PHP_EXTS
WORKDIR /var/www

# deps для сборки расширений
RUN apk add --no-cache --virtual .build-deps \
        linux-headers \
        libpq-dev \
        postgresql-dev \
        ${PHPIZE_DEPS} \
    && install-php-extensions ${PHP_EXTS}

# Копируем composer-файлы
COPY composer.* ./

# prod-зависимости без dev
RUN composer install --no-scripts --no-dev -o \
    && cp -r vendor /tmp/vendor-prod
############################################################################


FROM base-app AS app-dev
WORKDIR /var/www

# Включаем нужные php.ini
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Скрипт запуска dev
COPY docker/app/app.dev.sh /usr/local/bin/app.dev.sh
RUN chmod +x /usr/local/bin/app.dev.sh

ENTRYPOINT ["/usr/local/bin/app.dev.sh"]


FROM base-app AS app-prod
WORKDIR /var/www

# Копируем только prod-vendor
COPY --from=prod-builder /tmp/vendor-prod ./vendor
COPY --from=prod-builder /tmp/composer.* ./
COPY . .

# Включаем production php.ini
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Скрипт для запуска prod приложения
COPY docker/app/app.prod.sh /usr/local/bin/app.prod.sh
RUN chmod +x /usr/local/bin/app.prod.sh

CMD ["/usr/local/bin/app.prod.sh"]
