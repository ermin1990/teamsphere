# 🚀 TeamSphere PWA - Testiranje i Instalacija

## ✅ Što je urađeno

1. ✅ **Web App Manifest** (`/public/manifest.json`) - Konfiguracija PWA aplikacije
2. ✅ **Service Worker** (`/public/sw.js`) - Network-first strategija, minimalno keširanje
3. ✅ **PWA ikone** (`/public/icons/`) - SVG ikone za sve veličine
4. ✅ **Meta tagovi** - PWA meta tagovi u svim layoutima
5. ✅ **Beta oznaka** - "(Beta)" u naslovu i headeru
6. ✅ **Auto-update** - Automatsko ažuriranje svakih 60 sekundi

## 🧪 Kako testirati PWA na LOCALHOST

### ⚠️ VAŽNO: HTTPS ili Localhost
PWA **MORA** raditi preko:
- `https://` (produkcija)
- `http://localhost:8000` (development)
- `http://127.0.0.1:8000` (development)

**NEĆE RADITI** preko:
- `http://192.168.x.x:8000` (lokalna IP adresa)
- HTTP (produkcija)

### 🖥️ Desktop (Chrome/Edge)

1. **Pokreni Laravel server:**
   ```bash
   php artisan serve
   ```

2. **Otvori u browseru:**
   ```
   http://localhost:8000
   ```

3. **Testiraj PWA:**
   - Otvori Developer Tools (F12)
   - Idi na **Application** tab
   - Provjeri:
     - ✅ **Manifest** - trebao bi vidjeti sve detalje
     - ✅ **Service Workers** - trebao bi biti registriran
     - ✅ **Cache Storage** - trebao bi vidjeti cache

4. **Instaliraj aplikaciju:**
   - U address baru (lijevo od zvjezdice) trebao bi vidjeti **instalacijsku ikonicu** ➕
   - Klikni na nju
   - Klikni "Install"
   - Aplikacija će se otvoriti u standalone prozoru

### 📱 Mobile (Android)

1. **Pristup preko localhost-a:**
   ```bash
   # Pokreni server dostupan u mreži
   php artisan serve --host=0.0.0.0
   
   # Pronađi svoju IP adresu
   ipconfig getifaddr en0  # macOS WiFi
   # ili
   ifconfig | grep "inet "
   ```

2. **Pristup sa mobilnog:**
   - Mora biti na istoj WiFi mreži
   - Ali **neće raditi preko IP adrese!**
   - Moraš koristiti ngrok ili sličan servis za HTTPS

3. **Alternativa - ngrok (preporučeno):**
   ```bash
   # Instaliraj ngrok
   brew install ngrok
   
   # Pokreni Laravel
   php artisan serve
   
   # U drugom terminalu pokreni ngrok
   ngrok http 8000
   
   # Koristi HTTPS URL koji ngrok generiše
   # Primjer: https://abc123.ngrok.io
   ```

4. **Instalacija na Android:**
   - Otvori Chrome
   - Idi na ngrok HTTPS URL
   - Tap na menu (⋮)
   - Tap "Install app" ili "Add to Home screen"

### 🍎 Mobile (iOS/Safari)

iOS ima **različita pravila** za PWA:
- **NE podržava** `beforeinstallprompt` event
- Ručna instalacija kroz Safari

**Koraci:**
1. Koristi ngrok HTTPS URL (kao za Android)
2. Otvori u Safari browseru
3. Tap Share button (□↑)
4. Scroll dolje i tap "Add to Home Screen"
5. Tap "Add"

## 🔍 Debug PWA

### Chrome DevTools
```
F12 → Application tab → Provjeri:
├── Manifest ✓
├── Service Workers ✓
└── Cache Storage ✓
```

### Console komande za testiranje
```javascript
// Provjeri service worker status
navigator.serviceWorker.getRegistrations()
  .then(regs => console.log('Service Workers:', regs));

// Očisti cache
navigator.serviceWorker.controller.postMessage({ type: 'CLEAR_CACHE' });

// Force update
navigator.serviceWorker.getRegistrations()
  .then(regs => regs.forEach(reg => reg.update()));

// Unregister (za fresh start)
navigator.serviceWorker.getRegistrations()
  .then(regs => regs.forEach(reg => reg.unregister()));
```

## 🎯 Lighthouse Audit

1. Otvori DevTools (F12)
2. Idi na **Lighthouse** tab
3. Selektuj:
   - ✅ Progressive Web App
   - ✅ Performance
   - ✅ Best Practices
4. Klikni "Analyze page load"
5. Trebao bi dobiti **90+** za PWA

## 🚨 Troubleshooting

### Problem: Ne vidim install dugme

**Rješenja:**
1. ✅ Provjeri da li koristiš `localhost` (ne IP adresu)
2. ✅ Očisti cache (Ctrl+Shift+Delete)
3. ✅ Hard reload (Ctrl+Shift+R)
4. ✅ Provjeri konzolu za greške
5. ✅ Provjeri da li je manifest.json dostupan: `http://localhost:8000/manifest.json`
6. ✅ Provjeri da li je sw.js dostupan: `http://localhost:8000/sw.js`

### Problem: Service Worker nije registriran

**Rješenja:**
1. Očisti sve service workere:
   ```javascript
   navigator.serviceWorker.getRegistrations()
     .then(regs => regs.forEach(reg => reg.unregister()))
     .then(() => location.reload());
   ```
2. Provjeri konzolu za greške
3. Otvori Application → Service Workers i vidi status

### Problem: Promjene se ne vide

**Rješenja:**
1. Service Worker koristi **network-first** strategiju, ali možda je keširano
2. Očisti cache:
   - Application → Cache Storage → Delete
   - Ili koristi console komandu gore
3. Unregister service worker i reload

## 📋 Checklist za deploy

Pre deploya na produkciju:

- [ ] Provjeri Lighthouse audit (90+ PWA score)
- [ ] Testiraj instalaciju na:
  - [ ] Desktop Chrome
  - [ ] Desktop Edge
  - [ ] Android Chrome
  - [ ] iOS Safari
- [ ] Provjeri da sve ikone rade
- [ ] Testiraj auto-update mehanizam
- [ ] Testiraj offline mode
- [ ] Provjeri manifest.json validnost
- [ ] Provjeri service worker console logove

## 🌐 Deploy na Produkciju

Kad deployaš na produkciju (sa HTTPS):
1. PWA će **automatski** raditi
2. Service Worker će se registrirati
3. Install prompt će se pojaviti
4. Aplikacija može offline (ograničeno)

**HTTPS je obavezan za produkciju!**

## 📚 Resursi

- [Web App Manifest Generator](https://www.pwabuilder.com/)
- [PWA Asset Generator](https://www.pwabuilder.com/imageGenerator)
- [Lighthouse PWA Audit](https://developers.google.com/web/tools/lighthouse)
- [Service Worker Cookbook](https://serviceworke.rs/)

---

**Status:** ✅ Spremno za testiranje  
**Environment:** Development + Production ready  
**Last Updated:** 3. Novembar 2025
