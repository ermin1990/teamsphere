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
     * Embed widget for match results.
     */
    public function embedMatch(LeagueMatch $match)
    {
        // Load necessary relationships
        $match->load([
            'homeTeam.players',
            'awayTeam.players',
            'homePlayer',
            'awayPlayer',
            'league.organization',
            'moderator'
        ]);

        return view('public.matches.embed', compact('match'));
    }
}