<?php
namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::with(['user', 'competitions', 'players'])->latest()->paginate(20);
        return view('admin.organizations.index', compact('organizations'));
    }

    public function show(Organization $organization)
    {
        $this->authorize('view', $organization);
        $organization->load(['user', 'competitions.matches', 'competitions.tournamentGroups', 'players']);
        return view('admin.organizations.show', compact('organization'));
    }
}
