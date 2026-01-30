<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrganizationLinkController extends Controller
{
    public function index(Organization $organization)
    {
        Gate::authorize('update', $organization);
        
        $links = $organization->links;
        return view('organizations.links.index', compact('organization', 'links'));
    }

    public function store(Request $request, Organization $organization)
    {
        Gate::authorize('update', $organization);

        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:255',
        ]);

        $url = strtolower($request->url);
        $type = 'other';
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            $type = 'youtube';
        } elseif (str_contains($url, 'facebook.com')) {
            $type = 'facebook';
        } elseif (str_contains($url, 'instagram.com')) {
            $type = 'instagram';
        }

        $organization->links()->create([
            'title' => $request->title,
            'url' => $request->url,
            'type' => $type,
            'sort_order' => $organization->links()->count() + 1,
        ]);

        return redirect()->back()->with('success', 'Link je uspješno dodan.');
    }

    public function destroy(Organization $organization, OrganizationLink $link)
    {
        Gate::authorize('update', $organization);
        
        if ($link->organization_id !== $organization->id) {
            abort(403);
        }

        $link->delete();

        return redirect()->back()->with('success', 'Link je uspješno obrisan.');
    }
}
