# 🎬 Projektor Funkcionalnost - TeamSphere

## Pregled

Projektor je moćna funkcionalnost koja omogućava kreiranje personalizovanih prikaza za rotaciju kroz više liga i turnira. Idealan je za TV ekrane, projektore u sportskim objektima, ili javne digitalne displeje.

## Karakteristike

### ✨ Glavne Mogućnosti

- **URL-Based Konfiguracija** - Sve postavke se čuvaju u URL-u, bez potrebe za bazom podataka
- **Automatska Rotacija** - Glatke tranzicije između liga sa podešivim trajanjem
- **Live Prioritet** - Automatsko produženje vremena za lige sa aktivnim mečevima
- **Fleksibilni Prikazi** - Prikazuj samo tabele, samo mečeve, ili kombinaciju
- **Kontrole iz Tastature** - Navigacija strelicama i pauza/play funkcionalnost
- **Auto-Refresh** - Automatsko osvježavanje podataka svakih 30 sekundi
- **Full Screen Mode** - Optimizovan za velike ekrane

### 🎨 Opcije Prikaza

1. **Mode (Šta prikazati)**
   - `both` - Tabele i mečevi (default)
   - `standings` - Samo tabele
   - `matches` - Samo uživo i naredni mečevi

2. **Layout**
   - `single` - Jedna liga u punom prikazu
   - `split` - Podijeljeni prikaz (tabela + mečevi)

3. **Trajanje Prikaza**
   - Individualno podešavanje za svaku ligu (5-300 sekundi)
   - Default trajanje za sve lige (podrazumijevano 20s)

4. **Dodatne Opcije**
   - Live Priority - Automatski produži vrijeme za uživo mečeve (min 60s)
   - Transition Speed - Brzina animacije prelaza (100-2000ms)

## Korištenje

### 1. Pristup Builder-u

Posjetite: `/projector/builder`

Na ovoj stranici možete:
- Odabrati javne lige/turnire iz liste
- Podesiti trajanje prikaza za svaku ligu
- Konfigurirati opcije prikaza
- Generisati URL za projektor

### 2. Generisanje URL-a

Nakon odabira liga i postavki, kliknite **"Generiši projektor URL"** za kreiranje linka. URL će izgledati ovako:

```
https://your-domain.com/projector/display?ids=1,5,12&durations=20,30,15&mode=both&layout=single&default_duration=20&transition=500&live_priority=1
```

### 3. Korištenje Projektora

#### URL Parametri

- `ids` - Lista ID-ova liga/turnira (odvojeno zarezima)
- `durations` - Lista trajanja za svaku ligu u sekundama (odvojeno zarezima)
- `mode` - Mod prikaza (`standings`, `matches`, `both`)
- `layout` - Layout tip (`single`, `split`)
- `default_duration` - Podrazumijevano trajanje (default: 20)
- `transition` - Brzina tranzicije u ms (default: 500)
- `live_priority` - Prioritet uživo mečeva (`1` ili `0`)

#### Primjeri URL-a

**Prikaz 3 lige sa različitim trajanjem:**
```
/projector/display?ids=1,2,3&durations=30,20,25&mode=both
```

**Samo tabele, bez live prioriteta:**
```
/projector/display?ids=5,8&durations=15,15&mode=standings&live_priority=0
```

**Split layout sa brzom tranzicijom:**
```
/projector/display?ids=1,2&durations=45,45&mode=both&layout=split&transition=300
```

### 4. Kontrole Tastature

Kada je projektor otvoren, možete koristiti:

- `→` ili `n` - Sljedeća liga
- `←` ili `p` - Prethodna liga
- `Space` - Pauza/Play rotacije
- `f` - Full screen mode (F11 za izlaz)

## Tehnički Detalji

### Struktura Fajlova

```
app/Http/Controllers/
  └── ProjectorController.php       # Kontroler sa logikom

resources/views/projector/
  ├── builder.blade.php              # Builder interface
  ├── display.blade.php              # Glavni display view
  └── partials/
      ├── competition-view.blade.php      # Wrapper komponenta
      ├── league-standings.blade.php      # Liga tabela
      ├── tournament-standings.blade.php  # Turnir grupne tabele
      └── live-matches.blade.php          # Uživo i naredni mečevi

routes/web.php                       # Projektor route-ovi
```

