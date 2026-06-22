#!/bin/bash
set -e

echo "Čekam bazu (postgres) da bude spremna..."
until php artisan db:show --json > /dev/null 2>&1; do
  echo "  ...baza nije spremna, čekam 2s"
  sleep 2
done
echo "Baza je spremna."

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan storage:link || true

echo "Entrypoint gotov, startujem: $*"
exec "$@"
