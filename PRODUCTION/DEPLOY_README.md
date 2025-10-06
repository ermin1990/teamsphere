# Team Sphere - Production Deployment

## 🚀 Brzi Deploy na Shared Hosting

### Šta je u ovom folderu:
- ✅ Svi Laravel fajlovi spremni za production
- ✅ Vendor dependencies (composer install)
- ✅ Buildani CSS/JS assets
- ✅ SQLite baza sa svim migracijama
- ✅ Cache-ani config, routes i views
- ✅ .env fajl za production

### Koraci za deploy:

1. **Upload fajlova:**
   - Uploaduj CIJELI `PRODUCTION` folder na svoj hosting
   - Folder `PRODUCTION` postane root folder tvog sajta

2. **Postavi permissions:**
   ```
   storage/           → 755 ili 775
   storage/logs/      → 775
   storage/framework/ → 775
   storage/app/       → 775
   bootstrap/cache/   → 775
   database/          → 755
   database/database.sqlite → 664 ili 666
   ```

3. **Provjeri .env fajl:**
   - Promijeni `APP_URL=https://tvoy-domain.com` na svoj domen
   - Ako želiš MySQL umjesto SQLite, promijeni DB_CONNECTION i dodaj DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

4. **Testiraj:**
   - Otvori `https://tvoy-domain.com`
   - Probaj se registrovati

### Ako nešto ne radi:

**Problem:** Bijela stranica ili 500 error
**Rješenje:** Provjeri PHP verziju (mora biti 8.2+), permissions i .env APP_KEY

**Problem:** 404 na svim stranicama osim /
**Rješenje:** Provjeri da li je .htaccess uploadovan i da li je mod_rewrite uključen

**Problem:** Ne može pisati u storage
**Rješenje:** Postavi permissions na 775 ili kontaktiraj hosting support

### Tehnički detalji:
- Laravel 12.0
- PHP 8.2+
- SQLite baza (za testiranje)
- Vite buildani assets
- Cache-ano za production

### Kontakt:
Ako imaš problema, javi se! 🎯