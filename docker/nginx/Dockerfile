FROM nginx:1.29.4-alpine

COPY docker/nginx/nginx.conf /etc/nginx/templates/default.conf.template

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

CMD ["nginx", "-g", "daemon off;"]
