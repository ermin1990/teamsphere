---
name: mojturnir-tournaments
description: Domain model for competitions/leagues/tournaments (Competition, matches, standings, groups, brackets). Use when touching anything about how a competition, match, or standings table works.
---

# Competition/tournament domain model

## `Competition` is both "league" and "tournament"

`type` is `'league'` or `'tournament'` — check with `$competition->isLeague()`, not a raw string compare. `is_team_based` is orthogonal (a league or tournament can be either individual players or teams). `status` moves through `draft → active/registration_open → in_progress → completed`. `is_public` gates anonymous (spectator) visibility, separate from status.

- **League**: flat round-robin. Matches are `LeagueMatch` (individual) or `TeamMatch` (team-based, itself containing individual `CompetitionMatch` games for doubles/singles within a tie). Standings computed/persisted by `LeagueStandingsService` into the `Standing` model (`competition_id`, `player_id` or `team_id`, `points`/`won`/`lost`/`drawn`, `participant_name`, ordered by `position`). Set/game differentials (S/G columns) are **not stored** — computed on the fly from completed `LeagueMatch.sets` JSON (see `_competition-body.blade.php`'s `$diffByPlayer` loop for the reference implementation; don't duplicate it elsewhere, include the partial instead).
- **Tournament**: `TournamentGroup`s (group stage) each with their own `standings()` (same `Standing` model, scoped to the group) plus optional knockout bracket. Matches are `CompetitionMatch` with a `phase` (`group`/`knockout`), `round_number`, `match_order`, `tournament_group_id`. Seeding maps (`$playerGroupSeeding` "A-1" format, `$playerPositionSeeding` plain int) come from `TournamentGroup->player_ids` order — built once in `CompetitionShowData::load()`, don't recompute.

## Bracket/group generation services

`TournamentGroupService` (create/recalculate group standings), `TournamentBracketService`/`KnockoutBracketService`/`JOOLABracketService` (seed and advance knockout rounds — JOOLA is a specific seeding algorithm variant). `BergerScheduleService` generates round-robin league schedules. Use these rather than hand-rolling pairing/seeding logic — they encode tie-break and advancement rules already fought over.

## Rendering: always via the shared partials

`public.leagues._competition-body` (standings table + schedule + stats, or delegates to `_tournament` when `type === 'tournament'`) and `public.leagues._tournament` (groups + knockout bracket) are the *only* places this rendering logic should exist — both the public spectator page and the player's own competition page include them. If a competition-display bug shows up, fix it in the partial, not in a page-specific copy (there shouldn't be one).

## Match card conventions

Win → `text-primary`. Not-yet-decided/scheduled → `text-secondary`, pulse for live. Set-by-set score cells: winner's cell `text-primary`, loser's `text-on-surface-variant/50`, empty `text-on-surface-variant/30`. Cap the number of set-cells shown at `2 * sets_to_win - 1` (max possible sets), not a hardcoded 5.

## Player vs organization vs club

A `Player` belongs to one `Organization` (`organization_id`) — that's their "home club", shown under their name in standings. It can differ from the competition's hosting organization when players join across orgs (see `CompetitionJoinRequest`/`PlayerInvitationController`), so don't assume they're always the same.
