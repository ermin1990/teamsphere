# ⚡ NAJBRŽE RJEŠENJE - PROMIJENI DOCUMENT ROOT

## 🎯 PROBLEM:
Hosting ne dozvoljava pristup folderima izvan document root-a.

## ✅ JEDNOSTAVNO RJEŠENJE:

### KORAK 1: Očisti staru strukturu (cPanel File Manager)

**Obriši:**
- `/home2/infinit4/teamsphere_app/` (ako postoji)
- Sve iz `/home2/infinit4/teamsphere.infinitycreative.agency/`

### KORAK 2: Upload cijeli projekat

**Upload u:** `/home2/infinit4/teamsphere.infinitycreative.agency/`

**Struktura nakon uploada:**
```
teamsphere.infinitycreative.agency/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← KLJUČNO!
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
└── artisan
```

### KORAK 3: Promijeni Document Root u cPanel

**U cPanel:**

1. **Nađi "Domains"** (može biti "Addon Domains" ili "Subdomains")
2. **Klikni na "Manage"** pored `teamsphere.infinitycreative.agency`
3. **Document Root** promijeni u:
   ```
   /home2/infinit4/teamsphere.infinitycreative.agency/public
   ```
4. **Save Changes**

### KORAK 4: Permissions

- `storage/` → 755
- `bootstrap/cache/` → 755
- `database/database.sqlite` → 664

### KORAK 5: Test

Otvori: `https://teamsphere.infinitycreative.agency`

---

## 📸 Screenshot pomoć za cPanel:

**Traži ovaj dio:**
```
Domain: teamsphere.infinitycreative.agency
Document Root: [________] <- Ovdje upišeš novu putanju
```

**Promijeni iz:**
```
/home2/infinit4/teamsphere.infinitycreative.agency
```

**U:**
```
/home2/infinit4/teamsphere.infinitycreative.agency/public
```

---

## ✅ NAKON PROMJENE:

- Browser pristupa: `https://teamsphere.infinitycreative.agency/`
- Server pokazuje na: `/home2/infinit4/teamsphere.infinitycreative.agency/public/`
- `index.php` u `public/` koristi: `__DIR__.'/../vendor/...'`
- Laravel pristupa: `/home2/infinit4/teamsphere.infinitycreative.agency/vendor/` ✅

**SVE RADI!** 🎉

---

## 🚨 AKO NE MOŽEŠ PROMJENITI DOCUMENT ROOT:

Kontaktiraj hosting support i pitaj:
> "Can you change the document root for teamsphere.infinitycreative.agency to point to the /public subfolder?"

Većina hosting-a to može uraditi!
