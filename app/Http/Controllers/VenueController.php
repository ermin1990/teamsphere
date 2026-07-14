<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Organization;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VenueController extends Controller
{
    public function index(Organization $organization)
    {
        Gate::authorize('update', $organization);

        $venues = $organization->venues()->with('city')->orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('organizations.venues.index', compact('organization', 'venues', 'cities'));
    }

    public function store(Request $request, Organization $organization)
    {
        Gate::authorize('update', $organization);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $organization->venues()->create([
            'name' => $request->name,
            'city_id' => $request->city_id,
            'address' => $request->address,
        ]);

        return redirect()->back()->with('success', 'Teren je dodan.');
    }

    public function update(Request $request, Organization $organization, Venue $venue)
    {
        Gate::authorize('update', $organization);
        abort_unless($venue->organization_id === $organization->id, 404);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $venue->update([
            'name' => $request->name,
            'city_id' => $request->city_id,
            'address' => $request->address,
        ]);

        return redirect()->back()->with('success', 'Teren je izmijenjen.');
    }

    public function destroy(Organization $organization, Venue $venue)
    {
        Gate::authorize('update', $organization);
        abort_unless($venue->organization_id === $organization->id, 404);

        $venue->delete();

        return redirect()->back()->with('success', 'Teren je obrisan.');
    }
}
