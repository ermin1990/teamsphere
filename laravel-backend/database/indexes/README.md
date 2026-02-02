# Database Indexes - TeamSphere

## 📁 SQL Skripte za indeksiranje baze podataka

Ovaj folder sadrži SQL skripte za optimizaciju performansi MySQL baze podataka kroz dodavanje indeksa.

---

## 📋 Fajlovi

### `01_add_indexes.sql` - **Sigurno dodavanje indeksa**
- ✅ **SIGURNA** skripta - možeš pokrenuti više puta
- Dodaje indekse **SAMO ako ne postoje**
- Koristi `INFORMATION_SCHEMA` za provjeru postojećih indeksa
- **Preporučeno za produkciju**

**Pokretanje:**
```bash
mysql -u username -p database_name < database/indexes/01_add_indexes.sql
```

---

### `02_rebuild_indexes.sql` - **Obnavljanje indeksa**
- ⚠️ Briše postojeće i kreira nove indekse
- Koristi **sigurne conditional DROP** operacije (kompatibilno sa starijim MySQL verzijama)
- Koristi se kada sumneš da su indeksi oštećeni
- Preporučeno pokretanje van radnog vremena (za velike baze)

**Pokretanje:**
```bash
mysql -u username -p database_name < database/indexes/02_rebuild_indexes.sql
```

**Kada koristiti:**
- Nakon velikih migracija podataka
- Sumnja na oštećene indekse
- Nakon optimizacije/defragmentacije tabela

---

### `03_rollback_indexes.sql` - **Vraćanje na početno stanje**
- 🔄 Briše **SVE custom indekse** koje smo dodali
- Ostavlja samo PRIMARY KEY i FOREIGN KEY indekse
- **SIGURNO** - neće obrisati sistemske indekse

**Pokretanje:**
```bash
mysql -u username -p database_name < database/indexes/03_rollback_indexes.sql
```

**Kada koristiti:**
- Indeksi uzrokuju probleme
- Želiš testirati performanse BEZ custom indeksa
- Nešto nije u redu i želiš resetovati na početak

---

## 🎯 Koje indekse dodajemo?

### **Matches (najvažnija tabela)**
- `league_id` + `scheduled_at` - listing mečeva
- `status` - filtriranje po statusu (live/completed)
- `league_id` + `status` - najčešća kombinacija
- `home_player_id`, `away_player_id` - player mečevi
- `home_team_id`, `away_team_id` - team mečevi
- `round` - kola u ligi
- `created_at` - sortiranje

### **Standings**
- `competition_id` - standings po natjecanju
- `team_id` + `player_id` - kombinovani lookup
- `position` - sortiranje

### **Players**
- `organization_id` - svi igrači organizacije
- `user_id` - player profile korisnika
- `name` (255 chars) - pretraga po imenu (TEXT kolona sa ograničenjem)
- `email` - lookup po emailu
- `created_at` - novi igrači

### **Teams**
- `competition_id` - timovi u natjecanju
- `captain_id` - kapiten tima

### **Pivot Tables**
- `team_user`: `team_id`, `user_id`
- `competition_user`: `competition_id`, `user_id`
- `competition_player`: `competition_id`, `player_id`

### **Competitions** (prije Leagues)
- `organization_id` - natjecanja organizacije
- `sport_id` - filter po sportu
- `slug` - public URL lookup
- `status` - aktivna/završena natjecanja

### **Organizations**
- `user_id` - organizacije korisnika
- `slug` - public lookup

### **Users**
- `email` (255 chars) - login lookup (TEXT kolona sa ograničenjem)
- `created_at` - novi korisnici

---

## 📊 Očekivane performanse

| Prije indeksa | Nakon indeksa | Poboljšanje |
|---------------|---------------|-------------|
| ~500ms | ~50ms | **10x brže** |
| Full table scan | Index seek | Efikasnije |
| Više RAM-a | Manje RAM-a | Optimizirano |

---

## ⚠️ Važne napomene

1. **Backup prije izvršavanja:**
   ```bash
   mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
   ```

2. **Testiranje na development bazi:**
   - Prvo pokreni na test bazi
   - Provjeri da li sve radi
   - Tek onda pokreni na produkciji

3. **Praćenje performansi:**
   - MySQL Slow Query Log
   - `EXPLAIN` komanda za analizu upita
   - Monitoring alati (PHPMyAdmin, Adminer)

4. **Održavanje indeksa:**
   - Redovno pokrećи `ANALYZE TABLE`
   - Periodično `OPTIMIZE TABLE` (mjesečno)

---

## 🔍 Provjera indeksa

Nakon pokretanja, možeš provjeriti indekse:

```sql
-- Svi indeksi na 'matches' tabeli
SHOW INDEX FROM matches;

-- Indeksi u cijeloj bazi
SELECT 
    TABLE_NAME, 
    INDEX_NAME, 
    COLUMN_NAME, 
    SEQ_IN_INDEX
FROM information_schema.statistics
WHERE table_schema = 'database_name'
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;
```

---

## 🚀 Quick Start

**Za prvo pokretanje (preporučeno):**
```bash
# 1. Backup
mysqldump -u root -p teamsphere > backup.sql

# 2. Dodaj indekse
mysql -u root -p teamsphere < database/indexes/01_add_indexes.sql

# 3. Testuj performanse
# (koristi svoju aplikaciju i prati brzinu)

# 4. Ako nešto nije u redu - rollback
mysql -u root -p teamsphere < database/indexes/03_rollback_indexes.sql
```

---

## 💡 Tips & Tricks

- **Veliki broj reda?** Pokreni noću/vikendom
- **Slow query log:** Analiziraj prije indeksiranja
- **Više memorije?** Indeksi koriste RAM - to je OK!
- **Dupli indeksi?** Skripta ih neće kreirati

---

## 📞 Support

Ako imaš problema:
1. Provjeri MySQL error log
2. Pokreni `SHOW WARNINGS;` nakon greške
3. Rollback skriptom vrati na staro stanje
4. Kontaktiraj developera

---

**Kreirao:** TeamSphere Development Team  
**Verzija:** 1.0  
**Datum:** 03.11.2025
