<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Competition;
use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\Sport;
use App\Models\TeamMatch;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Http\Request;

class PublicMatchController extends Controller
{
    /**
     * Display all public competitions (leagues and tournaments), filterable by
     * city/sport, plus a cross-league feed of recent results and upcoming
     * matches so visitors can see activity without picking a league first.
     */
    public function indexLeagues(Request $request)
    {
        $competitionsQuery = Competition::where('is_public', true)
            ->with(['organization', 'sport', 'city']);

        if ($request->filled('city_id')) {
            $competitionsQuery->where('competitions.city_id', $request->city_id);
        }
        if ($request->filled('sport_id')) {
            $competitionsQuery->where('competitions.sport_id', $request->sport_id);
        }
        if ($request->filled('type')) {
            $competitionsQuery->where('competitions.type', $request->type);
        }
        $matchedPlayers = collect();
        if ($request->filled('q')) {
            $q = $request->q;
            $competitionsQuery->where(function ($query) use ($q) {
                $query->where('competitions.name', 'like', "%{$q}%")
                    ->orWhereHas('organization', fn ($oq) => $oq->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('players', fn ($pq) => $pq->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('teams', fn ($tq) => $tq->where('name', 'like', "%{$q}%"));
            });

            // Direct player match, shown as a "jump straight to their profile"
            // shortcut above the competition list - only players with at
            // least one public league are eligible, same visibility rule as
            // the competitions search above.
            $matchedPlayers = Player::where('name', 'like', "%{$q}%")
                ->whereHas('leagues', fn ($lq) => $lq->where('is_public', true))
                ->with([
                    'organization',
                    'leagues' => fn ($lq) => $lq->where('is_public', true)->with('season'),
                ])
                ->limit(5)
                ->get();
        }

        // Active/finished toggle - defaults to "active" so the browse page
        // isn't dominated by long-finished leagues; "sve" (all) and
        // "zavrsene" (finished) are one click away.
        $statusFilter = $request->get('status', 'active');
        if ($statusFilter === 'active') {
            $competitionsQuery->whereIn('status', ['active', 'in_progress']);
        } elseif ($statusFilter === 'zavrsene') {
            $competitionsQuery->where('status', 'completed');
        } elseif ($statusFilter === 'uskoro') {
            // Otvoreno za prijave, još nije počelo - isti obrazac kao
            // PlayerLeagueController::index(), samo javno i uz start_date
            // provjeru (tamošnja verzija ne razlikuje "još nije počelo").
            $competitionsQuery->where('registration_open', true)
                ->whereIn('status', ['draft', 'active'])
                ->where(fn ($q) => $q->whereNull('registration_deadline')->orWhere('registration_deadline', '>=', now()))
                ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '>=', now()->startOfDay()));
        }

        // Grouped by organization (sorted alphabetically) so visitors can
        // scan "who's running what" at a glance, same layout as the admin
        // leagues list.
        $competitions = $competitionsQuery
            ->join('organizations', 'organizations.id', '=', 'competitions.organization_id')
            ->orderBy('organizations.name')
            ->orderBy('competitions.name')
            ->select('competitions.*')
            ->get()
            ->groupBy(fn ($competition) => $competition->organization->name);

        $competitionsCount = $competitions->sum->count();

        // Cities/sports that have at least one public competition, for the filters.
        $cities = City::whereHas('competitions', function ($query) {
                $query->where('is_public', true);
            })
            ->orderBy('name')
            ->get();

        $sportIds = Competition::where('is_public', true)->pluck('sport_id')->unique();
        $sports = Sport::whereIn('id', $sportIds)->orderBy('name')->get();

        return view('public.leagues.organizations', compact(
            'competitions', 'competitionsCount', 'cities', 'sports', 'statusFilter', 'matchedPlayers'
        ));
    }

    /**
     * Display all public competitions in a given city, across all organizations.
     */
    public function indexLeaguesByCity(City $city)
    {
        $competitions = Competition::where('is_public', true)
            ->where('city_id', $city->id)
            ->with(['organization', 'sport'])
            ->get();

        return view('public.leagues.index', compact('competitions', 'city'));
    }

    /**
     * Display all public competitions for a specific organization.
     */
    public function indexLeaguesByOrganization(Organization $organization, Request $request)
    {
        // Load organization links/banners
        $organization->load('links');

        $seasons = $organization->seasons()->orderByDesc('starts_at')->get();
        $selectedSeasonId = $request->query('season_id');
        if ($selectedSeasonId === null && $seasons->isNotEmpty()) {
            $selectedSeasonId = (string) ($seasons->firstWhere('is_active', true)?->id ?? '');
        }

        // Get all public competitions for this organization
        $competitions = Competition::where('organization_id', $organization->id)
            ->where('is_public', true)
            ->when($selectedSeasonId, fn ($q) => $q->where('season_id', $selectedSeasonId))
            ->with(['organization', 'sport'])
            ->get();

        return view('public.leagues.index', compact('competitions', 'organization', 'seasons', 'selectedSeasonId'));
    }

