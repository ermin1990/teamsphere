<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Sezone
                </h2>
                <p class="text-gray-400 mt-1">Sezone za {{ $organization->name }} — igrači svoju historiju vide grupisanu po sezonama</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('organizations.show', $organization) }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Nazad
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 text-green-400 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Add Season -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-6">Dodaj sezonu</h3>
                <form action="{{ route('organizations.seasons.store', $organization) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-400 mb-2">Naziv (npr. Sezona 2025/26)</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Početak</label>
                        <input type="date" name="starts_at" class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Kraj</label>
                        <input type="date" name="ends_at" class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="md:col-span-3 flex items-center">
                        <input type="checkbox" name="is_active" value="1" id="is_active_new" class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                        <label for="is_active_new" class="ml-3 text-sm text-gray-300">Aktivna sezona (nove lige se automatski vezuju za ovu)</label>
                    </div>
                    <div>
                        <button type="submit" class="w-full h-[42px] bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all duration-200">
                            Dodaj
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Seasons -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-6">Sve sezone</h3>
                @if($seasons->count() > 0)
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($seasons as $season)
                            <div class="flex items-center justify-between p-5 bg-gray-700/30 border-2 {{ $season->is_active ? 'border-blue-500/50' : 'border-gray-600/50' }} rounded-xl">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-white font-semibold">{{ $season->name }}</span>
                                        @if($season->is_active)
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30">Aktivna</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-400 text-sm mt-1">
                                        {{ $season->starts_at?->format('d.m.Y') ?? '?' }} – {{ $season->ends_at?->format('d.m.Y') ?? '?' }}
                                        · {{ $season->competitions_count }} {{ $season->competitions_count === 1 ? 'takmičenje' : 'takmičenja' }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    @unless($season->is_active)
                                        <form action="{{ route('organizations.seasons.update', [$organization, $season]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="name" value="{{ $season->name }}">
                                            <input type="hidden" name="starts_at" value="{{ $season->starts_at }}">
                                            <input type="hidden" name="ends_at" value="{{ $season->ends_at }}">
                                            <input type="hidden" name="is_active" value="1">
                                            <button type="submit" class="text-xs text-blue-400 hover:text-blue-300 font-medium">Postavi kao aktivnu</button>
                                        </form>
                                    @endunless
                                    <form action="{{ route('organizations.seasons.destroy', [$organization, $season]) }}" method="POST"
                                          onsubmit="return confirm('Obrisati sezonu {{ $season->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 font-medium">Obriši</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-center py-8">Nema još dodanih sezona.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
