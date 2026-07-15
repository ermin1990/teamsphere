<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    /**
     * "Moj plan" - only relevant to organizers (users who own at least one
     * organization); players have no plan/limits of their own.
     */
    public function show()
    {
        $user = Auth::user();

        abort_unless($user->organizations()->exists(), 403, 'Plan i ograničenja se odnose samo na organizatore.');

        $organizationIds = $user->organizations()->pluck('id');
        $currentPlan = $user->currentPlan();

        $usageStats = [
            'organizations_used' => $user->organizations()->count(),
            'competitions_used' => Competition::whereIn('organization_id', $organizationIds)->where('type', 'tournament')->count(),
            'leagues_used' => Competition::whereIn('organization_id', $organizationIds)->where('type', 'league')->count(),
            'max_organizations' => $currentPlan ? $currentPlan->max_organizations : null,
            'max_competitions_per_organization' => $currentPlan ? $currentPlan->max_competitions_per_organization : null,
            'max_leagues_per_organization' => $currentPlan ? $currentPlan->max_leagues_per_organization : null,
        ];

        $organizationsCount = $organizationIds->count();

        return view('plan.show', compact('currentPlan', 'usageStats', 'organizationsCount'));
    }
}
