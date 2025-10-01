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
                    @else bg-red-500/20 text-red-400 @endif"
                >
                    {{ ucfirst($league->status) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-6">

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

                    @if($league->status === 'active')
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-xl font-semibold text-white mb-4">{{ __('Standings') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-700">
                                        <th class="text-left py-3 px-2 text-gray-400 font-medium">#</th>
                                        <th class="text-left py-3 px-2 text-gray-400 font-medium">{{ $league->is_team_based ? __('Team') : __('Player') }}</th>
                                        <th class="text-center py-3 px-2 text-gray-400 font-medium">{{ __('P') }}</th>
                                        <th class="text-center py-3 px-2 text-gray-400 font-medium">{{ __('W') }}</th>
                                        <th class="text-center py-3 px-2 text-gray-400 font-medium">{{ __('D') }}</th>
                                        <th class="text-center py-3 px-2 text-gray-400 font-medium">{{ __('L') }}</th>
                                        <th class="text-center py-3 px-2 text-gray-400 font-medium">{{ __('GF') }}</th>
                                        <th class="text-center py-3 px-2 text-gray-400 font-medium">{{ __('GA') }}</th>
                                        <th class="text-center py-3 px-2 text-gray-400 font-medium">{{ __('GD') }}</th>
                                        <th class="text-center py-3 px-2 text-gray-400 font-medium">{{ __('Pts') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($league->standings->sortBy('position') as $standing)
                                    <tr class="border-b border-gray-700/50 hover:bg-gray-700/20">
                                        <td class="py-3 px-2 text-white font-medium">{{ $standing->position }}</td>
                                        <td class="py-3 px-2 text-white">{{ $standing->participant_name }}</td>
                                        <td class="py-3 px-2 text-center text-gray-300">{{ $standing->played }}</td>
                                        <td class="py-3 px-2 text-center text-green-400">{{ $standing->won }}</td>
                                        <td class="py-3 px-2 text-center text-yellow-400">{{ $standing->drawn }}</td>
                                        <td class="py-3 px-2 text-center text-red-400">{{ $standing->lost }}</td>
                                        <td class="py-3 px-2 text-center text-gray-300">{{ $standing->goals_for }}</td>
                                        <td class="py-3 px-2 text-center text-gray-300">{{ $standing->goals_against }}</td>
                                        <td class="py-3 px-2 text-center {{ $standing->goal_difference >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                            {{ $standing->goal_difference >= 0 ? '+' : '' }}{{ $standing->goal_difference }}
                                        </td>
                                        <td class="py-3 px-2 text-center text-white font-bold">{{ $standing->points }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(true)
                        <div class="mt-6 pt-6 border-t border-gray-600/50">
                            <h4 class="text-lg font-semibold text-white mb-4">{{ __('Points Rules') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-400">{{ $league->settings['points_win'] ?? 3 }}</div>
                                    <div class="text-sm text-gray-400">{{ __('Points for Win') }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-yellow-400">{{ $league->settings['points_draw'] ?? 1 }}</div>
                                    <div class="text-sm text-gray-400">{{ __('Points for Draw') }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-400">{{ $league->settings['points_loss'] ?? 0 }}</div>
                                    <div class="text-sm text-gray-400">{{ __('Points for Loss') }}</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($league->sport->slug === 'stoni-tenis')
                        <div class="mt-6 pt-6 border-t border-gray-600/50">
                            <h4 class="text-lg font-semibold text-white mb-4">{{ __('Match Rules') }}</h4>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-white">{{ __('Best of :sets sets (:wins sets to win)', ['sets' => ($league->settings['sets_to_win'] ?? 2) * 2 - 1, 'wins' => $league->settings['sets_to_win'] ?? 2]) }}</div>
                                <div class="text-sm text-gray-400">{{ __('Sets to win match') }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($league->status === 'active')
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-xl font-semibold text-white mb-4">{{ __('Match Schedule') }}</h3>

                        @if($league->matches->count() > 0)
                        <div class="space-y-6">
                            @php
                                $matchesByRound = $league->matches->groupBy('round');
                            @endphp

                            @foreach($matchesByRound as $round => $roundMatches)
                            <div>
                                <h4 class="text-lg font-medium text-white mb-3">{{ __('Round :round', ['round' => $round]) }}</h4>
                                <div class="space-y-3">
                                    @foreach($roundMatches as $match)
                                    <a href="{{ route('organizations.leagues.matches.show', [$organization, $league, $match]) }}"
                                       class="flex items-center justify-between p-4 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 transition-colors cursor-pointer block">
                                        <div class="flex items-center space-x-4">
                                            <div class="text-center">
                                                <div class="text-white font-medium">
                                                    @if($league->is_team_based)
                                                        {{ $match->homeTeam->name ?? 'TBD' }}
                                                    @else
                                                        {{ $match->homePlayer->name ?? 'TBD' }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-gray-400">vs</div>
                                            <div class="text-center">
                                                <div class="text-white font-medium">
                                                    @if($league->is_team_based)
                                                        {{ $match->awayTeam->name ?? 'TBD' }}
                                                    @else
                                                        {{ $match->awayPlayer->name ?? 'TBD' }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            @if(in_array($match->status, ['in_progress', 'completed', 'forfeited']))
                                                @if($match->status === 'forfeited')
                                                <div class="text-white font-bold flex items-center space-x-2">
                                                    <span class="
                                                        @if($match->forfeited_by === 'home') text-red-400
                                                        @else text-green-400 @endif
                                                    ">
                                                        @if($league->is_team_based)
                                                            {{ $match->homeTeam->name ?? 'TBD' }}
                                                        @else
                                                            {{ $match->homePlayer->name ?? 'TBD' }}
                                                        @endif
                                                    </span>
                                                    <span class="text-gray-400">vs</span>
                                                    <span class="
                                                        @if($match->forfeited_by === 'away') text-red-400
                                                        @else text-green-400 @endif
                                                    ">
                                                        @if($league->is_team_based)
                                                            {{ $match->awayTeam->name ?? 'TBD' }}
                                                        @else
                                                            {{ $match->awayPlayer->name ?? 'TBD' }}
                                                        @endif
                                                    </span>
                                                </div>
                                                @elseif($match->status === 'completed')
                                                <div class="text-white font-bold flex items-center space-x-2">
                                                    <span class="
                                                        @if($match->home_score > $match->away_score) text-green-400
                                                        @elseif($match->home_score < $match->away_score) text-red-400
                                                        @else text-yellow-400 @endif
                                                    ">
                                                        @if($league->is_team_based)
                                                            {{ $match->homeTeam->name ?? 'TBD' }}
                                                        @else
                                                            {{ $match->homePlayer->name ?? 'TBD' }}
                                                        @endif
                                                    </span>
                                                    <span class="text-gray-400">{{ $match->home_score }} - {{ $match->away_score }}</span>
                                                    <span class="
                                                        @if($match->away_score > $match->home_score) text-green-400
                                                        @elseif($match->away_score < $match->home_score) text-red-400
                                                        @else text-yellow-400 @endif
                                                    ">
                                                        @if($league->is_team_based)
                                                            {{ $match->awayTeam->name ?? 'TBD' }}
                                                        @else
                                                            {{ $match->awayPlayer->name ?? 'TBD' }}
                                                        @endif
                                                    </span>
                                                </div>
                                                @else
                                                <div class="text-white font-bold">
                                                    {{ $match->home_score }} - {{ $match->away_score }}
                                                </div>
                                                @endif
                                            @elseif($match->status === 'cancelled')
                                            @if($match->home_score || $match->away_score)
                                            <div class="text-white font-bold">
                                                {{ $match->home_score }} - {{ $match->away_score }}
                                            </div>
                                            @else
                                            <div class="text-orange-400 italic">
                                                Cancelled
                                            </div>
                                            @endif
                                            @else
                                            <div class="text-gray-400">
                                                {{ $match->scheduled_at ? $match->scheduled_at->format('M d, H:i') : __('Not scheduled') }}
                                            </div>
                                            @endif
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if($match->status === 'completed') bg-green-500/20 text-green-400
                                                @elseif($match->status === 'in_progress') bg-yellow-500/20 text-yellow-400
                                                @elseif($match->status === 'forfeited') bg-red-500/20 text-red-400
                                                @elseif($match->status === 'cancelled') bg-orange-500/20 text-orange-400
                                                @else bg-gray-500/20 text-gray-400 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $match->status)) }}
                                            </span>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11M9 11h6"></path>
                                </svg>
                            </div>
                            <h4 class="text-white font-semibold mb-2">{{ __('No matches scheduled') }}</h4>
                            <p class="text-gray-400">{{ __('Matches will be scheduled when the league starts.') }}</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($league->status === 'draft')
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-xl font-semibold text-white mb-6">{{ __('League Configuration') }}</h3>
                        
                        <form action="{{ route('organizations.leagues.update', [$organization, $league]) }}" method="POST" class="space-y-8">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-lg font-medium text-white mb-4">{{ __('Competition Format') }}</label>
                                <div class="space-y-3">
                                    <div class="flex items-center p-3 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 transition-colors">
                                        <input type="radio" id="format_round_robin" name="format" value="round_robin" 
                                               {{ old('format', $league->settings['format'] ?? '') === 'round_robin' ? 'checked' : '' }}
                                               class="w-4 h-4 text-purple-600 bg-gray-700 border-gray-600 focus:ring-purple-500 focus:ring-2">
                                        <label for="format_round_robin" class="ml-3 text-white cursor-pointer flex-1">
                                            <div class="font-medium">{{ __('Single Round Robin') }}</div>
                                            <div class="text-sm text-gray-400">{{ __('Each player/team plays every other once') }}</div>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center p-3 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 transition-colors">
                                        <input type="radio" id="format_dual_robin" name="format" value="dual_robin"
                                               {{ old('format', $league->settings['format'] ?? '') === 'dual_robin' ? 'checked' : '' }}
                                               class="w-4 h-4 text-purple-600 bg-gray-700 border-gray-600 focus:ring-purple-500 focus:ring-2">
                                        <label for="format_dual_robin" class="ml-3 text-white cursor-pointer flex-1">
                                            <div class="font-medium">{{ __('Double Round Robin') }}</div>
                                            <div class="text-sm text-gray-400">{{ __('Each player/team plays every other twice') }}</div>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center p-3 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 transition-colors">
                                        <input type="radio" id="format_knockout" name="format" value="knockout"
                                               {{ old('format', $league->settings['format'] ?? '') === 'knockout' ? 'checked' : '' }}
                                               class="w-4 h-4 text-purple-600 bg-gray-700 border-gray-600 focus:ring-purple-500 focus:ring-2">
                                        <label for="format_knockout" class="ml-3 text-white cursor-pointer flex-1">
                                            <div class="font-medium">{{ __('Knockout Only') }}</div>
                                            <div class="text-sm text-gray-400">{{ __('Single elimination tournament') }}</div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            @if(!$league->is_team_based || $league->sport->slug === 'stoni-tenis')
                            <div>
                                <label class="block text-lg font-medium text-white mb-4">{{ __('Sets to Win Match') }}</label>
                                <div class="space-y-3">
                                    <div class="flex items-center p-3 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 transition-colors">
                                        <input type="radio" id="sets_2" name="sets_to_win" value="2"
                                               {{ old('sets_to_win', $league->settings['sets_to_win'] ?? '') == 2 ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500 focus:ring-2">
                                        <label for="sets_2" class="ml-3 text-white cursor-pointer flex-1">
                                            <div class="font-medium">{{ __('Best of 3 (2 sets to win)') }}</div>
                                            <div class="text-sm text-gray-400">{{ __('Quick matches') }}</div>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center p-3 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 transition-colors">
                                        <input type="radio" id="sets_3" name="sets_to_win" value="3"
                                               {{ old('sets_to_win', $league->settings['sets_to_win'] ?? '') == 3 ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500 focus:ring-2">
                                        <label for="sets_3" class="ml-3 text-white cursor-pointer flex-1">
                                            <div class="font-medium">{{ __('Best of 5 (3 sets to win)') }}</div>
                                            <div class="text-sm text-gray-400">{{ __('Standard matches') }}</div>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center p-3 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 transition-colors">
                                        <input type="radio" id="sets_4" name="sets_to_win" value="4"
                                               {{ old('sets_to_win', $league->settings['sets_to_win'] ?? '') == 4 ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500 focus:ring-2">
                                        <label for="sets_4" class="ml-3 text-white cursor-pointer flex-1">
                                            <div class="font-medium">{{ __('Best of 7 (4 sets to win)') }}</div>
                                            <div class="text-sm text-gray-400">{{ __('Long matches') }}</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div>
                                <label class="block text-lg font-medium text-white mb-4">{{ __('Points System') }}</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-3">{{ __('Points for Win') }}</label>
                                        <div class="space-y-2">
                                            @for($i = 1; $i <= 5; $i++)
                                            <div class="flex items-center">
                                                <input type="radio" id="points_win_{{ $i }}" name="points_win" value="{{ $i }}"
                                                       {{ old('points_win', $league->settings['points_win'] ?? 3) == $i ? 'checked' : '' }}
                                                       class="w-4 h-4 text-green-600 bg-gray-700 border-gray-600 focus:ring-green-500 focus:ring-2">
                                                <label for="points_win_{{ $i }}" class="ml-2 text-white cursor-pointer">{{ $i }} {{ __('points') }}</label>
                                            </div>
                                            @endfor
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-3">{{ __('Points for Draw') }}</label>
                                        <div class="space-y-2">
                                            @for($i = 0; $i <= 3; $i++)
                                            <div class="flex items-center">
                                                <input type="radio" id="points_draw_{{ $i }}" name="points_draw" value="{{ $i }}"
                                                       {{ old('points_draw', $league->settings['points_draw'] ?? 1) == $i ? 'checked' : '' }}
                                                       class="w-4 h-4 text-yellow-600 bg-gray-700 border-gray-600 focus:ring-yellow-500 focus:ring-2">
                                                <label for="points_draw_{{ $i }}" class="ml-2 text-white cursor-pointer">{{ $i }} {{ __('points') }}</label>
                                            </div>
                                            @endfor
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-3">{{ __('Points for Loss') }}</label>
                                        <div class="space-y-2">
                                            @for($i = 0; $i <= 2; $i++)
                                            <div class="flex items-center">
                                                <input type="radio" id="points_loss_{{ $i }}" name="points_loss" value="{{ $i }}"
                                                       {{ old('points_loss', $league->settings['points_loss'] ?? 0) == $i ? 'checked' : '' }}
                                                       class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 focus:ring-red-500 focus:ring-2">
                                                <label for="points_loss_{{ $i }}" class="ml-2 text-white cursor-pointer">{{ $i }} {{ __('points') }}</label>
                                            </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-600/50">
                                <button type="submit"
                                        class="w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-8 py-4 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-purple-500/25">
                                    {{ __('Save League Settings') }}
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-xl font-semibold text-white mb-4">{{ __('League Settings') }}</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Competition Format') }}</label>
                                <p class="text-white">
                                    @php
                                        $format = $league->settings['format'] ?? 'round_robin';
                                    @endphp
                                    @switch($format)
                                        @case('round_robin')
                                            {{ __('Single Round Robin') }}
                                            @break
                                        @case('dual_robin')
                                            {{ __('Double Round Robin') }}
                                            @break
                                        @case('knockout')
                                            {{ __('Knockout Only') }}
                                            @break
                                        @default
                                            {{ ucfirst(str_replace('_', ' ', $format)) }}
                                    @endswitch
                                </p>
                            </div>

                            @if(true)
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Points for Win') }}</label>
                                        <p class="text-white">{{ $league->settings['points_win'] ?? 3 }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Points for Draw') }}</label>
                                        <p class="text-white">{{ $league->settings['points_draw'] ?? 1 }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Points for Loss') }}</label>
                                        <p class="text-white">{{ $league->settings['points_loss'] ?? 0 }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($league->sport->slug === 'stoni-tenis')
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">{{ __('Sets to Win Match') }}</label>
                                    <p class="text-white">{{ __('Best of :sets sets (:wins sets to win)', ['sets' => ($league->settings['sets_to_win'] ?? 2) * 2 - 1, 'wins' => $league->settings['sets_to_win'] ?? 2]) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

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

                <div class="space-y-6">

                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-4">{{ __('Quick Actions') }}</h3>
                        <div class="space-y-3">
                            @if($league->status === 'draft' && $league->players->count() >= 2)
                            <form method="POST" action="{{ route('organizations.leagues.start', [$organization, $league]) }}"
                                  onsubmit="return confirm('{{ __('Are you sure you want to start this league? This will generate matches and standings.') }}')"
                                  class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-green-500/25 mb-3">
                                    {{ __('Start League') }}
                                </button>
                            </form>
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

                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-4">{{ __('Statistics') }}</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-400">{{ $league->is_team_based ? __('Teams') : __('Players') }}</span>
                                <span class="text-white font-medium">{{ $league->is_team_based ? $league->teams->count() : $league->players->count() }}</span>
                            </div>
                            @if($league->status === 'active')
                            <div class="flex justify-between">
                                <span class="text-gray-400">{{ __('Total Matches') }}</span>
                                <span class="text-white font-medium">{{ $league->matches->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">{{ __('Completed') }}</span>
                                <span class="text-white font-medium">{{ $league->matches->where('status', 'completed')->count() }}</span>
                            </div>
                            @if($league->standings->count() > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-400">{{ __('Leader') }}</span>
                                <span class="text-white font-medium">{{ $league->standings->sortBy('position')->first()->participant_name }}</span>
                            </div>
                            @endif
                            @if($league->is_team_based && $league->max_teams)
                            <div class="flex justify-between">
                                <span class="text-gray-400">{{ __('Max Teams') }}</span>
                                <span class="text-white font-medium">{{ $league->max_teams }}</span>
                            </div>
                            @endif
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

    <script>
        // Update selected players count
        function updateSelectedCount() {
            const selectedCountElement = document.getElementById('selectedCount');
            if (selectedCountElement) {
                const checkboxes = document.querySelectorAll('input[name="player_ids[]"]:checked');
                selectedCountElement.textContent = checkboxes.length;
            }
        }

        // Add event listeners to checkboxes
        document.querySelectorAll('input[name="player_ids[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        // Initialize count on page load
        updateSelectedCount();
    </script>
</x-app-layout>