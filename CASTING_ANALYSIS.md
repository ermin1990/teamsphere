# Analiza Type Casting-a u Modelima

## Problem
PHP radi **strict type comparison** (`===`) u mnogim situacijama, posebno u route model bindingu i relacionim upitima. MySQL vraća ID polja kao **string**, što uzrokuje da poređenja kao `$model->id === 1` vraćaju `false` čak i kad su vrednosti iste.

## Rešenje
Dodati `protected $casts` za **sve ID polja** u modelima da bi se automatski kastovali u `integer`.

---

## ✅ Modeli sa ISPRAVNIM casting-om

### 1. Organization.php
```php
protected $casts = [
    'is_active' => 'boolean',
    'user_id' => 'integer',  // ✅ Kastovano
];
```
**Status**: ✅ ISPRAVNO
**Napomena**: Trebalo bi dodati casting i za relacije ako se koriste

---

### 2. Competition.php
```php
protected $casts = [
    'organization_id' => 'integer',  // ✅ FIX PRIMENJEN
    'sport_id' => 'integer',         // ✅ FIX PRIMENJEN
    // ... ostali castovi
];
```
**Status**: ✅ ISPRAVNO (nedavno popravljeno)
**Problem pre fixa**: Competition nije mogla da se nađe jer `organization_id` bilo string

---

### 3. CompetitionMatch.php
```php
protected $casts = [
    'competition_id' => 'integer',      // ✅ FIX PRIMENJEN
    'home_team_id' => 'integer',        // ✅ FIX PRIMENJEN
    'away_team_id' => 'integer',        // ✅ FIX PRIMENJEN
    'home_player_id' => 'integer',      // ✅ FIX PRIMENJEN
    'away_player_id' => 'integer',      // ✅ FIX PRIMENJEN
    'tournament_group_id' => 'integer', // ✅ FIX PRIMENJEN
    'table_id' => 'integer',            // ✅ FIX PRIMENJEN
    'referee_user_id' => 'integer',     // ✅ FIX PRIMENJEN
    // ... ostali castovi
];
```
**Status**: ✅ ISPRAVNO (nedavno popravljeno)
**Problem pre fixa**: Mečevi nisu mogli da se nađu jer route binding nije mogao da poveže match sa competition

---

## ⚠️ Modeli sa NEPOTPUNIM casting-om

### 4. Player.php
```php
protected $casts = [
    'date_of_birth' => 'date',
    'is_active' => 'boolean',
    // ❌ NEDOSTAJE: user_id, organization_id
];
```
**Status**: ⚠️ TREBA POPRAVITI
**Potrebne izmene**:
```php
protected $casts = [
    'user_id' => 'integer',          // DODATI
    'organization_id' => 'integer',  // DODATI
    'date_of_birth' => 'date',
    'is_active' => 'boolean',
];
```

---

### 5. Team.php
```php
protected $casts = [
    'status' => 'string',
    // ❌ NEDOSTAJE: competition_id, captain_id
];
```
**Status**: ⚠️ TREBA POPRAVITI
**Potrebne izmene**:
```php
protected $casts = [
    'competition_id' => 'integer',  // DODATI
    'captain_id' => 'integer',      // DODATI
    'status' => 'string',
];
```

---

### 6. LeagueMatch.php
```php
protected $casts = [
    'scheduled_at' => 'datetime',
    'played_at' => 'datetime',
    'sets' => 'array',
    // ... ostali castovi
    // ❌ NEDOSTAJE: competition_id, home_team_id, away_team_id, 
    //              home_player_id, away_player_id, table_id, referee_user_id,
    //              moderator_id, edited_by, completed_by
];
```
**Status**: ⚠️ TREBA POPRAVITI (dijeli istu tabelu sa CompetitionMatch)
**Potrebne izmene**:
```php
protected $casts = [
    'competition_id' => 'integer',    // DODATI
    'home_team_id' => 'integer',      // DODATI
    'away_team_id' => 'integer',      // DODATI
    'home_player_id' => 'integer',    // DODATI
    'away_player_id' => 'integer',    // DODATI
    'table_id' => 'integer',          // DODATI
    'referee_user_id' => 'integer',   // DODATI
    'moderator_id' => 'integer',      // DODATI
    'edited_by' => 'integer',         // DODATI
    'completed_by' => 'integer',      // DODATI
    // ... existing casts
];
```

---

### 7. TournamentGroup.php
```php
protected $casts = [
    'player_ids' => 'array',
    'standings' => 'array',
    'is_completed' => 'boolean',
    'completed_at' => 'datetime',
    // ❌ NEDOSTAJE: competition_id, group_number
];
```
**Status**: ⚠️ TREBA POPRAVITI
**Potrebne izmene**:
```php
protected $casts = [
    'competition_id' => 'integer',  // DODATI
    'group_number' => 'integer',    // DODATI
    'player_ids' => 'array',
    'standings' => 'array',
    'is_completed' => 'boolean',
    'completed_at' => 'datetime',
];
```

---

## Prioritet popravki

### Visok prioritet (utiče na route binding i autorizaciju)
1. ✅ **CompetitionMatch** - POPRAVLJENO
2. ✅ **Competition** - POPRAVLJENO
3. ✅ **Organization** - POPRAVLJENO
4. ⚠️ **LeagueMatch** - TREBA POPRAVITI
5. ⚠️ **Player** - TREBA POPRAVITI

### Srednji prioritet (utiče na relacione upite)
6. ⚠️ **Team** - TREBA POPRAVITI
7. ⚠️ **TournamentGroup** - TREBA POPRAVITI

---

## Pravilo za budućnost

**UVEK** dodaj integer casting za:
- `id` polje (ako se eksplicitno koristi)
- Sve `*_id` foreign key polje
- Sve `*_number` polja (npr. `group_number`, `round_number`)
- Sve numeričke vrednosti koje se porede sa `===`

**Primer template-a**:
```php
protected $casts = [
    // Foreign keys - OBAVEZNO integer
    'user_id' => 'integer',
    'organization_id' => 'integer',
    'parent_id' => 'integer',
    
    // Boolean polja
    'is_active' => 'boolean',
    'is_public' => 'boolean',
    
    // Datetime polja
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    
    // Array/JSON polja
    'settings' => 'array',
    'metadata' => 'array',
];
```

---

## Zašto je ovo važno?

1. **Route Model Binding**: Laravel koristi strict comparison za matching modela
2. **Relacioni upiti**: `whereHas()` i `with()` koriste strict comparison
3. **Authorization**: Policy checks često porede ID-eve
4. **Debugging**: Lakše je naći bugove kad su tipovi konzistentni

---

## Kako testirati?

```php
// Debug u kontroleru ili model metodi
dd([
    'value' => $model->foreign_key_id,
    'type' => gettype($model->foreign_key_id),
    'is_integer' => is_int($model->foreign_key_id),
]);
```

Ako `type` nije `integer`, dodaj casting!
