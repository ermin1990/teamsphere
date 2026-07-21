# Kako dati podatke o ligi AI-u (template)

Kopiraj ovaj prompt i popuni [zagrade], zajedno sa svojom tabelom i rasporedom (u istom formatu kao dosad — tablica sa "Odig/Pob/Set/Bod" i lista "Domaćin - Gost / Rezultat").

---

## PROMPT ZA AI

Radim na Laravel 12 aplikaciji za organizaciju sportskih takmičenja (tenis lige). Treba mi Laravel Seeder koji unosi jednu ligu u bazu, tačno po sljedećim pravilima koja već koristim za sve ostale lige u ovom projektu:

**Kontekst projekta:**
- Model `League` (tabela `competitions`, `type='league'`), `Player`, `LeagueMatch` (tabela `matches` / model), `Season`, `Organization`.
- Igrači se organizuju kroz `organization_id`.
- Mečevi imaju: `home_player_id`, `away_player_id`, `home_score`, `away_score` (broj OSVOJENIH SETOVA, ne gemova), `sets` (JSON niz `[{"home": X, "away": Y}, ...]` — **OBAVEZNO ovaj format s ključevima `home`/`away`, NE numerički niz `[X,Y]`**), `status` (`completed` ili `scheduled`), `forfeited_by` (`home`/`away`/`null` — ko je predao meč ako je WO), `round` (broj kola), `competition_id`.
- Tabela (`Standing` model) se NE popunjava ručno — gradi se automatski pozivom `app(\App\Services\LeagueStandingsService::class)->rebuildForCompetition($competition)` nakon što se svi mečevi kreiraju.
- **VAŽNO pravilo za bodove:** `points_for_win = 3`, `points_for_loss = 1` (svaki ODIGRANI poraz nosi 1 bod, ne 0). Postavi ovo na competition prije rebuildForCompetition.
- **VAŽNO pravilo za WO (predani) mečeve:** ako je meč predat (protivnik se nije pojavio/diskvalifikovan), pobjednik dobija odigran meč + pobjedu; GUBITNIK NE dobija ni odigran meč ni poraz (kao da meč nije ni postojao za njega). Ovo je već ugrađeno u `LeagueStandingsService` — samo treba postaviti `forfeited_by` ispravno na meču, servis sam obračunava.
- Igrače treba dodati i kao UČESNIKE takmičenja preko pivot tabele `competition_player` (relacija `$league->players()->syncWithoutDetaching([...])` sa `joined_at`), ne samo kreirati Player zapise.
- Raspored generiši round-robin metodom (circle method) da se mečevi organizuju po kolima (`round` 1..N-1 za N igrača), na osnovu NEUREĐENOG PARA igrača (ne redoslijeda unosa), tako da svaki igrač igra tačno jednom po kolu.

**Podaci koje ti dajem:**

Organizacija: [npr. "Tuzlanska liga" — ili reci "ista organizacija kao ranije"]
Sezona: [npr. 2026/02]
Naziv lige: [npr. "4A. Liga"]

Tabela (za provjeru — tvoj rezultat MORA se tačno poklapati sa ovim brojevima OU/Pob/Bod za svakog igrača):
[zalijepi cijelu tabelu sa Tim/Odig/Pob/Set/Bod]

Raspored (Domaćin - Gost / Rezultat, prazno = nije odigrano, "WO" = predat meč):
[zalijepi cijelu listu mečeva]

**Zadatak:**
1. Kreiraj `database/seeders/League[NAZIV]Seeder.php` po uzoru na postojeće (`League2ALigaSeason202602Seeder.php` je referentni primjer u ovom projektu ako ga imaš dostupnog).
2. Ako u rasporedu postoje DVA igrača s istim skraćenim imenom (npr. dva "M. Prezime"), NE pretpostavljaj nasumično koji je koji — rekonstruiši identitet svakog reda MATEMATIČKI iz OU/Pob/Izgubljeno brojeva u tabeli (svaki igrač ima jedinstven broj odigranih/pobjeda/poraza — dodijeli redove tako da se ti brojevi tačno poklope za SVAKOG igrača). Pokaži mi tu logiku pre nego što kreiraš seeder.
3. Pokreni seeder i provjeri da izračunata tabela (played/won/lost/points po igraču) TAČNO odgovara tabeli koju sam dao — ako se ne poklapa, prvo provjeri je li greška u tvom mapiranju mečeva na kola/igrače, ne u pravilima bodovanja.
4. Prijavi mi konačan URL lige (`/takmicenja/{slug}`).

---

## Napomena
Ovaj template pretpostavlja da AI kojem ga daješ ima pristup istom repozitoriju (fajlovima projekta), jer referencira postojeće modele/servise. Ako radiš sa AI-em koji NEMA pristup kodu, dodaj mu i sadržaj `app/Services/LeagueStandingsService.php` i jedan postojeći seeder kao primjer, da vidi tačan obrazac.
