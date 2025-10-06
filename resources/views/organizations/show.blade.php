<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $organization->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $organization->description ?: __('No description provided') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('organizations.edit', $organization) }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('dashboard') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    ← {{ __('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Organization Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Leagues Count -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">{{ __('Leagues') }}</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ $organization->leagues->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-green-400 font-medium">{{ $organization->leagues->count() > 0 ? '+' . $organization->leagues->count() : '0' }}%</span>
                            <span class="text-gray-500 ml-2">{{ __('total leagues') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Teams Count -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">{{ __('Players') }}</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ optional($organization->players)->count() ?? 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-green-400 font-medium">{{ (optional($organization->players)->count() ?? 0) > 0 ? '+' . (optional($organization->players)->count() ?? 0) : '0' }}%</span>
                            <span class="text-gray-500 ml-2">{{ __('messages.app.total_players') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Matches Count -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">{{ __('Matches') }}</p>
                            <p class="text-3xl font-bold text-white mt-1">0</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-green-400 font-medium">+0%</span>
                            <span class="text-gray-500 ml-2">{{ __('messages.app.from_last_month') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leagues Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">{{ __('Leagues') }}</h3>
                    @if($organization->canCreateMoreLeagues())
                        <a href="{{ route('organizations.leagues.create', $organization) }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 inline-block">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>{{ __('Create League') }}</span>
                            </span>
                        </a>
                    @endif
                </div>

                @if($organization->leagues->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($organization->leagues as $league)
                            <a href="{{ route('organizations.leagues.show', [$organization, $league]) }}" class="bg-gray-700/30 rounded-xl p-4 hover:bg-gray-600/30 transition-all duration-200 transform hover:scale-[1.02] cursor-pointer block">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr($league->name, 0, 2) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-white font-semibold">{{ $league->name }}</h4>
                                        <p class="text-gray-400 text-sm">{{ $league->sport->name }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">{{ $league->is_team_based ? __('Teams') : __('Players') }}:</span>
                                        <span class="text-white">{{ $league->teams->count() }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">{{ __('Type') }}:</span>
                                        <span class="text-white">{{ $league->is_team_based ? __('Team') : __('Individual') }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">{{ __('Status') }}:</span>
                                        <span class="
                                            @if($league->status === 'active') text-green-400
                                            @elseif($league->status === 'draft') text-yellow-400
                                            @elseif($league->status === 'completed') text-blue-400
                                            @else text-red-400 @endif">
                                            {{ ucfirst($league->status) }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-white font-semibold mb-2">{{ __('messages.app.no_leagues_yet') }}</h4>
                        <p class="text-gray-400 mb-4">{{ __('messages.app.create_first_league') }}</p>
                        @if($organization->canCreateMoreLeagues())
                            <button class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 inline-block">
                                {{ __('Create Your First League') }}
                            </button>
                        @else
                            <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-4 inline-block">
                                <p class="text-yellow-400 text-sm">{{ __('messages.app.max_leagues_reached') }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Players Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">{{ __('messages.app.players') }}</h3>
                    <a href="{{ route('organizations.players.index', $organization) }}" class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-4 py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-emerald-500/25">
                        <span class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>{{ __('messages.app.manage_players') }}</span>
                        </span>
                    </a>
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
                                            <p class="text-gray-400 text-sm">{{ $player->position ?: __('messages.app.no_position') }}</p>
                                        </div>
                                        @if($player->jersey_number)
                                            <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-2 py-1 rounded-lg text-xs font-bold">
                                                {{ $player->jersey_number }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-400">{{ __('Type') }}:</span>
                                            <span class="text-{{ $player->user ? 'green' : 'yellow' }}-400">{{ $player->user ? __('messages.app.registered') : __('messages.app.named') }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-400">{{ __('Status') }}:</span>
                                            <span class="text-{{ $player->is_active ? 'green' : 'red' }}-400">{{ $player->is_active ? __('messages.app.active') : __('messages.app.inactive') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    @if((optional($organization->players)->count() ?? 0) > 6)
                        <div class="mt-4 text-center">
                            <a href="{{ route('organizations.players.index', $organization) }}" class="text-emerald-400 hover:text-emerald-300 text-sm font-medium transition-colors">
                                {{ __('messages.app.view_all') }} {{ optional($organization->players)->count() ?? 0 }} {{ __('messages.app.players') }} →
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-white font-semibold mb-2">{{ __('messages.app.no_players_yet') }}</h4>
                        <p class="text-gray-400 mb-4">{{ __('messages.app.add_players_description') }}</p>
                        <a href="{{ route('organizations.players.index', $organization) }}" class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-emerald-500/25 inline-block">
                            {{ __('messages.app.add_your_first_player') }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- Recent Friendly Matches -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">{{ __('messages.app.recent_friendly_matches') }}</h3>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('organizations.friendly-matches.index', $organization) }}" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-green-500/25">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>{{ __('messages.app.start_new_match') }}</span>
                            </span>
                        </a>
                        <a href="{{ route('organizations.friendly-matches.index', $organization) }}" class="text-green-400 hover:text-green-300 text-sm font-medium transition-colors">
                            {{ __('messages.app.view_all_caps') }} →
                        </a>
                    </div>
                </div>

                <livewire:friendly-matches-list :organization-id="$organization->id" :organization="$organization" :limit="6" />
            </div>

            <!-- Organization Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Details -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">{{ __('messages.app.organization_details') }}</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ __('messages.app.name') }}:</span>
                            <span class="text-white">{{ $organization->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ __('messages.app.url_slug') }}:</span>
                            <span class="text-white">{{ $organization->slug }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ __('messages.app.created') }}:</span>
                            <span class="text-white">{{ $organization->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ __('messages.app.status') }}:</span>
                            <span class="text-{{ $organization->is_active ? 'green' : 'red' }}-400">{{ $organization->is_active ? __('Active') : __('Inactive') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ __('messages.app.owner') }}:</span>
                            <span class="text-white">{{ $organization->user->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Plan & Limits -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">{{ __('messages.app.plan_limits') }}</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ __('messages.app.your_plan') }}:</span>
                            <span class="text-blue-400 font-medium">{{ $organization->user->currentPlan() ? $organization->user->currentPlan()->name : 'Free' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ __('messages.app.leagues_used') }}:</span>
                            <span class="text-white">{{ $organization->leagues->count() }}/{{ $organization->user->currentPlan() ? $organization->user->currentPlan()->max_leagues_per_organization : '∞' }}</span>
                        </div>
                        <div class="w-full bg-gray-600 rounded-full h-2 mt-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $organization->user->currentPlan() ? (($organization->leagues->count() / $organization->user->currentPlan()->max_leagues_per_organization) * 100) : 0 }}%"></div>
                        </div>
                        <div class="pt-2">
                            <p class="text-gray-400 text-sm">{{ __('messages.app.remaining_leagues') }}: <span class="text-white">{{ $organization->getRemainingLeaguesCount() }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            @if($organization->leagues->count() === 0)
            <div class="bg-red-500/10 border border-red-500/20 rounded-2xl p-6">
                <h3 class="text-xl font-bold text-red-400 mb-4">{{ __('messages.app.danger_zone') }}</h3>
                <p class="text-gray-400 mb-4">{{ __('messages.app.delete_warning') }}</p>
                <form method="POST" action="{{ route('organizations.destroy', $organization) }}"                     onsubmit="return confirm('{{ __('messages.app.delete_confirmation') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl transition-all duration-200">
                        {{ __('messages.app.delete_organization') }}
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>