<?php
namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrganizationController extends Controller
{
    private function debugLog($message, $data = [])
    {
        // Write to public folder for easy access
        $logFile = public_path('debug_organization.log');
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] $message\n" . print_r($data, true) . "\n\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    public function index()
    {
        $this->debugLog('AdminOrganizationController::index called', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? 'not authenticated',
        ]);
        
        $organizations = Organization::with(['user', 'leagues'])->latest()->paginate(20);
        
        $this->debugLog('AdminOrganizationController::index - organizations loaded', [
            'count' => $organizations->count(),
        ]);
        
        return view('admin.organizations.index', compact('organizations'));
    }

    public function show(Organization $organization)
    {
        // Log IMMEDIATELY at the start
        $logFile = public_path('debug_organization.log');
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] === SHOW METHOD CALLED ===\n", FILE_APPEND);
        
        $user = Auth::user();
        
        $this->debugLog('AdminOrganizationController::show called', [
            'user_id' => $user->id ?? 'not authenticated',
            'user_email' => $user->email ?? 'not authenticated',
            'organization_id' => $organization->id,
            'organization_name' => $organization->name,
            'organization_slug' => $organization->slug,
            'organization_owner_id' => $organization->user_id,
        ]);
        
        // Check if user is owner
        $isOwner = $user && $user->id === $organization->user_id;
        $this->debugLog('AdminOrganizationController::show - ownership check', [
            'is_owner' => $isOwner,
        ]);
        
        // Check if user is member
        $isMember = false;
        if ($user) {
            $isMember = $organization->organizationUsers()->where('user_id', $user->id)->exists();
            $orgUsers = $organization->organizationUsers()->get();
            $this->debugLog('AdminOrganizationController::show - membership check', [
                'is_member' => $isMember,
                'organization_users_count' => $orgUsers->count(),
                'organization_users' => $orgUsers->toArray(),
            ]);
        }
        
        // Check policy authorization
        try {
            $this->authorize('view', $organization);
            $this->debugLog('AdminOrganizationController::show - authorization PASSED');
        } catch (\Exception $e) {
            $this->debugLog('AdminOrganizationController::show - authorization FAILED', [
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
        
        $this->debugLog('AdminOrganizationController::show - returning view');
        
        return view('admin.organizations.show', compact('organization'));
    }
}
