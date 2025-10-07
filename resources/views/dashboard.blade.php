<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ __('Dashboard') }}
                </h2>
                <p class="text-gray-400 mt-1">{{ __('Welcome back, :name!', ['name' => Auth::user()->name]) }}</p>
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
                        <h3 class="text-2xl font-bold text-white mb-2">{{ __('Welcome to Team Sphere') }}</h3>
                        <p class="text-gray-400">{{ __('messages.app.manage_teams_description') }}</p>
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

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Organizations Card -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">{{ __('Organizations') }}</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ Auth::user()->organizations->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-green-400 font-medium">{{ Auth::user()->organizations->count() > 0 ? '+' . Auth::user()->organizations->count() : '0' }}%</span>
                            <span class="text-gray-500 ml-2">{{ __('messages.app.total_organizations') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Leagues Card -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">{{ __('Leagues') }}</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ Auth::user()->organizations->sum(function($org) { return $org->leagues->count(); }) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-green-400 font-medium">{{ Auth::user()->organizations->sum(function($org) { return $org->leagues->count(); }) > 0 ? '+' . Auth::user()->organizations->sum(function($org) { return $org->leagues->count(); }) : '0' }}%</span>
                            <span class="text-gray-500 ml-2">{{ __('messages.app.total_leagues') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Organizations -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">{{ __('messages.app.my_organizations') }}</h3>
                    @if(Auth::user()->canCreateMoreOrganizations())
                        <a href="{{ route('organizations.create') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>{{ __('messages.app.create_organization') }}</span>
                            </span>
                        </a>
                    @endif
                </div>

                @if(Auth::user()->organizations->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach(Auth::user()->organizations as $organization)
                            <a href="{{ route('organizations.show', $organization) }}" class="bg-gray-700/30 rounded-xl p-4 hover:bg-gray-600/30 transition-all duration-200 transform hover:scale-[1.02] cursor-pointer block">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr($organization->name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold">{{ $organization->name }}</h4>
                                        <p class="text-gray-400 text-sm">{{ $organization->user->currentPlan() ? $organization->user->currentPlan()->name : 'Free' }} Plan</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">{{ __('messages.app.leagues') }}:</span>
                                        <span class="text-white">{{ $organization->leagues->count() }}/{{ $organization->user->currentPlan() ? $organization->user->currentPlan()->max_leagues_per_organization : '∞' }}</span>
                                    </div>
                                    <div class="w-full bg-gray-600 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $organization->user->currentPlan() ? (($organization->leagues->count() / $organization->user->currentPlan()->max_leagues_per_organization) * 100) : 0 }}%"></div>
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
                        <h4 class="text-white font-semibold mb-2">{{ __('messages.app.no_organizations_yet') }}</h4>
                        <p class="text-gray-400 mb-4">{{ __('messages.app.create_first_org_description') }}</p>
                        <a href="{{ route('organizations.create') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 inline-block">
                            {{ __('messages.app.create_your_first_organization') }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- Available Sports -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h3 class="text-xl font-bold text-white mb-4">{{ __('messages.app.available_sports') }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach(\App\Models\Sport::active()->get() as $sport)
                        <div class="bg-gray-700/30 rounded-xl p-4 text-center hover:bg-gray-600/30 transition-all duration-200 transform hover:scale-[1.02] cursor-pointer group">
                            <div class="text-3xl mb-2">{{ $sport->icon }}</div>
                            <h4 class="text-white font-semibold text-sm mb-1">{{ $sport->name }}</h4>
                            <p class="text-gray-400 text-xs mb-2">{{ Str::limit($sport->description, 40) }}</p>

                            <!-- Sport Rules Preview -->
                            <div class="text-xs text-gray-500 space-y-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @if($sport->isPointsBased())
                                    <div>{{ $sport->getMaxPointsPerGame() }} {{ __('messages.app.points') }}/{{ __('messages.app.game') }}</div>
                                    <div>{{ $sport->getGamesToWin() }} {{ __('messages.app.games_to_win') }}</div>
                                @elseif($sport->isSetsGamesBased())
                                    <div>{{ $sport->getGamesPerSet() }} {{ __('messages.app.games') }}/{{ __('messages.app.set') }}</div>
                                    <div>{{ $sport->getSetsToWin() }} {{ __('messages.app.sets_to_win') }}</div>
                                @elseif($sport->isTimeBased())
                                    <div>{{ $sport->getRule('periods') }}x {{ $sport->getRule('period_duration') / 60 }}min</div>
                                    <div>{{ $sport->getPlayersPerTeam() }} {{ __('messages.app.players') }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
