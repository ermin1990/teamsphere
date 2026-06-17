#!/bin/sh
set -e

echo "Starting entrypoint"

# optional: run migrations if RUN_MIGRATIONS=true
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  echo "RUN_MIGRATIONS=true, attempting migrations"
  n=0
  until php artisan migrate --force; do
    n=$((n+1))
    echo "Migration attempt $n failed, retrying in 3s..."
    if [ $n -ge 20 ]; then
      echo "Migrations failed after 20 attempts, continuing startup"
      break
    fi
    sleep 3
  done
fi

# create storage symlink if missing
if [ ! -L public/storage ]; then
  php artisan storage:link || true
fi

# cache config, routes and views for performance (no-op if fails)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# ensure permissions
chown -R www:www storage bootstrap/cache || true

echo "Starting php-fpm and nginx"
exec sh -c "php-fpm -F & nginx -g 'daemon off;'"
