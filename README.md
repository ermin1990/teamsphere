# TeamSphere - Tournament Management System

TeamSphere je sveobuhvatni sistem za upravljanje turnirima i takmičenjima, izgrađen sa Laravel framework-om.

## 🚀 Brzi početak

### Zahtjevi sistema

- PHP 8.2 ili noviji
- Composer
- Node.js & NPM
- MySQL 8.0+ (preporučeno) ili SQLite

### Instalacija

1. **Klonirajte repozitorij:**
   ```bash
   git clone https://github.com/ermin1990/teamsphere.git
   cd teamsphere
   ```

2. **Instalirajte PHP dependencies:**
   ```bash
   composer install
   ```

3. **Instalirajte Node.js dependencies:**
   ```bash
   npm install
   ```

4. **Konfiguracija okruženja:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Konfiguracija baze podataka:**
   - **Za MySQL (preporučeno za produkciju):**
     ```bash
     # U .env fajlu postavite:
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=teamsphere
     DB_USERNAME=your_username
     DB_PASSWORD=your_password
     ```

   - **Za SQLite (za razvoj):**
     ```bash
     # U .env fajlu postavite:
     DB_CONNECTION=sqlite
     # Ostavite ostale DB_ varijable zakomentarisane
     ```

6. **Pokrenite migracije:**
   ```bash
   php artisan migrate
   ```

7. **Pokrenite seedere (opciono):**
   ```bash
   php artisan db:seed
   ```

8. **Build frontend assets:**
   ```bash
   npm run build
   # Ili za development:
   npm run dev
   ```

9. **Pokrenite aplikaciju:**
   ```bash
   php artisan serve
   ```

Aplikacija će biti dostupna na `http://localhost:8000`

## 🗄️ Migracija sa SQLite na MySQL

Ako želite preći sa SQLite na MySQL za bolje performanse u produkciji:

### Korak 1: Backup postojećih podataka
```bash
# Ako koristite SQLite, napravite backup
cp database/database.sqlite database/backup.sqlite
```

### Korak 2: Ažurirajte .env fajl
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=teamsphere
DB_USERNAME=your_mysql_username
DB_PASSWORD=your_mysql_password
```

### Korak 3: Kreirajte MySQL bazu
```sql
CREATE DATABASE teamsphere CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Korak 4: Pokrenite migracije
```bash
php artisan migrate
```

### Korak 5: Migrirajte podatke (ako postoje)
Ako imate postojeće podatke u SQLite bazi, možete koristiti Laravel-ove alate za migraciju podataka ili third-party pakete kao što je `laravel-migration-snapshot`.

## 🏗️ Arhitektura

- **Framework:** Laravel 11
- **Frontend:** Livewire + Alpine.js + Tailwind CSS
- **Baza:** MySQL / SQLite
- **Cache:** Redis (opciono)
- **Queue:** Database

## 📁 Struktura projekta

```
teamsphere/
├── app/                    # Laravel aplikacija
│   ├── Http/Controllers/   # Kontroleri
│   ├── Livewire/          # Livewire komponente
│   ├── Models/            # Eloquent modeli
│   └── Services/          # Business logika
├── database/              # Migracije i seeders
├── public/                # Statički fajlovi
├── resources/             # Views i assets
│   ├── css/
│   ├── js/
│   └── views/
├── routes/                # Rute
└── tests/                 # Testovi
```

## 🧪 Testiranje

```bash
# Pokrenite testove
php artisan test

# Sa coverage
php artisan test --coverage
```

## 🚀 Deployment

### Za produkciju sa MySQL:

1. Konfigurirajte `.env` fajl sa produkcionim postavkama
2. Pokrenite migracije: `php artisan migrate --force`
3. Build assets: `npm run build`
4. Konfigurirajte web server (nginx/apache) za Laravel
5. Postavite cron job za scheduler: `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1`

### Nginx konfiguracija primjer:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/teamsphere/public;

    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## 🤝 Doprinos

1. Forkujte projekat
2. Kreirajte feature branch (`git checkout -b feature/AmazingFeature`)
3. Commitujte promjene (`git commit -m 'Add some AmazingFeature'`)
4. Pushujte na branch (`git push origin feature/AmazingFeature`)
5. Otvorite Pull Request

## 📝 License

Ovaj projekat je licenciran pod MIT licencom - pogledajte [LICENSE](LICENSE) fajl za detalje.

## 📧 Kontakt

Za pitanja ili podršku, kontaktirajte developera.
