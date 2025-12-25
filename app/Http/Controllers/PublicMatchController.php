<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\TeamMatch;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Team;
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
        // Ensure competition is public, or allow access if user is the owner
        $isOwner = auth()->check() && auth()->id() === $competition->organization->user_id;
        if (!$competition->is_public && !$isOwner) {
            abort(404, 'Competition not found.');
        }

        try {
            // Load necessary relationships
            $competition->load([
                'organization',
                'sport',
                'standings' => function ($query) {
                    $query->orderBy('position', 'asc');
                },
                'standings.team',
                'standings.player',
                'tournamentGroups', // Load tournament groups for standings
                'tournamentGroups.standings.player',
            ]);

            // Create player seeding maps for tournaments
            $playerGroupSeeding = []; // For knockout phase: "A-1" format
            $playerPositionSeeding = []; // For group phase: just position number
            if ($competition->type === 'tournament') {
                foreach ($competition->tournamentGroups as $group) {
                    foreach ($group->player_ids ?? [] as $index => $playerId) {
                        $playerGroupSeeding[$playerId] = $group->name . '-' . ($index + 1);
                        $playerPositionSeeding[$playerId] = $index + 1;
                    }
                }
            }

            // Load matches based on competition type
            if ($competition->type === 'league') {
                $competition->load(['standings.team', 'standings.player']);
                if ($competition->is_team_based) {
                    $competition->load([
                        'teamMatches' => function ($query) {
                            $query->orderBy('round')
                                  ->orderBy('scheduled_at', 'desc')
                                  ->with(['homeTeam', 'awayTeam']);
                        }
                    ]);
                } else {
                    $competition->load([
                        'leagueMatches' => function ($query) {
                            $query->orderBy('round')
                                  ->orderBy('scheduled_at', 'desc')
                                  ->with(['homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer']);
                        }
                    ]);
                }
            } else {
                // For tournaments, matches are CompetitionMatch
                $competition->load([
                    'matches' => function ($query) {
                        $query->orderBy('round_number')
                              ->orderBy('match_order')
                              ->with(['homePlayer', 'awayPlayer', 'tournamentGroup']);
                    }
                ]);
            }

            $organization = $competition->organization;

            return view('public.leagues.show', compact('competition', 'organization', 'playerGroupSeeding', 'playerPositionSeeding'));
        } catch (\Exception $e) {
            \Log::error('Error loading competition: ' . $e->getMessage(), [
                'competition_id' => $competition->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Error loading competition: ' . $e->getMessage());
        }
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
            'moderator',
            'referee',
            'table'
        ]);

        $organization = $competition->organization;

        return view('public.matches.show', compact('organization', 'competition', 'match'));
    }

    /**
     * Display public team match details.
     */
    public function showTeamMatch(Competition $competition, TeamMatch $teamMatch)
    {
        // Ensure match belongs to competition
        if ($teamMatch->competition_id !== $competition->id) {
            abort(404, 'Team match not found.');
        }

        // Load necessary relationships
        $teamMatch->load([
            'homeTeam',
            'awayTeam',
            'competition.organization',
            'individualMatches.homePlayer',
            'individualMatches.awayPlayer',
            'homeCaptain',
            'awayCaptain',
        ]);

        $doublesPlayers = [
            'home_1' => null,
            'home_2' => null,
            'away_1' => null,
            'away_2' => null,
        ];

        if ($teamMatch->lineup) {
            $playerIds = [
                $teamMatch->lineup['home_dubl_1'] ?? null,
                $teamMatch->lineup['home_dubl_2'] ?? null,
                $teamMatch->lineup['away_dubl_1'] ?? null,
                $teamMatch->lineup['away_dubl_2'] ?? null,
            ];
            $players = Player::whereIn('id', array_filter($playerIds))->get()->keyBy('id');
            
            $doublesPlayers = [
                'home_1' => $players->get($teamMatch->lineup['home_dubl_1'] ?? null),
                'home_2' => $players->get($teamMatch->lineup['home_dubl_2'] ?? null),
                'away_1' => $players->get($teamMatch->lineup['away_dubl_1'] ?? null),
                'away_2' => $players->get($teamMatch->lineup['away_dubl_2'] ?? null),
            ];
        }

        $organization = $competition->organization;
        $matches = $teamMatch->individualMatches()->orderBy('match_order')->get();

        return view('public.team-matches.show', compact('organization', 'competition', 'teamMatch', 'matches', 'doublesPlayers'));
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
            'moderator',
            'referee',
            'table'
        ]);

        $organization = $competition->organization;

        return view('public.matches.live', compact('organization', 'competition', 'match'));
    }

    /**
     * Get live matches data as JSON for AJAX updates.
     */
    public function getLiveMatchesData()
    {
        // Get all live league matches + recently completed (last 2 minutes)
        $liveLeagueMatches = LeagueMatch::where(function($query) {
                $query->where('status', 'in_progress')
                      ->orWhere(function($q) {
                          $q->where('status', 'completed')
                            ->where('completed_at', '>=', now()->subMinutes(2));
                      });
            })
            ->with(['competition.organization', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer', 'moderator'])
            ->get();

        // Get all live tournament matches + recently completed (last 2 minutes)
        $liveTournamentMatches = \App\Models\CompetitionMatch::where(function($query) {
                $query->where('status', 'in_progress')
                      ->orWhere(function($q) {
                          $q->where('status', 'completed')
                            ->where('completed_at', '>=', now()->subMinutes(2));
                      });
            })
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
    public function getMatchData($matchId)
    {
        // Try to find as LeagueMatch first, then CompetitionMatch
        $match = LeagueMatch::with(['competition.organization', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer', 'moderator', 'table', 'referee'])->find($matchId);
        $isCompetitionMatch = false;
        if (!$match) {
            $match = \App\Models\CompetitionMatch::with(['competition.organization', 'homePlayer', 'awayPlayer', 'tournamentGroup', 'table', 'referee'])->find($matchId);
            $isCompetitionMatch = true;
        }

        if (!$match) {
            return response()->json(['success' => false, 'message' => 'Match not found'], 404);
        }

        // Get parent competition/league
        $parent = $match->competition ?? $match->league;

        // Always provide player/team names for frontend display
        $homePlayerName = $match->homePlayer->name ?? ($match->homeTeam->name ?? 'N/A');
        $awayPlayerName = $match->awayPlayer->name ?? ($match->awayTeam->name ?? 'N/A');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $match->id,
                'round' => $match->round ?? $match->round_number ?? null,
                'home_score' => $match->home_score ?? 0,
                'away_score' => $match->away_score ?? 0,
                'status' => $match->status,
                'updated_at' => $match->updated_at->toISOString(),
                'home_player' => $match->homePlayer ? [
                    'id' => $match->homePlayer->id,
                    'name' => $homePlayerName,
                ] : ($match->homeTeam ? [
                    'id' => $match->homeTeam->id,
                    'name' => $homePlayerName,
                ] : ['id' => null, 'name' => 'N/A']),
                'away_player' => $match->awayPlayer ? [
                    'id' => $match->awayPlayer->id,
                    'name' => $awayPlayerName,
                ] : ($match->awayTeam ? [
                    'id' => $match->awayTeam->id,
                    'name' => $awayPlayerName,
                ] : ['id' => null, 'name' => 'N/A']),
                'home_team' => $match->homeTeam ? [
                    'id' => $match->homeTeam->id,
                    'name' => $match->homeTeam->name,
                ] : null,
                'away_team' => $match->awayTeam ? [
                    'id' => $match->awayTeam->id,
                    'name' => $match->awayTeam->name,
                ] : null,
                'sets' => $match->sets ?? [],
                'competition' => $parent ? [
                    'id' => $parent->id,
                    'name' => $parent->name,
                    'organization' => [
                        'id' => $parent->organization->id,
                        'name' => $parent->organization->name,
                    ],
                    'sport' => [
                        'id' => $parent->sport->id,
                        'name' => $parent->sport->name,
                    ],
                ] : null,
            ],
            'last_updated' => now()->toISOString(),
        ]);
    }

    /**
     * Display tournament PDF export.
     */
    public function tournamentPdf(Competition $competition)
    {
        // Ensure competition is public, or allow access if user is the owner
        $isOwner = auth()->check() && auth()->id() === $competition->organization->user_id;
        if (!$competition->is_public && !$isOwner) {
            abort(404, 'Competition not found.');
        }

        // Only allow for tournaments
        if ($competition->type !== 'tournament') {
            abort(404, 'PDF export is only available for tournaments.');
        }

        try {
            // Load necessary relationships for PDF export
            $competition->load([
                'organization',
                'sport',
                'tournamentGroups.standings.player',
                'matches' => function ($query) {
                    $query->orderBy('round_number')
                          ->orderBy('match_order')
                          ->with(['homePlayer', 'awayPlayer', 'tournamentGroup']);
                }
            ]);

            // Recalculate standings for all tournament groups to ensure they are up to date
            foreach ($competition->tournamentGroups as $group) {
                $groupService = app(\App\Services\TournamentGroupService::class);
                $groupService->recalculateGroupStandings($group);
            }

            // Reload standings after recalculation
            $competition->load([
                'tournamentGroups.standings.player',
            ]);

            // Create player seeding maps
            $playerGroupSeeding = []; // For knockout phase: "A-1" format
            $playerPositionSeeding = []; // For group phase: just position number
            foreach ($competition->tournamentGroups as $group) {
                foreach ($group->player_ids ?? [] as $index => $playerId) {
                    $playerGroupSeeding[$playerId] = $group->name . '-' . ($index + 1);
                    $playerPositionSeeding[$playerId] = $index + 1;
                }
            }

            // Group matches for display
            $groupMatches = $competition->matches->whereNotNull('tournament_group_id')->groupBy('tournament_group_id');
            $knockoutMatches = $competition->matches->where('phase', 'knockout')
                ->sortBy(['round_number', 'match_order'])
                ->groupBy('round_number');

            // Determine what tabs to show
            $hasGroupMatches = $groupMatches->count() > 0;
            $hasKnockoutMatches = $knockoutMatches->count() > 0;
            $showGroupsTab = $hasGroupMatches;
            $showKnockoutTab = $hasKnockoutMatches;
            $showPdfTab = $hasGroupMatches || $hasKnockoutMatches;

            $organization = $competition->organization;

            return view('public.leagues.tournament_pdf', compact(
                'competition',
                'organization',
                'groupMatches',
                'knockoutMatches',
                'hasGroupMatches',
                'hasKnockoutMatches',
                'showGroupsTab',
                'showKnockoutTab',
                'showPdfTab',
                'playerGroupSeeding',
                'playerPositionSeeding'
            ));

        } catch (\Exception $e) {
            \Log::error('Error loading tournament PDF: ' . $e->getMessage(), [
                'competition_id' => $competition->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Error loading tournament PDF: ' . $e->getMessage());
        }
    }

    /**
     * Display competition semafor - large screen display for competition results
     */
    public function competitionSemafor(Competition $competition)
    {
        // Ensure competition is public, or allow access if user is the owner
        $isOwner = auth()->check() && auth()->id() === $competition->organization->user_id;
        if (!$competition->is_public && !$isOwner) {
            abort(404, 'Competition not found.');
        }

        try {
            // Load all necessary data for semafor display
            $competition->load([
                'organization',
                'sport',
                'standings.team',
                'standings.player',
                'tournamentGroups.standings.player',
                'tournamentGroups.standings.team',
                'matches' => function ($query) {
                    $query->with(['homePlayer', 'awayPlayer', 'homeTeam', 'awayTeam'])
                          ->orderBy('round_number')
                          ->orderBy('match_order')
                          ->orderBy('scheduled_at');
                }
            ]);

            // Group matches by phase for knockout display
            $matchesByPhase = $competition->matches->groupBy('phase');

            // Get current active phase
            $currentPhase = 'groups'; // default
            if ($competition->current_phase) {
                $currentPhase = $competition->current_phase;
            } elseif ($matchesByPhase->has('knockout')) {
                $currentPhase = 'knockout';
            }

            return view('public.leagues.semafor', compact(
                'competition',
                'matchesByPhase',
                'currentPhase'
            ));

        } catch (\Exception $e) {
            \Log::error('Competition semafor error: ' . $e->getMessage());
            abort(500, 'Error loading competition data.');
        }
    }

    /**
     * Display a public team/club profile.
     */
    public function showTeam(Team $team)
    {
        $team->load(['organization', 'players']);
        
        try {
            $team->load(['coaches' => function($query) {
                $query->where('is_active', true);
            }]);
        } catch (\Exception $e) {
            // Table might not exist yet
            \Log::warning('Could not load coaches for team: ' . $e->getMessage());
        }
        
        $matches = TeamMatch::where('home_team_id', $team->id)
            ->orWhere('away_team_id', $team->id)
            ->with(['homeTeam', 'awayTeam', 'competition'])
            ->orderBy('scheduled_at', 'desc')
            ->get()
            ->groupBy('competition_id');

        return view('public.teams.show', compact('team', 'matches'));
    }

    public function showTeamCompetitionMatches(Team $team, Competition $competition)
    {
        $team->load(['organization']);
        
        $matches = TeamMatch::where('competition_id', $competition->id)
            ->where(function($query) use ($team) {
                $query->where('home_team_id', $team->id)
                      ->orWhere('away_team_id', $team->id);
            })
            ->with(['homeTeam', 'awayTeam', 'competition'])
            ->orderBy('scheduled_at', 'desc')
            ->paginate(20);

        return view('public.teams.competition-matches', compact('team', 'competition', 'matches'));
    }
}