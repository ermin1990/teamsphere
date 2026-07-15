<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Competition;
use App\Models\CompetitionJoinRequest;
use App\Models\Sport;
use App\Services\CompetitionShowData;
use Illuminate\Http\Request;

class PlayerLeagueController extends Controller
{
    /**
     * Browse public competitions that are open for registration, with the
     * current user's application state (not applied / pending / already a
     * member) computed per competition so the view can render the right
     * call to action.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        $query = Competition::where('registration_open', true)
            ->whereIn('status', ['draft', 'active'])
            ->where(function ($q) {
                $q->whereNull('registration_deadline')
                  ->orWhere('registration_deadline', '>=', now());
            })
            ->with(['organization', 'sport', 'city', 'season']);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($sportId = $request->input('sport_id')) {
            $query->where('sport_id', $sportId);
        }

        if ($cityId = $request->input('city_id')) {
            $query->where('city_id', $cityId);
        }

        $competitions = $query->orderBy('name')->paginate(12)->withQueryString();
        $sports = Sport::active()->orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        $competitionIds = $competitions->pluck('id');

        $memberCompetitionIds = Competition::whereIn('id', $competitionIds)
            ->whereHas('players', function ($q) use ($userId) {
                $q->where('players.user_id', $userId);
            })
            ->pluck('id');

        $pendingCompetitionIds = CompetitionJoinRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereIn('competition_id', $competitionIds)
            ->pluck('competition_id');

        return view('player.leagues.index', compact('competitions', 'memberCompetitionIds', 'pendingCompetitionIds', 'sports', 'cities'));
    }

    /**
     * Authenticated view of a competition's standings/matches for its own
     * members - unlike the anonymous spectator route
     * (PublicMatchController::showLeague), this doesn't require
     * is_public: a player who's a member of a private league must still be
     * able to see their own results.
     */
    public function show(Competition $competition)
    {
        $isMember = $competition->players()->where('players.user_id', auth()->id())->exists();
        $isOwner = auth()->id() === $competition->organization->user_id;
        abort_unless($competition->is_public || $isMember || $isOwner, 404);

        ['playerGroupSeeding' => $playerGroupSeeding, 'playerPositionSeeding' => $playerPositionSeeding] = CompetitionShowData::load($competition);
        $organization = $competition->organization;

        return view('player.leagues.show', compact('competition', 'organization', 'playerGroupSeeding', 'playerPositionSeeding'));
    }

    /**
     * Submit a request to join a public, individual (non-team-based)
     * competition. The organizer approves/rejects it from "Upravljaj
     * igračima".
     */
    public function store(Request $request, Competition $competition)
    {
        abort_unless($competition->registration_open, 403, 'Ovo takmičenje nije otvoreno za prijave.');
        abort_if($competition->is_team_based, 403, 'Prijava je dostupna samo za pojedinačna takmičenja - za timska takmičenja kontaktiraj organizatora.');

        $userId = auth()->id();

        if ($competition->players()->where('players.user_id', $userId)->exists()) {
            return back()->with('error', 'Već si prijavljen na ovo takmičenje.');
        }

        if (CompetitionJoinRequest::where('competition_id', $competition->id)->where('user_id', $userId)->where('status', 'pending')->exists()) {
            return back()->with('error', 'Već imaš zahtjev na čekanju za ovo takmičenje.');
        }

        CompetitionJoinRequest::create([
            'competition_id' => $competition->id,
            'user_id' => $userId,
            'status' => 'pending',
            'message' => $request->input('message'),
        ]);

        return back()->with('success', 'Zahtjev za pridruživanje je poslan organizatoru.');
    }
}
