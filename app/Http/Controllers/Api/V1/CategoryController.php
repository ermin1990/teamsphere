<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use ApiResponses;

    public function index(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $categories = $organization->categories()->orderBy('name')->paginate($this->perPage($request));

        return $this->paginated($categories, CategoryResource::class);
    }

    public function store(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $slug = Str::slug($validated['name']);
        $count = $organization->categories()->where('slug', 'like', "$slug%")->count();
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        $category = $organization->categories()->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return $this->created(new CategoryResource($category), 'Category created successfully');
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $organization = $category->organization;
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $slug = Str::slug($validated['name']);
        $existing = $organization->categories()
            ->where('slug', $slug)
            ->where('id', '!=', $category->id)
            ->first();
        if ($existing) {
            $slug = $slug . '-' . time();
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? $category->is_active,
        ]);

        return $this->ok(new CategoryResource($category), 'Category updated successfully');
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('update', $category->organization);

        $category->delete();

        return $this->ok(null, 'Category deleted successfully');
    }
}
