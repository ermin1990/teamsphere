<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use App\Models\League;
use App\Models\Plan;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Check if user is authenticated and has admin privileges
            if (!auth()->check() || !auth()->user()->is_admin) {
                abort(403, 'Unauthorized access to admin panel');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_organizations' => Organization::count(),
            'total_leagues' => League::count(),
            'active_plans' => User::whereHas('userPlans')->count(),
            'free_users' => User::whereDoesntHave('userPlans')->count(),
        ];

        $recentUsers = User::with(['userPlans.plan'])->latest()->take(10)->get()->map(function ($user) {
            $user->currentPlan = $user->userPlans->where('is_active', true)->first()?->plan;
            return $user;
        });
        $recentOrganizations = Organization::with('user')->latest()->take(10)->get();
        $recentLeagues = League::with(['organization', 'organization.user'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentOrganizations', 'recentLeagues'));
    }

    public function users()
    {
        $users = User::with(['currentPlan', 'organizations', 'leagues'])
                    ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function showUser(User $user)
    {
        $user->load(['currentPlan', 'organizations.leagues', 'leagues.organization']);

        return view('admin.users.show', compact('user'));
    }

    public function plans()
    {
        $plans = Plan::all()->map(function ($plan) {
            $plan->user_plans_count = $plan->userPlans()->active()->count();
            return $plan;
        });

        return view('admin.plans.index', compact('plans'));
    }

    public function organizations()
    {
        $organizations = Organization::with(['user', 'leagues'])
                                   ->paginate(20);

        return view('admin.organizations.index', compact('organizations'));
    }

    public function showOrganization(Organization $organization)
    {
        $organization->load(['user', 'leagues.players', 'leagues.teams']);

        return view('admin.organizations.show', compact('organization'));
    }

    public function leagues()
    {
        $leagues = League::with(['organization.user'])
                        ->paginate(20);

        return view('admin.leagues.index', compact('leagues'));
    }

    public function showLeague(League $league)
    {
        $league->load(['organization.user', 'players', 'teams', 'matches']);

        return view('admin.leagues.show', compact('league'));
    }
}
