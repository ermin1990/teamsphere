<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\VenueResource;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    use ApiResponses;

    public function index(Request $request): JsonResponse
    {
        $query = Venue::with('city');

        if ($request->has('city_id')) {
            $query->where('city_id', $request->input('city_id'));
        }

        $venues = $query->orderBy('name')->get();

        return $this->ok(VenueResource::collection($venues));
    }

    public function show(Venue $venue): JsonResponse
    {
        $venue->load('city');

        return $this->ok(new VenueResource($venue));
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $venue = Venue::create($validated);

        return $this->created(new VenueResource($venue->load('city')), 'Venue created successfully');
    }

    public function update(Request $request, Venue $venue): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $venue->update($validated);

        return $this->ok(new VenueResource($venue->load('city')), 'Venue updated successfully');
    }

    public function destroy(Request $request, Venue $venue): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $venue->delete();

        return $this->ok(null, 'Venue deleted successfully');
    }
}
