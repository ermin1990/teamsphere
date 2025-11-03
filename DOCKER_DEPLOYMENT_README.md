# TeamSphere - Railway Docker Deployment

Ovaj projekat je konfigurisan za deployment na Railway platformi koristeći Docker.

## 🚀 De### Poznati problemi i rješenja

#### Railway "docker executable not found" greška
Uklonite `startCommand` iz `railway.json` - Railway automatski koristi `CMD` iz Dockerfile-a.

#### Nginx "unknown app_key variable" greška
Uklonite `fastcgi_param APP_KEY $APP_KEY;` iz nginx konfiguracije. Nginx ne može interpretirati environment varijable na ovaj način. Laravel čita environment varijable direktno.

#### Composer install greška
Ako se javlja greška prilikom `composer install` sa porukom "Could not open input file: artisan", to znači da se Laravel skripti pokušavaju pokrenuti prije nego što je aplikacioni kod kopiran. Dockerfile koristi `--no-scripts` flag da spriječi ovo.

#### PHP ekstenzije u Alpine Linux-u
Dockerfile koristi Alpine Linux pakete:
- `oniguruma-dev` (ne `libonig-dev`)
- `libzip-dev` za zip ekstenziju
- `freetype-dev` i `libjpeg-turbo-dev` za GD ekstenziju

#### Environment varijable
Railway automatski dodaje neke varijable (DATABASE_URL, REDIS_URL). Provjerite Railway dashboard za tačne nazive.ailway

### 1. Priprema

1. Kopirajte `.env.production.example` u `.env.production`:
   ```bash
   cp .env.production.example .env.production
   ```

2. Popunite sve environment varijable u `.env.production` fajlu

### 2. Railway Deployment

1. Idite na [Railway.app](https://railway.app) i prijavite se
2. Kliknite "New Project" → "Deploy from GitHub repo"
3. Odaberite ovaj repozitorij
4. Railway će automatski detektovati `railway.json` i `Dockerfile`
5. Dodajte environment varijable u Railway dashboard-u

### 3. Environment Varijable

Obavezne environment varijable za Railway:

```env
# Application
APP_NAME=TeamSphere
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://your-railway-url.railway.app

# Database (Railway PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=postgres.railway.internal
DB_PORT=5432
DB_DATABASE=railway
DB_USERNAME=postgres
DB_PASSWORD=your-postgres-password

# Cache (Railway Redis)
REDIS_URL=redis://user:password@host:port

# Mail (koristite Railway SMTP ili eksterni servis)
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=TeamSphere

# Ostale
CACHE_DRIVER=redis
QUEUE_CONNECTION=database
SESSION_DRIVER=redis
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 4. Database Setup

Railway automatski kreira PostgreSQL bazu. Nakon deployment-a pokrenite:

```bash
php artisan migrate
php artisan db:seed  # ako želite test podatke
```

### 5. Storage Permissions

Railway će automatski podesiti permissions za storage foldere.

## 🐳 Lokalni Development sa Docker-om

Za lokalni development možete koristiti docker-compose:

```bash
# Build i pokreni servise
docker-compose -f docker-compose.yml up --build

# Aplikacija će biti dostupna na http://localhost:8000
```

### Testiranje Docker Build-a

Da testirate da li Dockerfile radi ispravno:

```bash
# Build image
docker build -t teamsphere:test .

# Run container
docker run -p 8000:80 --env-file .env teamsphere:test

# Aplikacija će biti dostupna na http://localhost:8000
```

## 📁 Struktura fajlova

```
.
├── Dockerfile                    # Docker konfiguracija
├── docker/
│   ├── nginx.conf               # Nginx konfiguracija
│   ├── default.conf             # Nginx site config
│   └── supervisord.conf         # Process manager
├── docker-compose.yml           # Lokalni development
├── railway.json                 # Railway konfiguracija
├── .dockerignore               # Docker ignore fajlovi
└── .env.production.example     # Template za production env
```

## 🔧 Troubleshooting

### Build Errors
- Provjerite da su svi environment varijabli postavljeni
- Provjerite da su `composer.json` i `package.json` validni

### Runtime Errors
- Provjerite Laravel logs: `php artisan tinker` → `Log::info('test')`
- Provjerite da baza postoji i da su migracije pokrenute

### Performance
- Koristite Redis za cache i sessions
- Optimizirajte assets: `npm run build`

## 📞 Support

Za pomoć kontaktirajte development tim.

## 🐛 Poznati problemi i rješenja

### Composer install greška
Ako se javlja greška prilikom `composer install` sa porukom "Could not open input file: artisan", to znači da se Laravel skripti pokušavaju pokrenuti prije nego što je aplikacioni kod kopiran. Dockerfile koristi `--no-scripts` flag da spriječi ovo.

### PHP ekstenzije u Alpine Linux-u
Dockerfile koristi Alpine Linux pakete:
- `oniguruma-dev` (ne `libonig-dev`)
- `libzip-dev` za zip ekstenziju
- `freetype-dev` i `libjpeg-turbo-dev` za GD ekstenziju

### Environment varijable
Railway automatski dodaje neke varijable:
- `DATABASE_URL` - PostgreSQL connection string (automatski parsiran)
- `REDIS_URL` - Redis connection string  
- `PORT` - Port na kojem aplikacija treba da sluša

Provjerite Railway dashboard za tačne nazive i vrijednosti.