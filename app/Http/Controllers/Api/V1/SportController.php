<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SportResource;
use App\Models\Sport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SportController extends Controller
{
    use ApiResponses;

    public function index(Request $request): JsonResponse
    {
        $query = Sport::query();

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $sports = $query->orderBy('name')->paginate($this->perPage($request, default: 50));

        return $this->paginated($sports, SportResource::class);
    }

    public function show(Sport $sport): JsonResponse
    {
        return $this->ok(new SportResource($sport));
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'rules' => ['nullable', 'array'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $sport = Sport::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(6),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'rules' => $validated['rules'] ?? null,
            'active' => $validated['active'] ?? true,
        ]);

        return $this->created(new SportResource($sport), 'Sport created successfully');
    }

    public function update(Request $request, Sport $sport): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'rules' => ['nullable', 'array'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $sport->update($validated);

        return $this->ok(new SportResource($sport), 'Sport updated successfully');
    }

    public function destroy(Request $request, Sport $sport): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $sport->delete();

        return $this->ok(null, 'Sport deleted successfully');
    }
}
