<?php
namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;

class AdminLeagueController extends Controller
{
    public function index(Request $request)
    {
        $query = Competition::with(['organization', 'sport']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Grouped-by-organization view works best unpaginated - bulk
        // selection needs every matching row on screen at once anyway.
        $leagues = $query
            ->join('organizations', 'organizations.id', '=', 'competitions.organization_id')
            ->orderBy('organizations.name')
            ->orderBy('competitions.name')
            ->select('competitions.*')
            ->get()
            ->groupBy(fn ($competition) => $competition->organization->name);

        $total = $leagues->sum->count();

        return view('admin.leagues.index', compact('leagues', 'total'));
    }

    public function show(Competition $league)
    {
        $league->load(['organization.user', 'sport', 'city']);

        return view('admin.leagues.show', compact('league'));
    }

    /**
     * Admin override: mark a league/tournament as no longer active, for
     * cases where the organizer running it hasn't closed it themselves.
     * Does not touch matches/standings - only stops it from being counted
     * as active going forward.
     */
    public function close(Competition $league)
    {
        abort_if($league->status === 'completed', 400, 'Ovo takmičenje je već zatvoreno.');

        $league->update([
            'status' => 'completed',
            'registration_open' => false,
        ]);

        return back()->with('success', 'Takmičenje "' . $league->name . '" je zatvoreno (više nije aktivno).');
    }

    /**
     * Same as close(), but for several selected competitions at once (the
     * checkbox-based bulk action on the admin leagues list).
     */
    public function bulkClose(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:competitions,id'],
        ]);

        $count = Competition::whereIn('id', $request->ids)
            ->where('status', '!=', 'completed')
            ->update(['status' => 'completed', 'registration_open' => false]);

        return back()->with('success', $count . ' ' . ($count === 1 ? 'takmičenje je zatvoreno' : 'takmičenja je zatvoreno') . '.');
    }
}
