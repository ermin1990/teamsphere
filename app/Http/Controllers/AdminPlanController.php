<?php
namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;

class AdminPlanController extends Controller
{
    public function index()
    {
        $plans = Plan::with('userPlans')->latest()->paginate(20);
        return view('admin.plans.index', compact('plans'));
    }

    public function show(Plan $plan)
    {
        $plan->load(['userPlans.user']);
        return view('admin.plans.show', compact('plan'));
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'max_organizations' => 'required|integer|min:0',
            'max_leagues_per_organization' => 'required|integer|min:0',
            'max_competitions_per_organization' => 'required|integer|min:0',
            'max_teams_per_league' => 'required|integer|min:0',
            'max_players_per_team' => 'required|integer|min:0',
            'features' => 'required|array',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.plans.show', $plan)->with('success', 'Plan updated successfully.');
    }

    public function assign(User $user)
    {
        $plans = Plan::all();
        $currentPlan = $user->userPlans()->where('is_active', true)->first();

        return view('admin.plans.assign', compact('user', 'plans', 'currentPlan'));
    }

    public function assignStore(Request $request, User $user)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'expires_at' => 'nullable|date|after:today',
        ]);

        // Deactivate current active plan
        $user->userPlans()->where('is_active', true)->update(['is_active' => false]);

        // Create new user plan
        $user->userPlans()->create([
            'plan_id' => $validated['plan_id'],
            'is_active' => true,
            'expires_at' => $validated['expires_at'],
        ]);

        return redirect()->route('admin.users.show', $user)->with('success', 'Plan assigned successfully.');
    }
}