<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\League;
use App\Models\LeagueMatch;
use Illuminate\Http\Request;

class PublicMatchController extends Controller
{
    /**
     * Display all public leagues with standings, results and upcoming matches.
     */
    public function indexLeagues()
    {
        // Get all public competitions with basic info
        $competitions = Competition::where('type', 'league')
            ->where(function($query) {
                $query->whereJsonContains('settings->is_public', true)
                      ->orWhereRaw("JSON_EXTRACT(settings, '$.is_public') IS NULL"); // For backward compatibility
            })
            ->with(['organization', 'sport'])
            ->get();

        return view('public.leagues.index', compact('competitions'));
    }

    /**
     * Display public match details.
     */
    public function showMatch(Competition $competition, LeagueMatch $match)
    {
        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404, 'Match not found.');
        }

        // Load necessary relationships
        $match->load([
            'homeTeam.players',
            'awayTeam.players',
            'homePlayer',
            'awayPlayer',
            'competition.organization',
            'moderator'
        ]);

        $organization = $competition->organization;

        return view('public.matches.show', compact('organization', 'competition', 'match'));
    }

    /**
     * Display public live score for a match.
     */
    public function liveScore(Competition $competition, LeagueMatch $match)
    {
        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404, 'Match not found.');
        }

        // Load necessary relationships
        $match->load([
            'homeTeam.players',
            'awayTeam.players',
            'homePlayer',
            'awayPlayer',
            'competition.organization',
            'moderator'
        ]);

        $organization = $competition->organization;

        return view('public.matches.live', compact('organization', 'competition', 'match'));
    }

    /**
     * Display public league overview.
     */
    public function showLeague(Competition $competition)
    {
        $organization = $competition->organization;

        // Load competition with matches and standings
        $competition->load([
            'sport',
            'matches.homeTeam',
            'matches.awayTeam',
            'matches.homePlayer',
            'matches.awayPlayer',
            'standings' => function($query) {
                $query->with(['team', 'player'])
                      ->orderBy('position', 'asc');
            }
        ]);

        return view('public.leagues.show', compact('organization', 'competition'));
    }

    /**
     * Display all live matches across all leagues.
     */
    public function liveMatches()
    {
        // Get all live matches from all leagues
        $liveMatches = LeagueMatch::where('status', 'in_progress')
            ->with(['competition.organization', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer', 'moderator'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('public.live-matches', compact('liveMatches'));
    }
}