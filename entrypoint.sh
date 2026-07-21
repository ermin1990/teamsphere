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

echo "🎾 Provjeravam inicijalno postavljanje lige (Tuzlanska liga)..."
php artisan db:seed --class=TuzlanskaLigaSeeder --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan storage:link || true

# named volume se popuni iz image-a SAMO pri prvom kreiranju, pa bi direktan mount
# zamrznuo assets na prvi deploy. Zato ovdje eksplicitno kopiramo svjež sadržaj
# public/ (uključujući storage symlink iznad) u dijeljeni volumen pri SVAKOM
# pokretanju, tako da nginx uvijek servira najnoviji build.
echo "Sinhronizujem javne asset-e (public/) u dijeljeni volumen za nginx..."
mkdir -p /var/www/html/public_export
rm -rf /var/www/html/public_export/*
cp -a /var/www/html/public/. /var/www/html/public_export/
# nginx worker ima drugi uid (npr. "nginx") od www-data, pa vlasnistvo ne pomaze -
# eksplicitno osiguravamo da su fajlovi citljivi/direktoriji prohodni za bilo koji uid.
chmod -R a+rX /var/www/html/public_export
echo "Asset-i sinhronizovani."

echo "Entrypoint gotov, startujem: $*"
exec "$@"
