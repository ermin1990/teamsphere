<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                📅 Raspored Stolova - {{ $organization->name }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('organizations.tables.index', $organization) }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Nazad na Stolove
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <a href="{{ route('organizations.show', $organization) }}" 
                           class="text-blue-400 hover:text-blue-300">
                            ← Nazad na organizaciju
                        </a>
                    </div>

                    @if($tables->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($tables as $table)
                                @php
                                    $allMatches = $table->leagueMatches->merge($table->competitionMatches);
                                    $totalMatches = $allMatches->count();
                                @endphp

                                <div class="bg-gray-700/50 rounded-xl border border-gray-600/50 overflow-hidden flex flex-col">
                                    <!-- Table Header -->
                                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                                <div>
                                                    <h3 class="text-lg font-bold text-white">{{ $table->name }}</h3>
                                                    @if($table->description)
                                                        <p class="text-blue-100 text-xs">{{ $table->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xl font-bold text-white">{{ $totalMatches }}</div>
                                                <div class="text-blue-100 text-xs">{{ $totalMatches == 1 ? 'Meč' : ($totalMatches < 5 ? 'Meča' : 'Mečeva') }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Matches List -->
                                    <div class="p-3 flex-1 overflow-y-auto max-h-96">
                                        @if($totalMatches > 0)
                                            <div class="space-y-2">
                                                @foreach($allMatches as $match)
                                                    <div class="bg-gray-800/50 rounded-lg p-3 border border-gray-600/30 hover:border-blue-500/50 transition-colors">
                                                        <!-- Match Info -->
                                                        <div class="flex items-center justify-between mb-2">
                                                            @if($match instanceof \App\Models\LeagueMatch)
                                                                <span class="bg-blue-500/20 text-blue-400 text-xs px-2 py-0.5 rounded font-medium">
                                                                    Liga
                                                                </span>
                                                            @else
                                                                <span class="bg-purple-500/20 text-purple-400 text-xs px-2 py-0.5 rounded font-medium">
                                                                    Turnir
                                                                </span>
                                                            @endif
                                                            
                                                            @if($match->status === 'in_progress')
                                                                <span class="bg-green-500/20 text-green-400 text-xs px-2 py-0.5 rounded font-bold animate-pulse">
                                                                    🔴 UŽIVO
                                                                </span>
                                                            @elseif($match->status === 'scheduled')
                                                                <span class="bg-yellow-500/20 text-yellow-400 text-xs px-2 py-0.5 rounded">
                                                                    Zakazano
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <!-- Players -->
                                                        <div class="space-y-1 text-sm">
                                                            <div class="flex items-center justify-between">
                                                                <span class="text-white truncate pr-2">
                                                                    {{ $match->homePlayer->name ?? 'TBA' }}
                                                                </span>
                                                                @if($match->status !== 'scheduled')
                                                                    <span class="text-blue-400 font-bold">
                                                                        {{ $match->home_score ?? 0 }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="flex items-center justify-between">
                                                                <span class="text-white truncate pr-2">
                                                                    {{ $match->awayPlayer->name ?? 'TBA' }}
                                                                </span>
                                                                @if($match->status !== 'scheduled')
                                                                    <span class="text-red-400 font-bold">
                                                                        {{ $match->away_score ?? 0 }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if($match->referee)
                                                            <div class="mt-2 text-xs text-gray-400 truncate">
                                                                👤 {{ $match->referee->name }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-6">
                                                <svg class="mx-auto h-8 w-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                <p class="mt-2 text-xs text-gray-400">Nema mečeva</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-300">Nema aktivnih stolova</h3>
                            <p class="mt-1 text-sm text-gray-400">Kreirajte stolove da biste mogli pratiti njihov raspored.</p>
                            <div class="mt-6">
                                <a href="{{ route('organizations.tables.create', $organization) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded">
                                    + Dodaj Sto
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
