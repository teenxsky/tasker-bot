ARG PHP_EXTS="pcntl pdo_pgsql pgsql zip"

# BASE-IMAGES
############################################################################
FROM phpswoole/swoole:6.1-php8.4-alpine AS base-app

ARG PHP_EXTS
ENV TZ=Asia/Vladivostok

# Установка необходимых библиотек
RUN apk add --no-cache \
        bash \
        curl \
        unzip \
        libpq \
        postgresql-libs \
        liburing \
    && curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions > /usr/local/bin/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions ${PHP_EXTS} \
    && rm -rf /var/cache/apk/* /tmp/*


FROM base-app AS prod-builder

ARG PHP_EXTS
WORKDIR /app

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
WORKDIR /app

# Установка node.js для hot reload
RUN apk add --no-cache nodejs npm \
    && npm install -g chokidar
ENV NODE_PATH=/usr/local/lib/node_modules

# Включаем нужные php.ini
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Скрипт запуска dev
COPY docker/app/app.dev.sh /usr/local/bin/app.dev.sh
RUN chmod +x /usr/local/bin/app.dev.sh

ENTRYPOINT ["/usr/local/bin/app.dev.sh"]


FROM base-app AS app-prod
WORKDIR /app

# Копируем только prod-vendor
COPY --from=prod-builder /tmp/vendor-prod ./vendor
COPY --from=prod-builder /tmp/composer.* ./
COPY . .

# Включаем production php.ini
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Скрипт для запуска prod приложения
COPY docker/app/app.prod.sh /usr/local/bin/app.prod.sh
RUN chmod +x /usr/local/bin/app.prod.sh


FROM postgres:17-alpine3.20 AS postgres

COPY docker/postgres/init-db.sh /docker-entrypoint-initdb.d/init-db.sh
RUN chmod +x /docker-entrypoint-initdb.d/init-db.sh
