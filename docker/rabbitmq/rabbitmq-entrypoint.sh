#!/bin/sh

set -e
envsubst < /etc/rabbitmq/rabbitmq.conf.template > /etc/rabbitmq/rabbitmq.conf
envsubst < /etc/rabbitmq/definitions.json.template > /etc/rabbitmq/definitions.json

exec docker-entrypoint.sh "$@"
