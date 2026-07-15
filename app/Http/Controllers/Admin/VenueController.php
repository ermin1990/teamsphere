<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index()
    {
        $venues = Venue::with('city')->orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('admin.venues.index', compact('venues', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Venue::create($request->only('name', 'city_id', 'address'));

        return redirect()->route('admin.venues.index')->with('status', 'Teren je dodan.');
    }

    public function update(Request $request, Venue $venue)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $venue->update($request->only('name', 'city_id', 'address'));

        return redirect()->route('admin.venues.index')->with('status', 'Teren je izmijenjen.');
    }

    public function destroy(Venue $venue)
    {
        $venue->delete();

        return redirect()->route('admin.venues.index')->with('status', 'Teren je obrisan.');
    }
}
