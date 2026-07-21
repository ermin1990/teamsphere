<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TableResource;
use App\Models\Organization;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    use ApiResponses;

    public function index(Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $tables = Table::where('organization_id', $organization->id)
            ->orderBy('name')
            ->get();

        return $this->ok(TableResource::collection($tables));
    }

    public function show(Organization $organization, Table $table): JsonResponse
    {
        $this->authorize('view', $organization);

        if ($table->organization_id !== $organization->id) {
            return $this->fail('This table does not belong to this organization.', 404);
        }

        return $this->ok(new TableResource($table));
    }

    public function store(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $table = Table::create([
            'organization_id' => $organization->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return $this->created(new TableResource($table), 'Table created successfully');
    }

    public function update(Request $request, Organization $organization, Table $table): JsonResponse
    {
        $this->authorize('update', $organization);

        if ($table->organization_id !== $organization->id) {
            return $this->fail('This table does not belong to this organization.', 404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $table->update($validated);

        return $this->ok(new TableResource($table), 'Table updated successfully');
    }

    public function destroy(Organization $organization, Table $table): JsonResponse
    {
        $this->authorize('update', $organization);

        if ($table->organization_id !== $organization->id) {
            return $this->fail('This table does not belong to this organization.', 404);
        }

        $table->delete();

        return $this->ok(null, 'Table deleted successfully');
    }
}
