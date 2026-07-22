<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements for the organization, optionally
     * filtered to a single competition.
     */
    public function index(Request $request, Organization $organization)
    {
        Gate::authorize('view', $organization);

        // 'competition_id' query values: empty/absent = show everything
        // (org-wide + every league), 'org' = only organization-wide
        // announcements, a numeric id = only that competition's.
        $filter = $request->query('competition_id');
        $onlyOrganizationWide = $filter === 'org';
        $competitionId = (!$onlyOrganizationWide && $filter) ? (int) $filter : null;

        $announcements = $organization->announcements()
            ->with(['competition', 'user'])
            ->when($onlyOrganizationWide, fn ($query) => $query->whereNull('competition_id'))
            ->when($competitionId, fn ($query) => $query->where('competition_id', $competitionId))
            ->latest()
            ->get();

        $competitions = $organization->competitions()->orderBy('name')->get();
        $selectedCompetition = $competitionId ? $competitions->firstWhere('id', $competitionId) : null;
        $canManage = Gate::allows('manage-announcements', $organization);

        return view('organizations.announcements.index', compact('organization', 'announcements', 'competitions', 'selectedCompetition', 'onlyOrganizationWide', 'canManage'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create(Request $request, Organization $organization)
    {
        Gate::authorize('manage-announcements', $organization);

        $competitions = $organization->competitions()->orderBy('name')->get();
        $selectedCompetitionId = $request->integer('competition_id') ?: null;

        return view('organizations.announcements.create', compact('organization', 'competitions', 'selectedCompetitionId'));
    }

    /**
     * Store a newly created announcement.
     */
    public function store(Request $request, Organization $organization)
    {
        Gate::authorize('manage-announcements', $organization);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'competition_id' => 'nullable|exists:competitions,id',
        ]);

        if (!empty($validated['competition_id'])) {
            $competition = $organization->competitions()->findOrFail($validated['competition_id']);
            $validated['competition_id'] = $competition->id;
        }

        $organization->announcements()->create([
            'competition_id' => $validated['competition_id'] ?? null,
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return redirect()->route('organizations.announcements.index', $organization)
            ->with('success', 'Obavijest je uspješno objavljena.');
    }

    /**
     * Show the form for editing an announcement.
     */
    public function edit(Organization $organization, Announcement $announcement)
    {
        Gate::authorize('manage-announcements', $organization);
        $this->ensureBelongsToOrganization($organization, $announcement);

        $competitions = $organization->competitions()->orderBy('name')->get();

        return view('organizations.announcements.edit', compact('organization', 'announcement', 'competitions'));
    }

    /**
     * Update an announcement.
     */
    public function update(Request $request, Organization $organization, Announcement $announcement)
    {
        Gate::authorize('manage-announcements', $organization);
        $this->ensureBelongsToOrganization($organization, $announcement);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'competition_id' => 'nullable|exists:competitions,id',
        ]);

        if (!empty($validated['competition_id'])) {
            $competition = $organization->competitions()->findOrFail($validated['competition_id']);
            $validated['competition_id'] = $competition->id;
        } else {
            $validated['competition_id'] = null;
        }

        $validated['is_featured'] = $request->boolean('is_featured');

        $announcement->update($validated);

        return redirect()->route('organizations.announcements.index', $organization)
            ->with('success', 'Obavijest je uspješno ažurirana.');
    }

    /**
     * Remove an announcement.
     */
    public function destroy(Organization $organization, Announcement $announcement)
    {
        Gate::authorize('manage-announcements', $organization);
        $this->ensureBelongsToOrganization($organization, $announcement);

        $announcement->delete();

        return redirect()->route('organizations.announcements.index', $organization)
            ->with('success', 'Obavijest je uspješno obrisana.');
    }

    private function ensureBelongsToOrganization(Organization $organization, Announcement $announcement): void
    {
        abort_if($announcement->organization_id !== $organization->id, 404);
    }
}
