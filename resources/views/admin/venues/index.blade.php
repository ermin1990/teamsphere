@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Tereni
            </h2>
            <p class="text-gray-400 mt-2">Tereni po gradovima - biraju se prilikom unosa rezultata meča</p>
        </div>
        <div class="text-left sm:text-right">
            <p class="text-sm text-gray-400">Ukupno terena</p>
            <p class="text-2xl font-bold text-white">{{ $venues->count() }}</p>
        </div>
    </div>

    @if(session('status'))
        <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 text-green-400 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <!-- Add Venue -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">Dodaj teren</h3>
        <form method="POST" action="{{ route('admin.venues.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Naziv</label>
                <input type="text" name="name" placeholder="npr. Zmaj od Bosne" required
                       class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Grad</label>
                <select name="city_id"
                        class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— nije odabran —</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Adresa</label>
                <input type="text" name="address"
                       class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="md:col-span-3">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-lg transition-all duration-200">
                    Dodaj teren
                </button>
            </div>
        </form>
        @error('name')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
    </div>

    <!-- Venues List -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-900/40">
                <tr>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium">Naziv</th>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium">Grad</th>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium">Adresa</th>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium text-right">Akcije</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @forelse($venues as $venue)
                    <tr>
                        <td class="px-6 py-4 text-white font-medium">{{ $venue->name }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ $venue->city->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ $venue->address ?? '—' }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.venues.destroy', $venue) }}"
                                  onsubmit="return confirm('Obrisati teren {{ $venue->name }}?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 font-medium">Obriši</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">Nema dodanih terena.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
