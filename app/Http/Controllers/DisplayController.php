<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisplayController extends Controller
{
    /**
     * Show public league selector
     */
    public function selector()
    {
        // Get all competitions with live matches
        $competitions = \App\Models\Competition::whereHas('matches', function($query) {
            $query->where('status', 'in_progress');
        })
        ->with(['sport', 'organization'])
        ->withCount(['matches as live_matches_count' => function($query) {
            $query->where('status', 'in_progress');
        }])
        ->get();

        return view('display.selector', compact('competitions'));
    }

    /**
     * Show admin page for selecting leagues to display
     */
    public function admin()
    {
        // Get all leagues with live matches
        $leagues = League::whereHas('matches', function($query) {
            $query->where('status', 'in_progress');
        })
        ->with(['sport', 'organization'])
        ->withCount(['matches as live_matches_count' => function($query) {
            $query->where('status', 'in_progress');
        }])
        ->get();

        // Get currently selected leagues for display
        $selectedLeagueIds = DB::table('display_leagues')
            ->pluck('league_id')
            ->toArray();

        return view('display.admin', compact('leagues', 'selectedLeagueIds'));
    }

    /**
     * Show the display screen with selected leagues
     */
    public function show(Request $request)
    {
        $selectedCompetitionIds = [];
        
        // Check if competitions are passed via URL parameter (public selector)
        if ($request->has('competitions')) {
            $selectedCompetitionIds = explode(',', $request->query('competitions'));
            $selectedCompetitionIds = array_map('intval', $selectedCompetitionIds);
            \Log::info('Display - Selected competitions from URL:', $selectedCompetitionIds);
        } else if ($request->has('leagues')) {
            // Legacy support for leagues parameter
            $selectedCompetitionIds = explode(',', $request->query('leagues'));
            $selectedCompetitionIds = array_map('intval', $selectedCompetitionIds);
            \Log::info('Display - Selected competitions from leagues URL:', $selectedCompetitionIds);
        } else {
            // Fall back to admin-selected leagues from database
            $selectedLeagueIds = DB::table('display_leagues')
                ->orderBy('display_order')
                ->pluck('league_id')
                ->toArray();
            
            if (!empty($selectedLeagueIds)) {
                // Get competitions from those leagues
                $selectedCompetitionIds = \App\Models\Competition::whereIn('league_id', $selectedLeagueIds)
                    ->pluck('id')
                    ->toArray();
            }
            \Log::info('Display - Selected competitions from DB:', $selectedCompetitionIds);
        }

        // If no competitions selected, get all live competitions
        if (empty($selectedCompetitionIds)) {
            $selectedCompetitionIds = \App\Models\Competition::whereHas('matches', function($query) {
                $query->where('status', 'in_progress');
            })->pluck('id')->toArray();
            \Log::info('Display - All live competitions:', $selectedCompetitionIds);
        }

        $matches = LeagueMatch::whereIn('competition_id', $selectedCompetitionIds)
            ->where('status', 'in_progress')
            ->with([
                'league.sport',
                'competition',
                'homePlayer',
                'awayPlayer',
                'homeTeam',
                'awayTeam'
            ])
            ->orderBy('competition_id')
            ->orderBy('scheduled_at')
            ->get();

        \Log::info('Display - Found matches count:', ['count' => $matches->count()]);
        \Log::info('Display - Matches:', $matches->pluck('id')->toArray());

        return view('display.show', compact('matches'));
    }

    /**
     * Toggle league for display
     */
    public function toggleLeague(Request $request, League $league)
    {
        $exists = DB::table('display_leagues')
            ->where('league_id', $league->id)
            ->exists();

        if ($exists) {
            // Remove from display
            DB::table('display_leagues')
                ->where('league_id', $league->id)
                ->delete();
            
            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Liga uklonjena sa displaya'
            ]);
        } else {
            // Add to display
            DB::table('display_leagues')->insert([
                'league_id' => $league->id,
                'display_order' => DB::table('display_leagues')->max('display_order') + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => 'Liga dodana na display'
            ]);
        }
    }
}

