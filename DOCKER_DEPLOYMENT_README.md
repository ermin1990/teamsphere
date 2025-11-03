# TeamSphere - Railway Docker Deployment

Ovaj projekat je konfigurisan za deployment na Railway platformi koristeći Docker.

## 🚀 Deployment na Railway

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

# Database (Railway će automatski dodati ove)
DATABASE_URL=mysql://user:password@host:port/database

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

1. Railway će automatski kreirati MySQL bazu
2. Pokrenite migracije nakon deployment-a:
   ```bash
   php artisan migrate
   ```

3. Seed-ujte bazu ako je potrebno:
   ```bash
   php artisan db:seed
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