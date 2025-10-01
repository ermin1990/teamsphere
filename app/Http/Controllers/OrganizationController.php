<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
{
    /**
     * Show the form for creating a new organization.
     */
    public function create()
    {
        return view('organizations.create');
    }

    /**
     * Store a newly created organization.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:organizations,slug'],
        ]);

        // Check if user can create more organizations
        if (!auth()->user()->canCreateMoreOrganizations()) {
            return back()->withErrors(['error' => __('You have reached the maximum number of organizations for your plan.')]);
        }

        $slug = $request->slug ?: Str::slug($request->name);

        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (Organization::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Organization::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'user_id' => auth()->id(),
            'is_active' => true,
        ]);

        return redirect()->route('dashboard')->with('success', __('Organization created successfully!'));
    }

    /**
     * Display the specified organization.
     */
    public function show(Organization $organization)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        return view('organizations.show', compact('organization'));
    }

    /**
     * Show the form for editing the specified organization.
     */
    public function edit(Organization $organization)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        return view('organizations.edit', compact('organization'));
    }

    /**
     * Update the specified organization.
     */
    public function update(Request $request, Organization $organization)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('organizations')->ignore($organization->id)],
        ]);

        $organization->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
        ]);

        return redirect()->route('organizations.show', $organization)->with('success', __('Organization updated successfully!'));
    }

    /**
     * Remove the specified organization.
     */
    public function destroy(Organization $organization)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if organization has leagues
        if ($organization->leagues()->count() > 0) {
            return back()->withErrors(['error' => __('Cannot delete organization with existing leagues.')]);
        }

        $organization->delete();

        return redirect()->route('dashboard')->with('success', __('Organization deleted successfully!'));
    }
}
