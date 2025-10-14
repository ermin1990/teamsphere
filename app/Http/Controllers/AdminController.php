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
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Check if user is authenticated and has admin privileges
            if (!auth()->user()->is_admin) {
                abort(403, 'Unauthorized access to admin panel. User is_admin: ' . (auth()->user()->is_admin ? 'true' : 'false'));
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
        try {
            $users = User::paginate(20);
            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            \Log::error('Error in AdminController::users: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showUser(User $user)
    {
        $user->load(['currentPlan', 'organizations.leagues', 'leagues.organization']);

        return view('admin.users.show', compact('user'));
    }

    public function changeUserPlan(User $user)
    {
        $plans = Plan::active()->get();
        $currentPlan = $user->currentPlan();

        return view('admin.users.change-plan', compact('user', 'plans', 'currentPlan'));
    }

    public function updateUserPlan(Request $request, User $user)
    {
        $validated = $request->validate([
            'plan_id' => 'nullable|exists:plans,id',
            'expires_at' => 'nullable|date|after:today',
        ]);

        // Deaktiviraj trenutni plan
        $user->userPlans()->active()->update(['is_active' => false]);

        if ($validated['plan_id']) {
            // Kreiraj novi plan
            $user->userPlans()->create([
                'plan_id' => $validated['plan_id'],
                'expires_at' => $validated['expires_at'],
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.users.show', $user)->with('success', 'User plan updated successfully!');
    }

    public function plans()
    {
        \Log::info('AdminController::plans called by user: ' . auth()->id() . ', is_admin: ' . (auth()->user()->is_admin ? 'true' : 'false'));
        
        $plans = Plan::all()->map(function ($plan) {
            $plan->user_plans_count = $plan->userPlans()->active()->count();
            return $plan;
        });

        return view('admin.plans.index', compact('plans'));
    }

    public function editPlan(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:plans,slug,' . $plan->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'max_organizations' => 'required|integer|min:0',
            'max_leagues_per_organization' => 'required|integer|min:0',
            'max_teams_per_league' => 'required|integer|min:0',
            'max_players_per_team' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.plans')->with('success', 'Plan updated successfully!');
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
