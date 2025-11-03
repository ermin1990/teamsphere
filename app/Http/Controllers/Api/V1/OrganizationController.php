<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    /**
     * Display a listing of public organizations.
     */
    public function index(): JsonResponse
    {
        // For now, return all organizations (since is_public column doesn't exist)
        // TODO: Add is_public column to organizations table
        $organizations = Organization::with(['user', 'leagues.sport'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $organizations,
            'message' => 'Organizations retrieved successfully'
        ]);
    }

    /**
     * Display the specified organization.
     */
    public function show(Organization $organization): JsonResponse
    {
        // For now, allow access to all organizations (since is_public column doesn't exist)
        // TODO: Add is_public column to organizations table

        $organization->load(['user', 'leagues.sport', 'leagues.matches.homeTeam', 'leagues.matches.awayTeam']);

        return response()->json([
            'success' => true,
            'data' => $organization,
            'message' => 'Organization retrieved successfully'
        ]);
    }

    /**
     * Get organizations for the authenticated user.
     */
    public function myOrganizations(): JsonResponse
    {
        $organizations = auth()->user()->organizations()
            ->with(['leagues.sport'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $organizations,
            'message' => 'Your organizations retrieved successfully'
        ]);
    }

    /**
     * Store a newly created organization.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'url_slug' => 'nullable|string|max:255|unique:organizations,url_slug',
        ]);

        $organization = auth()->user()->organizations()->create($validated);

        return response()->json([
            'success' => true,
            'data' => $organization,
            'message' => 'Organization created successfully'
        ], 201);
    }

    /**
     * Update the specified organization.
     */
    public function update(Request $request, Organization $organization): JsonResponse
    {
        // Check if user can access organization
        if (!auth()->user()->canAccessOrganization($organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'url_slug' => 'nullable|string|max:255|unique:organizations,url_slug,' . $organization->id,
        ]);

        $organization->update($validated);

        return response()->json([
            'success' => true,
            'data' => $organization,
            'message' => 'Organization updated successfully'
        ]);
    }

    /**
     * Remove the specified organization.
     */
    public function destroy(Organization $organization): JsonResponse
    {
        // Check if user can access organization
        if (!auth()->user()->canAccessOrganization($organization)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $organization->delete();

        return response()->json([
            'success' => true,
            'message' => 'Organization deleted successfully'
        ]);
    }
}