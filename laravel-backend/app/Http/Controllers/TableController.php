<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TableController extends Controller
{
    /**
     * Display a listing of tables for the organization.
     */
    public function index(Organization $organization)
    {
        // Check if user has access to this organization
        if ($organization->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to organization');
        }

        $tables = Table::where('organization_id', $organization->id)
            ->orderBy('name')
            ->get();

        return view('tables.index', compact('organization', 'tables'));
    }

    /**
     * Display table schedule with all assigned matches.
     */
    public function schedule(Organization $organization)
    {
        // Check if user has access to this organization
        if ($organization->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to organization');
        }

        $tables = Table::where('organization_id', $organization->id)
            ->where('is_active', true)
            ->with([
                'leagueMatches' => function ($query) {
                    $query->with(['homePlayer', 'awayPlayer', 'league'])
                        ->whereIn('status', ['scheduled', 'in_progress'])
                        ->orderBy('scheduled_at');
                },
                'competitionMatches' => function ($query) {
                    $query->with(['homePlayer', 'awayPlayer', 'competition'])
                        ->whereIn('status', ['scheduled', 'in_progress'])
                        ->orderBy('round');
                }
            ])
            ->orderBy('name')
            ->get();

        return view('tables.schedule', compact('organization', 'tables'));
    }

    /**
     * Show the form for creating a new table.
     */
    public function create(Organization $organization)
    {
        // Check if user has access to this organization
        if ($organization->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to organization');
        }

        return view('tables.create', compact('organization'));
    }

    /**
     * Store a newly created table in storage.
     */
    public function store(Request $request, Organization $organization)
    {
        // Check if user has access to this organization
        if ($organization->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to organization');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['organization_id'] = $organization->id;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        Table::create($validated);

        return redirect()
            ->route('organizations.tables.index', $organization)
            ->with('success', 'Sto je uspješno kreiran!');
    }

    /**
     * Show the form for editing the specified table.
     */
    public function edit(Organization $organization, Table $table)
    {
        // Check if user has access to this organization
        if ($organization->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to organization');
        }

        // Check if table belongs to this organization
        if ($table->organization_id !== $organization->id) {
            abort(403, 'This table does not belong to this organization');
        }

        return view('tables.edit', compact('organization', 'table'));
    }

    /**
     * Update the specified table in storage.
     */
    public function update(Request $request, Organization $organization, Table $table)
    {
        // Check if user has access to this organization
        if ($organization->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to organization');
        }

        // Check if table belongs to this organization
        if ($table->organization_id !== $organization->id) {
            abort(403, 'This table does not belong to this organization');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $table->update($validated);

        return redirect()
            ->route('organizations.tables.index', $organization)
            ->with('success', 'Sto je uspješno ažuriran!');
    }

    /**
     * Remove the specified table from storage.
     */
    public function destroy(Organization $organization, Table $table)
    {
        // Check if user has access to this organization
        if ($organization->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to organization');
        }

        // Check if table belongs to this organization
        if ($table->organization_id !== $organization->id) {
            abort(403, 'This table does not belong to this organization');
        }

        $table->delete();

        return redirect()
            ->route('organizations.tables.index', $organization)
            ->with('success', 'Sto je uspješno obrisan!');
    }
}
