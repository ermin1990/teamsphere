<?php
namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class AdminOrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::with(['user', 'leagues'])->latest()->paginate(20);
        return view('admin.organizations.index', compact('organizations'));
    }

    public function show(Organization $organization)
    {
        $organization->load(['user', 'leagues.matches']);
        return view('admin.organizations.show', compact('organization'));
    }
}
