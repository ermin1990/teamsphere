<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PlayerController extends Controller
{
    /**
     * Display a listing of public players.
     */
    public function index(): JsonResponse
    {
        $players = Player::with(['user', 'leagues.organization', 'leagues.sport'])
            ->whereHas('leagues', function ($query) {
                $query->where('is_public', true);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $players,
            'message' => 'Players retrieved successfully'
        ]);
    }

    /**
     * Display the specified player.
     */
    public function show(Player $player): JsonResponse
    {
        // Check if player's leagues are public or user has access
        $hasAccess = $player->leagues->contains(function ($league) {
            return $league->is_public || (auth()->check() && auth()->user()->canAccessLeague($league));
        });

        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found or access denied'
            ], 404);
        }

        $player->load(['user', 'leagues.organization', 'leagues.sport']);

        return response()->json([
            'success' => true,
            'data' => $player,
            'message' => 'Player retrieved successfully'
        ]);
    }

    /**
     * Get players for a specific league.
     */
    public function leaguePlayers(League $league): JsonResponse
    {
        // Check if user can access league
        if (!auth()->user()->canAccessLeague($league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $players = $league->players()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $players,
            'message' => 'League players retrieved successfully'
        ]);
    }

    /**
     * Store a newly created player.
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
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:players,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'user_id' => 'nullable|exists:users,id|unique:players,user_id',
        ]);

        $player = $league->players()->create($validated);

        return response()->json([
            'success' => true,
            'data' => $player->load(['user']),
            'message' => 'Player created successfully'
        ], 201);
    }

    /**
     * Update the specified player.
     */
    public function update(Request $request, Player $player): JsonResponse
    {
        // Check if user can access player's leagues
        $hasAccess = $player->leagues->contains(function ($league) {
            return auth()->user()->canAccessLeague($league);
        });

        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:players,email,' . $player->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'user_id' => 'nullable|exists:users,id|unique:players,user_id,' . $player->id,
        ]);

        $player->update($validated);

        return response()->json([
            'success' => true,
            'data' => $player->load(['user']),
            'message' => 'Player updated successfully'
        ]);
    }

    /**
     * Remove the specified player.
     */
    public function destroy(Player $player): JsonResponse
    {
        // Check if user can access player's leagues
        $hasAccess = $player->leagues->contains(function ($league) {
            return auth()->user()->canAccessLeague($league);
        });

        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $player->delete();

        return response()->json([
            'success' => true,
            'message' => 'Player deleted successfully'
        ]);
    }
}