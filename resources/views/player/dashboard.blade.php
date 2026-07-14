<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Moje lige
            </h2>
            <p class="text-gray-400 mt-1">Takmičenja u kojima igraš, grupisano po sezonama</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 text-green-400 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @forelse($bySeason as $seasonName => $seasonCompetitions)
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4">{{ $seasonName }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($seasonCompetitions as $competition)
                            <div class="flex items-center justify-between p-4 bg-gray-700/30 border border-gray-600/50 rounded-xl hover:border-blue-500/50 transition-all">
                                <a href="{{ route('public.leagues.show', $competition) }}" class="flex-1">
                                    <p class="text-white font-semibold">{{ $competition->name }}</p>
                                    <p class="text-gray-400 text-xs mt-1">
                                        {{ $competition->organization->name }}
                                        @if(isset($rankings[$competition->organization_id]))
                                            · Rang {{ $rankings[$competition->organization_id]['position'] }}/{{ $rankings[$competition->organization_id]['total'] }}
                                        @endif
                                    </p>
                                </a>
                                <div class="flex items-center gap-3">
                                    @if($competition->isLeague())
                                        <a href="{{ route('player.matches.create', $competition) }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium whitespace-nowrap">
                                            + Zabilježi meč
                                        </a>
                                    @endif
                                    <a href="{{ route('public.leagues.show', $competition) }}" class="text-blue-400 text-sm font-medium whitespace-nowrap">Pogledaj →</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-10 text-center">
                    <p class="text-gray-400">Još nisi dodan ni na jedno takmičenje.</p>
                    <p class="text-gray-500 text-sm mt-2">Kada te organizator doda ili pozove na ligu, ona će se pojaviti ovdje.</p>
                    <a href="{{ route('public.leagues.index') }}" class="inline-block mt-4 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all">
                        Pronađi ligu
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
