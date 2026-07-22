<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CompetitionResource;
use App\Models\Competition;
use App\Models\Organization;
use App\Models\Standing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompetitionController extends Controller
{
    use ApiResponses;

    /**
     * Browse publicly open competitions across every organization - the
     * mobile "find a league to join" screen. Mirrors the filters on the web's
     * PlayerLeagueController::index (search/sport_id/city_id), scoped to
     * competitions currently accepting registrations.
     */
    public function browse(Request $request): JsonResponse
    {
        $query = Competition::where('registration_open', true)
            ->where('is_public', true)
            ->whereIn('status', ['draft', 'active'])
            ->where(function ($q) {
                $q->whereNull('registration_deadline')
                  ->orWhere('registration_deadline', '>=', now());
            })
            ->with(['organization', 'sport', 'city', 'season']);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($sportId = $request->input('sport_id')) {
            $query->where('sport_id', $sportId);
        }

        if ($cityId = $request->input('city_id')) {
            $query->where('city_id', $cityId);
        }

        $competitions = $query->orderBy('name')->paginate($this->perPage($request));

        return $this->paginated($competitions, CompetitionResource::class);
    }

    /**
     * List competitions for an organization.
     */
    public function index(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $competitions = $organization->competitions()
            ->withCount(['players', 'matches', 'tournamentGroups'])
            ->orderByDesc('created_at')
            ->paginate($this->perPage($request));

        return $this->paginated($competitions, CompetitionResource::class);
    }

    /**
     * Create a new competition for an organization.
     */
    public function store(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        if (!$request->user()->canCreateMoreCompetitions($organization->id)) {
            return $this->fail('You have reached the maximum number of competitions allowed for this organization.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'organizer_contact' => ['nullable', 'string', 'max:255'],
            'entry_fee' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'season_id' => ['nullable', 'exists:seasons,id'],
            'registration_deadline' => ['nullable', 'date'],
            'type' => ['required', 'in:tournament,league'],
            'is_team_based' => ['required_if:type,league', 'boolean'],
            'is_double_round' => ['nullable', 'boolean'],
            'is_recreational' => ['nullable', 'boolean'],
            'allow_rematches' => ['nullable', 'boolean'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'max_teams' => ['nullable', 'integer', 'min:2', 'max:64'],
            'max_participants' => ['nullable', 'integer', 'min:2'],
            'group_count' => ['nullable', 'integer', 'min:1'],
            'players_per_group' => ['nullable', 'integer', 'min:1'],
            // Tournament specific validation
            'players_advancing_per_group' => ['required_if:type,tournament', 'integer', 'min:1', 'max:4'],
            'advancement_method' => ['required_if:type,tournament', 'in:automatic,manual'],
        ]);

        $competition = Competition::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name'] . '-' . time()),
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'organizer_contact' => $validated['organizer_contact'] ?? null,
            'entry_fee' => $validated['entry_fee'] ?? null,
            'organization_id' => $organization->id,
            'sport_id' => $organization->sport_id,
            'category_id' => $validated['category_id'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'season_id' => $organization->seasons()->where('id', $validated['season_id'] ?? null)->exists() ? $validated['season_id'] : null,
            'registration_deadline' => $validated['registration_deadline'] ?? null,
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'max_teams' => $validated['max_teams'] ?? 8,
            'is_team_based' => (bool) ($validated['is_team_based'] ?? false),
            'is_double_round' => (bool) ($validated['is_double_round'] ?? false),
            'is_recreational' => (bool) ($validated['is_recreational'] ?? false),
            'allow_rematches' => (bool) ($validated['allow_rematches'] ?? false),
            'status' => 'draft',
            'is_active' => true,
            'max_participants' => $validated['max_participants'] ?? null,
            'group_count' => $validated['group_count'] ?? 4,
            'players_per_group' => $validated['players_per_group'] ?? 4,
            'players_advancing_per_group' => $validated['players_advancing_per_group'] ?? 2,
            'advancement_method' => $validated['advancement_method'] ?? 'automatic',
            'current_phase' => 'groups',
            'sets_to_win' => 3,
            'points_per_set' => 11,
            'deuce_at' => 10,
            'must_win_by_two' => true,
            'points_for_win' => 2,
            'points_for_draw' => 1,
            'points_for_loss' => 0,
            'has_tiebreak' => false,
            'tiebreak_points' => 7,
            'manual_knockout_selection' => true,
        ]);

        Organization::clearOrganizationCache();

        return $this->created(new CompetitionResource($competition->load('organization')), 'Competition created successfully');
    }

    /**
     * Show a single competition.
     */
    public function show(Competition $competition): JsonResponse
    {
        $this->authorize('view', $competition->organization);

        $competition->load(['organization', 'sport', 'tournamentGroups'])
            ->loadCount(['players', 'matches', 'tournamentGroups']);

        return $this->ok(new CompetitionResource($competition));
    }

    /**
     * Update a competition's settings.
     */
    public function update(Request $request, Competition $competition): JsonResponse
    {
        $this->authorize('update', $competition->organization);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'organizer_contact' => ['nullable', 'string', 'max:255'],
            'entry_fee' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'season_id' => ['nullable', 'exists:seasons,id'],
            'registration_deadline' => ['nullable', 'date'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'max_teams' => ['nullable', 'integer', 'min:2', 'max:64'],
            'max_participants' => ['nullable', 'integer', 'min:2'],
            'is_public' => ['sometimes', 'boolean'],
            'registration_open' => ['sometimes', 'boolean'],
            'is_double_round' => ['sometimes', 'boolean'],
            'is_recreational' => ['sometimes', 'boolean'],
            'allow_rematches' => ['sometimes', 'boolean'],
            'sets_to_win' => ['sometimes', 'integer', 'min:1', 'max:7'],
            'points_per_set' => ['nullable', 'integer', 'min:7', 'max:21'],
            'deuce_at' => ['nullable', 'integer', 'min:5'],
            'must_win_by_two' => ['sometimes', 'boolean'],
            'points_for_win' => ['sometimes', 'integer', 'min:0', 'max:10'],
            'points_for_draw' => ['sometimes', 'integer', 'min:0', 'max:10'],
            'points_for_loss' => ['sometimes', 'integer', 'min:0', 'max:10'],
            'has_tiebreak' => ['sometimes', 'boolean'],
            'tiebreak_points' => ['nullable', 'integer', 'min:5', 'max:15'],
            'players_advancing_per_group' => ['nullable', 'integer', 'min:1', 'max:4'],
            'group_rounds' => ['nullable', 'integer', 'min:1', 'max:2'],
        ]);

        if (array_key_exists('season_id', $validated)) {
            $validated['season_id'] = $competition->organization->seasons()->where('id', $validated['season_id'])->exists()
                ? $validated['season_id']
                : null;
        }

        $competition->update($validated);

        return $this->ok(new CompetitionResource($competition->fresh(['organization', 'sport'])), 'Competition updated successfully');
    }

    /**
     * Delete a competition.
     */
    public function destroy(Competition $competition): JsonResponse
    {
        $this->authorize('update', $competition->organization);

        $competition->delete();

        return $this->ok(null, 'Competition deleted successfully');
    }

    /**
     * Start a draft competition - generates matches/standings depending on
     * competition type, mirroring CompetitionController::startCompetition.
     */
    public function start(Request $request, Competition $competition): JsonResponse
    {
        $this->authorize('update', $competition->organization);

        if ($competition->status !== 'draft') {
            return $this->fail('Competition has already started.', 422);
        }

        if ($competition->isTournament() && $competition->tournamentGroups()->count() === 0) {
            return $this->fail('Please setup groups before starting the tournament.', 422);
        }

        $currentPhase = $competition->isTournament() ? 'groups' : 'league';

        $competition->update([
            'status' => 'active',
            'current_phase' => $currentPhase,
        ]);

        $manualMatches = $request->boolean('manual_matches');

        if ($competition->isTournament()) {
            $competition->generateGroupMatches();
        } elseif ($competition->isLeague()) {
            if ($competition->is_team_based) {
                if (!$manualMatches) {
                    $competition->generateTeamMatches();
                } else {
                    foreach ($competition->teams as $team) {
                        Standing::firstOrCreate([
                            'competition_id' => $competition->id,
                            'team_id' => $team->id,
                        ], [
                            'played' => 0,
                            'won' => 0,
                            'drawn' => 0,
                            'lost' => 0,
                            'points' => 0,
                        ]);
                    }
                }
            } else {
                if (!$manualMatches) {
                    $competition->generateLeagueMatches();
                    $competition->generateLeagueStandings();
                }
            }
        }

        return $this->ok(new CompetitionResource($competition->fresh()), 'Competition started successfully');
    }

    /**
     * Complete a tournament competition.
     */
    public function complete(Competition $competition): JsonResponse
    {
        $this->authorize('update', $competition->organization);

        if (!$competition->isTournament()) {
            return $this->fail('This is not a tournament.', 422);
        }

        $competition->completeTournament();

        return $this->ok(new CompetitionResource($competition->fresh()), 'Competition completed successfully');
    }

    /**
     * Reset a competition back to draft status - deletes all matches,
     * groups and standings, mirroring CompetitionController::reset.
     */
    public function reset(Competition $competition): JsonResponse
    {
        $this->authorize('update', $competition->organization);

        $competition->matches()->delete();
        $competition->tournamentGroups()->delete();
        $competition->standings()->delete();

        $competition->update([
            'status' => 'draft',
            'current_phase' => 'groups',
            'groups_completed_at' => null,
        ]);

        return $this->ok(new CompetitionResource($competition->fresh()), 'Competition has been reset to draft status');
    }
}
