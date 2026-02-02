<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories for the organization.
     */
    public function index(Organization $organization)
    {
        $this->authorize('update', $organization);

        $categories = $organization->categories()->orderBy('name')->get();

        return view('organizations.categories.index', compact('organization', 'categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(Organization $organization)
    {
        $this->authorize('update', $organization);

        return view('organizations.categories.create', compact('organization'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $slug = Str::slug($request->name);
        
        // Ensure slug is unique within organization
        $count = $organization->categories()->where('slug', 'like', "$slug%")->count();
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        $organization->categories()->create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('organizations.categories.index', $organization)->with('success', __('Category created successfully!'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $organization = $category->organization;
        $this->authorize('update', $organization);

        return view('organizations.categories.edit', compact('organization', 'category'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $organization = $category->organization;
        $this->authorize('update', $organization);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $slug = Str::slug($request->name);
        
        // Ensure slug is unique within organization (excluding current category)
        $existing = $organization->categories()
            ->where('slug', $slug)
            ->where('id', '!=', $category->id)
            ->first();
            
        if ($existing) {
            $slug = $slug . '-' . time();
        }

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('organizations.categories.index', $organization)->with('success', __('Category updated successfully!'));
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        $organization = $category->organization;
        $this->authorize('update', $organization);

        $category->delete();

        return redirect()->route('organizations.categories.index', $organization)->with('success', __('Category deleted successfully!'));
    }
}
