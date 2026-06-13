#!/usr/bin/env sh
set -e

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -f public/index.php ] && [ -d public-dist ]; then
    cp -a public-dist/. public/
fi

chown -R www-data:www-data storage bootstrap/cache public

php artisan storage:link --force >/dev/null 2>&1 || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

php artisan config:cache
php artisan view:cache

exec "$@"
