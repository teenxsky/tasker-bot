FROM postgres:17-alpine3.20 AS postgres

COPY docker/postgres/init-db.sh /docker-entrypoint-initdb.d/init-db.sh
RUN chmod +x /docker-entrypoint-initdb.d/init-db.sh
