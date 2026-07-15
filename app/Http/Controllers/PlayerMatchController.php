<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Player;
use App\Models\Venue;
use App\Services\LeagueStandingsService;
use Illuminate\Http\Request;

class PlayerMatchController extends Controller
{
    /**
     * Find the Player row that links the authenticated user to this
     * competition, or abort - this is the self-entry authorization check.
     */
    private function currentPlayerIn(Competition $competition): Player
    {
        abort_if($competition->is_team_based, 403, 'Samostalni unos meča je dostupan samo za pojedinačna (ne-ekipna) takmičenja.');
        abort_unless($competition->status === 'active', 403, 'Ovo takmičenje trenutno ne prima rezultate mečeva.');

        $player = $competition->players()->where('players.user_id', auth()->id())->first();

        abort_unless($player, 403, 'Nisi prijavljen kao igrač na ovom takmičenju.');

        return $player;
    }

    /**
     * Show the "log a match" form: pick an opponent, then enter the exact
     * number of set score fields the competition is configured for
     * (sets_to_win), matching the sport's terminology (games vs points).
     */
    public function create(Competition $competition)
    {
        $player = $this->currentPlayerIn($competition);

        $opponents = $competition->players()->where('players.id', '!=', $player->id)->orderBy('name')->get();
        $maxSets = max(1, (2 * ($competition->sets_to_win ?: 1)) - 1);
        $unitLabel = $competition->sport->isSetsGamesBased() ? 'Gemovi' : 'Poeni';

        return view('player.matches.create', compact('competition', 'player', 'opponents', 'maxSets', 'unitLabel'));
    }

    public function store(Request $request, Competition $competition, LeagueStandingsService $standingsService)
    {
        $player = $this->currentPlayerIn($competition);

        $playedAtRules = ['nullable', 'date', 'before_or_equal:now'];
        if ($competition->start_date) {
            $playedAtRules[] = 'after_or_equal:' . $competition->start_date->toDateString();
        }

        $request->validate([
            'opponent_id' => ['required', 'exists:players,id'],
            'played_at' => $playedAtRules,
            'sets' => ['required', 'array', 'min:1'],
            'sets.*.mine' => ['nullable', 'integer', 'min:0'],
            'sets.*.theirs' => ['nullable', 'integer', 'min:0'],
        ]);

        $opponentId = (int) $request->opponent_id;

        if ($opponentId === $player->id) {
            return back()->withErrors(['opponent_id' => 'Ne možeš igrati protiv samog sebe.']);
        }

        if (!$competition->players()->where('players.id', $opponentId)->exists()) {
            return back()->withErrors(['opponent_id' => 'Protivnik mora biti prijavljen na ovo takmičenje.']);
        }

        // Rekreativne lige po prirodi dozvoljavaju revanš mečeve (isto kao
        // organizatorov "Dodaj Meč"), pored postojećeg zasebnog prekidača
        // "Dozvoli povratne mečeve".
        $rematchesAllowed = $competition->allow_rematches || $competition->is_recreational;
        if (!$rematchesAllowed && CompetitionMatch::pairAlreadyPlayed($competition->id, $player->id, $opponentId)) {
            return back()->withErrors(['opponent_id' => 'Već ste odigrali meč protiv ovog igrača. Organizator mora označiti ligu kao rekreativnu ili uključiti "Dozvoli povratne mečeve" da bi se moglo igrati ponovo.']);
        }

        // Drop trailing all-zero rows (fields left blank at 0-0 by a player
        // who didn't need every set) before counting winners, same trimming
        // idea as the organizer's quick-result normalization.
        $sets = collect($request->sets)
            ->map(fn ($set) => ['mine' => (int) ($set['mine'] ?? 0), 'theirs' => (int) ($set['theirs'] ?? 0)])
            ->reverse()
            ->skipWhile(fn ($set) => $set['mine'] === 0 && $set['theirs'] === 0)
            ->reverse()
            ->values();

        if ($sets->isEmpty()) {
            return back()->withErrors(['sets' => 'Unesi rezultat bar jednog seta.']);
        }

        $mySets = $sets->filter(fn ($set) => $set['mine'] > $set['theirs'])->count();
        $theirSets = $sets->filter(fn ($set) => $set['theirs'] > $set['mine'])->count();

        // Player is always "mine" here - map to home/away based on who's who.
        $normalizedSets = $sets->map(fn ($set) => ['home' => $set['mine'], 'away' => $set['theirs']])->all();

        $nextRound = (int) ($competition->matches()->max('round_number') ?? 0) + 1;

        $match = CompetitionMatch::create([
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
        ]);

        if ($competition->isLeague()) {
            $standingsService->rebuildForCompetition($competition);
        }

        return redirect()->route('player.dashboard')->with('success', 'Meč je zabilježen!');
    }

