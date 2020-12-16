FROM composer as builder
WORKDIR /app
COPY ./ /app/
RUN composer install


FROM php:7.4-fpm-alpine as fpm
COPY --from=builder /app/ /var/www/project/


FROM php:7.4-cli-alpine as cron
COPY ./dockerfiles/cron/app.crontab /etc/cron.d/app.crontab
COPY --from=builder /app/vendor /app/vendor
RUN chmod 0644 /etc/cron.d/app.crontab \
    && crontab /etc/cron.d/app.crontab

CMD ["crond", "-f"]


FROM node as frontend-builder
WORKDIR /app
COPY ./ /app/
RUN npm i
RUN npm run prod


FROM nginx:alpine as proxy
COPY ./nginx.conf /etc/nginx/conf.d/lolz.conf
COPY ./public /var/www/project/public
COPY --from=frontend-builder /app/public/build /var/www/project/public/build