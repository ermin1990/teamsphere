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

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- My Organizations -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-white">Moje Organizacije</h3>
                            <p class="text-gray-400">Upravljajte svojim organizacijama i takmičenjima</p>
                        </div>
                        @if(Auth::user()->canCreateMoreOrganizations())
                            <a href="{{ route('organizations.create') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25">
                                <span class="flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span>Kreiraj Organizaciju</span>
                                </span>
                            </a>
                        @endif
                    </div>

                @if($organizations->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($organizations as $organization)
                            <div class="bg-gray-700/30 rounded-xl p-6 hover:bg-gray-600/30 transition-all duration-200 border border-gray-600/20 hover:border-gray-500/30">
                                <!-- Organization Header -->
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                        <span class="text-white font-bold text-lg">{{ substr($organization->name, 0, 2) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-white font-bold text-lg">{{ $organization->name }}</h4>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="space-y-3">
                                    <a href="{{ route('organizations.show', $organization) }}" class="block bg-gray-600/50 hover:bg-gray-500/50 text-white px-4 py-3 rounded-lg transition-all duration-200 text-center font-semibold">
                                        <span class="flex items-center justify-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <span>Pogledaj Organizaciju</span>
                                        </span>
                                    </a>

                                    @if($organization->user_id === Auth::id())
                                        <div class="text-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Vlasnik
                                            </span>
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Vi ste u toj organizaciji
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-white mb-3">Još nema organizacija</h4>
                        <p class="text-gray-400 mb-6 max-w-md mx-auto">Još niste dodani kao igrač ni u jednu organizaciju. Kreirajte svoju prvu organizaciju da započnete sa upravljanjem takmičenjima.</p>
                        @if(Auth::user()->canCreateMoreOrganizations())
                        <a href="{{ route('organizations.create') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 inline-block">
                            <span class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span class="text-lg font-semibold">Kreirajte svoju prvu organizaciju</span>
                            </span>
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

            <!-- Plan & Limits Section -->
            @php
                $currentPlan = Auth::user()->currentPlan();
            @endphp
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">Vaš Plan i Ograničenja</h3>
                        <p class="text-gray-400 text-sm">Trenutni plan i dostupne mogućnosti</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Current Plan -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-sm">Trenutni Plan</span>
                            @if($currentPlan)
                                <span class="px-2 py-1 bg-green-600/20 text-green-400 text-xs rounded-full">{{ $currentPlan->name }}</span>
                            @else
                                <span class="px-2 py-1 bg-gray-600/20 text-gray-400 text-xs rounded-full">Free</span>
                            @endif
                        </div>
                        <div class="text-2xl font-bold text-white">
                            @if($currentPlan)
                                {{ $currentPlan->price }} {{ $currentPlan->currency }}/mo
                            @else
                                Free
                            @endif
                        </div>
                    </div>

                    <!-- Organizations Used -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-sm">Organizacije</span>
                            @if($currentPlan)
                                <span class="text-xs text-gray-500">{{ $usageStats['organizations_used'] }}/{{ $usageStats['max_organizations'] }}</span>
                            @endif
                        </div>
                        <div class="text-2xl font-bold text-white">{{ $usageStats['organizations_used'] }}</div>
                        <div class="text-xs text-gray-400">
                            @if($currentPlan)
                                {{ $usageStats['max_organizations'] - $usageStats['organizations_used'] }} preostalo
                            @else
                                Neograničeno
                            @endif
                        </div>
                    </div>

                    <!-- Competitions Used -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-sm">Turniri</span>
                            @if($currentPlan)
                                <span class="text-xs text-gray-500">{{ $usageStats['competitions_used'] }}/{{ $organizations->count() * $usageStats['max_competitions_per_organization'] }}</span>
                            @endif
                        </div>
                        <div class="text-2xl font-bold text-white">{{ $usageStats['competitions_used'] }}</div>
                        <div class="text-xs text-gray-400">
                            @if($currentPlan)
                                {{ ($organizations->count() * $usageStats['max_competitions_per_organization']) - $usageStats['competitions_used'] }} preostalo
                            @else
                                Neograničeno
                            @endif
                        </div>
                    </div>

                    <!-- Leagues Used -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-sm">Lige</span>
                            @if($currentPlan)
                                <span class="text-xs text-gray-500">{{ $usageStats['leagues_used'] }}/{{ $organizations->count() * $usageStats['max_leagues_per_organization'] }}</span>
                            @endif
                        </div>
                        <div class="text-2xl font-bold text-white">{{ $usageStats['leagues_used'] }}</div>
                        <div class="text-xs text-gray-400">
                            @if($currentPlan)
                                {{ ($organizations->count() * $usageStats['max_leagues_per_organization']) - $usageStats['leagues_used'] }} preostalo
                            @else
                                Neograničeno
                            @endif
                        </div>
                    </div>
                </div>

                                        @if(!$currentPlan || $usageStats['organizations_used'] >= $usageStats['max_organizations'])
                <div class="text-center">
                    <a href="#" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-purple-500/25 inline-block">
                        <span class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>Upgrade Plan</span>
                        </span>
                    </a>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
