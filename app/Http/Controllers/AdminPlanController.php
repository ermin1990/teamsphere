<?php
namespace App\Http\Controllers;

use App\Models\Plan;
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
}
