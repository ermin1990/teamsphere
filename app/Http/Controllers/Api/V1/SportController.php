<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SportController extends Controller
{
    /**
     * Display a listing of sports.
     */
    public function index(): JsonResponse
    {
        $sports = Sport::withCount(['leagues'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sports,
            'message' => 'Sports retrieved successfully'
        ]);
    }

    /**
     * Display the specified sport.
     */
    public function show(Sport $sport): JsonResponse
    {
        $sport->load(['leagues' => function ($query) {
            $query->where('is_public', true)
                  ->with(['organization'])
                  ->orderBy('created_at', 'desc');
        }]);

        return response()->json([
            'success' => true,
            'data' => $sport,
            'message' => 'Sport retrieved successfully'
        ]);
    }
}