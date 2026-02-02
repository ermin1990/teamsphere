<?php

namespace App\Http\Controllers;

use App\Models\LeagueMatch;
use App\Models\Organization;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Get players where user is registered with eager loading
        $players = Player::where('user_id', $user->id)
            ->with(['organization', 'homeMatches.league.sport', 'awayMatches.league.sport'])
            ->get();

        // Get organization IDs from players and owned organizations
        $playerOrganizationIds = $players->pluck('organization_id')->unique()->filter();
        $ownedOrganizationIds = $user->organizations()->pluck('id');
        $allOrganizationIds = $playerOrganizationIds->merge($ownedOrganizationIds)->unique();

        // Load all organizations with eager loading for better performance
        $organizations = Organization::whereIn('id', $allOrganizationIds)
            ->with([
                'leagues' => function($query) {
                    $query->select('id', 'organization_id', 'name', 'status');
                },
                'competitions' => function($query) {
                    $query->select('id', 'organization_id', 'name', 'type', 'status');
                }
            ])
            ->get();

        // Get upcoming matches for this player with eager loading
        $upcomingMatches = collect();
        if ($players->count() > 0) {
            $playerIds = $players->pluck('id');
            $upcomingMatches = LeagueMatch::where(function($query) use ($playerIds) {
                $query->whereIn('home_player_id', $playerIds)
                      ->orWhereIn('away_player_id', $playerIds);
            })
            ->with(['league.sport', 'homePlayer:id,name', 'awayPlayer:id,name'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->limit(10)
            ->get();
        }

        // Check if user is a referee in any organization
        $isReferee = $user->organizationUsers()->where('role', 'referee')->exists();

        // Get current plan for usage statistics
        $currentPlan = $user->currentPlan();

        // Calculate player counts per organization
        $playerCountsByOrganization = [];
        foreach ($players as $player) {
            if ($player->organization_id) {
                $playerCountsByOrganization[$player->organization_id] = ($playerCountsByOrganization[$player->organization_id] ?? 0) + 1;
            }
        }

        // Calculate usage statistics
        $usageStats = [
            'organizations_used' => $user->organizations()->count(),
            'competitions_used' => \App\Models\Competition::whereIn('organization_id', $allOrganizationIds)->where('type', 'tournament')->count(),
            'leagues_used' => \App\Models\Competition::whereIn('organization_id', $allOrganizationIds)->where('type', 'league')->count(),
            'max_organizations' => $currentPlan ? $currentPlan->max_organizations : null,
            'max_competitions_per_organization' => $currentPlan ? $currentPlan->max_competitions_per_organization : null,
            'max_leagues_per_organization' => $currentPlan ? $currentPlan->max_leagues_per_organization : null,
        ];

        return view('dashboard', compact(
            'organizations',
            'players',
            'upcomingMatches',
            'isReferee',
            'currentPlan',
            'usageStats',
            'playerCountsByOrganization'
        ));
    }
}