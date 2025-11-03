<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\LeagueMatch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MatchController extends Controller
{
    /**
     * Display a listing of public matches.
     */
    public function index(): JsonResponse
    {
        $matches = LeagueMatch::with(['league.organization', 'league.sport', 'homeTeam', 'awayTeam'])
            ->whereHas('league', function ($query) {
                $query->where('is_public', true);
            })
            ->orderBy('scheduled_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $matches,
            'message' => 'Matches retrieved successfully'
        ]);
    }

    /**
     * Display the specified match.
     */
    public function show(LeagueMatch $match): JsonResponse
    {
        // Check if match's league is public or user has access
        if (!$match->league->is_public && (!auth()->check() || !auth()->user()->canAccessLeague($match->league))) {
            return response()->json([
                'success' => false,
                'message' => 'Match not found or access denied'
            ], 404);
        }

        $match->load(['league.organization', 'league.sport', 'homeTeam', 'awayTeam']);

        return response()->json([
            'success' => true,
            'data' => $match,
            'message' => 'Match retrieved successfully'
        ]);
    }

    /**
     * Get matches for a specific league.
     */
    public function leagueMatches(League $league): JsonResponse
    {
        // Check if user can access league
        if (!auth()->user()->canAccessLeague($league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $matches = $league->matches()
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('scheduled_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $matches,
            'message' => 'League matches retrieved successfully'
        ]);
    }

    /**
     * Store a newly created match.
     */
    public function store(Request $request, League $league): JsonResponse
    {
        // Check if user can access league
        if (!auth()->user()->canAccessLeague($league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validated = $request->validate([
            'home_team_id' => 'required|exists:teams,id',
            'away_team_id' => 'required|exists:teams,id|different:home_team_id',
            'scheduled_at' => 'required|date|after:now',
            'venue' => 'nullable|string|max:255',
            'referee_id' => 'nullable|exists:users,id',
        ]);

        $match = $league->matches()->create($validated);

        return response()->json([
            'success' => true,
            'data' => $match->load(['homeTeam', 'awayTeam', 'league']),
            'message' => 'Match created successfully'
        ], 201);
    }

    /**
     * Update the specified match.
     */
    public function update(Request $request, LeagueMatch $match): JsonResponse
    {
        // Check if user can access match's league
        if (!auth()->user()->canAccessLeague($match->league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validated = $request->validate([
            'home_team_id' => 'required|exists:teams,id',
            'away_team_id' => 'required|exists:teams,id|different:home_team_id',
            'scheduled_at' => 'required|date',
            'venue' => 'nullable|string|max:255',
            'referee_id' => 'nullable|exists:users,id',
            'status' => 'in:scheduled,in_progress,completed,cancelled',
        ]);

        $match->update($validated);

        return response()->json([
            'success' => true,
            'data' => $match->load(['homeTeam', 'awayTeam', 'league']),
            'message' => 'Match updated successfully'
        ]);
    }

    /**
     * Update match score.
     */
    public function updateScore(Request $request, LeagueMatch $match): JsonResponse
    {
        // Check if user can access match's league
        if (!auth()->user()->canAccessLeague($match->league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validated = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        $match->update($validated);

        return response()->json([
            'success' => true,
            'data' => $match->load(['homeTeam', 'awayTeam']),
            'message' => 'Match score updated successfully'
        ]);
    }

    /**
     * Update match status.
     */
    public function updateStatus(Request $request, LeagueMatch $match): JsonResponse
    {
        // Check if user can access match's league
        if (!auth()->user()->canAccessLeague($match->league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $match->update($validated);

        return response()->json([
            'success' => true,
            'data' => $match,
            'message' => 'Match status updated successfully'
        ]);
    }

    /**
     * Remove the specified match.
     */
    public function destroy(LeagueMatch $match): JsonResponse
    {
        // Check if user can access match's league
        if (!auth()->user()->canAccessLeague($match->league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $match->delete();

        return response()->json([
            'success' => true,
            'message' => 'Match deleted successfully'
        ]);
    }
}