#!/bin/bash
set -e

echo "Čekam bazu (postgres) da bude spremna..."
until php artisan db:show --json > /dev/null 2>&1; do
  echo "  ...baza nije spremna, čekam 2s"
  sleep 2
done
echo "Baza je spremna."

php artisan migrate --force

# Jednokratni uvoz starih (MySQL->PostgreSQL) podataka.
# Komanda je idempotentna: uveze samo ako je baza prazna (nema korisnika).
php artisan db:import-legacy || echo "UPOZORENJE: uvoz starih podataka nije uspio (nastavljam)."

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan storage:link || true

echo "Entrypoint gotov, startujem: $*"
exec "$@"
