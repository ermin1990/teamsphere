<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateBanner($request, requireImage: true);

        Banner::create([
            'title' => $validated['title'] ?? null,
            'image_path' => $request->hasFile('image') ? $request->file('image')->store('banners', 'public') : null,
            'image_url' => $request->hasFile('image') ? null : ($validated['image_url'] ?? null),
            'link_url' => $validated['link_url'] ?? null,
            'placements' => $validated['placements'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.banners.index')->with('status', 'Baner je dodan.');
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $this->validateBanner($request);

        $updateData = [
            'title' => $validated['title'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'placements' => $validated['placements'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        if ($request->hasFile('image')) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $updateData['image_path'] = $request->file('image')->store('banners', 'public');
            $updateData['image_url'] = null;
        } elseif (!empty($validated['image_url'])) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $updateData['image_path'] = null;
            $updateData['image_url'] = $validated['image_url'];
        }

        $banner->update($updateData);

        return redirect()->route('admin.banners.index')->with('status', 'Baner je izmijenjen.');
    }

    public function toggle(Banner $banner)
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        return redirect()->route('admin.banners.index')->with('status', 'Status banera je izmijenjen.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')->with('status', 'Baner je obrisan.');
    }

    private function validateBanner(Request $request, bool $requireImage = false): array
    {
        return $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'image_url' => array_filter([
                'nullable', 'url', 'max:1000',
                $requireImage ? 'required_without:image' : null,
            ]),
            'link_url' => ['nullable', 'url', 'max:1000'],
            'placements' => ['required', 'array', 'min:1'],
            'placements.*' => ['in:' . implode(',', array_keys(Banner::PLACEMENTS))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
