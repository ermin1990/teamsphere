---
name: laravel-conventions
description: Laravel/PHP conventions and known pitfalls specific to this codebase (teamsphere, Laravel 12). Use for any backend work - routes, controllers, models, migrations, services.
---

# Laravel conventions for this project

Laravel 12, PHP 8.4. No routes/api.php (bootstrap/app.php has no `api:` key) — everything is a `web` route.

## Known landmines (already bit us once — don't repeat)

- **Never add a global `Route::model('name', SomeClass::class)`** in `bootstrap/app.php`. It silently overrides every route's own type-hint for that parameter name, handing controllers an empty unsaved model instead of throwing — no error, no log, just wrong data. Let per-route implicit binding (matching the controller's own type-hint) do its job.
- **`@push('styles')`/`@stack('styles')` needs the *actual* rendered layout.** `<x-app-layout>` resolves via the class `App\View\Components\AppLayout::render()` → `view('layouts.app')`, **not** `resources/views/components/app-layout.blade.php` (that file is dead/unused — class components beat same-named anonymous ones). Add stack hooks to `layouts/app.blade.php`.
- `@push`/`@endpush` vs `@section`/`@endsection` — mismatching these compiles fine until the section-stack is popped wrong at runtime (`Cannot end a section without first starting one`). Grep `@push(` pairs with `@endpush` after editing, not `@endsection`.
- Two matches models share one `matches` table: `LeagueMatch` (round-robin leagues) and `CompetitionMatch` (tournaments, also aliased for team-match individual games). Check which one a controller actually type-hints before assuming.

## Where things live

- Controllers split by audience: `PublicMatchController` (anonymous spectators, `/takmicenja`+), `PlayerLeagueController`/`PlayerMatchController`/`PlayerDashboardController` (logged-in players, `/moje-lige`), `CompetitionController`/`LeagueController`/`TeamMatchController` (organizers, under `organizations/{organization}/...`), `RefereeController` (referee flows), `Admin*Controller` (admin panel).
- `App\Services\CompetitionShowData::load($competition)` is the single eager-loading + tournament-seeding helper shared by the public and player competition-show pages — reuse it, don't reimplement.
- Standings math: `LeagueStandingsService` (round-robin leagues), `TournamentGroupService`/`TournamentBracketService`/`JOOLABracketService`/`KnockoutBracketService` (tournaments). Don't hand-roll points/standings logic in a controller or view.
- Route names for the two competing "public" and "player" areas are namespaced `competitions.*`/`teams.*` and `player.*` respectively — see the `mojturnir-page` skill for the full map.

## Testing/checking changes

No PHPUnit/Pest suite is actively exercised in this workflow — verify by rendering: `php artisan tinker` with `Auth::login($user)` + `app('view')->share('errors', new \Illuminate\Support\ViewErrorBag)` (needed when calling a controller method directly, since the real `ShareErrorsFromSession` middleware isn't in the loop) then call the controller method and `->render()` the result. Cheaper than spinning up real HTTP+session for a quick view-error check. For actual routing/middleware behavior, hit the running dev server with `curl` instead — direct controller calls skip route-model-binding surprises like the one above.

Always `php -l` every edited `.blade.php`/`.php` file before considering a change done.
