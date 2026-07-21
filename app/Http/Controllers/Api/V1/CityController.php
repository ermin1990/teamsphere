<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CityResource;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CityController extends Controller
{
    use ApiResponses;

    public function index(): JsonResponse
    {
        $cities = City::orderBy('name')->get();

        return $this->ok(CityResource::collection($cities));
    }

    public function show(City $city): JsonResponse
    {
        return $this->ok(new CityResource($city));
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $city = City::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(4),
        ]);

        return $this->created(new CityResource($city), 'City created successfully');
    }

    public function update(Request $request, City $city): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $city->update(['name' => $validated['name']]);

        return $this->ok(new CityResource($city), 'City updated successfully');
    }

    public function destroy(Request $request, City $city): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $city->delete();

        return $this->ok(null, 'City deleted successfully');
    }
}
