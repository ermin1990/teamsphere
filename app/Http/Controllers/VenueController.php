<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VenueController extends Controller
{
    /**
     * List the venues owned by the current user.
     */
    public function index()
    {
        $venues = auth()->user()->venues()->with('city')->get();

        return view('venues.index', compact('venues'));
    }

    /**
     * Show the form for registering a new venue.
     */
    public function create()
    {
        $cities = City::orderBy('name')->get();

        return view('venues.create', compact('cities'));
    }

    /**
     * Store a newly registered venue, owned by the current user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'url_slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:venues,slug',
                'regex:/^[a-z0-9-]+$/',
            ],
        ]);

        $venue = Venue::create([
            'name' => $validated['name'],
            'slug' => $validated['url_slug'] ?? $this->uniqueSlug($validated['name']),
            'description' => $validated['description'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'address' => $validated['address'] ?? null,
            'contact_email' => $validated['contact_email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'website' => $validated['website'] ?? null,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('venues.edit', $venue)->with('success', 'Teren je uspješno registrovan!');
    }

    /**
     * Show the form for editing the venue's own profile.
     */
    public function edit(Venue $venue)
    {
        Gate::authorize('update', $venue);

        $cities = City::orderBy('name')->get();

        return view('venues.edit', compact('venue', 'cities'));
    }

    /**
     * Update the venue's profile, including an optional logo upload.
     */
    public function update(Request $request, Venue $venue)
    {
        Gate::authorize('update', $venue);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                'unique:venues,slug,' . $venue->id,
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('logo')) {
            if ($venue->logo) {
                Storage::disk('public')->delete($venue->logo);
            }
            $validated['logo'] = $request->file('logo')->store('venues', 'public');
        }

        $venue->update($validated);

        return redirect()->route('venues.edit', $venue)->with('success', 'Teren je ažuriran.');
    }

    /**
     * Generate a unique slug from the venue name, following the same
     * regex/uniqueness convention as Organization::url_slug.
     */
    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Venue::where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }
}
