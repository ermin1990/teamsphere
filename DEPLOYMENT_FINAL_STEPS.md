# 🚀 FINALNI DEPLOYMENT STEPS - KORAK PO KORAK

## ✅ PRIPREMLJENO LOKALNO:
- [✅] Config cache
- [✅] Routes cache  
- [✅] Views cache
- [✅] Database (.sqlite)
- [✅] .env.production spreman
- [✅] index.SERVER.php kreiran
- [✅] clear-cache.php kreiran

---

## 📦 KORAK 1: PROVJERI ŠTA UPLOAD-UJEŠ

### Upload u `teamsphere_app/` (IZVAN public_html):

```
✅ app/
✅ bootstrap/
✅ config/
✅ database/ (SA database.sqlite!)
✅ resources/
✅ routes/
✅ storage/ (SVE folderi sa .gitignore fajlovima)
✅ vendor/
✅ .env (preimenuj .env.production → .env)
✅ .htaccess
✅ artisan
✅ composer.json
✅ composer.lock
```

### Upload u `teamsphere.infinitycreative.agency/`:

```
✅ index.php (koristi index.SERVER.php!)
✅ .htaccess
✅ robots.txt
✅ clear-cache.php (za čišćenje cache-a)
```

---

## 🔧 KORAK 2: PODESI .env NA SERVERU

Otvori: `teamsphere_app/.env`

**KRITIČNE LINIJE - PROVJERI:**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://teamsphere.infinitycreative.agency
```

⚠️ **APP_URL MORA:**
- Imati `https://`
- NEMA trailing slash `/`
- Tačan domen

---

## 📝 KORAK 3: PODESI index.php

Otvori: `teamsphere.infinitycreative.agency/index.php`

**Zamijeni sa (ili upload index.SERVER.php):**

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../teamsphere_app/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../teamsphere_app/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../teamsphere_app/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

⚠️ **KLJUČNO:** Koristi `__DIR__.'/../teamsphere_app/...` (RELATIVNE putanje!)

---

## 🔐 KORAK 4: PODESI PERMISSIONS

U cPanel File Manager:

### A) storage folder:
1. Desni klik na `teamsphere_app/storage/`
2. Change Permissions → `755`
3. ✅ Check "Recurse into subdirectories"
4. Change Permissions

### B) bootstrap/cache folder:
1. Desni klik na `teamsphere_app/bootstrap/cache/`
2. Change Permissions → `755`
3. ✅ Check "Recurse into subdirectories"
4. Change Permissions

### C) database.sqlite:
1. Desni klik na `teamsphere_app/database/database.sqlite`
2. Change Permissions → `664`
3. Change Permissions

---

## 🧹 KORAK 5: OČISTI CACHE (VAŽNO!)

Pošto si cache-ovao lokalno sa lokalnim putanjama, trebaš očistiti cache na serveru!

**Metoda 1: Koristi clear-cache.php**
1. Otvori browser: `https://teamsphere.infinitycreative.agency/clear-cache.php`
2. Trebao bi vidjeti: "✅ Config cache cleared"
3. **OBRIŠI clear-cache.php odmah!**

**Metoda 2: Ručno preko cPanel**
1. Obriši: `teamsphere_app/bootstrap/cache/config.php`
2. Obriši: `teamsphere_app/bootstrap/cache/routes-v7.php`
3. Obriši sve iz: `teamsphere_app/storage/framework/views/` (osim .gitignore)

---

## 🧪 KORAK 6: TESTIRANJE

### 1. Otvori stranicu:
```
https://teamsphere.infinitycreative.agency
```

### 2. Šta očekuješ:
✅ Laravel stranica se učitava
✅ Linkovi su oblika: `https://teamsphere.infinitycreative.agency/login`
✅ CSS se učitava
✅ Možeš se ulogovati

### 3. Ako vidiš greške:

#### A) Error 500:
- Provjeri error_log u cPanel
- Provjeri permissions (storage/ mora biti 755)

#### B) Linkovi pokazuju na `/home2/infinit4/...`:
- Provjeri `.env` → `APP_URL=https://...`
- Očisti cache (korak 5)

#### C) "No encryption key":
- Provjeri da li `.env` postoji
- Provjeri `APP_KEY=base64:...`

#### D) "Database error":
- Provjeri da li `database.sqlite` postoji
- Provjeri permission: 664

---

## 📋 POST-DEPLOYMENT CHECKLIST:

- [ ] Login radi ✅
- [ ] Registracija radi ✅
- [ ] Dashboard se prikazuje ✅
- [ ] Linkovi su tačni (https://...) ✅
- [ ] CSS/JS se učitava ✅
- [ ] Možeš kreirati ligu ✅
- [ ] Možeš unijeti meč ✅
- [ ] Live score radi ✅
- [ ] Tabela se ažurira ✅

---

## 🛡️ SECURITY CHECKLIST:

- [ ] `.env` postavljen na production ✅
- [ ] `APP_DEBUG=false` ✅
- [ ] `clear-cache.php` OBRISAN sa servera ✅
- [ ] Permissions tačni (755/664) ✅

---

## 💾 BACKUP NA SERVERU (Nakon uspješnog deploymenta):

1. Download `database.sqlite` sa servera
2. Download `.env` sa servera
3. Čuvaj lokalno kao backup!

---

## 🆘 AKO NEŠTO PUKNE:

1. Provjeri error_log u cPanel
2. Pošalji mi log
3. Imam backup lokalno - možeš ponovo upload-ovati!

---

## 🎉 SUCCESS ZNACI:

Kada otvoriš `https://teamsphere.infinitycreative.agency` i vidiš:
- ✅ Laravel welcome/login stranica
- ✅ URL bar pokazuje: `https://teamsphere.infinitycreative.agency`
- ✅ Linkovi vode na: `https://teamsphere.infinitycreative.agency/dashboard`, itd.
- ✅ Možeš se ulogovati i koristiti aplikaciju

**APLIKACIJA JE LIVE!** 🚀
