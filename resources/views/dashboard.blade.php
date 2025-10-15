<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Kontrolna Tabla
                </h2>
                <p class="text-gray-400 mt-1">Dobrodošli nazad, {{ Auth::user()->name }}!</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-400">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-sm text-gray-500">{{ now()->format('H:i') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Welcome Card -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-8 border border-gray-700/50 shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-2">Dobrodošli u Team Sphere</h3>
                        <p class="text-gray-400">Pogledajte svoje organizacije i nadolazeće mečeve.</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referee Section -->
            @if($isReferee)
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">Sudijska Kontrolna Tabla</h3>
                        <p class="text-gray-400">Vi ste sudija u jednoj ili više organizacija</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('referee.dashboard') }}" class="bg-gradient-to-r from-orange-600 to-red-700 hover:from-orange-700 hover:to-red-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-orange-500/25 inline-block">
                        <span class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Idi na Sudijsku Kontrolnu Tablu</span>
                        </span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- My Organizations Card -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">Moje Organizacije</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ $organizations->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-green-400 font-medium">{{ $organizations->count() > 0 ? '+' . $organizations->count() : '0' }}</span>
                            <span class="text-gray-500 ml-2">Organizacija gdje igrate</span>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Matches Card -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">Nadolazeći Mečevi</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ $upcomingMatches->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-green-400 font-medium">{{ $upcomingMatches->count() > 0 ? '+' . $upcomingMatches->count() : '0' }}</span>
                            <span class="text-gray-500 ml-2">Zakazanih mečeva</span>
                        </div>
                    </div>
                </div>

                <!-- Total Matches Card -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">Ukupno Mečeva</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ $players->sum(function($player) { return $player->homeMatches->count() + $player->awayMatches->count(); }) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-green-400 font-medium">{{ $players->sum(function($player) { return $player->homeMatches->count() + $player->awayMatches->count(); }) > 0 ? '+' . $players->sum(function($player) { return $player->homeMatches->count() + $player->awayMatches->count(); }) : '0' }}</span>
                            <span class="text-gray-500 ml-2">Svih vremena mečeva</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Organizations -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">Moje Organizacije</h3>
                    @if(Auth::user()->canCreateMoreOrganizations())
                        <a href="{{ route('organizations.create') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Kreiraj Organizaciju</span>
                            </span>
                        </a>
                    @endif
                </div>

                @if($organizations->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($organizations as $organization)
                            <a href="{{ route('organizations.show', $organization) }}" class="bg-gray-700/30 rounded-xl p-4 hover:bg-gray-600/30 transition-all duration-200 transform hover:scale-[1.02] cursor-pointer block">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr($organization->name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold">{{ $organization->name }}</h4>
                                        <p class="text-gray-400 text-sm">{{ $players->where('organization_id', $organization->id)->count() }} player profile(s)</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h4 class="text-white font-semibold mb-2">Još nema organizacija</h4>
                        <p class="text-gray-400 mb-4">Još niste dodani kao igrač ni u jednu organizaciju.</p>
                        @if(Auth::user()->canCreateMoreOrganizations())
                        <a href="{{ route('organizations.create') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 inline-block">
                            Kreirajte svoju prvu organizaciju
                        </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Upcoming Matches -->
            @if($upcomingMatches->count() > 0)
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h3 class="text-xl font-bold text-white mb-4">Nadolazeći Mečevi</h3>
                <p class="text-gray-400 mb-6">Vaši zakazani mečevi</p>

                <div class="space-y-4">
                    @foreach($upcomingMatches->sortBy('scheduled_at') as $match)
                        <div class="bg-gray-700/30 rounded-xl p-4 hover:bg-gray-600/30 transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="text-2xl">{{ $match->league->sport->icon }}</div>
                                    <div>
                                        <h4 class="text-white font-semibold">
                                            {{ $match->homePlayer?->name ?? 'TBD' }} vs {{ $match->awayPlayer?->name ?? 'TBD' }}
                                            @if($match->home_player_id == $players->first()->id || $match->away_player_id == $players->first()->id)
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    You
                                                </span>
                                            @endif
                                        </h4>
                                        <p class="text-gray-400 text-sm">
                                            {{ $match->league->name }} • {{ $match->scheduled_at ? $match->scheduled_at->format('M d, Y H:i') : 'Nije zakazano' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($match->status === 'scheduled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Scheduled
                                        </span>
                                    @elseif($match->status === 'in_progress')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                            LIVE
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($match->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
