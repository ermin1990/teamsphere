@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Gradovi
            </h2>
            <p class="text-gray-400 mt-2">Katalog gradova koji se koristi za javno filtriranje liga po lokaciji</p>
        </div>
        <div class="text-left sm:text-right">
            <p class="text-sm text-gray-400">Ukupno gradova</p>
            <p class="text-2xl font-bold text-white">{{ $cities->count() }}</p>
        </div>
    </div>

    @if(session('status'))
        <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 text-green-400 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <!-- Add City -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">Dodaj grad</h3>
        <form method="POST" action="{{ route('admin.cities.store') }}" class="flex flex-col sm:flex-row gap-4">
            @csrf
            <input type="text" name="name" placeholder="npr. Tuzla" required
                   class="flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-lg transition-all duration-200 whitespace-nowrap">
                Dodaj grad
            </button>
        </form>
        @error('name')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
    </div>

    <!-- Cities List -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-900/40">
                <tr>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium">Naziv</th>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium">Lige</th>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium">Tereni</th>
                    <th class="px-6 py-4 text-gray-400 text-sm font-medium text-right">Akcije</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @forelse($cities as $city)
                    <tr>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.cities.update', $city) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $city->name }}"
                                       class="px-3 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="submit" class="text-xs text-blue-400 hover:text-blue-300 font-medium">Sačuvaj</button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-gray-300">{{ $city->competitions_count }}</td>
                        <td class="px-6 py-4 text-gray-300">{{ $city->venues_count }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.cities.destroy', $city) }}"
                                  onsubmit="return confirm('Obrisati grad {{ $city->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 font-medium">Obriši</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">Nema dodanih gradova.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
