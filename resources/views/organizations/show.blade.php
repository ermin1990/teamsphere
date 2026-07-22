<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <!-- Organization Info Section -->
            <div class="flex items-center space-x-5">
                <div>
                    <h2 class="font-bold text-2xl md:text-4xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                        {{ $organization->name }}
                    </h2>
                    @if($organization->description)
                        <p class="text-gray-400 text-sm md:text-base mt-1 line-clamp-1 max-w-xl">{{ $organization->description }}</p>
                    @endif
                    <div class="flex items-center mt-2 space-x-3 text-xs">
                        <span class="px-2 py-1 bg-blue-500/10 text-blue-400 rounded-md border border-blue-500/20">Vlasnik</span>
                        @if($organization->is_active)
                            <span class="px-2 py-1 bg-green-500/10 text-green-400 rounded-md border border-green-500/20">Aktivno</span>
                        @endif
                        @if($organization->sport)
                            <span class="px-2 py-1 bg-gray-700/50 text-gray-300 rounded-md border border-gray-600/50">{{ $organization->sport->icon }} {{ $organization->sport->name }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons Group -->
            <div class="flex flex-wrap items-center gap-3">
                @if(isset($isOwner) && $isOwner)
                    <div class="flex bg-gray-800/50 backdrop-blur-md p-1.5 rounded-2xl border border-gray-700/50 shadow-lg">
                        <a href="{{ route('organizations.edit', $organization) }}"
                           class="flex items-center space-x-2 px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-xl transition-all duration-200"
                           title="Postavke">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="hidden lg:inline text-sm font-semibold">Uredi</span>
                        </a>
                        <a href="{{ route('organizations.links.index', $organization) }}"
                           class="flex items-center space-x-2 px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-xl transition-all duration-200"
                           title="Banneri i Logo">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                            <span class="hidden lg:inline text-sm font-semibold">Banneri i Logo</span>
                        </a>
                        <a href="{{ route('organizations.seasons.index', $organization) }}"
                           class="flex items-center space-x-2 px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700/50 rounded-xl transition-all duration-200"
                           title="Sezone">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="hidden lg:inline text-sm font-semibold">Sezone</span>
                        </a>
                    </div>

                    <a href="{{ route('organizations.users.index', $organization) }}"
                       class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-2xl shadow-lg shadow-blue-500/25 transition-all duration-200 transform hover:scale-[1.03]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <span class="font-bold text-sm">Sudije</span>
                    </a>
                @endif

                @if($canManageAnnouncements ?? false)
                    <a href="{{ route('organizations.announcements.index', $organization) }}"
                       class="flex items-center space-x-2 bg-purple-600 hover:bg-purple-700 text-white px-5 py-3 rounded-2xl shadow-lg shadow-purple-500/25 transition-all duration-200 transform hover:scale-[1.03]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                        <span class="font-bold text-sm">Objave</span>
                    </a>
                    <a href="{{ route('organizations.rules', $organization) }}"
                       class="flex items-center space-x-2 bg-gray-700 hover:bg-gray-600 text-white px-5 py-3 rounded-2xl shadow-lg transition-all duration-200 transform hover:scale-[1.03]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="font-bold text-sm">Pravila</span>
                    </a>
                @endif

                <a href="{{ route('dashboard') }}" 
                   class="flex items-center justify-center p-3 text-gray-400 hover:text-white bg-gray-800/50 hover:bg-gray-700/50 rounded-2xl border border-gray-700/50 transition-all duration-200"
                   title="Nazad na Dashboard">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </a>
            </div>
        </div>
    </x-slot>    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Organization Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Leagues Count -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 border border-gray-700/50 shadow-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wide">Lige</p>
                            <p class="text-2xl font-bold text-white">{{ $organization->leagues->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Competitions Count -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 border border-gray-700/50 shadow-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wide">Turniri</p>
                            <p class="text-2xl font-bold text-white">{{ $organization->competitions->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Teams Count -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 border border-gray-700/50 shadow-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wide">Igrači</p>
                            <p class="text-2xl font-bold text-white">{{ optional($organization->players)->count() ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>            <!-- Leagues Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-white">Lige</h3>
                    @if($organization->user_id === Auth::id())
                        <a href="{{ route('organizations.competitions.create', $organization) }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-3 py-2 sm:px-4 sm:py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 inline-flex items-center justify-center">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span class="text-sm sm:text-base">Kreiraj Ligu</span>
                            </span>
                        </a>
                    @endif
                </div>

                @if($seasons->count() > 0)
                    <form method="GET" class="mb-6 flex items-center gap-3">
                        <label for="season_id" class="text-sm text-gray-400 font-medium">Sezona:</label>
                        <select name="season_id" id="season_id" onchange="this.form.submit()"
                                class="bg-gray-700 border border-gray-600 text-white text-sm rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" {{ empty($selectedSeasonId) ? 'selected' : '' }}>Sve sezone</option>
                            @foreach($seasons as $season)
                                <option value="{{ $season->id }}" {{ (string) $selectedSeasonId === (string) $season->id ? 'selected' : '' }}>
                                    {{ $season->name }}{{ $season->is_active ? ' (aktivna)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                @php
                    $seasonFilteredCompetitions = $selectedSeasonId
                        ? $organization->competitions->where('season_id', (int) $selectedSeasonId)
                        : $organization->competitions;
                    $activeLeagues = $seasonFilteredCompetitions->where('type', 'league')->whereIn('status', ['active', 'draft', 'in_progress']);
                    $completedLeagues = $seasonFilteredCompetitions->where('type', 'league')->where('status', 'completed');
                @endphp

                @if($activeLeagues->count() > 0)
                    <h4 class="text-white font-semibold mb-4 flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></span>
                        Aktivne Lige
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                        @foreach($activeLeagues as $competition)
                            <div class="bg-gray-700/30 rounded-xl p-4 hover:bg-gray-600/30 transition-all duration-200 relative group">
                                <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" class="block">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">{{ substr($competition->name, 0, 2) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-white font-semibold">{{ $competition->name }}</h4>
                                            <p class="text-gray-400 text-sm">{{ $competition->sport->name }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-400">Timovi:</span>
                                            <span class="text-white">{{ $competition->teams->count() }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-400">Status:</span>
                                            <span class="
                                                @if($competition->status === 'active' || $competition->status === 'in_progress') text-green-400
                                                @elseif($competition->status === 'draft') text-yellow-400
                                                @else text-red-400 @endif">
                                                {{ $competition->status === 'in_progress' ? 'U tijeku' : ucfirst($competition->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($completedLeagues->count() > 0)
                    <h4 class="text-gray-400 font-semibold mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Završene Lige
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($completedLeagues as $competition)
                            <div class="bg-gray-900/30 rounded-xl p-4 hover:bg-gray-800/30 transition-all duration-200 opacity-75 hover:opacity-100">
                                <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" class="block">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-10 h-10 bg-gray-700 rounded-xl flex items-center justify-center">
                                            <span class="text-gray-400 font-bold text-sm">{{ substr($competition->name, 0, 2) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-gray-300 font-semibold">{{ $competition->name }}</h4>
                                            <p class="text-gray-500 text-sm">{{ $competition->sport->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Završeno:</span>
                                        <span class="text-gray-400">{{ $competition->updated_at->format('d.m.Y') }}</span>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($activeLeagues->count() === 0 && $completedLeagues->count() === 0)
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-white font-semibold mb-2">Još nema liga</h4>
                        <p class="text-gray-400 mb-4">Kreirajte svoju prvu ligu</p>
                    </div>
                @endif
            </div>

            <!-- Competitions Section (Tournaments) -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-white">Turniri</h3>
                    @if($organization->user_id === Auth::id() && $organization->canCreateMoreCompetitions())
                        <a href="{{ route('organizations.competitions.create', $organization) }}" class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-3 py-2 sm:px-4 sm:py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-purple-500/25 inline-flex items-center justify-center">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm sm:text-base">Kreiraj Turnir</span>
                            </span>
                        </a>
                    @endif
                </div>

                @if($seasonFilteredCompetitions->count() > 0)
                    @php
                        $activeTournaments = $seasonFilteredCompetitions->where('type', 'tournament')->whereIn('status', ['active', 'draft']);
                        $completedTournaments = $seasonFilteredCompetitions->where('type', 'tournament')->where('status', 'completed');
                    @endphp

                    @if($activeTournaments->count() > 0)
                    <h4 class="text-white font-semibold mb-4 flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                        Aktivni Turniri
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                        @foreach($activeTournaments as $competition)
                            <div class="bg-gray-700/30 rounded-xl p-4 hover:bg-gray-600/30 transition-all duration-200 relative group">
                                <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" class="block">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">{{ substr($competition->name, 0, 2) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-white font-semibold">{{ $competition->name }}</h4>
                                            <p class="text-gray-400 text-sm">{{ $competition->sport->name }}</p>
                                        </div>
                                    </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Učesnici:</span>
                                        <span class="text-white">
                                            @if($competition->is_team_based)
                                                {{ $competition->teams->count() }}
                                            @else
                                                {{ $competition->players->count() }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Status:</span>
                                        <span class="
                                            @if($competition->status === 'active') text-green-400
                                            @elseif($competition->status === 'draft') text-yellow-400
                                            @elseif($competition->status === 'completed') text-blue-400
                                            @else text-red-400 @endif">
                                            {{ ucfirst($competition->status) }}
                                        </span>
                                    </div>
                                </div>
                                </a>
                                
                                @if($competition->status === 'draft')
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <form action="{{ route('organizations.competitions.destroy', [$organization, $competition]) }}" 
                                          method="POST" 
                                          onclick="event.stopPropagation();"
                                          onsubmit="return confirm('Are you sure you want to delete {{ $competition->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500/20 hover:bg-red-500/30 text-red-400 p-2 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @endif

                    @if($completedTournaments->count() > 0)
                    <h4 class="text-gray-400 font-semibold mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Završeni Turniri
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($completedTournaments as $competition)
                            <div class="bg-gray-900/30 rounded-xl p-4 hover:bg-gray-800/30 transition-all duration-200 opacity-75 hover:opacity-100">
                                <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" class="block">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-10 h-10 bg-gray-700 rounded-xl flex items-center justify-center">
                                            <span class="text-gray-400 font-bold text-sm">{{ substr($competition->name, 0, 2) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-gray-300 font-semibold">{{ $competition->name }}</h4>
                                            <p class="text-gray-500 text-sm">{{ $competition->sport->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Završeno:</span>
                                        <span class="text-gray-400">{{ $competition->updated_at->format('d.m.Y') }}</span>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    @if($activeTournaments->count() === 0 && $completedTournaments->count() === 0)
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-white font-semibold mb-2">Još nema turnira</h4>
                            <p class="text-gray-400 mb-4">Kreirajte svoj prvi turnir</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-white font-semibold mb-2">Još nema turnira</h4>
                        <p class="text-gray-400 mb-4">Kreirajte svoj prvi turnir</p>
                    </div>
                @endif
            </div>

            <!-- Teams Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-white">Timovi</h3>
                    @if($organization->user_id === Auth::id())
                        <a href="{{ route('organizations.teams.index', $organization) }}" class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-3 py-2 sm:px-4 sm:py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-emerald-500/25 inline-flex items-center justify-center">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="text-sm sm:text-base">Upravljaj Timovima</span>
                            </span>
                        </a>
                    @endif
                </div>
                
                @if($organization->teams->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($organization->teams->take(6) as $team)
                            <div class="bg-gray-700/30 rounded-xl p-4 border border-gray-600/30">
                                <h4 class="text-white font-semibold">{{ $team->name }}</h4>
                                <p class="text-gray-400 text-xs mt-1">{{ $team->players->count() }} Igrača</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic text-sm">Nema definisanih timova.</p>
                @endif
            </div>

            <!-- Players Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-white">Igrači</h3>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 sm:ml-auto">
                        @if((optional($organization->players)->count() ?? 0) > 6)
                            <a href="{{ route('organizations.players.index', $organization) }}" class="text-emerald-400 hover:text-emerald-300 text-sm font-medium transition-colors text-center sm:text-left">
                                Pogledaj sve {{ optional($organization->players)->count() ?? 0 }} Igrači →
                            </a>
                        @endif
                        @if($organization->user_id === Auth::id())
                            <a href="{{ route('organizations.players.index', $organization) }}" class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-3 py-2 sm:px-4 sm:py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-emerald-500/25 inline-flex items-center justify-center">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span class="text-sm sm:text-base">Upravljaj Igračima</span>
                                </span>
                            </a>
                        @endif
                    </div>
                </div>

                @if((optional($organization->players)->count() ?? 0) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach(optional($organization->players)->take(6) ?? collect() as $player)
                            <a href="{{ route('organizations.players.show', ['organization' => $organization, 'player' => $player]) }}" class="block">
                                <div class="bg-gray-700/30 rounded-xl p-4 hover:bg-gray-600/30 transition-all duration-200 transform hover:scale-[1.02] cursor-pointer">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">{{ substr($player->name, 0, 2) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-white font-semibold">{{ $player->name }}</h4>
                                            <p class="text-gray-400 text-sm">
                                                @if($player->teams->count() > 0)
                                                    {{ $player->teams->first()->name }}
                                                @else
                                                    {{ $player->position ?: 'Nema kluba' }}
                                                @endif
                                            </p>
                                        </div>
                                        @if($player->jersey_number)
                                            <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-2 py-1 rounded-lg text-xs font-bold">
                                                {{ $player->jersey_number }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-400">Tip:</span>
                                            <span class="text-{{ $player->user ? 'green' : 'yellow' }}-400">{{ $player->user ? 'Registrovan' : 'Imenovan' }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-400">Status:</span>
                                            <span class="text-{{ $player->is_active ? 'green' : 'red' }}-400">{{ $player->is_active ? 'Aktivan' : 'Neaktivan' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-white font-semibold mb-2">Još nema igrača</h4>
                        <p class="text-gray-400 mb-4">Dodajte igrače da počnete graditi svoje timove i pratiti statistiku</p>
                        @if($organization->user_id === Auth::id())
                            <a href="{{ route('organizations.players.index', $organization) }}" class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-emerald-500/25 inline-block">
                                Dodajte Svojeg Prvog Igrača
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Categories Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-white">Kategorije</h3>
                    @if($organization->user_id === Auth::id())
                        <a href="{{ route('organizations.categories.index', $organization) }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-3 py-2 sm:px-4 sm:py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 inline-flex items-center justify-center">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span class="text-sm sm:text-base">Upravljaj Kategorijama</span>
                            </span>
                        </a>
                    @endif
                </div>
                
                @if($organization->categories->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($organization->categories as $category)
                            <span class="px-3 py-1 bg-gray-700/50 border border-gray-600/50 rounded-full text-gray-300 text-sm">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic text-sm">Nema definisanih kategorija.</p>
                @endif
            </div>

            <!-- Tables Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-white">Stolovi</h3>
                    @if($organization->user_id === Auth::id())
                        <a href="{{ route('organizations.tables.index', $organization) }}" class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-3 py-2 sm:px-4 sm:py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-purple-500/25 inline-flex items-center justify-center">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm sm:text-base">Upravljaj Stolovima</span>
                            </span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Recent Friendly Matches -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex flex-col sm:flex-row gap-4 mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-white">Nedavni Prijateljski Mečevi</h3>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 sm:ml-auto">
                        @if($organization->user_id === Auth::id())
                        <a href="{{ route('organizations.friendly-matches.index', $organization) }}" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-3 py-2 sm:px-4 sm:py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-green-500/25 inline-flex items-center justify-center">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span class="text-sm sm:text-base">Započni Novi Meč</span>
                            </span>
                        </a>
                        @endif
                        <a href="{{ route('organizations.friendly-matches.index', $organization) }}" class="text-green-400 hover:text-green-300 text-sm font-medium transition-colors text-center sm:text-left">
                            Pogledaj Sve →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Organization Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Details -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Detalji Organizacije</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Naziv:</span>
                            <span class="text-white">{{ $organization->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">URL Slug:</span>
                            <span class="text-white">{{ $organization->url_slug }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Kreirano:</span>
                            <span class="text-white">{{ $organization->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Status:</span>
                            <span class="text-{{ $organization->is_active ? 'green' : 'red' }}-400">{{ $organization->is_active ? 'Aktivno' : 'Neaktivno' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Vlasnik:</span>
                            <span class="text-white">{{ $organization->user->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Plan & Limits -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Plan i Ograničenja</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Vaš Plan:</span>
                            <span class="text-blue-400 font-medium">{{ $organization->user->currentPlan() ? $organization->user->currentPlan()->name : 'Free' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Iskorištenih Liga:</span>
                            <span class="text-white">{{ $organization->leagues->count() }}/{{ $organization->user->currentPlan() ? $organization->user->currentPlan()->max_leagues_per_organization : '∞' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Iskorištenih Turnira:</span>
                            <span class="text-white">{{ $organization->competitions->count() }}/{{ $organization->user->currentPlan() ? $organization->user->currentPlan()->max_competitions_per_organization : '∞' }}</span>
                        </div>
                        <div class="w-full bg-gray-600 rounded-full h-2 mt-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $organization->user->currentPlan() ? (($organization->leagues->count() / $organization->user->currentPlan()->max_leagues_per_organization) * 100) : 0 }}%"></div>
                        </div>
                        <div class="pt-2">
                            <p class="text-gray-400 text-sm">Preostalih liga: <span class="text-white">{{ $organization->getRemainingLeaguesCount() }}</span></p>
                            <p class="text-gray-400 text-sm">Preostalih turnira: <span class="text-white">{{ $organization->getRemainingCompetitionsCount() }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            @if($organization->leagues->count() === 0)
            <div class="bg-red-500/10 border border-red-500/20 rounded-2xl p-6">
                <h3 class="text-xl font-bold text-red-400 mb-4">Opasna Zona</h3>
                <p class="text-gray-400 mb-4">Jednom kada obrišete ovu organizaciju, nema povratka. Budite sigurni.</p>
                <form method="POST" action="{{ route('organizations.destroy', $organization) }}"                     onsubmit="return confirm('Da li ste sigurni da želite obrisati ovu organizaciju? Ova akcija se ne može poništiti.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl transition-all duration-200">
                        Obriši Organizaciju
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>