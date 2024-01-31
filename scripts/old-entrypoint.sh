#! /bin/sh

php-fpm --daemonize

php artisan migrate --force --no-interaction
#php artisan key:generate --force --no-interaction

# php artisan db:seed --force --no-interaction # on db init only, after migrations

nginx -g "daemon off;"
