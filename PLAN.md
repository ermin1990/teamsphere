# Plan Migracije: TeamSphere SaaS (Preact + Firebase)

Ovaj dokument opisuje plan transformacije TeamSphere Laravel aplikacije u modernu SaaS platformu koristeći Preact (Frontend) i Firebase (Backend/Hosting).

## 📂 1. Struktura Projekta

Cilj je restrukturirati repozitorijum u monorepo stil:
```
/ (root)
  ├── laravel-backend/ (Postojeći Laravel kod - referenca i backend logika)
  ├── frontend/ (Novi Preact SPA projekat)
  ├── functions/ (Firebase Cloud Functions - Node.js backend logika)
  ├── firebase.json (Firebase konfiguracija)
  └── firestore.rules (Sigurnosna pravila baze)
```

## 🧠 2. Analiza i Migracija

### A. Baza Podataka (Relaciona -> NoSQL)
Prelazak sa MySQL na Firestore (NoSQL).

**Novi Data Model (Firestore):**
*   **`users`** (Globalna kolekcija)
    *   `uid`, `email`, `role`, `organizationId`
*   **`organizations`** (Svaki dokument je klijent/klub)
    *   `name`, `subscriptionStatus`, `settings`
    *   **Sub-collection: `competitions`**
        *   **Sub-collection: `matches`**
        *   **Sub-collection: `players`**
        *   **Sub-collection: `standings`**

### B. Projector & Live Score
*   **Tehnologija:** Preact + Firestore `onSnapshot`.
*   **Prednost:** Real-time ažuriranje bez refresha, smooth animacije koristeći CSS/framer-motion.

### C. Logika Turnira
*   **Knockout Bracket:** JS funkcija za generisanje stabla na klijentu.
*   **Grupna Faza:** JS kalkulacija tabela bazirana na rezultatima mečeva.

---

## 📝 Detaljan Plan Rada

### Faza 1: Infrastruktura (U TOKU)
1.  [x] Kreiranje brancha `Teamsphere/PreactInit`.
2.  [x] Kreiranje plana rada (`PLAN.md`).
3.  [x] Reorganizacija foldera (Laravel -> laravel-backend).
4.  [x] Inicijalizacija Preact projekta (Vite).
5.  [x] Instalacija zavisnosti (Firebase, Preact Router, Lucide).
6.  [x] Postavljanje Tailwind-a preko CDN-a.
7.  [x] Firebase config setup.
8.  [x] Osnovni ruter i layout.
9.  [ ] Firebase Emulators setup.


### Faza 2: Auth & Organizacije (U TOKU)
1.  [x] Login ekran (Firebase Auth - Google/Email).
2.  [x] Registracija sa automatskim kreiranjem organizacije.
3.  [x] Super Admin dashboard (Pregled i suspendovanje organizacija).
4.  [x] Dashboard layout sa ulogama (Super Admin vs Klijent).
5.  [x] Firestore Security Rules (Inicijalni template).
6.  [ ] Org Admin dashboard (Pregled turnira).


### Faza 3: Migracija Funkcionalnosti (Table Tennis)
1.  [ ] Upravljanje igračima (CRUD).
2.  [ ] Kreiranje turnira (Wizard).
3.  [ ] Unos rezultata (Live Score interfejs).
4.  [ ] **Projector View** (Prioritet: Rotacija, Animacije, Bracket).

### Faza 4: Monetizacija & Polish
1.  [ ] Pretplate (Stripe integracija/Ograničenja).
2.  [ ] Custom domene za organizacije.
3.  [ ] Optimizacija performansi.

## Status Projekta

### Faza 1: Reorganizacija i Inicijalizacija ✅
- [x] Kreiranje brancha `Teamsphere/PreactInit`.
- [x] Kreiranje plana rada (`PLAN.md`).
- [x] Reorganizacija foldera (Laravel -> laravel-backend).
- [x] Inicijalizacija Preact projekta (Vite).
- [x] Instalacija zavisnosti (Firebase, Preact Router, Lucide).
- [x] Postavljanje Tailwind-a preko CDN-a.
- [x] Firebase config setup.
- [x] Osnovni ruter i layout.
- [x] Firebase Emulators setup.

### Faza 2: Multi-tenant SaaS Auth ✅
- [x] Preact Context za Auth (RBAC: Super Admin vs Org Admin)
- [x] Login & Register sa Email/Pass
- [x] **Google Login** sa auto-onboardingom (kreira Org automatski)
- [x] Super Admin Dashboard (pregled svih klijenata/organizacija)
- [x] Firestore security rules za izolaciju podataka (multi-tenancy)

### Faza 3: Upravljanje Podacima (CRUD) ✅ / ⏳
- [x] Upravljanje igračima (Listanje, Dodavanje, Brisanje)
- [x] Kreiranje takmičenja (Osnovni podaci: Sport, Tip)
- [ ] Detalji takmičenja i generisanje mečeva (Berger/KO sistem) - **SLJEDEĆI KORAK**
- [ ] Dodavanje igrača u specifično takmičenje

### Faza 4: Mečevi i Scoring (Real-time) ⏳
- [ ] Live scoring interfejs (Setovi, Poeni)
- [ ] Real-time sinhronizacija preko Firestore
- [ ] Projektor prikaz za javne rezultate

### Faza 5: Hosting i Finalizacija ⏳
- [ ] `npx firebase deploy`
- [ ] Čišćenje koda i optimizacija performansi
