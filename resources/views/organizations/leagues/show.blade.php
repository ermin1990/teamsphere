 <x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $league->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $organization->name }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-3 py-1 text-sm rounded-full
                    @if($league->status === 'active') bg-green-500/20 text-green-400
                    @elseif($league->status === 'draft') bg-yellow-500/20 text-yellow-400
                    @elseif($league->status === 'completed') bg-blue-500/20 text-blue-400
                    @else bg-red-500/20 text-red-400 @endif">
                    {{ ucfirst($league->status) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- League Info -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- League Details -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-xl font-semibold text-white mb-4">{{ __('League Details') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Sport') }}</label>
                                <p class="text-white">{{ $league->sport->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Competition Type') }}</label>
                                <p class="text-white">{{ $league->is_team_based ? __('Team-based') : __('Individual') }}</p>
                            </div>
                            @if($league->start_date)
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Start Date') }}</label>
                                <p class="text-white">{{ $league->start_date->format('M d, Y') }}</p>
                            </div>
                            @endif
                            @if($league->end_date)
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('End Date') }}</label>
                                <p class="text-white">{{ $league->end_date->format('M d, Y') }}</p>
                            </div>
                            @endif
                            @if($league->is_team_based && $league->max_teams)
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Max Teams') }}</label>
                                <p class="text-white">{{ $league->max_teams }}</p>
                            </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Created') }}</label>
                                <p class="text-white">{{ $league->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        @if($league->description)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-400 mb-2">{{ __('Description') }}</label>
                            <p class="text-white">{{ $league->description }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- League Settings -->
                    @if($league->settings)
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-xl font-semibold text-white mb-4">{{ __('League Settings') }}</h3>
                        <div class="space-y-4">
                            @if($league->is_team_based || isset($league->settings['format']))
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Competition Format') }}</label>
                                <p class="text-white">
                                    @if(isset($league->settings['format']))
                                        @switch($league->settings['format'])
                                            @case('round_robin')
                                                {{ __('Single Round Robin') }}
                                                @break
                                            @case('dual_robin')
                                                {{ __('Double Round Robin') }}
                                                @break
                                            @case('dual_robin_knockout')
                                                {{ __('Double Round Robin + Knockout') }}
                                                @break
                                            @case('knockout')
                                                {{ __('Knockout Only') }}
                                                @break
                                            @default
                                                {{ ucfirst(str_replace('_', ' ', $league->settings['format'])) }}
                                        @endswitch
                                    @else
                                        <span class="text-gray-500 italic">{{ __('Not configured yet') }}</span>
                                    @endif
                                </p>
                            </div>
                            @endif

                            @if($league->is_team_based && $league->sport->slug !== 'stoni-tenis')
                                @if(isset($league->settings['points_win']))
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Points for Win') }}</label>
                                        <p class="text-white">{{ $league->settings['points_win'] }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Points for Draw') }}</label>
                                        <p class="text-white">{{ $league->settings['points_draw'] ?? 1 }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Points for Loss') }}</label>
                                        <p class="text-white">{{ $league->settings['points_loss'] }}</p>
                                    </div>
                                </div>
                                @endif
                            @else
                                @if(isset($league->settings['sets_to_win']))
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Sets to Win Match') }}</label>
                                    <p class="text-white">{{ __('Best of :sets sets (:wins sets to win)', ['sets' => $league->settings['sets_to_win'] * 2 - 1, 'wins' => $league->settings['sets_to_win']]) }}</p>
                                </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Teams/Players Section -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-white">
                                {{ $league->is_team_based ? __('Teams') : __('Players') }}
                            </h3>
                        </div>

                        @if($league->is_team_based)
                            @if($league->teams->count() > 0)
                            <div class="space-y-3">
                                @foreach($league->teams as $team)
                                <div class="flex items-center justify-between p-4 bg-gray-700/30 rounded-lg">
                                    <div>
                                        <h4 class="text-white font-medium">{{ $team->name }}</h4>
                                        <p class="text-gray-400 text-sm">{{ $team->players->count() }} players</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400">
                                            Active
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-white font-semibold mb-2">{{ __('No teams yet') }}</h4>
                                <p class="text-gray-400">{{ __('Get started by adding your first team.') }}</p>
                            </div>
                            @endif
                        @else
                            @if($league->players->count() > 0)
                            <div class="space-y-3">
                                @foreach($league->players as $player)
                                <div class="flex items-center justify-between p-4 bg-gray-700/30 rounded-lg">
                                    <div>
                                        <h4 class="text-white font-medium">{{ $player->name }}</h4>
                                        @if($player->email)
                                        <div class="text-gray-400 text-sm">{{ $player->email }}</div>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400">
                                            Active
                                        </span>
                                        <span class="text-gray-400 text-xs">
                                            Joined {{ \Carbon\Carbon::parse($player->pivot->joined_at)->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-white font-semibold mb-2">{{ __('No players yet') }}</h4>
                                <p class="text-gray-400">{{ __('Get started by adding players to the league.') }}</p>
                            </div>
                            @endif
                        @endif
                    </div>

                    <!-- Select Players Section -->
                    @if($league->status === 'draft')
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-xl font-semibold text-white mb-4">{{ __('Select Players to Start League') }}</h3>

                        @php
                            // Get players that are not already in the league
                            $currentPlayerIds = $league->players->pluck('id')->toArray();
                            $availablePlayers = $organization->players
                                ->filter(function($player) use ($currentPlayerIds) {
                                    return !in_array($player->id, $currentPlayerIds);
                                });
                        @endphp

                        @if($availablePlayers->count() > 0)
                        <form id="addPlayersForm" action="{{ route('organizations.leagues.addPlayers', [$organization, $league]) }}" method="POST">
                            @csrf
                            @method('POST')

                            <div class="space-y-3 mb-6">
                                @foreach($availablePlayers as $player)
                                <div class="flex items-center p-4 bg-gray-700/30 rounded-lg">
                                    <input type="checkbox" id="player_{{ $player->id }}" name="player_ids[]" value="{{ $player->id }}"
                                           class="w-4 h-4 text-green-600 bg-gray-700 border-gray-600 rounded focus:ring-green-500 focus:ring-2">
                                    <label for="player_{{ $player->id }}" class="ml-3 flex-1">
                                        <div class="text-white font-medium">{{ $player->name }}</div>
                                        @if($player->email)
                                        <div class="text-gray-400 text-sm">{{ $player->email }}</div>
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-400">
                                    {{ __('Selected:') }} <span id="selectedCount">0</span> {{ __('players') }}
                                </div>
                                <button type="submit"
                                        class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-green-500/25">
                                    {{ __('Add Selected Players to League') }}
                                </button>
                            </div>
                        </form>
                        @elseif($organization->players->count() > 0)
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h4 class="text-white font-semibold mb-2">{{ __('All players already added') }}</h4>
                            <p class="text-gray-400">{{ __('All players from this organization have been added to the league.') }}</p>
                        </div>
                        @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-white font-semibold mb-2">{{ __('No players available') }}</h4>
                            <p class="text-gray-400">{{ __('Add players to your organization first.') }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">

                    <!-- Quick Actions -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-4">{{ __('Quick Actions') }}</h3>
                        <div class="space-y-3">
                            @if($league->status === 'draft')
                            <button onclick="openFormatModal()"
                                    class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-purple-500/25">
                                {{ __('Set Competition Format') }}
                            </button>

                            <button onclick="openRulesModal()"
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-blue-500/25">
                                {{ __('Configure Rules') }}
                            </button>

                            <button onclick="startLeague()"
                                    class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-green-500/25">
                                {{ __('Start League') }}
                            </button>
                            @endif

                            <a href="{{ route('organizations.show', $organization) }}"
                               class="w-full bg-gray-700/50 hover:bg-gray-600/50 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 text-center block">
                                {{ __('Back to Organization') }}
                            </a>

                            <hr class="border-gray-600/50">

                            <form method="POST" action="{{ route('organizations.leagues.destroy', [$organization, $league]) }}"
                                  onsubmit="return confirm('{{ __('Are you sure you want to delete this league? This action cannot be undone.') }}')"
                                  class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-red-500/25">
                                    {{ __('Delete League') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- League Stats -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-4">{{ __('Statistics') }}</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-400">{{ $league->is_team_based ? __('Teams') : __('Players') }}</span>
                                <span class="text-white font-medium">{{ $league->is_team_based ? $league->teams->count() : $league->players->count() }}</span>
                            </div>
                            @if($league->is_team_based && $league->max_teams)
                            <div class="flex justify-between">
                                <span class="text-gray-400">{{ __('Max Teams') }}</span>
                                <span class="text-white font-medium">{{ $league->max_teams }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-400">{{ __('Status') }}</span>
                                <span class="text-white font-medium">{{ ucfirst($league->status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Format Configuration Modal -->
    @if($league->status === 'draft')
    <div id="formatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-white">{{ __('Set Competition Format') }}</h3>
                        <button onclick="closeFormatModal()" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form action="{{ route('organizations.leagues.update', [$organization, $league]) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="format_only" class="block text-sm font-medium text-white mb-2">
                            {{ __('Competition Format') }} <span class="text-red-400">*</span>
                        </label>
                        <select id="format_only" name="format" required
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">{{ __('Select format') }}</option>
                            <option value="round_robin" {{ old('format', $league->settings['format'] ?? '') === 'round_robin' ? 'selected' : '' }}>
                                {{ __('Single Round Robin') }}
                            </option>
                            <option value="dual_robin" {{ old('format', $league->settings['format'] ?? '') === 'dual_robin' ? 'selected' : '' }}>
                                {{ __('Double Round Robin') }}
                            </option>
                            <option value="dual_robin_knockout" {{ old('format', $league->settings['format'] ?? '') === 'dual_robin_knockout' ? 'selected' : '' }}>
                                {{ __('Double Round Robin + Knockout') }}
                            </option>
                            <option value="knockout" {{ old('format', $league->settings['format'] ?? '') === 'knockout' ? 'selected' : '' }}>
                                {{ __('Knockout Only') }}
                            </option>
                        </select>
                        @error('format')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-600/50">
                        <button type="button" onclick="closeFormatModal()"
                                class="px-6 py-3 border border-gray-600/50 text-white/70 hover:text-white hover:border-gray-500 rounded-lg transition-all duration-200">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                                class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-8 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-purple-500/25">
                            {{ __('Save Format') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Rules Configuration Modal -->
    <div id="rulesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-white">{{ __('Configure League Rules') }}</h3>
                        <button onclick="closeRulesModal()" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form action="{{ route('organizations.leagues.update', [$organization, $league]) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    @if($league->is_team_based && $league->sport->slug !== 'stoni-tenis')
                        <!-- Team-based competition settings -->
                        <div>
                            <label for="format" class="block text-sm font-medium text-white mb-2">
                                {{ __('Competition Format') }} <span class="text-red-400">*</span>
                            </label>
                            <select id="format" name="format" required
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">{{ __('Select format') }}</option>
                                <option value="round_robin" {{ old('format', $league->settings['format'] ?? '') === 'round_robin' ? 'selected' : '' }}>
                                    {{ __('Single Round Robin') }}
                                </option>
                                <option value="dual_robin" {{ old('format', $league->settings['format'] ?? '') === 'dual_robin' ? 'selected' : '' }}>
                                    {{ __('Double Round Robin') }}
                                </option>
                                <option value="dual_robin_knockout" {{ old('format', $league->settings['format'] ?? '') === 'dual_robin_knockout' ? 'selected' : '' }}>
                                    {{ __('Double Round Robin + Knockout') }}
                                </option>
                                <option value="knockout" {{ old('format', $league->settings['format'] ?? '') === 'knockout' ? 'selected' : '' }}>
                                    {{ __('Knockout Only') }}
                                </option>
                            </select>
                            @error('format')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Points system -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="points_win" class="block text-sm font-medium text-white mb-2">
                                    {{ __('Points for Win') }} <span class="text-red-400">*</span>
                                </label>
                                <input type="number" id="points_win" name="points_win"
                                       value="{{ old('points_win', $league->settings['points_win'] ?? 3) }}"
                                       min="1" max="10" required
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                @error('points_win')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="points_draw" class="block text-sm font-medium text-white mb-2">
                                    {{ __('Points for Draw') }}
                                </label>
                                <input type="number" id="points_draw" name="points_draw"
                                       value="{{ old('points_draw', $league->settings['points_draw'] ?? 1) }}"
                                       min="0" max="5"
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                @error('points_draw')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="points_loss" class="block text-sm font-medium text-white mb-2">
                                    {{ __('Points for Loss') }} <span class="text-red-400">*</span>
                                </label>
                                <input type="number" id="points_loss" name="points_loss"
                                       value="{{ old('points_loss', $league->settings['points_loss'] ?? 0) }}"
                                       min="0" max="5" required
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                @error('points_loss')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @else
                        <!-- Individual competition settings or Table Tennis -->
                        <div>
                            <label for="sets_to_win" class="block text-sm font-medium text-white mb-2">
                                {{ __('Sets to Win Match') }} <span class="text-red-400">*</span>
                            </label>
                            <select id="sets_to_win" name="sets_to_win" required
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">{{ __('Select sets to win') }}</option>
                                <option value="2" {{ old('sets_to_win', $league->settings['sets_to_win'] ?? '') == 2 ? 'selected' : '' }}>
                                    {{ __('Best of 3 (2 sets to win)') }}
                                </option>
                                <option value="3" {{ old('sets_to_win', $league->settings['sets_to_win'] ?? '') == 3 ? 'selected' : '' }}>
                                    {{ __('Best of 5 (3 sets to win)') }}
                                </option>
                                <option value="4" {{ old('sets_to_win', $league->settings['sets_to_win'] ?? '') == 4 ? 'selected' : '' }}>
                                    {{ __('Best of 7 (4 sets to win)') }}
                                </option>
                            </select>
                            @error('sets_to_win')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-600/50">
                        <button type="button" onclick="closeRulesModal()"
                                class="px-6 py-3 border border-gray-600/50 text-white/70 hover:text-white hover:border-gray-500 rounded-lg transition-all duration-200">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                                class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-blue-500/25">
                            {{ __('Save Rules') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Update selected players count
        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('input[name="player_ids[]"]:checked');
            document.getElementById('selectedCount').textContent = checkboxes.length;
        }

        // Add event listeners to checkboxes
        document.querySelectorAll('input[name="player_ids[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        // Initialize count on page load
        document.addEventListener('DOMContentLoaded', updateSelectedCount);

        function startLeague() {
            if (confirm('{{ __("Are you sure you want to start this league?") }}')) {
                // Create a form to submit PATCH request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("organizations.leagues.start", [$organization, $league]) }}';

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Add method spoofing for PATCH
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function openFormatModal() {
            document.getElementById('formatModal').classList.remove('hidden');
        }

        function closeFormatModal() {
            document.getElementById('formatModal').classList.add('hidden');
        }

        // Close format modal when clicking outside
        @if($league->status === 'draft')
        document.getElementById('formatModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFormatModal();
            }
        });
        @endif

        function openRulesModal() {
            document.getElementById('rulesModal').classList.remove('hidden');
        }

        function closeRulesModal() {
            document.getElementById('rulesModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('rulesModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRulesModal();
            }
        });
    </script>
</x-app-layout>