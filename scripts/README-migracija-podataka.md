# Migracija starih podataka (MySQL → PostgreSQL u Dockeru)

Projekat je prešao sa MySQL na PostgreSQL. **Schemu grade Laravel migracije**
(`php artisan migrate`, pokreće se automatski u `entrypoint.sh`), pa se ovdje
uvoze **samo podaci** iz starog MySQL dumpa.

## Fajlovi

- `scripts/mysql_dump_to_pgsql.py` — konvertuje MySQL dump (samo INSERT-e) u Postgres SQL.
- `database/legacy_data.pgsql` — već generisani, Postgres-kompatibilan dump podataka.
- `scripts/import-legacy-data.sh` — učitava taj fajl u Postgres kontejner.

Preskočene (framework/prolazne) tabele: `migrations`, `cache`, `cache_locks`,
`sessions`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens`.

## Regenerisanje (ako se dump promijeni)

```bash
python scripts/mysql_dump_to_pgsql.py infinit4_testteamsphere.sql database/legacy_data.pgsql
```

## Uvoz — korak po korak

### 1. Kreiraj `.env` (ako ga nema)

```bash
cp .env.example .env
```

Postavi **APP_KEY** i **DB_PASSWORD** (Postgres neće startati sa praznom lozinkom):

```
DB_CONNECTION=pgsql
DB_HOST=postgres          # ime servisa u docker-compose
DB_PORT=5432
DB_DATABASE=teamsphere
DB_USERNAME=root
DB_PASSWORD=nekaJakaLozinka
```

`php artisan key:generate` ili ručno upiši `APP_KEY=base64:...`.

### 2. Automatski uvoz pri deployu (glavni način)

`entrypoint.sh` na startu kontejnera pokreće:

```
php artisan migrate --force      # schema
php artisan db:import-legacy      # stari podaci (jednokratno)
```

Komanda `db:import-legacy` ([app/Console/Commands/ImportLegacyData.php](../app/Console/Commands/ImportLegacyData.php)):

1. **Jednokratna** — provjerava marker tabelu `legacy_data_imports`. Ako je uvoz
   već obavljen, preskače (budući redeployi NE diraju prave podatke).
2. **Čisti test podatke** — `TRUNCATE` svih domenskih tabela (sve osim
   framework tabela) uz `RESTART IDENTITY CASCADE`.
3. **Uvozi** stare podatke i resetuje sekvence.
4. Sve u **jednoj transakciji** uz `session_replication_role = replica`
   (FK isključeni) — ili prođe sve, ili se ništa ne mijenja.
5. **Race-safe** — Postgres advisory lock (app i queue container ne mogu
   uvoziti istovremeno).

Dakle za sistem koji je već gore dovoljno je **redeploy** (`docker compose build`
+ `up -d`) — uvoz se desi sam. Provjera u logu:

```bash
docker compose logs app | grep -i "Uvoz završen"
```

Ponovni prisilni uvoz (opet očisti pa uvezi):

```bash
docker compose exec -T app php artisan db:import-legacy --force
```

### 3. Ručni uvoz (fallback, samo za praznu bazu)

> Napomena: ovaj put NE briše postojeće podatke i pretpostavlja praznu schemu.
> Za već pokrenut sistem koristi automatski uvoz iz koraka 2.

```powershell
docker compose cp database/legacy_data.pgsql postgres:/tmp/legacy_data.pgsql
docker compose exec -T postgres psql -v ON_ERROR_STOP=1 -U root -d teamsphere -f /tmp/legacy_data.pgsql
```

### 4. Provjera

```bash
docker compose exec -T postgres psql -U root -d teamsphere -c \
  "SELECT 'users' t, count(*) FROM users UNION ALL SELECT 'matches', count(*) FROM matches UNION ALL SELECT 'players', count(*) FROM players;"
```

## Napomene

- Šifre korisnika (bcrypt) prenose se kako jesu — login radi bez reseta.
- `id` vrijednosti se čuvaju 1:1; sekvence se resetuju na `MAX(id)` na kraju fajla.
- Boolean kolone: `tinyint(1)` (0/1) → `TRUE/FALSE`; `varchar` boolean ('0'/'1')
  Postgres prihvata direktno.
- Ako `psql` prijavi npr. `column "x" does not exist` — znači da se živa MySQL
  schema malo razišla od migracija; javi kolonu pa je uskladimo (drop iz dumpa
  ili dodatna migracija).
