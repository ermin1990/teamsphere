<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TableController extends Controller
{
    /**
     * Get standings for a specific league.
     */
    public function standings(League $league): JsonResponse
    {
        // Check if league is public or user has access
        if (!$league->is_public && (!auth()->check() || !auth()->user()->canAccessLeague($league))) {
            return response()->json([
                'success' => false,
                'message' => 'League not found or access denied'
            ], 404);
        }

        $standings = $league->getStandings();

        return response()->json([
            'success' => true,
            'data' => $standings,
            'message' => 'League standings retrieved successfully'
        ]);
    }

    /**
     * Display a listing of tables for a league.
     */
    public function index(League $league): JsonResponse
    {
        // Check if user can access league
        if (!auth()->user()->canAccessLeague($league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $tables = $league->tables()
            ->with(['league'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tables,
            'message' => 'League tables retrieved successfully'
        ]);
    }

    /**
     * Store a newly created table.
     */
    public function store(Request $request, League $league): JsonResponse
    {
        // Check if user can access league
        if (!auth()->user()->canAccessLeague($league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $table = $league->tables()->create($validated);

        return response()->json([
            'success' => true,
            'data' => $table,
            'message' => 'Table created successfully'
        ], 201);
    }

    /**
     * Update the specified table.
     */
    public function update(Request $request, Table $table): JsonResponse
    {
        // Check if user can access table's league
        if (!auth()->user()->canAccessLeague($table->league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $table->update($validated);

        return response()->json([
            'success' => true,
            'data' => $table,
            'message' => 'Table updated successfully'
        ]);
    }

    /**
     * Remove the specified table.
     */
    public function destroy(Table $table): JsonResponse
    {
        // Check if user can access table's league
        if (!auth()->user()->canAccessLeague($table->league)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $table->delete();

        return response()->json([
            'success' => true,
            'message' => 'Table deleted successfully'
        ]);
    }
}