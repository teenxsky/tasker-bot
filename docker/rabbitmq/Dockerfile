FROM rabbitmq:4.1.2-management-alpine AS rabbitmq

RUN apk update && \
    apk add --no-cache gettext && \
    rm -rf /var/cache/apk/*

COPY ./docker/rabbitmq/rabbitmq.conf /etc/rabbitmq/rabbitmq.conf.template
COPY ./docker/rabbitmq/rabbitmq-definitions.json /etc/rabbitmq/definitions.json.template

COPY ./docker/rabbitmq/rabbitmq-entrypoint.sh /usr/local/bin/rabbitmq-entrypoint.sh
RUN chmod +x /usr/local/bin/rabbitmq-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/rabbitmq-entrypoint.sh"]
CMD ["rabbitmq-server"]
