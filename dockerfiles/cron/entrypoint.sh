#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php-fpm "$@"
fi

if [ ! -f /app/data/db.sqlite ]; then
    cd /app && /usr/local/bin/php bin/console doctrine:database:create
    cd /app && /usr/local/bin/php bin/console doctrine:migrations:migrate
    cd /app && /usr/local/bin/php bin/console feed:install
    cd /app && /usr/local/bin/php bin/console feed:regenerate
fi

cd /app && /usr/local/bin/php bin/console doctrine:migrations:migrate

exec "$@"