    /**
     * Find which side (home/away) of an EXISTING match the current user
     * plays, or abort - the authorization check for entering a result on a
     * match that already has both players assigned (scheduled by the
     * organizer or generated by the round-robin), as opposed to
     * create()/store() above which logs a brand new match from scratch.
     */
    private function myPlayerInMatch(CompetitionMatch $match): Player
    {
        $competition = $match->competition;

        abort_if($competition->is_team_based, 403, 'Samostalni unos rezultata je dostupan samo za pojedinačna (ne-ekipna) takmičenja.');
        abort_unless($competition->isLeague(), 403, 'Samostalni unos rezultata je dostupan samo za lige.');
        abort_unless($competition->status === 'active', 403, 'Ovo takmičenje trenutno ne prima rezultate mečeva.');

        $player = Player::whereIn('id', [$match->home_player_id, $match->away_player_id])
            ->where('user_id', auth()->id())
            ->first();

        abort_unless($player, 403, 'Nisi jedan od igrača u ovom meču.');

        return $player;
    }

    /**
     * Show the "enter/edit result" form for an existing match assigned to
     * the current player (scheduled, in progress, or already completed) -
     * opponent is fixed (unlike create()), and adds venue (filtered to the
     * competition's city) alongside date/time + sets.
     */
    public function editResult(CompetitionMatch $match)
    {
        $player = $this->myPlayerInMatch($match);
        $competition = $match->competition;

        $isHome = $match->home_player_id === $player->id;
        $opponent = $isHome ? $match->awayPlayer : $match->homePlayer;

        $maxSets = max(1, (2 * ($competition->sets_to_win ?: 1)) - 1);
        $unitLabel = $competition->sport->isSetsGamesBased() ? 'Gemovi' : 'Poeni';

        $venues = $competition->city_id
            ? Venue::where('city_id', $competition->city_id)->orderBy('name')->get()
            : collect();

        // Prefill existing sets (mine/theirs) if this match was already
        // in_progress with a partial score saved.
        $existingSets = collect($match->sets ?? [])->map(function ($set) use ($isHome) {
            $home = $set['home'] ?? $set['home_score'] ?? 0;
            $away = $set['away'] ?? $set['away_score'] ?? 0;
            return $isHome ? ['mine' => $home, 'theirs' => $away] : ['mine' => $away, 'theirs' => $home];
        });

        return view('player.matches.edit-result', compact(
            'match', 'competition', 'opponent', 'maxSets', 'unitLabel', 'venues', 'existingSets'
        ));
    }

    /**
     * Live, point-by-point scoring for a player's own match - same
     * Livewire component the organizer uses, gated by the same
     * authorization check as editResult()/updateResult() above.
     */
    public function liveScore(CompetitionMatch $match)
    {
        $this->myPlayerInMatch($match);

        $match->load(['competition.organization', 'homePlayer', 'awayPlayer']);

        return view('live-score-page', ['match' => $match]);
    }

    public function updateResult(Request $request, CompetitionMatch $match, LeagueStandingsService $standingsService)
    {
        $player = $this->myPlayerInMatch($match);
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
            return back()->withErrors(['sets' => 'Unesi rezultat bar jednog seta.']);
        }

        $mySets = $sets->filter(fn ($set) => $set['mine'] > $set['theirs'])->count();
        $theirSets = $sets->filter(fn ($set) => $set['theirs'] > $set['mine'])->count();

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

        return redirect()->route('player.dashboard.matches')->with('success', 'Rezultat je sačuvan!');
    }

    /**
     * Reset a match back to "scheduled" with no result - lets a player undo
     * a result they entered on the wrong match, and re-enter it correctly
     * afterwards via editResult()/updateResult() above.
     */
    public function resetResult(CompetitionMatch $match, LeagueStandingsService $standingsService)
    {
        $this->myPlayerInMatch($match);
        $competition = $match->competition;

        $match->update([
            'home_score' => null,
            'away_score' => null,
            'sets' => null,
            'venue_id' => null,
            'played_at' => null,
            'status' => 'scheduled',
        ]);

        if ($competition->isLeague()) {
            $standingsService->rebuildForCompetition($competition);
        }

        return redirect()->route('player.dashboard.matches')->with('success', 'Meč je resetovan - možeš ponovo unijeti rezultat.');
    }
}
