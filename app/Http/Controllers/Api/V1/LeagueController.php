<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\LeagueResource;
use App\Models\League;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeagueController extends Controller
{
    /**
     * Display a listing of public leagues.
     */
    public function index(): JsonResponse
    {
        $leagues = League::with(['organization', 'sport'])
            ->withCount(['matches', 'players'])
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => LeagueResource::collection($leagues),
            'message' => 'Leagues retrieved successfully'
        ]);
    }

    /**
     * Display the specified league.
     */
    public function show(League $league): JsonResponse
    {
        // Check if league is public or user has access
        if (!$league->is_public && (!auth()->check() || !auth()->user()->canAccessLeague($league))) {
            return response()->json([
                'success' => false,
                'message' => 'League not found or access denied'
            ], 404);
        }

        $league->load(['organization', 'sport', 'matches.homeTeam', 'matches.awayTeam', 'players']);
        $league->loadCount(['matches', 'players']);

        return response()->json([
            'success' => true,
            'data' => new LeagueResource($league),
            'message' => 'League retrieved successfully'
        ]);
    }

    /**
     * Get leagues for a specific organization.
     */
    public function organizationLeagues(Organization $organization): JsonResponse
    {
        // Check if user can access organization
        if (!auth()->user()->canAccessOrganization($organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $leagues = $organization->leagues()
            ->with(['sport'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $leagues,
            'message' => 'Organization leagues retrieved successfully'
        ]);
    }

    /**
     * Store a newly created league.
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport_id' => 'required|exists:sports,id',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $league = $organization->leagues()->create($validated);

        return response()->json([
            'success' => true,
            'data' => $league->load(['organization', 'sport']),
            'message' => 'League created successfully'
        ], 201);
    }

    /**
     * Update the specified league.
     */
    public function update(Request $request, League $league): JsonResponse
    {
        // Check if user can access league
        if (!auth()->user()->canAccessLeague($league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport_id' => 'required|exists:sports,id',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $league->update($validated);

        return response()->json([
            'success' => true,
            'data' => $league->load(['organization', 'sport']),
            'message' => 'League updated successfully'
        ]);
    }

    /**
     * Remove the specified league.
     */
    public function destroy(League $league): JsonResponse
    {
        // Check if user can access league
        if (!auth()->user()->canAccessLeague($league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $league->delete();

        return response()->json([
            'success' => true,
            'message' => 'League deleted successfully'
        ]);
    }
}