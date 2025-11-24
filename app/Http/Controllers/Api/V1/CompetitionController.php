<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CompetitionResource;
use App\Models\Competition;
use App\Models\Organization;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompetitionController extends Controller
{
    /**
     * Display a listing of competitions for an organization (public access).
     */
    public function publicIndex($organizationId): JsonResponse
    {
        $organization = Organization::findOrFail($organizationId);

        // For now, allow access to all organizations (since is_public column doesn't exist)
        // TODO: Add is_public column to organizations table

        $competitions = Competition::where('organization_id', $organization->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CompetitionResource::collection($competitions),
            'message' => 'Organization competitions retrieved successfully'
        ]);
    }

    /**
     * Display a listing of competitions for an organization.
     */
    public function index(Organization $organization): JsonResponse
    {
        // Check if user can access organization
        if (!auth()->user()->canAccessOrganization($organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $competitions = $organization->competitions()
            ->with(['sport'])
            ->withCount(['groups', 'players', 'matches'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CompetitionResource::collection($competitions),
            'message' => 'Competitions retrieved successfully'
        ]);
    }

    /**
     * Display the specified competition.
     */
    public function show(Competition $competition): JsonResponse
    {
        // Check if user can access competition's organization
        if (!auth()->user()->canAccessOrganization($competition->organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $competition->load([
            'organization',
            'sport',
            'groups.players',
            'knockoutMatches.homeTeam',
            'knockoutMatches.awayTeam',
            'knockoutMatches.homePlayer',
            'knockoutMatches.awayPlayer'
        ]);

        $competition->loadCount(['groups', 'players', 'matches']);

        return response()->json([
            'success' => true,
            'data' => new CompetitionResource($competition),
            'message' => 'Competition retrieved successfully'
        ]);
    }

    /**
     * Store a newly created competition.
     */
    public function store(Request $request, Organization $organization): JsonResponse
    {
        // Check if user can access organization
        if (!auth()->user()->canAccessOrganization($organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        // Check if user can create more competitions
        if (!auth()->user()->canCreateMoreCompetitions($organization->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached the maximum number of competitions allowed for this organization.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport_id' => 'required|exists:sports,id',
            'description' => 'nullable|string',
            'is_team_based' => 'boolean',
            'max_teams' => 'nullable|integer|min:2|max:64',
            'max_players_per_team' => 'nullable|integer|min:1|max:20',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $competition = $organization->competitions()->create($validated);

        return response()->json([
            'success' => true,
            'data' => new CompetitionResource($competition->load(['organization', 'sport'])),
            'message' => 'Competition created successfully'
        ], 201);
    }

    /**
     * Update the specified competition.
     */
    public function update(Request $request, Competition $competition): JsonResponse
    {
        // Check if user can access competition's organization
        if (!auth()->user()->canAccessOrganization($competition->organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport_id' => 'required|exists:sports,id',
            'description' => 'nullable|string',
            'is_team_based' => 'boolean',
            'max_teams' => 'nullable|integer|min:2|max:64',
            'max_players_per_team' => 'nullable|integer|min:1|max:20',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $competition->update($validated);

        return response()->json([
            'success' => true,
            'data' => new CompetitionResource($competition->load(['organization', 'sport'])),
            'message' => 'Competition updated successfully'
        ]);
    }

    /**
     * Remove the specified competition.
     */
    public function destroy(Competition $competition): JsonResponse
    {
        // Check if user can access competition's organization
        if (!auth()->user()->canAccessOrganization($competition->organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $competition->delete();

        return response()->json([
            'success' => true,
            'message' => 'Competition deleted successfully'
        ]);
    }

    /**
     * Add player to competition.
     */
    public function addPlayer(Request $request, Competition $competition): JsonResponse
    {
        // Check if user can access competition's organization
        if (!auth()->user()->canAccessOrganization($competition->organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
        ]);

        $competition->players()->attach($validated['player_id']);

        return response()->json([
            'success' => true,
            'message' => 'Player added to competition successfully'
        ]);
    }

    /**
     * Remove player from competition.
     */
    public function removePlayer(Competition $competition, Player $player): JsonResponse
    {
        // Check if user can access competition's organization
        if (!auth()->user()->canAccessOrganization($competition->organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $competition->players()->detach($player->id);

        return response()->json([
            'success' => true,
            'message' => 'Player removed from competition successfully'
        ]);
    }

    /**
     * Start competition.
     */
    public function start(Competition $competition): JsonResponse
    {
        // Check if user can access competition's organization
        if (!auth()->user()->canAccessOrganization($competition->organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        if ($competition->status !== 'setup') {
            return response()->json([
                'success' => false,
                'message' => 'Competition is not in setup status'
            ], 400);
        }

        $competition->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'data' => $competition,
            'message' => 'Competition started successfully'
        ]);
    }

    /**
     * Complete competition.
     */
    public function complete(Competition $competition): JsonResponse
    {
        // Check if user can access competition's organization
        if (!auth()->user()->canAccessOrganization($competition->organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        if ($competition->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Competition is not active'
            ], 400);
        }

        $competition->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'data' => $competition,
            'message' => 'Competition completed successfully'
        ]);
    }

    /**
     * Reset competition.
     */
    public function reset(Competition $competition): JsonResponse
    {
        // Check if user can access competition's organization
        if (!auth()->user()->canAccessOrganization($competition->organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $competition->reset();

        return response()->json([
            'success' => true,
            'data' => $competition,
            'message' => 'Competition reset successfully'
        ]);
    }
}