# Instrukcije za upload izmena na produkcijski server

## Fajlovi koji trebaju biti uploadovani:

1. `app/Http/Controllers/CompetitionController.php`
2. `app/Services/TournamentGroupService.php`
3. `resources/views/organizations/competitions/matches/edit.blade.php`

## Koraci:

1. **Upload fajlova preko FTP/cPanel File Manager**
   - Uploaduj sve tri fajla na odgovarajuće lokacije

2. **Pokreni komande na serveru (preko SSH ili Terminal u cPanel)**
   ```bash
   cd ~/public_html  # ili gde god je root aplikacije
   php artisan view:clear
   php artisan cache:clear
   php artisan config:clear
   ```

3. **Alternativa ako nemaš SSH pristup:**
   - Otvori `public/emergency_clear.php` u browseru
   - URL: https://teamsphere.infinitycreative.agency/emergency_clear.php

## Testiranje:

Nakon upload-a, pokušaj:
1. Uneti rezultat meča
2. Proveriti da li se tabela u grupi ažurira
3. Pogledati `storage/logs/laravel.log` na serveru da vidiš da li ima grešaka

## Šta je popravljeno:

✅ `json_decode()` greška na `sets` polju - podržava oba formata (array i string)
✅ Validacija setova u `updateMatch` i `quickResult` - prihvata `home_score/away_score`
✅ Normalizacija setova - konvertuje u `home/away` format pre čuvanja
✅ Izračunavanje `points_won` i `points_lost` iz setova - podržava oba formata
✅ Route parameter fix za `quickResult` - koristi route model binding
✅ Logging dodат da se prati izvršavanje

## Ako i dalje ne radi:

Proveri log fajl na serveru (`storage/logs/laravel.log`) i pošalji mi poslednje greške.
