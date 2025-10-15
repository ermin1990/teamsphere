<?php
namespace App\Http\Controllers;

use App\Models\League;
use Illuminate\Http\Request;

class AdminLeagueController extends Controller
{
    public function index()
    {
        $leagues = League::with(['organization.user', 'sport'])->latest()->paginate(20);
        return view('admin.leagues.index', compact('leagues'));
    }

    public function show(League $league)
    {
        $league->load(['organization.user', 'sport', 'matches.homeTeam', 'matches.awayTeam']);
        return view('admin.leagues.show', compact('league'));
    }
}
