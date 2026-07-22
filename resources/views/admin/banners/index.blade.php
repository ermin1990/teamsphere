@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Banneri
            </h2>
            <p class="text-gray-400 mt-2">Reklamni banneri (slika + link) prikazani na javnim stranicama</p>
        </div>
        <div class="text-left sm:text-right">
            <p class="text-sm text-gray-400">Ukupno banera</p>
            <p class="text-2xl font-bold text-white">{{ $banners->count() }}</p>
        </div>
    </div>

    @if(session('status'))
        <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 text-green-400 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <!-- Add Banner -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">Dodaj baner</h3>
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Naziv (interno, opcionalno)</label>
                <input type="text" name="title" placeholder="npr. Sponzor - Ljetna promocija"
                       class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Mjesto prikaza (može više)</label>
                <div class="space-y-2 px-1 py-1">
                    @foreach(\App\Models\Banner::PLACEMENTS as $value => $label)
                        <label class="flex items-center gap-2 text-sm text-gray-300">
                            <input type="checkbox" name="placements[]" value="{{ $value }}"
                                   class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @error('placements')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Otpremi sliku</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white file:cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">...ili URL slike</label>
                <input type="url" name="image_url" placeholder="https://..."
                       class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Link (opcionalno)</label>
                <input type="url" name="link_url" placeholder="https://..."
                       class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Redoslijed (manji broj = prvi)</label>
                <input type="number" name="sort_order" value="0" min="0"
                       class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-lg transition-all duration-200">
                    Dodaj baner
                </button>
            </div>
        </form>
        @error('image')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
        @error('image_url')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
        @error('link_url')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
    </div>

    <!-- Banners List -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-900/40">
                <tr>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium">Slika</th>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium">Naziv</th>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium">Mjesto</th>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium">Link</th>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium">Status</th>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium text-right">Akcije</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @forelse($banners as $banner)
                    <tr>
                        <td class="px-6 py-4">
                            <img src="{{ $banner->imageSrc() }}" alt="{{ $banner->title }}" class="w-20 h-12 object-cover rounded-lg border border-gray-700/50">
                        </td>
                        <td class="px-6 py-4 text-white font-medium">{{ $banner->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-300 text-sm">
                            {{ $banner->placementLabels() }}
                        </td>
                        <td class="px-6 py-4 text-gray-300 text-sm max-w-[200px] truncate">
                            @if($banner->link_url)
                                <a href="{{ $banner->link_url }}" target="_blank" class="text-blue-400 hover:underline">{{ $banner->link_url }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}">
                                @csrf
                                <button type="submit" class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $banner->is_active ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-gray-600/30 text-gray-400 border border-gray-600/50' }}">
                                    {{ $banner->is_active ? 'Aktivan' : 'Neaktivan' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <button type="button" onclick="document.getElementById('edit-banner-{{ $banner->id }}').classList.toggle('hidden')" class="text-xs text-blue-400 hover:text-blue-300 font-medium mr-3">Uredi</button>
                            <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}"
                                  onsubmit="return confirm('Obrisati ovaj baner?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 font-medium">Obriši</button>
                            </form>
                        </td>
                    </tr>
                    <tr id="edit-banner-{{ $banner->id }}" class="hidden bg-gray-900/40">
                        <td colspan="6" class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Naziv</label>
                                    <input type="text" name="title" value="{{ $banner->title }}"
                                           class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Mjesto prikaza (može više)</label>
                                    <div class="space-y-2 px-1 py-1">
                                        @foreach(\App\Models\Banner::PLACEMENTS as $value => $label)
                                            <label class="flex items-center gap-2 text-sm text-gray-300">
                                                <input type="checkbox" name="placements[]" value="{{ $value }}"
                                                       {{ in_array($value, $banner->placements ?? []) ? 'checked' : '' }}
                                                       class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                                                {{ $label }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Zamijeni sliku (upload)</label>
                                    <input type="file" name="image" accept="image/*"
                                           class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white file:cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">...ili URL slike</label>
                                    <input type="url" name="image_url" value="{{ $banner->image_path ? '' : $banner->image_url }}" placeholder="https://..."
                                           class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Link</label>
                                    <input type="url" name="link_url" value="{{ $banner->link_url }}" placeholder="https://..."
                                           class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Redoslijed</label>
                                    <input type="number" name="sort_order" value="{{ $banner->sort_order }}" min="0"
                                           class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="md:col-span-2">
                                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-lg transition-all duration-200">
                                        Sačuvaj izmjene
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Nema dodanih banera.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
