#!/bin/bash
set -e

echo "Čekam bazu (mysql) da bude spremna..."
until php artisan db:show --json > /dev/null 2>&1; do
  echo "  ...baza nije spremna, čekam 2s"
  sleep 2
done
echo "Baza je spremna."

# Napomena: prvobitni uvoz produkcijskih podataka (infinit4_testteamsphere.sql) se dešava
# automatski preko MySQL image-a (docker-entrypoint-initdb.d), samo kad je volumen prazan -
# ne treba nikakva Laravel komanda za to.
php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan storage:link || true

echo "Entrypoint gotov, startujem: $*"
exec "$@"
