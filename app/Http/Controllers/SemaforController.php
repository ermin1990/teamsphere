<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\LeagueMatch;
use Illuminate\Http\Request;

class SemaforController extends Controller
{
    /**
     * Display the semafor page with live tournaments selection and display.
     */
    public function index(Request $request)
    {
        // Get all competitions that have live matches
        $liveCompetitions = Competition::whereHas('leagueMatches', function ($query) {
            $query->where('status', 'in_progress');
        })
        ->with(['organization', 'sport', 'leagueMatches' => function ($query) {
            $query->where('status', 'in_progress')
                  ->with(['homePlayer', 'awayPlayer', 'homeTeam', 'awayTeam']);
        }])
        ->get();

        // Get selected competition IDs from request (for display mode)
        $selectedCompetitionIds = $request->get('leagues', []);

        // Filter selected competitions if any are selected
        $selectedCompetitions = collect();
        if (!empty($selectedCompetitionIds)) {
            $selectedCompetitions = $liveCompetitions->whereIn('id', $selectedCompetitionIds);
        }

        return view('semafor', compact('liveCompetitions', 'selectedCompetitions', 'selectedCompetitionIds'));
    }
}