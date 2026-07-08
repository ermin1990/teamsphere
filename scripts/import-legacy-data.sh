#!/usr/bin/env bash
#
# Ucita konvertovane stare (MySQL) podatke u PostgreSQL bazu unutar Dockera.
# Pokrenuti TEK NAKON sto su migracije odradjene (app kontejner ih pokrece na startu).
#
# Koristenje:
#   bash scripts/import-legacy-data.sh
#
set -euo pipefail

cd "$(dirname "$0")/.."

DATA_FILE="database/legacy_data.pgsql"
SERVICE="postgres"

# Ucitaj DB kredencijale iz .env (fallback na uobicajene vrijednosti)
DB_DATABASE="$(grep -E '^DB_DATABASE=' .env 2>/dev/null | cut -d= -f2- || true)"
DB_USERNAME="$(grep -E '^DB_USERNAME=' .env 2>/dev/null | cut -d= -f2- || true)"
DB_DATABASE="${DB_DATABASE:-teamsphere}"
DB_USERNAME="${DB_USERNAME:-root}"

if [ ! -f "$DATA_FILE" ]; then
  echo "Nema $DATA_FILE. Prvo pokreni: python scripts/mysql_dump_to_pgsql.py"
  exit 1
fi

echo "Ucitavam $DATA_FILE u bazu '$DB_DATABASE' (korisnik '$DB_USERNAME')..."
docker compose cp "$DATA_FILE" "$SERVICE:/tmp/legacy_data.pgsql"
docker compose exec -T "$SERVICE" \
  psql -v ON_ERROR_STOP=1 -U "$DB_USERNAME" -d "$DB_DATABASE" -f /tmp/legacy_data.pgsql
docker compose exec -T "$SERVICE" rm -f /tmp/legacy_data.pgsql

echo ""
echo "Provjera (broj redova po tabeli):"
docker compose exec -T "$SERVICE" psql -U "$DB_USERNAME" -d "$DB_DATABASE" -c "
SELECT 'users' AS tabela, count(*) FROM users
UNION ALL SELECT 'organizations', count(*) FROM organizations
UNION ALL SELECT 'competitions', count(*) FROM competitions
UNION ALL SELECT 'players', count(*) FROM players
UNION ALL SELECT 'matches', count(*) FROM matches
UNION ALL SELECT 'standings', count(*) FROM standings
ORDER BY tabela;"

echo ""
echo "Gotovo. Stari podaci su uvezeni."
