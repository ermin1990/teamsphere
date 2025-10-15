<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\Organization;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerDashboardController extends Controller
{
    /**
     * Display player dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get players where user is registered
        $players = Player::where('user_id', $user->id)->with('organization')->get();

        // Get organizations from players
        $organizations = $players->pluck('organization')->unique();

        // Get recent matches for this player
        $recentMatches = collect();
        foreach ($players as $player) {
            // Get matches where player is home or away player
            $playerMatches = LeagueMatch::where(function($query) use ($player) {
                $query->where('home_player_id', $player->id)
                      ->orWhere('away_player_id', $player->id);
            })
            ->with(['league', 'homePlayer', 'awayPlayer'])
            ->where('scheduled_at', '>=', now()->subDays(30))
            ->orderBy('scheduled_at', 'desc')
            ->limit(10)
            ->get();

            $recentMatches = $recentMatches->merge($playerMatches);
        }

        return view('player.dashboard', compact('organizations', 'players', 'recentMatches'));
    }

    /**
     * Display player's organizations.
     */
    public function organizations()
    {
        $user = Auth::user();

        // Get players where user is registered
        $players = Player::where('user_id', $user->id)->with('organization')->get();

        // Get organizations from players
        $organizations = $players->pluck('organization')->unique();

        return view('player.organizations', compact('organizations', 'players'));
    }

    /**
     * Display matches for a specific organization.
     */
    public function organizationMatches(Player $player)
    {
        $user = Auth::user();

        // Ensure the player belongs to the authenticated user
        if ($player->user_id !== $user->id) {
            abort(403, 'You are not authorized to view this player profile.');
        }

        // Get matches for this player in this organization
        $matches = LeagueMatch::where(function($query) use ($player) {
            $query->where('home_player_id', $player->id)
                  ->orWhere('away_player_id', $player->id);
        })
        ->whereHas('league', function($query) use ($player) {
            $query->where('organization_id', $player->organization_id);
        })
        ->with(['league', 'homePlayer', 'awayPlayer'])
        ->orderBy('scheduled_at', 'desc')
        ->paginate(20);

        $organization = $player->organization;

        return view('player.organization-matches', compact('organization', 'player', 'matches'));
    }

    /**
     * Show match details for player.
     */
    public function showMatch(Request $request, Organization $organization, $leagueSlug, LeagueMatch $match)
    {
        $user = Auth::user();

        // Find player's record in this organization
        $player = Player::where('user_id', $user->id)
            ->where('organization_id', $organization->id)
            ->first();

        if (!$player) {
            abort(403, 'You are not a player in this organization.');
        }

        // Ensure match involves this player
        if ($match->home_player_id !== $player->id && $match->away_player_id !== $player->id) {
            abort(403, 'You are not authorized to view this match.');
        }

        // Find the league by slug
        $league = League::where('slug', $leagueSlug)
            ->where('organization_id', $organization->id)
            ->first();

        if (!$league) {
            abort(404, 'League not found.');
        }

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404, 'Match not found in this league.');
        }

        // Load teams with players for team-based leagues
        if ($league->is_team_based) {
            $match->load(['homeTeam.players', 'awayTeam.players']);
        }

        return view('organizations.leagues.matches.show', compact('organization', 'league', 'match'));
    }
}
