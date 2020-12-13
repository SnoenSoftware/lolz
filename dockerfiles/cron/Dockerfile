FROM php:7.4-cli-alpine

COPY app.crontab /etc/cron.d/app.crontab
RUN chmod 0644 /etc/cron.d/app.crontab \
    && crontab /etc/cron.d/app.crontab

CMD ["crond", "-f"]