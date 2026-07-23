<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CompetitionMatchResource;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Player;
use App\Services\LeagueStandingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Self-service match endpoints for the authenticated user's own player
 * profile(s) - mirrors the web PlayerMatchController/PlayerDashboardController
 * (create a match, enter a result on a scheduled match, list "my matches").
 */
class PlayerMatchController extends Controller
{
    use ApiResponses;

    private function myPlayerIds(Request $request)
    {
        return Player::where('user_id', $request->user()->id)->pluck('id');
    }

    private function myMatchesQuery(Request $request)
    {
        $playerIds = $this->myPlayerIds($request);

        return CompetitionMatch::where(function ($query) use ($playerIds) {
                $query->whereIn('home_player_id', $playerIds)
                      ->orWhereIn('away_player_id', $playerIds);
            })
            ->with(['competition.organization', 'homePlayer', 'awayPlayer', 'venue']);
    }

    /**
     * All of the authenticated player's matches, any status, most recent first.
     */
    public function index(Request $request): JsonResponse
    {
        $matches = $this->myMatchesQuery($request)
            ->orderByRaw('played_at IS NULL, played_at DESC, scheduled_at DESC')
            ->paginate($this->perPage($request));

        return $this->paginated($matches, CompetitionMatchResource::class);
    }

    /**
     * Upcoming matches (scheduled or currently in progress), soonest first.
     */
    public function upcoming(Request $request): JsonResponse
    {
        $matches = $this->myMatchesQuery($request)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderByRaw("status = 'in_progress' desc")
            ->orderBy('scheduled_at')
            ->paginate($this->perPage($request));

        return $this->paginated($matches, CompetitionMatchResource::class);
    }

    /**
     * Completed matches, most recently played first.
     */
    public function completed(Request $request): JsonResponse
    {
        $matches = $this->myMatchesQuery($request)
            ->where('status', 'completed')
            ->orderByDesc('played_at')
            ->paginate($this->perPage($request));

        return $this->paginated($matches, CompetitionMatchResource::class);
    }

    /**
     * Match details - visible to anyone if the competition is public,
     * otherwise only to members/the organization owner - mirrors the
     * visibility check in PlayerLeagueController::show.
     */
    public function show(Request $request, CompetitionMatch $match): JsonResponse
    {
        $match->load(['competition.organization', 'homePlayer', 'awayPlayer', 'homeTeam', 'awayTeam', 'venue']);

        $competition = $match->competition;
        $isMember = $competition->players()->where('players.user_id', $request->user()->id)->exists();
        $isOwner = $request->user()->id === $competition->organization->user_id;

        abort_unless($competition->is_public || $isMember || $isOwner, 404);

        return $this->ok(new CompetitionMatchResource($match));
    }

    /**
     * Find the Player row that links the authenticated user to this
     * competition, or fail - the authorization check for logging a brand new
     * match (self-scheduled result), mirroring
     * PlayerMatchController::currentPlayerIn on the web.
     */
    private function currentPlayerIn(Request $request, Competition $competition): Player
    {
        abort_if($competition->is_team_based, 403, 'Samostalni unos meča je dostupan samo za pojedinačna (ne-ekipna) takmičenja.');
        abort_unless($competition->status === 'active', 403, 'Ovo takmičenje trenutno ne prima rezultate mečeva.');

        $player = $competition->players()->where('players.user_id', $request->user()->id)->first();

        abort_unless($player, 403, 'Nisi prijavljen kao igrač na ovom takmičenju.');

        return $player;
    }

    /**
     * Log a brand new match result against a chosen opponent from the same
     * competition - allowed only when the league permits rematches (or is
     * marked recreational), same rule the organizer's own "add match" and the
     * web self-entry flow use.
     */
    public function store(Request $request, Competition $competition, LeagueStandingsService $standingsService): JsonResponse
    {
        $player = $this->currentPlayerIn($request, $competition);

        $request->validate([
            'opponent_id' => ['required', 'exists:players,id'],
            'played_at' => ['nullable', 'date', 'before_or_equal:now'],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'sets' => ['required', 'array', 'min:1'],
            'sets.*.mine' => ['nullable', 'integer', 'min:0'],
            'sets.*.theirs' => ['nullable', 'integer', 'min:0'],
        ]);

        $opponentId = (int) $request->opponent_id;

        if ($opponentId === $player->id) {
            return $this->fail('Ne možeš igrati protiv samog sebe.', 422);
        }

        if (!$competition->players()->where('players.id', $opponentId)->exists()) {
            return $this->fail('Protivnik mora biti prijavljen na ovo takmičenje.', 422);
        }

        $rematchesAllowed = $competition->allow_rematches || $competition->is_recreational;
        if (!$rematchesAllowed && CompetitionMatch::pairAlreadyPlayed($competition->id, $player->id, $opponentId)) {
            return $this->fail('Već ste odigrali meč protiv ovog igrača. Organizator mora označiti ligu kao rekreativnu ili uključiti "Dozvoli povratne mečeve" da bi se moglo igrati ponovo.', 422);
        }

        $sets = collect($request->sets)
            ->map(fn ($set) => ['mine' => (int) ($set['mine'] ?? 0), 'theirs' => (int) ($set['theirs'] ?? 0)])
            ->reverse()
            ->skipWhile(fn ($set) => $set['mine'] === 0 && $set['theirs'] === 0)
            ->reverse()
            ->values();

        if ($sets->isEmpty()) {
            return $this->fail('Unesi rezultat bar jednog seta.', 422);
        }

        $mySets = $sets->filter(fn ($set) => $set['mine'] > $set['theirs'])->count();
        $theirSets = $sets->filter(fn ($set) => $set['theirs'] > $set['mine'])->count();
        $normalizedSets = $sets->map(fn ($set) => ['home' => $set['mine'], 'away' => $set['theirs']])->all();

        if ($error = $this->validateSetsAgainstConfig($competition, $mySets, $theirSets)) {
            return $this->fail($error, 422);
        }

        $match = DB::transaction(function () use ($competition, $player, $opponentId, $mySets, $theirSets, $normalizedSets, $request) {
            $nextRound = (int) ($competition->matches()->lockForUpdate()->max('round_number') ?? 0) + 1;

            return CompetitionMatch::create([
                'competition_id' => $competition->id,
                'home_player_id' => $player->id,
                'away_player_id' => $opponentId,
                'home_score' => $mySets,
                'away_score' => $theirSets,
                'sets' => $normalizedSets,
                'round' => $nextRound,
                'round_number' => $nextRound,
                'status' => 'completed',
                'played_at' => $request->played_at ?? now(),
                'venue_id' => $request->venue_id,
            ]);
        });

        if ($competition->isLeague()) {
            $standingsService->rebuildForCompetition($competition);
        }

        return $this->created(new CompetitionMatchResource($match->load(['homePlayer', 'awayPlayer', 'venue'])), 'Meč je zabilježen!');
    }

