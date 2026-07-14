<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Player;
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

        $request->validate([
            'opponent_id' => ['required', 'exists:players,id'],
            'played_at' => ['nullable', 'date'],
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

        if (!$competition->allow_rematches && CompetitionMatch::pairAlreadyPlayed($competition->id, $player->id, $opponentId)) {
            return back()->withErrors(['opponent_id' => 'Već ste odigrali meč protiv ovog igrača. Organizator mora uključiti "Dozvoli povratne mečeve" da bi se moglo igrati ponovo.']);
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
            $standingsService->applyForMatch($competition, $match);
        }

        return redirect()->route('player.dashboard')->with('success', 'Meč je zabilježen!');
    }
}
