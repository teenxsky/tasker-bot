#!/bin/sh

[ ! -n "${REDIS_PASSWORD}" ] && echo "env REDIS_PASSWORD is not set" && exit 1
[ ! -n "${REDIS_USERNAME}" ] && echo "env REDIS_USERNAME is not set" && exit 1

mkdir -p /usr/local/etc/redis

echo "requirepass ${REDIS_PASSWORD}" > /usr/local/etc/redis/redis.conf
echo "user ${REDIS_USERNAME} on >${REDIS_PASSWORD} allcommands allkeys" >> /usr/local/etc/redis/redis.conf

exec redis-server /usr/local/etc/redis/redis.conf
