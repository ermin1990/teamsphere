<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::withCount(['competitions', 'venues'])->orderBy('name')->get();

        return view('admin.cities.index', compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        City::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(4),
        ]);

        return redirect()->route('admin.cities.index')->with('status', 'Grad je dodan!');
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $city->update(['name' => $request->name]);

        return redirect()->route('admin.cities.index')->with('status', 'Grad je izmijenjen!');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('admin.cities.index')->with('status', 'Grad je obrisan.');
    }
}
