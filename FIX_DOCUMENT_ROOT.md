# 🚨 PRAVI PROBLEM - HOSTING NE DOZVOLJAVA PRISTUP IZVAN DOCUMENT ROOT

## ❌ ŠTA NE RADI:

Shared hosting **NE dozvoljava** Laravel-u da pristupi folderima izvan document root-a:

```
/home2/infinit4/
├── teamsphere.infinitycreative.agency/  <- Document root
└── teamsphere_app/                      <- NEDOSTUPAN! ❌
```

Zato dobijaš server putanje umjesto web URL-ova!

---

## ✅ RJEŠENJE: SVE U JEDAN FOLDER

### Nova struktura (Shared hosting friendly):

```
/home2/infinit4/teamsphere.infinitycreative.agency/
├── public/           <- Ovo postaje "web root"
│   ├── index.php
│   ├── .htaccess
│   └── robots.txt
│
├── app/              <- Laravel aplikacija
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── .htaccess         <- Ovaj redirektuje na public/
├── artisan
└── composer.json
```

---

## 🔧 KAKO TO POSTAVITI:

### OPCIJA 1: Podesi subdomain da pokazuje na public folder (NAJBOLJE)

**U cPanel:**
1. Idi na **Domains** ili **Subdomains**
2. Nađi `teamsphere.infinitycreative.agency`
3. Klikni "Manage" ili "Edit"
4. Promijeni **Document Root** sa:
   - IZ: `/home2/infinit4/teamsphere.infinitycreative.agency`
   - U: `/home2/infinit4/teamsphere.infinitycreative.agency/public`
5. Save

**Onda index.php treba biti ORIGINALNI:**
```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
```

---

### OPCIJA 2: .htaccess redirekcija (Ako ne možeš promjeniti Document Root)

**Root `.htaccess`** (`/home2/infinit4/teamsphere.infinitycreative.agency/.htaccess`):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**Public `.htaccess`** (`/home2/infinit4/teamsphere.infinitycreative.agency/public/.htaccess`):

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 📦 KAKO RE-UPLOAD-OVATI:

### 1. Očisti staru strukturu na serveru
- Obriši `teamsphere_app/` ako postoji
- Obriši sve iz `teamsphere.infinitycreative.agency/`

### 2. Upload CIJELI projekat u jedan folder

**Upload u `/home2/infinit4/teamsphere.infinitycreative.agency/`:**

```
✅ app/
✅ bootstrap/
✅ config/
✅ database/
✅ public/         <- Ovo je bitno!
✅ resources/
✅ routes/
✅ storage/
✅ vendor/
✅ .env (upload .env.production kao .env)
✅ .htaccess (root)
✅ artisan
✅ composer.json
```

### 3. Podesi Document Root (cPanel Domains)

Promijeni da pokazuje na:
```
/home2/infinit4/teamsphere.infinitycreative.agency/public
```

### 4. GOTOVO!

Otvori: `https://teamsphere.infinitycreative.agency`

---

## 🎯 ZAŠTO OVO RADI:

1. **Cijela aplikacija je u document root-u** - nema pristup problema
2. **Document root pokazuje na `public/`** - samo public fajlovi su izloženi
3. **index.php koristi standardne putanje** - `__DIR__.'/../vendor/...'`
4. **Laravel može pristupiti svim folderima** - sve je u istom root-u

---

## 🔐 SECURITY:

Ovo je **SIGURNO** jer:
- Browser pristupa samo `public/` folderu
- `.env`, `vendor/`, `app/` su **izvan web root-a** (public/)
- cPanel pokazuje direktno na `public/` folder

---

## 📋 BRZI KORACI:

1. **cPanel → Domains → Manage teamsphere.infinitycreative.agency**
2. **Document Root:** `/home2/infinit4/teamsphere.infinitycreative.agency/public`
3. **Save**
4. **Upload cijeli projekat** u `/home2/infinit4/teamsphere.infinitycreative.agency/`
5. **Permissions:** storage/ → 755, database.sqlite → 664
6. **Done!** ✅

---

## 💡 ALTERNATIVA (Ako cPanel ne dozvoljava promjenu Document Root):

Koristi **symlink** ili složenu `.htaccess` redirekciju - ali to je komplikovanije.

Najbolje: **Promijeni Document Root na `public/`**!
