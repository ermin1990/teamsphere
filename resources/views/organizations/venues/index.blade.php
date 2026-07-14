<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Tereni
                </h2>
                <p class="text-gray-400 mt-1">Tereni za {{ $organization->name }} — biraju se prilikom unosa rezultata meča</p>
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

            <!-- Add Venue -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-6">Dodaj teren</h3>
                <form action="{{ route('organizations.venues.store', $organization) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Naziv (npr. Zmaj od Bosne)</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Grad</label>
                        <select name="city_id" class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">— nije odabran —</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Adresa</label>
                        <input type="text" name="address" class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <button type="submit" class="w-full h-[42px] bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all duration-200">
                            Dodaj
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Venues -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-6">Svi tereni</h3>
                @if($venues->count() > 0)
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($venues as $venue)
                            <div class="flex items-center justify-between p-5 bg-gray-700/30 border-2 border-gray-600/50 rounded-xl">
                                <div>
                                    <span class="text-white font-semibold">{{ $venue->name }}</span>
                                    <p class="text-gray-400 text-sm mt-1">
                                        {{ $venue->city?->name ?? 'Bez grada' }}
                                        @if($venue->address) · {{ $venue->address }} @endif
                                    </p>
                                </div>
                                <form action="{{ route('organizations.venues.destroy', [$organization, $venue]) }}" method="POST"
                                      onsubmit="return confirm('Obrisati teren {{ $venue->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 font-medium">Obriši</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-center py-8">Nema još dodanih terena.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
