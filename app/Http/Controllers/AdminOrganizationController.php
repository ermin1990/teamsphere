<?php
namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminOrganizationController extends Controller
{
    public function index()
    {
        Log::info('AdminOrganizationController::index called', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? 'not authenticated',
        ]);
        
        $organizations = Organization::with(['user', 'leagues'])->latest()->paginate(20);
        
        Log::info('AdminOrganizationController::index - organizations loaded', [
            'count' => $organizations->count(),
        ]);
        
        return view('admin.organizations.index', compact('organizations'));
    }

    public function show(Organization $organization)
    {
        $user = Auth::user();
        
        Log::info('AdminOrganizationController::show called', [
            'user_id' => $user->id ?? 'not authenticated',
            'user_email' => $user->email ?? 'not authenticated',
            'organization_id' => $organization->id,
            'organization_name' => $organization->name,
            'organization_slug' => $organization->slug,
            'organization_owner_id' => $organization->user_id,
        ]);
        
        // Check if user is owner
        $isOwner = $user && $user->id === $organization->user_id;
        Log::info('AdminOrganizationController::show - ownership check', [
            'is_owner' => $isOwner,
        ]);
        
        // Check if user is member
        $isMember = false;
        if ($user) {
            $isMember = $organization->organizationUsers()->where('user_id', $user->id)->exists();
            Log::info('AdminOrganizationController::show - membership check', [
                'is_member' => $isMember,
                'organization_users_count' => $organization->organizationUsers()->count(),
            ]);
        }
        
        // Check policy authorization
        try {
            $this->authorize('view', $organization);
            Log::info('AdminOrganizationController::show - authorization PASSED');
        } catch (\Exception $e) {
            Log::error('AdminOrganizationController::show - authorization FAILED', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'user_id' => $user->id ?? null,
                'organization_id' => $organization->id,
                'is_owner' => $isOwner,
                'is_member' => $isMember,
            ]);
            throw $e;
        }
        
        $organization->load(['user', 'leagues.matches']);
        
        Log::info('AdminOrganizationController::show - returning view');
        
        return view('admin.organizations.show', compact('organization'));
    }
}
