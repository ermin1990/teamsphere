# League Seeder Setup - 2026/02 Sezona

## Što trebam prilagoditi prije pokretanja?

1. **sport_id** - Provjerite koji ID je tenis/vaš sport:
   ```bash
   php artisan tinker
   >>> \App\Models\Sport::all();
   ```
   Zamijenim u seederu red 30 i 61

2. **user_id** - Vlasnik organizacije (trenutno je 1):
   ```bash
   php artisan tinker
   >>> \App\Models\User::all();
   ```
   Zamijenim u seederu red 31

3. **Organization name** - Trebate li novu org ili koristiti postojeću?
   - Ako postoji: prilagodim query na `['slug' => 'vas-slug']`

## Pokretanje lokalno

```bash
# Prije nego što pokrenete, backup baze (opciono)
php artisan migrate:refresh --seed  # Ako trebate čist početak

# Ili samo seeder:
php artisan db:seed --class=LeagueSeason202602Seeder
```

## Što će seeder uraditi?

✓ Kreirati organizaciju (ili pronaći postojeću)
✓ Kreirati sezonu 2026/02
✓ Kreirati ligu "1. Liga - Saša Vilušić"
✓ Kreirati 8 igrača
✓ Kreirati 28 mečeva sa rezultatima
⚠️ **Provjeriti dupla imena** - javit će ako neko ime već postoji u bazi

## Dupliranje imena - Šta će ispisati?

Ako postoji igra s istim imenom, seeder će ispisati:
```
⚠️  DUPLIRANA IMENA:
  - Emir Poljo (ID: 15) - DUPLIRANJE - KORIŠTEN POSTOJEĆI IGRAČ
```

Trebat će vam da potvrdite: **Da li je to isti igrač ili drugačija osoba?**

## Test prvi put

```bash
php artisan db:seed --class=LeagueSeason202602Seeder
```

Provjerite output - trebalo bi da vidi sve 8 igrača i 28 mečeva.

## Što se nalazi gdje?

- Seeder: `database/seeders/LeagueSeason202602Seeder.php`
- Liga će biti dostupna na API: `GET /api/v1/leagues/{slug}`
- Mečevi: `GET /api/v1/leagues/{slug}/matches`
