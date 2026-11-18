FROM php:8.5.12-fpm-alpine as builder
COPY --from=composer/composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY ./composer.json ./composer.lock ./.env /app/
COPY ./config /app/config
COPY ./bin /app/bin
COPY ./src /app/src
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction
COPY ./ /app/


FROM php:8.5.12-fpm-alpine as fpm
LABEL org.opencontainers.image.source = "https://github.com/SnoenSoftware/lolz"
COPY --from=builder /app/ /var/www/lolz/


FROM php:8.5.12-cli-alpine as cron
LABEL org.opencontainers.image.source = "https://github.com/SnoenSoftware/lolz"
COPY ./dockerfiles/cron/app.crontab /etc/cron.d/app.crontab
COPY ./dockerfiles/cron/entrypoint.sh /entrypoint.sh
COPY --from=builder /app /app/
RUN chmod 0644 /etc/cron.d/app.crontab \
    && crontab /etc/cron.d/app.crontab

ENTRYPOINT [ "/entrypoint.sh" ]
CMD [ "crond", "-f" ]


FROM node:lts-alpine as frontend-builder
WORKDIR /app
COPY ./package.json ./yarn.lock /app/
RUN corepack enable && yarn
COPY ./ /app
RUN yarn
RUN yarn prod


FROM nginx:alpine as proxy
LABEL org.opencontainers.image.source = "https://github.com/SnoenSoftware/lolz"
COPY ./nginx.conf /etc/nginx/conf.d/default.conf
COPY ./public /var/www/lolz/public
COPY --from=frontend-builder /app/public/build /var/www/lolz/public/build
