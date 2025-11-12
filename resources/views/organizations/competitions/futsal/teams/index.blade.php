<x-app-layout>
    <x-slot name="header">
        <!-- Mobile Layout -->
        <div class="block md:hidden">
            <div class="text-center">
                <h2 class="font-bold text-2xl bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent mb-2">
                    ⚽ Futsal Timovi
                </h2>
                <p class="text-gray-400 text-sm">{{ $competition->name }}</p>
            </div>
        </div>

        <!-- Desktop Layout -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent flex items-center gap-3">
                    ⚽ Futsal Timovi
                </h2>
                <p class="text-gray-400 mt-1">{{ $competition->name }} - {{ $organization->name }}</p>
            </div>
            @can('update', $organization)
                <a href="{{ route('organizations.competitions.futsal.teams.create', [$organization, $competition]) }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg transition-all duration-200 transform hover:scale-[1.02] font-semibold shadow-lg hover:shadow-green-500/50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Dodaj Novi Tim
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-lg p-4 animate-fade-in">
                    <p class="text-green-400 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            {{-- Back Button --}}
            <div class="mb-6">
                <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Nazad na Takmičenje
                </a>
            </div>

            {{-- Stats Bar --}}
            <div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-green-500/10 to-emerald-500/10 border border-green-500/20 rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-green-400">{{ $teams->count() }}</p>
                            <p class="text-xs text-gray-400">Timova</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-500/10 to-cyan-500/10 border border-blue-500/20 rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-blue-400">{{ $teams->sum(function($team) { return $team->activePlayers->count(); }) }}</p>
                            <p class="text-xs text-gray-400">Igrača</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-yellow-500/10 to-orange-500/10 border border-yellow-500/20 rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-yellow-400">
                                {{ $teams->filter(function($team) { return $team->activePlayers->count() >= 5; })->count() }}
                            </p>
                            <p class="text-xs text-gray-400">Spremnih</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 border border-purple-500/20 rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-xl">👨‍💼</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-purple-400">{{ $teams->filter(function($team) { return $team->coach_name; })->count() }}</p>
                            <p class="text-xs text-gray-400">Trenera</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($teams->isEmpty())
                {{-- Empty State --}}
                <div class="bg-gradient-to-br from-gray-800/50 to-gray-900/50 border-2 border-dashed border-gray-700 rounded-xl p-12">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2">Nema Timova</h3>
                        <p class="text-gray-400 mb-6">Započnite sa dodavanjem prvog futsal tima u takmičenje</p>
                        @can('update', $organization)
                            <a href="{{ route('organizations.competitions.futsal.teams.create', [$organization, $competition]) }}"
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg transition-all duration-200 transform hover:scale-[1.02] font-semibold shadow-lg hover:shadow-green-500/50">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Kreiraj Prvi Tim
                            </a>
                        @endcan
                    </div>
                </div>
            @else
                {{-- Teams Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($teams as $team)
                        <div class="group bg-gradient-to-br from-gray-800/50 to-gray-900/50 border border-gray-700 rounded-xl overflow-hidden hover:border-green-500/50 hover:shadow-xl hover:shadow-green-500/10 transition-all duration-300 transform hover:-translate-y-1">
                            {{-- Team Header with Colors --}}
                            <div class="h-24 relative overflow-hidden"
                                 style="background: linear-gradient(135deg, {{ $team->primary_color ?? '#10b981' }} 0%, {{ $team->secondary_color ?? '#059669' }} 100%);">
                                <div class="absolute inset-0 bg-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    @if($team->logo_url)
                                        <img src="{{ $team->logo_url }}" alt="{{ $team->name }}" 
                                             class="w-16 h-16 object-contain drop-shadow-lg">
                                    @else
                                        <span class="text-5xl drop-shadow-lg">⚽</span>
                                    @endif
                                </div>
                                {{-- Status Badge --}}
                                @if($team->activePlayers->count() >= 5)
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2 py-1 bg-green-500/90 text-white text-xs font-bold rounded-full flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Spreman
                                        </span>
                                    </div>
                                @else
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2 py-1 bg-yellow-500/90 text-white text-xs font-bold rounded-full">
                                            Nekompletan
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-6">
                                {{-- Team Name --}}
                                <div class="mb-4">
                                    <h3 class="text-xl font-bold text-white mb-1 group-hover:text-green-400 transition-colors">
                                        {{ $team->name }}
                                    </h3>
                                    @if($team->short_name)
                                        <p class="text-sm text-gray-400">{{ $team->short_name }}</p>
                                    @endif
                                </div>

                                {{-- Team Stats --}}
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="bg-gray-700/30 rounded-lg p-3 text-center">
                                        <div class="text-2xl font-bold text-blue-400">{{ $team->activePlayers->count() }}</div>
                                        <div class="text-xs text-gray-400 mt-1">Igrača</div>
                                    </div>
                                    <div class="bg-gray-700/30 rounded-lg p-3 text-center">
                                        <div class="text-2xl font-bold text-green-400">
                                            {{ $team->getAllMatches()->count() }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">Utakmica</div>
                                    </div>
                                </div>

                                {{-- Coach Info --}}
                                @if($team->coach_name)
                                    <div class="flex items-center gap-2 mb-4 text-sm text-gray-300 bg-gray-700/20 rounded-lg p-2">
                                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="text-gray-400">Trener:</span>
                                        <span class="font-medium text-white">{{ $team->coach_name }}</span>
                                    </div>
                                @endif

                                {{-- Team Colors --}}
                                @if($team->primary_color || $team->secondary_color)
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="text-sm text-gray-400">Boje:</span>
                                        <div class="flex gap-1">
                                            @if($team->primary_color)
                                                <div class="w-8 h-8 rounded-lg border-2 border-gray-600 shadow-lg"
                                                     style="background-color: {{ $team->primary_color }}"
                                                     title="Primarna boja"></div>
                                            @endif
                                            @if($team->secondary_color)
                                                <div class="w-8 h-8 rounded-lg border-2 border-gray-600 shadow-lg"
                                                     style="background-color: {{ $team->secondary_color }}"
                                                     title="Sekundarna boja"></div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- Action Buttons --}}
                                <div class="flex gap-2">
                                    <a href="{{ route('organizations.competitions.futsal.teams.show', [$organization, $competition, $team]) }}"
                                       class="flex-1 text-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg transition-all duration-200 font-semibold shadow-lg hover:shadow-green-500/50">
                                        Detalji
                                    </a>
                                    @can('update', $organization)
                                        <a href="{{ route('organizations.competitions.futsal.teams.edit', [$organization, $competition, $team]) }}"
                                           class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
