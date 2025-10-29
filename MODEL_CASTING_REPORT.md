# Model Casting Analysis Report

Generated: 2025-10-29 23:05:24

## Summary

- Total models: 17
- Models with issues: 6
- Models OK: 11

## Models with Missing ID Casts

### BugReport

**Missing casts for:**
- `user_id`

**Suggested fix:**
```php
protected $casts = [
    'user_id' => 'integer',  // ADD THIS
    'resolved_at' => 'datetime',
];
```

### FriendlyMatch

**Missing casts for:**
- `organization_id`
- `home_player_id`
- `away_player_id`

**Suggested fix:**
```php
protected $casts = [
    'organization_id' => 'integer',  // ADD THIS
    'home_player_id' => 'integer',  // ADD THIS
    'away_player_id' => 'integer',  // ADD THIS
    'sets' => 'array',
    'set_durations' => 'array',
    'completed_at' => 'datetime',
];
```

### League

**Missing casts for:**
- `organization_id`
- `sport_id`

**Suggested fix:**
```php
protected $casts = [
    'organization_id' => 'integer',  // ADD THIS
    'sport_id' => 'integer',  // ADD THIS
    'start_date' => 'date',
    'end_date' => 'date',
    'max_teams' => 'integer',
    'is_team_based' => 'boolean',
    'settings' => 'array',
    'is_active' => 'boolean',
    'is_public' => 'boolean',
];
```

### OrganizationUser

**Missing casts for:**
- `organization_id`
- `user_id`

**Suggested fix:**
```php
protected $casts = [
    'organization_id' => 'integer',  // ADD THIS
    'user_id' => 'integer',  // ADD THIS
    'joined_at' => 'datetime',
];
```

### Table

**Missing casts for:**
- `organization_id`

**Suggested fix:**
```php
protected $casts = [
    'organization_id' => 'integer',  // ADD THIS
    'is_active' => 'boolean',
];
```

### UserPlan

**Missing casts for:**
- `user_id`
- `plan_id`

**Suggested fix:**
```php
protected $casts = [
    'user_id' => 'integer',  // ADD THIS
    'plan_id' => 'integer',  // ADD THIS
    'started_at' => 'datetime',
    'expires_at' => 'datetime',
    'is_active' => 'boolean',
];
```

