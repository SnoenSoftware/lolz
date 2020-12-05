FROM php:7.4-cli

RUN apt-get update && apt-get install -y cron

COPY app.crontab /etc/cron.d/app.crontab
RUN chmod 0644 /etc/cron.d/app.crontab \
    && crontab /etc/cron.d/app.crontab \
    && touch /var/log/cron.log

CMD cron && tail -f /var/log/cron.log