    /**
     * Check the submitted set score against the competition's configured
     * sets_to_win - neither side should exceed it, and the match must
     * actually be decided (one side reaches it). Returns an error message,
     * or null when valid.
     */
    private function validateSetsAgainstConfig(Competition $competition, int $mySets, int $theirSets): ?string
    {
        $setsToWin = $competition->sets_to_win;

        if (!$setsToWin) {
            return null;
        }

        if ($mySets > $setsToWin || $theirSets > $setsToWin) {
            return "Broj osvojenih setova ne može biti veći od {$setsToWin}.";
        }

        if ($mySets !== $setsToWin && $theirSets !== $setsToWin) {
            return "Meč nije završen - jedna strana mora osvojiti {$setsToWin} set(ova).";
        }

        return null;
    }

    /**
     * Find which side (home/away) of an EXISTING match the current user
     * plays, or fail - mirrors PlayerMatchController::myPlayerInMatch.
     */
    private function myPlayerInMatch(Request $request, CompetitionMatch $match): Player
    {
        $competition = $match->competition;

        abort_if($competition->is_team_based, 403, 'Samostalni unos rezultata je dostupan samo za pojedinačna (ne-ekipna) takmičenja.');
        abort_unless($competition->isLeague(), 403, 'Samostalni unos rezultata je dostupan samo za lige.');
        abort_unless($competition->status === 'active', 403, 'Ovo takmičenje trenutno ne prima rezultate mečeva.');

        $player = Player::whereIn('id', [$match->home_player_id, $match->away_player_id])
            ->where('user_id', $request->user()->id)
            ->first();

        abort_unless($player, 403, 'Nisi jedan od igrača u ovom meču.');

        return $player;
    }

    /**
     * Enter/edit the result of an existing match assigned to the current
     * player (scheduled, in progress, or already completed) - opponent is
     * fixed, unlike store() above which logs a brand new match.
     */
    public function updateResult(Request $request, CompetitionMatch $match, LeagueStandingsService $standingsService): JsonResponse
    {
        $player = $this->myPlayerInMatch($request, $match);
        $competition = $match->competition;
        $isHome = $match->home_player_id === $player->id;

        $request->validate([
            'played_at' => ['required', 'date', 'before_or_equal:now'],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'sets' => ['required', 'array', 'min:1'],
            'sets.*.mine' => ['nullable', 'integer', 'min:0'],
            'sets.*.theirs' => ['nullable', 'integer', 'min:0'],
        ]);

        $sets = collect($request->sets)
            ->map(fn ($set) => ['mine' => (int) ($set['mine'] ?? 0), 'theirs' => (int) ($set['theirs'] ?? 0)])
            ->reverse()
            ->skipWhile(fn ($set) => $set['mine'] === 0 && $set['theirs'] === 0)
            ->reverse()
            ->values();

        if ($sets->isEmpty()) {
            return $this->fail('Unesi rezultat bar jednog seta.', 422);
        }

        $mySets = $sets->filter(fn ($set) => $set['mine'] > $set['theirs'])->count();
        $theirSets = $sets->filter(fn ($set) => $set['theirs'] > $set['mine'])->count();

        if ($error = $this->validateSetsAgainstConfig($competition, $mySets, $theirSets)) {
            return $this->fail($error, 422);
        }

        $normalizedSets = $sets->map(fn ($set) => $isHome
            ? ['home' => $set['mine'], 'away' => $set['theirs']]
            : ['home' => $set['theirs'], 'away' => $set['mine']]
        )->all();

        $match->update([
            'home_score' => $isHome ? $mySets : $theirSets,
            'away_score' => $isHome ? $theirSets : $mySets,
            'sets' => $normalizedSets,
            'venue_id' => $request->venue_id,
            'status' => 'completed',
            'played_at' => $request->played_at,
        ]);

        if ($competition->isLeague()) {
            $standingsService->rebuildForCompetition($competition);
        }

        return $this->ok(new CompetitionMatchResource($match->fresh(['homePlayer', 'awayPlayer', 'venue'])), 'Rezultat je sačuvan!');
    }
}
