<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueMatch;
use Illuminate\Http\Request;

class PublicMatchController extends Controller
{
    /**
     * Display public match details.
     */
    public function showMatch(League $league, LeagueMatch $match)
    {
        // Ensure match belongs to league
        if ($match->competition_id !== $league->id) {
            abort(404, 'Match not found.');
        }

        // Load necessary relationships
        $match->load([
            'homeTeam.players',
            'awayTeam.players',
            'homePlayer',
            'awayPlayer',
            'league.organization',
            'moderator'
        ]);

        $organization = $league->organization;

        return view('public.matches.show', compact('organization', 'league', 'match'));
    }

    /**
     * Display public live score for a match.
     */
    public function liveScore(League $league, LeagueMatch $match)
    {
        // Ensure match belongs to league
        if ($match->competition_id !== $league->id) {
            abort(404, 'Match not found.');
        }

        // Load necessary relationships
        $match->load([
            'homeTeam.players',
            'awayTeam.players',
            'homePlayer',
            'awayPlayer',
            'league.organization',
            'moderator'
        ]);

        $organization = $league->organization;

        return view('public.matches.live', compact('organization', 'league', 'match'));
    }

    /**
     * Display public league overview.
     */
    public function showLeague(League $league)
    {
        $organization = $league->organization;

        // Load league with matches and standings
        $league->load([
            'sport',
            'matches.homeTeam',
            'matches.awayTeam',
            'matches.homePlayer',
            'matches.awayPlayer',
            'standings'
        ]);

        return view('public.leagues.show', compact('organization', 'league'));
    }

    /**
     * Display all live matches across all leagues.
     */
    public function liveMatches()
    {
        // Get all live matches from all leagues
        $liveMatches = LeagueMatch::where('status', 'in_progress')
            ->with(['league.organization', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer', 'moderator'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('public.live-matches', compact('liveMatches'));
    }
}