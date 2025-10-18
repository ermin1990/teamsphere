<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\League;
use App\Models\LeagueMatch;
use Illuminate\Http\Request;

class PublicMatchController extends Controller
{
    /**
     * Display all public competitions (leagues and tournaments) with standings, results and upcoming matches.
     */
    public function indexLeagues()
    {
        // Get all public competitions with basic info
        $competitions = Competition::where('is_public', true)
            ->with(['organization', 'sport'])
            ->get();

        return view('public.leagues.index', compact('competitions'));
    }

    /**
     * Display a specific public competition (league or tournament).
     */
    public function showLeague(Competition $competition)
    {
        // Ensure competition is public
        if (!$competition->is_public) {
            abort(404, 'Competition not found.');
        }

        // Load necessary relationships
        $competition->load([
            'organization',
            'sport',
            'standings.team',
            'standings.player',
            'matches' => function ($query) {
                $query->orderBy('scheduled_at', 'desc')
                      ->with(['homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer']);
            }
        ]);

        $organization = $competition->organization;

        return view('public.leagues.show', compact('competition', 'organization'));
    }

    /**
     * Display public match details.
     */
    public function showMatch(Competition $competition, LeagueMatch $match)
    {
        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404, 'Match not found.');
        }

        // Load necessary relationships
        $match->load([
            'homeTeam.players',
            'awayTeam.players',
            'homePlayer',
            'awayPlayer',
            'competition.organization',
            'moderator'
        ]);

        $organization = $competition->organization;

        return view('public.matches.show', compact('organization', 'competition', 'match'));
    }

    /**
     * Display public live score for a match.
     */
    public function liveScore(Competition $competition, LeagueMatch $match)
    {
        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404, 'Match not found.');
        }

        // Load necessary relationships
        $match->load([
            'homeTeam.players',
            'awayTeam.players',
            'homePlayer',
            'awayPlayer',
            'competition.organization',
            'moderator'
        ]);

        $organization = $competition->organization;

        return view('public.matches.live', compact('organization', 'competition', 'match'));
    }

    /**
     * Get live matches data as JSON for AJAX updates.
     */
    public function getLiveMatchesData()
    {
        // Get all live league matches
        $liveLeagueMatches = LeagueMatch::where('status', 'in_progress')
            ->with(['competition.organization', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer', 'moderator'])
            ->get();

        // Get all live tournament matches
        $liveTournamentMatches = \App\Models\CompetitionMatch::where('status', 'in_progress')
            ->with(['competition.organization', 'homePlayer', 'awayPlayer', 'tournamentGroup'])
            ->get();

        // Combine all live matches
        $allLiveMatches = $liveLeagueMatches->concat($liveTournamentMatches);

        // Group matches by competition
        $matchesByCompetition = $allLiveMatches->groupBy(function($match) {
            return $match->competition->id;
        });

        $data = [];
        foreach ($matchesByCompetition as $competitionId => $competitionMatches) {
            $competition = $competitionMatches->first()->competition;
            $matches = [];

            foreach ($competitionMatches as $match) {
                // Handle both LeagueMatch and CompetitionMatch
                $isTournamentMatch = $match instanceof \App\Models\CompetitionMatch;

                $matches[] = [
                    'id' => $match->id,
                    'round' => $match->round ?? $match->round_number ?? null,
                    'home_score' => $match->home_score ?? 0,
                    'away_score' => $match->away_score ?? 0,
                    'status' => $match->status,
                    'updated_at' => $match->updated_at->toISOString(),
                    'home_player' => $match->homePlayer ? [
                        'id' => $match->homePlayer->id,
                        'name' => $match->homePlayer->name,
                    ] : null,
                    'away_player' => $match->awayPlayer ? [
                        'id' => $match->awayPlayer->id,
                        'name' => $match->awayPlayer->name,
                    ] : null,
                    'home_team' => $match->homeTeam ? [
                        'id' => $match->homeTeam->id,
                        'name' => $match->homeTeam->name,
                    ] : null,
                    'away_team' => $match->awayTeam ? [
                        'id' => $match->awayTeam->id,
                        'name' => $match->awayTeam->name,
                    ] : null,
                    'sets' => $match->sets ?? [],
                    'is_tournament_match' => $isTournamentMatch,
                    'phase' => $isTournamentMatch ? ($match->phase ?? 'groups') : 'league',
                    'tournament_group' => $isTournamentMatch && $match->tournamentGroup ? [
                        'id' => $match->tournamentGroup->id,
                        'name' => $match->tournamentGroup->name,
                    ] : null,
                ];
            }

            $data[] = [
                'competition' => [
                    'id' => $competition->id,
                    'name' => $competition->name,
                    'type' => $competition->type,
                    'organization' => [
                        'id' => $competition->organization->id,
                        'name' => $competition->organization->name,
                    ],
                    'sport' => [
                        'id' => $competition->sport->id,
                        'name' => $competition->sport->name,
                    ],
                ],
                'matches' => $matches,
            ];
        }

        // Sort competitions by ID for consistent ordering
        usort($data, function($a, $b) {
            return $a['competition']['id'] <=> $b['competition']['id'];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total_live_matches' => $allLiveMatches->count(),
            'last_updated' => now()->toISOString(),
        ]);
    }

    /**
     * Display all live matches across all leagues.
     */
    public function liveMatches()
    {
        // Get all live matches from all leagues
        $liveMatches = LeagueMatch::where('status', 'in_progress')
            ->with(['competition.organization', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer', 'moderator'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('public.live-matches', compact('liveMatches'));
    }

    /**
     * Get single match data as JSON for AJAX updates.
     */
    public function getMatchData(LeagueMatch $match)
    {
        // Load necessary relationships
        $match->load(['competition.organization', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer', 'moderator']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $match->id,
                'round' => $match->round,
                'home_score' => $match->home_score ?? 0,
                'away_score' => $match->away_score ?? 0,
                'status' => $match->status,
                'updated_at' => $match->updated_at->toISOString(),
                'home_player' => $match->homePlayer ? [
                    'id' => $match->homePlayer->id,
                    'name' => $match->homePlayer->name,
                ] : null,
                'away_player' => $match->awayPlayer ? [
                    'id' => $match->awayPlayer->id,
                    'name' => $match->awayPlayer->name,
                ] : null,
                'home_team' => $match->homeTeam ? [
                    'id' => $match->homeTeam->id,
                    'name' => $match->homeTeam->name,
                ] : null,
                'away_team' => $match->awayTeam ? [
                    'id' => $match->awayTeam->id,
                    'name' => $match->awayTeam->name,
                ] : null,
                'sets' => $match->sets ?? [],
                'competition' => [
                    'id' => $match->competition->id,
                    'name' => $match->competition->name,
                    'organization' => [
                        'id' => $match->competition->organization->id,
                        'name' => $match->competition->organization->name,
                    ],
                    'sport' => [
                        'id' => $match->competition->sport->id,
                        'name' => $match->competition->sport->name,
                    ],
                ],
            ],
            'last_updated' => now()->toISOString(),
        ]);
    }
}