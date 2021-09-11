FROM php:7.4-fpm-alpine as builder
COPY --from=composer/composer /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY ./composer.json ./composer.lock /app/
RUN composer install
COPY ./ /app/


FROM php:7.4-fpm-alpine as fpm
LABEL org.opencontainers.image.source = "https://github.com/brbkaffe/lolz"
COPY --from=builder /app/ /var/www/lolz/


FROM php:7.4-cli-alpine as cron
LABEL org.opencontainers.image.source = "https://github.com/brbkaffe/lolz"
COPY ./dockerfiles/cron/app.crontab /etc/cron.d/app.crontab
COPY ./dockerfiles/cron/entrypoint.sh /entrypoint.sh
COPY --from=builder /app /app/
RUN chmod 0644 /etc/cron.d/app.crontab \
    && crontab /etc/cron.d/app.crontab

ENTRYPOINT [ "/entrypoint.sh" ]
CMD [ "crond", "-f" ]


FROM node as frontend-builder
WORKDIR /app
COPY ./package.json ./yarn.lock /app/
RUN yarn
COPY ./ /app
RUN yarn prod


FROM nginx:alpine as proxy
LABEL org.opencontainers.image.source = "https://github.com/brbkaffe/lolz"
COPY ./nginx.conf /etc/nginx/conf.d/lolz.conf
COPY ./public /var/www/lolz/public
COPY --from=frontend-builder /app/public/build /var/www/lolz/public/build