<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\FriendlyMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
{
    // ...existing code...

    public function showFriendlyMatch(Organization $organization, FriendlyMatch $match)
    {
        // Ensure user is a member of this organization
        $isMember = $organization->user_id === auth()->id() || 
                   auth()->user()->organizationUsers()->where('organization_id', $organization->id)->exists();
        
        if (!$isMember) {
            abort(403);
        }

        // For doubles, split names if needed
        if (str_contains($match->home_player_name, ' / ')) {
            [$match->home_player_name, $match->home_player2_name] = explode(' / ', $match->home_player_name);
        } else {
            $match->home_player2_name = null;
        }
        if (str_contains($match->away_player_name, ' / ')) {
            [$match->away_player_name, $match->away_player2_name] = explode(' / ', $match->away_player_name);
        } else {
            $match->away_player2_name = null;
        }
        return view('organizations.friendly-matches.show', [
            'organization' => $organization,
            'match' => $match,
        ]);
    }


    /**
     * Display the specified organization.
     */
    public function show(Organization $organization)
    {
        // Allow access if user owns the organization OR is registered as a player in it OR is a referee
        $isOwner = $organization->user_id === auth()->id();
        $isPlayer = $organization->players()->where('user_id', auth()->id())->exists();
        $isReferee = auth()->user()->organizationUsers()
            ->where('organization_id', $organization->id)
            ->where('role', 'referee')
            ->exists();

        if (!$isOwner && !$isPlayer && !$isReferee) {
            abort(403);
        }

        $organization->load(['leagues', 'competitions.sport', 'players', 'user']);

        return view('organizations.show', compact('organization', 'isOwner', 'isPlayer', 'isReferee'));
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

    /**
     * Show friendly matches index for the organization.
     */
    public function friendlyMatches(Organization $organization)
    {
        $isOwner = $organization->user_id === auth()->id();

        \Log::info('Friendly matches accessed', [
            'organization_id' => $organization->id,
            'organization_slug' => $organization->slug,
            'auth_user_id' => auth()->id()
        ]);

        return view('organizations.friendly-matches.index', compact('organization', 'isOwner'));
    }

    /**
     * Show table tennis friendly match interface.
     */
    public function tableTennisFriendly(Organization $organization)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        return view('organizations.friendly-matches.table-tennis', compact('organization'));
    }

    /**
     * Display a listing of organizations.
     */
    public function index()
    {
        $organizations = auth()->user()->organizations;

        return view('organizations.index', compact('organizations'));
    }

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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'url_slug' => [
                'required',
                'string',
                'max:255',
                'unique:organizations,slug',
                'regex:/^[a-z0-9-]+$/'
            ],
        ]);

        $organization = Organization::create([
            'name' => $request->name,
            'slug' => $request->url_slug,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);

        // Clear organization cache
        Organization::clearOrganizationCache();

        return redirect()->route('organizations.show', $organization)
            ->with('success', 'Organization created successfully!');
    }
}