### Route-ovi

```php
Route::prefix('projector')->name('projector.')->group(function () {
    Route::get('/builder', [ProjectorController::class, 'builder']);
    Route::get('/display', [ProjectorController::class, 'display']);
    Route::get('/competition/{competition}', [ProjectorController::class, 'getCompetitionView']);
});
```

### JavaScript Rotacija

Display stranica koristi JavaScript za:
- Automatsku rotaciju kroz lige
- Progress bar animaciju
- Live refresh podataka (svakih 30s)
- Kontrole iz tastature
- Glatke fade tranzicije

```javascript
// Osnovna struktura rotacije
let currentIndex = 0;
const rotationData = [...]; // Competition data

function showCompetition(index) {
    // Hide all views
    // Show current view
    // Start progress bar
    // Update timer
}
```

## Best Practices

### Za Optimalan Prikaz

1. **Trajanje Prikaza**
   - 15-30 sekundi za samo tabele
   - 30-60 sekundi za kompletan prikaz (tabele + mečevi)
   - 60+ sekundi za turnire sa grupama

2. **Broj Liga**
   - Optimalno: 3-5 liga za glatku rotaciju
   - Maksimum: 10+ liga (ali rotacija postaje spora)

3. **Layout**
   - `single` za detaljniji prikaz pojedinačnih liga
   - `split` za brži pregled tabele i mečeva istovremeno

4. **Live Priority**
   - Uključite za sportske objekte sa aktivnim mečevima
   - Isključite za statične prikaze ili arhivske lige

### Za TV/Projektor Setup

1. Otvorite projektor URL u browseru
2. Pritisnite `F` ili `F11` za full screen
3. Postavite browser da automatski pokreće URL pri startu
4. Isključite browser kontrole i mouse cursor

### Refresh Podataka

- Display automatski osvježava podatke svakih 30 sekundi
- Za ručni refresh koristite `Ctrl+R` ili zatvorite/otvorite browser
- URL ostaje isti, podaci se učitavaju fresh svaki put

## Dijeljenje Projektora

Jednostavno podijelite generisani URL sa:
- Članovima organizacije
- Drugim objektima
- JavnimDisplayima

URL je public i ne zahtijeva autentifikaciju - svako sa linkom može pristupiti projektoru.

## Troubleshooting

### Projektor ne prikazuje lige

**Problem:** "Nijedna od odabranih liga nije dostupna"

**Rješenje:**
- Provjerite da su lige postavljene kao javne (`is_public = true`)
- Provjerite da ID-ovi u URL-u odgovaraju postojećim ligama
- Osvježite stranicu

### Tranzicije su spore

**Problem:** Animacije se čine usporenim

**Rješenje:**
- Smanjite `transition` parametar (npr. 300 umjesto 500)
- Provjerite internet konekciju
- Zatvorite druge aplikacije koje koriste CPU

### Live mečevi se ne prikazuju

**Problem:** Uživo mečevi nisu vidljivi

**Rješenje:**
- Provjerite da je `mode` postavljen na `matches` ili `both`
- Provjerite da postoje aktivni mečevi (`status = 'in_progress'`)
- Pričekajte 30s za auto-refresh

### Rotacija se zaustavila

**Problem:** Projektor stoji na jednoj ligi

**Rješenje:**
- Pritisnite `Space` da uključite rotaciju (možda je pauzirana)
- Osvježite stranicu (`F5`)
- Provjerite JavaScript console za greške (`F12`)

## Buduća Poboljšanja (Opciono)

Moguća proširenja:
- QR kod za brz pristup projektoru
- Mobilna aplikacija za kontrolu projektora
- Statistike gledanosti
- Custom branding/logoi po projektoru
- Video reklame između liga
- Integracija sa socijalnim mrežama

---

**Verzija:** 1.0  
**Datum:** 21.01.2026  
**Autor:** TeamSphere Development Team
