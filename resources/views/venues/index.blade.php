<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Moji Tereni
                </h2>
                <p class="text-gray-400 mt-1">Upravljajte terenima koje ste registrovali</p>
            </div>
            <a href="{{ route('venues.create') }}"
               class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                Registruj Teren
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($venues->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($venues as $venue)
                    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-200">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-12 h-12 rounded-lg bg-gray-700/50 border border-gray-600 overflow-hidden flex items-center justify-center shrink-0">
                                @if($venue->logoSrc())
                                    <img src="{{ $venue->logoSrc() }}" alt="{{ $venue->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-gray-500 text-[10px]">Bez loga</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="text-white font-semibold text-xl">{{ $venue->name }}</h3>
                                @if($venue->city)
                                    <p class="text-gray-400 text-sm">{{ $venue->city->name }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="bg-gray-600/50 px-2 py-1 rounded text-xs text-gray-300">{{ $venue->slug }}</span>
                            <a href="{{ route('venues.edit', $venue) }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">
                                Uredi →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <h3 class="text-xl font-semibold text-white mb-2">Još nema registrovanih terena</h3>
                <p class="text-gray-400 mb-6">Registrujte svoj teren da bi bio vidljiv na javnoj stranici sa svojim ligama i mečevima.</p>
                <a href="{{ route('venues.create') }}"
                   class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl inline-block">
                    Registruj Svoj Prvi Teren
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
