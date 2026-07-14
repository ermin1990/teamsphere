<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SeasonController extends Controller
{
    public function index(Organization $organization)
    {
        Gate::authorize('update', $organization);

        $seasons = $organization->seasons()->withCount('competitions')->orderByDesc('starts_at')->get();

        return view('organizations.seasons.index', compact('organization', 'seasons'));
    }

    public function store(Request $request, Organization $organization)
    {
        Gate::authorize('update', $organization);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_active')) {
            $organization->seasons()->update(['is_active' => false]);
        }

        $organization->seasons()->create([
            'name' => $request->name,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with('success', 'Sezona je dodana.');
    }

    public function update(Request $request, Organization $organization, Season $season)
    {
        Gate::authorize('update', $organization);
        abort_unless($season->organization_id === $organization->id, 404);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_active')) {
            $organization->seasons()->where('id', '!=', $season->id)->update(['is_active' => false]);
        }

        $season->update([
            'name' => $request->name,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with('success', 'Sezona je izmijenjena.');
    }

    public function destroy(Organization $organization, Season $season)
    {
        Gate::authorize('update', $organization);
        abort_unless($season->organization_id === $organization->id, 404);

        $season->delete();

        return redirect()->back()->with('success', 'Sezona je obrisana.');
    }
}