    /**
     * Display the dedicated "Obavijesti" tab for a public organization page
     * (organization-wide announcements only - each competition has its own
     * tab for its own + organization-wide announcements).
     */
    public function organizationAnnouncements(Organization $organization)
    {
        $announcements = $organization->organizationWideAnnouncements()->latest()->get();

        return view('public.leagues.organization-announcements', compact('organization', 'announcements'));
    }

    /**
     * Directory of all venues (tereni) - lets a visitor browse to any
     * venue's page to see which leagues/matches are played there. Grouped
     * by city in the view; filterable by city and searchable by name.
     */
    public function indexVenues(Request $request)
    {
        $query = Venue::with('city')->withCount([
            'leagueMatches',
            'tournamentMatches',
        ]);

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->input('city_id'));
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->input('q') . '%');
        }

        $venues = $query->orderBy('name')->get();
        $venuesByCity = $venues->groupBy(fn ($venue) => $venue->city?->name ?? 'Ostalo')->sortKeys();
        $cities = City::whereHas('venues')->orderBy('name')->get();

        return view('public.venues.index', compact('venues', 'venuesByCity', 'cities'));
    }

    public function showVenue(Venue $venue)
    {
        $venue->load('city', 'user');

        $competitionIds = LeagueMatch::where('venue_id', $venue->id)->pluck('competition_id')
            ->merge(\App\Models\CompetitionMatch::where('venue_id', $venue->id)->pluck('competition_id'))
            ->unique();

        $competitions = Competition::whereIn('id', $competitionIds)
            ->where('is_public', true)
            ->with(['organization', 'sport'])
            ->orderByDesc('updated_at')
            ->get();

        $publicCompetitionIds = $competitions->pluck('id');

        $recentLeagueMatches = LeagueMatch::where('venue_id', $venue->id)
            ->whereIn('competition_id', $publicCompetitionIds)
            ->with(['competition', 'homePlayer', 'awayPlayer', 'homeTeam', 'awayTeam'])
            ->orderByDesc('scheduled_at')
            ->limit(15)
            ->get();

        $recentTournamentMatches = \App\Models\CompetitionMatch::where('venue_id', $venue->id)
            ->whereIn('competition_id', $publicCompetitionIds)
            ->with(['competition', 'homePlayer', 'awayPlayer'])
            ->orderByDesc('scheduled_at')
            ->limit(15)
            ->get();

        $recentMatches = $recentLeagueMatches->concat($recentTournamentMatches)
            ->sortByDesc(fn ($match) => $match->scheduled_at ?? $match->created_at)
            ->take(15)
            ->values();

        return view('public.venues.show', compact('venue', 'competitions', 'recentMatches'));
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
            ['playerGroupSeeding' => $playerGroupSeeding, 'playerPositionSeeding' => $playerPositionSeeding] = \App\Services\CompetitionShowData::load($competition);

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
     * Display the dedicated "Obavijesti" tab for a public competition (org-wide
     * announcements plus this competition's own).
     */
    public function competitionAnnouncements(Competition $competition)
    {
        $isOwner = auth()->check() && auth()->id() === $competition->organization->user_id;
        if (!$competition->is_public && !$isOwner) {
            abort(404, 'Competition not found.');
        }

        $organization = $competition->organization;
        $announcements = $competition->visibleAnnouncements();

        return view('public.leagues.announcements', compact('competition', 'organization', 'announcements'));
    }

    /**
     * Display the dedicated "Pravila" tab for a public competition (auto
     * scoring settings plus the organizer's free-text rules).
     */
    public function competitionRules(Competition $competition)
    {
        $isOwner = auth()->check() && auth()->id() === $competition->organization->user_id;
        if (!$competition->is_public && !$isOwner) {
            abort(404, 'Competition not found.');
        }

        $organization = $competition->organization;

        return view('public.leagues.rules', compact('competition', 'organization'));
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
            'table',
            'venue'
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
     * Display a minimal embeddable widget for a match (for iframes on external sites).
     */
    public function embedMatch(LeagueMatch $match)
    {
        $match->load(['competition', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer', 'moderator']);

        return view('public.matches.embed', compact('match'));
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
                'current_set_home_score' => $match->current_set_home_score ?? 0,
                'current_set_away_score' => $match->current_set_away_score ?? 0,
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