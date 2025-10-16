<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $competition->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $organization->name }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-3 py-1 text-sm rounded-full
                    @if($competition->status === 'active') bg-green-500/20 text-green-400
                    @elseif($competition->status === 'draft') bg-yellow-500/20 text-yellow-400
                    @elseif($competition->status === 'completed') bg-blue-500/20 text-blue-400
                    @else bg-red-500/20 text-red-400 @endif"
                >
                    {{ ucfirst($competition->status) }}
                </span>
                @if($competition->type === 'tournament')
                <span class="px-3 py-1 text-sm rounded-full bg-purple-500/20 text-purple-400">
                    {{ __('Tournament') }}
                </span>
                @else
                <span class="px-3 py-1 text-sm rounded-full bg-blue-500/20 text-blue-400">
                    {{ __('League') }}
                </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                
                <!-- Main Content - Tournament/League Display (3 columns) -->
                <div class="lg:col-span-3 space-y-6">
                    
            <!-- Tournament Groups with Tables and Matches -->
            @if(($competition->status === 'active' || $competition->status === 'completed') && $competition->type === 'tournament')
                @php
                    $knockoutMatches = App\Models\CompetitionMatch::where('competition_id', $competition->id)
                        ->where('phase', 'knockout')
                        ->with(['homePlayer', 'awayPlayer'])
                        ->orderBy('round_number')
                        ->orderBy('id')
                        ->get()
                        ->groupBy('round_number');

                    $groupMatches = App\Models\CompetitionMatch::where('competition_id', $competition->id)
                        ->whereNotNull('tournament_group_id')
                        ->with(['homePlayer', 'awayPlayer', 'tournamentGroup'])
                        ->orderBy('tournament_group_id')
                        ->orderBy('id')
                        ->get()
                        ->groupBy('tournament_group_id');
                @endphp

                <!-- Knockout Phase Bracket -->
                @if($knockoutMatches->count() > 0)
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-3xl font-bold text-white">🏆 {{ __('Knockout Phase') }}</h3>
                            @if($isOwner)
                                <div class="flex gap-2">
                                    @if($competition->current_phase === 'groups')
                                        <button onclick="autoGenerateBracket()"
                                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">
                                            🔄 {{ __('Auto Generate') }}
                                        </button>
                                    @endif
                                    
                                    @php
                                        // Check if current knockout round is completed
                                        $currentKnockoutRound = $knockoutMatches->keys()->max();
                                        $currentRoundMatches = $knockoutMatches->get($currentKnockoutRound);
                                        $roundCompleted = $currentRoundMatches && $currentRoundMatches->every(fn($m) => $m->status === 'completed');
                                        $isFinal = $currentRoundMatches && $currentRoundMatches->count() == 1;
                                    @endphp
                                    
                                    @if($roundCompleted && !$isFinal)
                                        <form method="POST" action="{{ route('organizations.competitions.generate-next-round', [$organization, $competition]) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition-colors font-semibold">
                                                ➡️ {{ __('Generate Next Round') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Tournament Bracket -->
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50">
                            @php
                                $totalRounds = $knockoutMatches->count();
                                $roundNames = [
                                    1 => $totalRounds == 3 ? __('Quarter Finals') : ($totalRounds == 2 ? __('Semi Finals') : __('Round 1')),
                                    2 => $totalRounds == 3 ? __('Semi Finals') : ($totalRounds == 2 ? __('Final') : __('Round 2')),
                                    3 => __('Final'),
                                    4 => __('Round 4'),
                                ];
                                
                                // Check if tournament is completed and get winner
                                $finalMatch = $knockoutMatches->get($totalRounds)?->first();
                                $winner = null;
                                if ($finalMatch && $finalMatch->status === 'completed') {
                                    $winner = $finalMatch->home_score > $finalMatch->away_score 
                                        ? $finalMatch->homePlayer 
                                        : $finalMatch->awayPlayer;
                                }
                            @endphp
                            
                            <!-- Winner Display (if tournament is completed) -->
                            @if($winner)
                            <div class="mb-8 text-center">
                                <div class="text-center">
                                    <h2 class="text-lg font-semibold text-amber-400 mb-2 tracking-wide">
                                        TOURNAMENT CHAMPION
                                    </h2>
                                    <p class="text-4xl font-black text-white mb-1" style="font-family: 'Inter', sans-serif; letter-spacing: -0.02em;">
                                        {{ $winner->name }}
                                    </p>
                                    <p class="text-sm text-gray-400 font-medium">{{ $competition->name }}</p>
                                </div>
                            </div>
                            @endif
                            
                            <div class="flex flex-col gap-8">
                                <!-- Rounds (from final to first) -->
                                @foreach($knockoutMatches->sortKeysDesc() as $roundNumber => $roundMatches)
                                <div>
                                    <h4 class="text-xl font-bold text-center mb-6 text-white">
                                        {{ $roundNames[$roundNumber] ?? __('Round') . ' ' . $roundNumber }}
                                    </h4>
                                    <div class="grid gap-4" style="grid-template-columns: repeat({{ $roundMatches->count() }}, minmax(0, 1fr));">
                                        @foreach($roundMatches as $match)
                                        <div class="bg-gray-700/40 rounded-xl border-2 border-gray-600/50 overflow-hidden hover:border-blue-500/50 transition-all">
                                            <!-- Match Header -->
                                            <div class="bg-gray-700/60 px-3 py-2 border-b border-gray-600/50">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs text-gray-400">{{ __('Match') }} #{{ $match->id }}</span>
                                                    @if($match->status === 'in_progress')
                                                        <span class="text-xs text-green-400 animate-pulse">🔴 {{ __('LIVE') }}</span>
                                                    @elseif($match->status === 'completed')
                                                        <span class="text-xs text-gray-400">✓</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Players -->
                                            <div class="p-3 space-y-2">
                                                <!-- Home Player -->
                                                <div class="flex items-center justify-between p-2 rounded-lg
                                                    @if($match->status === 'completed' && $match->home_score > $match->away_score) 
                                                        bg-green-600/20 border border-green-500/30
                                                    @else 
                                                        bg-gray-800/50
                                                    @endif">
                                                    <div class="flex items-center space-x-2 flex-1 min-w-0">
                                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                            <span class="text-white font-bold text-sm">{{ substr($match->homePlayer->name ?? 'TBD', 0, 1) }}</span>
                                                        </div>
                                                        <span class="text-white font-semibold text-sm truncate">
                                                            {{ $match->homePlayer->name ?? 'TBD' }}
                                                        </span>
                                                    </div>
                                                    <span class="text-2xl font-bold ml-2
                                                        @if($match->status === 'completed' && $match->home_score > $match->away_score) 
                                                            text-green-400
                                                        @elseif($match->status === 'completed') 
                                                            text-gray-500
                                                        @else 
                                                            text-white
                                                        @endif">
                                                        {{ $match->status !== 'scheduled' ? ($match->home_score ?? 0) : '-' }}
                                                    </span>
                                                </div>

                                                <!-- Away Player -->
                                                <div class="flex items-center justify-between p-2 rounded-lg
                                                    @if($match->status === 'completed' && $match->away_score > $match->home_score) 
                                                        bg-green-600/20 border border-green-500/30
                                                    @else 
                                                        bg-gray-800/50
                                                    @endif">
                                                    <div class="flex items-center space-x-2 flex-1 min-w-0">
                                                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                            <span class="text-white font-bold text-sm">{{ substr($match->awayPlayer->name ?? 'TBD', 0, 1) }}</span>
                                                        </div>
                                                        <span class="text-white font-semibold text-sm truncate">
                                                            {{ $match->awayPlayer->name ?? 'TBD' }}
                                                        </span>
                                                    </div>
                                                    <span class="text-2xl font-bold ml-2
                                                        @if($match->status === 'completed' && $match->away_score > $match->home_score) 
                                                            text-green-400
                                                        @elseif($match->status === 'completed') 
                                                            text-gray-500
                                                        @else 
                                                            text-white
                                                        @endif">
                                                        {{ $match->status !== 'scheduled' ? ($match->away_score ?? 0) : '-' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Actions -->
                                            @if($match->status === 'scheduled' && $isOwner)
                                            <div class="px-3 pb-3 flex gap-2">
                                                <a href="{{ route('competitions.live-score', [$match->id]) }}"
                                                   class="flex-1 bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-2 rounded-lg transition-colors text-center font-semibold">
                                                    ▶️ {{ __('Start') }}
                                                </a>
                                                <button onclick="openQuickResultModal({{ $match->id }}, '{{ $match->homePlayer->name ?? 'TBD' }}', '{{ $match->awayPlayer->name ?? 'TBD' }}')"
                                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-2 rounded-lg transition-colors text-center font-semibold">
                                                    ⚡ {{ __('Result') }}
                                                </button>
                                            </div>
                                            @elseif($match->status === 'in_progress')
                                            <div class="px-3 pb-3">
                                                <a href="{{ route('competitions.live-score', [$match->id]) }}"
                                                   class="block bg-green-600/20 text-green-400 text-xs px-3 py-2 rounded-lg text-center font-semibold hover:bg-green-600/30 transition-colors">
                                                    👁️ {{ __('Watch Live') }}
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Group Phase (Accordion when completed) -->
                @if($groupMatches->count() > 0)
                    @php
                        // Check if all group matches are completed
                        $allGroupMatchesCompleted = $groupMatches->flatten()->every(function($match) {
                            return $match->status === 'completed';
                        });
                    @endphp

                    <div class="mb-8">
                        <button onclick="toggleGroupPhase()" 
                                class="w-full bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 hover:border-gray-600/50 transition-all text-left">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <h3 class="text-2xl font-bold text-white">📋 {{ __('Group Phase') }}</h3>
                                    @if($allGroupMatchesCompleted)
                                        <span class="px-3 py-1 text-xs rounded-full bg-green-600/20 text-green-400">
                                            ✓ {{ __('Completed') }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs rounded-full bg-yellow-600/20 text-yellow-400">
                                            ⏳ {{ __('In Progress') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($allGroupMatchesCompleted && $isOwner && $knockoutMatches->count() === 0)
                                        <button onclick="autoGenerateBracket(); event.stopPropagation();"
                                                class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition-colors font-semibold">
                                            ➡️ {{ __('Start Knockout Phase') }}
                                        </button>
                                    @endif
                                    <svg id="group-phase-icon" class="w-6 h-6 text-gray-400 transition-transform {{ $allGroupMatchesCompleted ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </button>

                        <div id="group-phase-content" class="mt-4 {{ $allGroupMatchesCompleted ? 'hidden' : '' }}">
                    <!-- Groups Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        @foreach($groupMatches as $groupId => $matchesInGroup)
                            @php
                                $group = $competition->tournamentGroups->firstWhere('id', $groupId);
                                $standings = App\Models\Standing::where('competition_id', $competition->id)
                                    ->where('tournament_group_id', $groupId)
                                    ->with('player')
                                    ->orderBy('points', 'desc')
                                    ->orderByRaw('(sets_won - sets_lost) desc')
                                    ->orderByRaw('(points_won - points_lost) desc')
                                    ->get();
                            @endphp
                            
                            <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700/50 shadow-xl overflow-hidden">
                                <!-- Group Header -->
                                <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 px-4 py-3 border-b border-gray-700/50">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-white font-bold text-base flex items-center space-x-2">
                                            <span class="bg-gradient-to-r from-blue-500 to-purple-600 px-3 py-1 rounded-full text-xs">
                                                {{ __('Group') }} {{ $group->name ?? 'Unknown' }}
                                            </span>
                                        </h4>
                                        <span class="text-gray-400 text-xs">
                                            {{ $matchesInGroup->where('status', 'completed')->count() }}/{{ $matchesInGroup->count() }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Standings Table -->
                                <div class="px-4 py-3 bg-gray-700/20">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="text-gray-400 border-b border-gray-700/50">
                                                <th class="text-left py-1 pr-2 font-medium">#</th>
                                                <th class="text-left py-1 font-medium">{{ __('Player') }}</th>
                                                <th class="text-center py-1 px-1 font-medium">{{ __('M') }}</th>
                                                <th class="text-center py-1 px-1 font-medium">{{ __('W') }}</th>
                                                <th class="text-center py-1 px-1 font-medium">{{ __('L') }}</th>
                                                <th class="text-center py-1 px-1 font-medium">{{ __('S') }}</th>
                                                <th class="text-center py-1 px-1 font-medium text-green-400">{{ __('P') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($standings->count() > 0)
                                                @foreach($standings as $index => $standing)
                                                <tr class="border-b border-gray-700/30 hover:bg-gray-700/30 transition-colors">
                                                    <td class="py-2 pr-2 text-gray-400 font-mono">{{ $index + 1 }}</td>
                                                    <td class="py-2 text-white font-medium">{{ $standing->player->name ?? 'Unknown' }}</td>
                                                    <td class="py-2 px-1 text-center text-gray-300">{{ $standing->played }}</td>
                                                    <td class="py-2 px-1 text-center text-green-400">{{ $standing->won }}</td>
                                                    <td class="py-2 px-1 text-center text-red-400">{{ $standing->lost }}</td>
                                                    <td class="py-2 px-1 text-center text-gray-300">
                                                        <span class="text-xs">{{ $standing->sets_won }}:{{ $standing->sets_lost }}</span>
                                                    </td>
                                                    <td class="py-2 px-1 text-center text-green-400 font-bold">{{ $standing->points }}</td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="7" class="py-3 text-center text-gray-500 text-xs">{{ __('No standings yet') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Matches for this group -->
                                <div class="px-4 py-3 space-y-2">
                                    <h5 class="text-gray-400 text-xs font-semibold mb-2 uppercase">{{ __('Matches') }}</h5>
                                    @foreach($matchesInGroup as $match)
                                    <div class="bg-gray-700/20 rounded-lg p-2 hover:bg-gray-700/40 transition-all border border-gray-600/10">
                                        <div class="flex items-center justify-between gap-2">
                                            <!-- Players and Scores -->
                                            <div class="flex-1 min-w-0">
                                                <!-- Home Player -->
                                                <div class="flex items-center justify-between mb-1">
                                                    <div class="flex items-center space-x-1.5 min-w-0 flex-1">
                                                        <div class="w-5 h-5 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                            <span class="text-white font-bold text-[9px]">{{ substr($match->homePlayer->name ?? 'TBD', 0, 2) }}</span>
                                                        </div>
                                                        <span class="text-white text-xs truncate">{{ $match->homePlayer->name ?? 'TBD' }}</span>
                                                    </div>
                                                    <span class="text-lg font-bold ml-2 flex-shrink-0
                                                        @if($match->status === 'completed' && $match->home_score > $match->away_score) text-green-400
                                                        @elseif($match->status === 'completed') text-gray-500
                                                        @else text-white @endif">
                                                        {{ $match->status !== 'scheduled' ? ($match->home_score ?? 0) : '-' }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Away Player -->
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-1.5 min-w-0 flex-1">
                                                        <div class="w-5 h-5 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                            <span class="text-white font-bold text-[9px]">{{ substr($match->awayPlayer->name ?? 'TBD', 0, 2) }}</span>
                                                        </div>
                                                        <span class="text-white text-xs truncate">{{ $match->awayPlayer->name ?? 'TBD' }}</span>
                                                    </div>
                                                    <span class="text-lg font-bold ml-2 flex-shrink-0
                                                        @if($match->status === 'completed' && $match->away_score > $match->home_score) text-green-400
                                                        @elseif($match->status === 'completed') text-gray-500
                                                        @else text-white @endif">
                                                        {{ $match->status !== 'scheduled' ? ($match->away_score ?? 0) : '-' }}
                                                    </span>
                                                </div>

                                                <!-- Set Scores -->
                                                @if($match->status === 'completed' && $match->sets && is_array($match->sets) && count($match->sets) > 0)
                                                <div class="flex gap-1 mt-1">
                                                    @foreach($match->sets as $set)
                                                    <div class="bg-gray-600/40 px-1.5 py-0.5 rounded text-[10px] text-gray-300">
                                                        {{ $set['home'] }}-{{ $set['away'] }}
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="flex flex-col gap-1 flex-shrink-0">
                                                @if($match->status === 'scheduled')
                                                    @if($isOwner)
                                                    <a href="{{ route('competitions.live-score', [$match->id]) }}" 
                                                       class="bg-green-600 hover:bg-green-700 text-white text-[10px] px-2 py-1 rounded transition-colors text-center whitespace-nowrap">
                                                        ▶️ {{ __('Live') }}
                                                    </a>
                                                    <button onclick="openQuickResultModal({{ $match->id }}, '{{ $match->homePlayer->name }}', '{{ $match->awayPlayer->name }}')"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-2 py-1 rounded transition-colors text-center whitespace-nowrap">
                                                        ⚡ {{ __('Result') }}
                                                    </button>
                                                    @else
                                                    <span class="text-[10px] bg-yellow-600/20 text-yellow-400 px-2 py-1 rounded text-center whitespace-nowrap">
                                                        {{ __('Soon') }}
                                                    </span>
                                                    @endif
                                                @elseif($match->status === 'in_progress')
                                                    <span class="text-[10px] bg-green-600/20 text-green-400 px-2 py-1 rounded text-center whitespace-nowrap animate-pulse">
                                                        🔴 {{ __('Live') }}
                                                    </span>
                                                    <a href="{{ route('competitions.live-score', [$match->id]) }}" 
                                                       class="text-blue-400 hover:text-blue-300 text-[10px] text-center whitespace-nowrap">
                                                        👁️ {{ __('Watch') }}
                                                    </a>
                                                @elseif($match->status === 'completed')
                                                    <span class="text-[10px] bg-gray-600/20 text-gray-400 px-2 py-1 rounded text-center whitespace-nowrap">
                                                        ✓ {{ __('FT') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                        </div>
                    </div>
                @endif
            @elseif($competition->status === 'active' && $competition->type === 'league')
                @php
                    $matches = App\Models\LeagueMatch::where('league_id', $competition->id)
                        ->with(['homePlayer', 'awayPlayer'])
                        ->orderBy('id')
                        ->get();
                @endphp
                
                <div class="mb-8">
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                        <h3 class="text-2xl font-bold text-white mb-6">{{ __('Matches') }}</h3>
                        
                        @if($matches->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($matches as $match)
                                <div class="bg-gray-700/30 rounded-xl p-5 hover:bg-gray-700/50 transition-all border border-gray-600/20 hover:border-gray-500/50">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <!-- Home Player -->
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center space-x-2 min-w-0 flex-1">
                                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <span class="text-white font-bold text-xs">{{ substr($match->homePlayer->name ?? 'TBD', 0, 2) }}</span>
                                                    </div>
                                                    <span class="text-white font-medium truncate">{{ $match->homePlayer->name ?? 'TBD' }}</span>
                                                </div>
                                                <span class="text-3xl font-bold ml-3 flex-shrink-0
                                                    @if($match->status === 'completed' && $match->home_score > $match->away_score) text-green-400
                                                    @elseif($match->status === 'completed') text-gray-500
                                                    @else text-white @endif">
                                                    {{ $match->status !== 'scheduled' ? ($match->home_score ?? 0) : '-' }}
                                                </span>
                                            </div>
                                            
                                            <!-- Away Player -->
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center space-x-2 min-w-0 flex-1">
                                                    <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <span class="text-white font-bold text-xs">{{ substr($match->awayPlayer->name ?? 'TBD', 0, 2) }}</span>
                                                    </div>
                                                    <span class="text-white font-medium truncate">{{ $match->awayPlayer->name ?? 'TBD' }}</span>
                                                </div>
                                                <span class="text-3xl font-bold ml-3 flex-shrink-0
                                                    @if($match->status === 'completed' && $match->away_score > $match->home_score) text-green-400
                                                    @elseif($match->status === 'completed') text-gray-500
                                                    @else text-white @endif">
                                                    {{ $match->status !== 'scheduled' ? ($match->away_score ?? 0) : '-' }}
                                                </span>
                                            </div>

                                            @if($match->status === 'completed' && $match->sets && is_array($match->sets) && count($match->sets) > 0)
                                            <div class="flex gap-1 mt-2">
                                                @foreach($match->sets as $set)
                                                <div class="bg-gray-600/30 px-2 py-1 rounded text-xs text-gray-300">
                                                    {{ $set['home'] }}-{{ $set['away'] }}
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif
                                        </div>
                                        
                                        <div class="flex flex-col gap-2 flex-shrink-0">
                                            @if($match->status === 'scheduled')
                                                <span class="text-xs bg-yellow-600/20 text-yellow-400 px-3 py-1.5 rounded-full text-center whitespace-nowrap">
                                                    {{ __('Scheduled') }}
                                                </span>
                                                @if($isOwner)
                                                <a href="{{ route('leagues.live-score', [$match->id]) }}" 
                                                   class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded-lg transition-colors text-center whitespace-nowrap">
                                                    ▶️ {{ __('Start Live') }}
                                                </a>
                                                <button onclick="openQuickResultModal({{ $match->id }}, '{{ $match->homePlayer->name }}', '{{ $match->awayPlayer->name }}', true)"
                                                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg transition-colors text-center whitespace-nowrap">
                                                    ⚡ {{ __('Quick Result') }}
                                                </button>
                                                @endif
                                            @elseif($match->status === 'in_progress')
                                                <span class="text-xs bg-green-600/20 text-green-400 px-3 py-1.5 rounded-full text-center whitespace-nowrap animate-pulse">
                                                    🔴 {{ __('Live') }}
                                                </span>
                                                <a href="{{ route('leagues.live-score', [$match->id]) }}" 
                                                   class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg transition-colors text-center whitespace-nowrap">
                                                    👁️ {{ __('Watch') }}
                                                </a>
                                            @elseif($match->status === 'completed')
                                                <span class="text-xs bg-gray-600/20 text-gray-400 px-3 py-1.5 rounded-full text-center whitespace-nowrap">
                                                    ✓ {{ __('Final') }}
                                                </span>
                                                <a href="{{ route('leagues.live-score', [$match->id]) }}" 
                                                   class="text-gray-400 hover:text-gray-300 text-xs text-center whitespace-nowrap">
                                                    {{ __('Details') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-400 text-lg">{{ __('No matches scheduled yet') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
                </div>
                
                <!-- Sidebar - Competition Info & Actions (1 column) -->
                <div class="space-y-6">

                    <!-- Competition Details -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-5 border border-gray-700/50 shadow-xl">
                        <h3 class="text-base font-bold text-white mb-3">{{ __('Details') }}</h3>
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">{{ __('Sport') }}</span>
                                <span class="text-white text-xs font-medium">{{ $competition->sport->name }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">{{ __('Format') }}</span>
                                <span class="text-white text-xs font-medium">{{ $competition->is_team_based ? __('Team') : __('Individual') }}</span>
                            </div>
                            @if($competition->start_date)
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">{{ __('Start') }}</span>
                                <span class="text-white text-xs font-medium">{{ $competition->start_date->format('M d, Y') }}</span>
                            </div>
                            @endif
                            @if($competition->type === 'tournament')
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">{{ __('Players') }}</span>
                                <span class="text-white text-xs font-medium">{{ $competition->players->count() }}/{{ $competition->max_participants }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">{{ __('Phase') }}</span>
                                <span class="text-white text-xs font-medium">{{ ucfirst($competition->current_phase ?? 'groups') }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Match Rules -->
                        <div class="mt-4 pt-4 border-t border-gray-700/30">
                            <h4 class="text-xs font-semibold text-gray-400 mb-2 uppercase">{{ __('Match Rules') }}</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="bg-gray-700/20 rounded p-2">
                                    <p class="text-gray-400 text-[10px]">{{ __('Sets') }}</p>
                                    <p class="text-white text-xs font-bold">{{ $competition->sets_to_win ?? 2 }}</p>
                                </div>
                                <div class="bg-gray-700/20 rounded p-2">
                                    <p class="text-gray-400 text-[10px]">{{ __('Points') }}</p>
                                    <p class="text-white text-xs font-bold">{{ $competition->points_per_set ?? 11 }}</p>
                                </div>
                                <div class="bg-gray-700/20 rounded p-2">
                                    <p class="text-gray-400 text-[10px]">{{ __('Deuce') }}</p>
                                    <p class="text-white text-xs font-bold">{{ $competition->must_win_by_two ? 'Yes' : 'No' }}</p>
                                </div>
                                @if($competition->type === 'tournament')
                                <div class="bg-gray-700/20 rounded p-2">
                                    <p class="text-gray-400 text-[10px]">{{ __('Win Pts') }}</p>
                                    <p class="text-white text-xs font-bold">{{ $competition->points_for_win ?? 2 }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($competition->status === 'draft' && $competition->type === 'tournament' && $competition->tournamentGroups->count() > 0)
                    <!-- Tournament Groups (Draft Only) -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-5 border border-gray-700/50 shadow-xl">
                        <h3 class="text-base font-bold text-white mb-3">{{ __('Groups Setup') }}</h3>
                        <div class="space-y-2">
                            @foreach($competition->tournamentGroups as $group)
                            <div class="bg-gray-700/20 rounded-lg p-2.5">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-white font-semibold text-xs">{{ __('Group') }} {{ $group->name }}</span>
                                    <span class="text-gray-400 text-[10px]">
                                        {{ $group->player_ids ? count($group->player_ids) : 0 }} {{ __('players') }}
                                    </span>
                                </div>
                                @if($group->player_ids && count($group->player_ids) > 0)
                                <div class="space-y-1">
                                    @foreach($group->player_ids as $playerId)
                                        @php
                                            $player = $competition->players->firstWhere('id', $playerId);
                                        @endphp
                                        @if($player)
                                        <div class="flex items-center space-x-1.5 text-xs text-gray-300">
                                            <div class="w-4 h-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-white font-bold text-[8px]">{{ substr($player->name, 0, 2) }}</span>
                                            </div>
                                            <span class="truncate">{{ $player->name }}</span>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Participants (Compact) -->
                    @if($competition->status === 'draft')
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-5 border border-gray-700/50 shadow-xl">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-base font-bold text-white">
                                {{ __('Participants') }}
                                <span class="text-gray-400 text-xs ml-1">({{ $competition->players->count() }}/{{ $competition->max_participants ?? '∞' }})</span>
                            </h3>
                            @if($isOwner)
                            <button onclick="document.getElementById('addPlayerModal').classList.remove('hidden')" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                + {{ __('Add') }}
                            </button>
                            @endif
                        </div>

                        @if($competition->players->count() > 0)
                        <div class="space-y-1.5 max-h-48 overflow-y-auto">
                            @foreach($competition->players as $player)
                            <div class="flex items-center justify-between bg-gray-700/20 rounded p-1.5 hover:bg-gray-700/30 transition-colors">
                                <div class="flex items-center space-x-2 min-w-0 flex-1">
                                    <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-[9px]">{{ substr($player->name, 0, 2) }}</span>
                                    </div>
                                    <span class="text-white text-xs truncate">{{ $player->name }}</span>
                                </div>
                                @if($isOwner)
                                <form action="{{ route('organizations.competitions.remove-player', [$organization, $competition, $player]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-xs">{{ __('No participants yet') }}</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Actions -->
                    @if($isOwner)
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-5 border border-gray-700/50 shadow-xl">
                        <h3 class="text-base font-bold text-white mb-3">{{ __('Actions') }}</h3>
                        <div class="space-y-2">
                            @if($competition->status === 'draft')
                                <a href="{{ route('organizations.competitions.settings', [$organization, $competition]) }}" 
                                   class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors inline-block text-center">
                                    ⚙️ {{ __('Competition Settings') }}
                                </a>
                            @endif

                            @if($competition->status === 'draft' && $competition->type === 'tournament' && $competition->players->count() >= 4)
                                @if($competition->tournamentGroups->count() === 0)
                                    <a href="{{ route('organizations.competitions.setup-groups', [$organization, $competition]) }}" 
                                       class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors inline-block text-center">
                                        {{ __('Setup Groups Manually') }}
                                    </a>
                                    <form action="{{ route('organizations.competitions.generate-groups', [$organization, $competition]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                            {{ __('Auto Generate Groups') }}
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('organizations.competitions.setup-groups', [$organization, $competition]) }}" 
                                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-block text-center">
                                        {{ __('Edit Groups') }}
                                    </a>
                                    <form action="{{ route('organizations.competitions.start', [$organization, $competition]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                                            {{ __('🚀 Start Tournament') }}
                                        </button>
                                    </form>
                                @endif
                            @endif

                            @if($competition->status === 'draft' && $competition->type === 'league' && $competition->players->count() >= 2)
                            <form action="{{ route('organizations.competitions.start', [$organization, $competition]) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    {{ __('Start League') }}
                                </button>
                            </form>
                            @endif

                            <a href="{{ route('organizations.show', $organization) }}"
                               class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors inline-block text-center">
                                {{ __('Back to Organization') }}
                            </a>

                            <!-- Danger Zone -->
                            @if($competition->status === 'active')
                            <div class="mt-6 pt-6 border-t border-gray-700">
                                <h4 class="text-sm font-semibold text-red-400 mb-3">{{ __('Danger Zone') }}</h4>
                                <form action="{{ route('organizations.competitions.reset', [$organization, $competition]) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to reset this competition? All matches, groups, and standings will be deleted!');">
                                    @csrf
                                    <button type="submit" class="w-full bg-yellow-600/20 hover:bg-yellow-600/30 text-yellow-400 px-4 py-2 rounded-lg transition-colors mb-2">
                                        🔄 {{ __('Reset Competition') }}
                                    </button>
                                </form>
                            </div>
                            @endif

                            @if($competition->status === 'draft')
                            <div class="mt-6 pt-6 border-t border-gray-700">
                                <h4 class="text-sm font-semibold text-red-400 mb-3">{{ __('Danger Zone') }}</h4>
                                <form action="{{ route('organizations.competitions.destroy', [$organization, $competition]) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this competition? This action cannot be undone!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-red-600/20 hover:bg-red-600/30 text-red-400 px-4 py-2 rounded-lg transition-colors">
                                        🗑️ {{ __('Delete Competition') }}
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>

                        @if($competition->status === 'draft')
                        <div class="mt-4 p-3 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                            <p class="text-blue-400 text-xs">
                                @if($competition->type === 'tournament')
                                    @if($competition->tournamentGroups->count() === 0)
                                        {{ __('Add at least 4 players, then setup groups to start') }}
                                    @else
                                        {{ __('✓ Groups are ready! You can now start the tournament') }}
                                    @endif
                                @else
                                    {{ __('Add at least 2 players to start') }}
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>
                    @endif

                </div>

            </div>
        </div>
    </div>

    <!-- Add Player Modal -->
    <div id="addPlayerModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-2xl p-6 max-w-md w-full border border-gray-700 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-white">{{ __('Add Player to Competition') }}</h3>
                <button onclick="document.getElementById('addPlayerModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('organizations.competitions.add-player', [$organization, $competition]) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="player_id" class="block text-sm font-medium text-white mb-2">{{ __('Select Player') }}</label>
                        <select id="player_id" name="player_id" required 
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">{{ __('Choose a player...') }}</option>
                            @foreach($organization->players->whereNotIn('id', $competition->players->pluck('id')) as $player)
                            <option value="{{ $player->id }}">{{ $player->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="document.getElementById('addPlayerModal').classList.add('hidden')"
                                class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            {{ __('Add Player') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Result Modal -->
    <div id="quickResultModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-2xl p-6 max-w-lg w-full border border-gray-700 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-white">⚡ {{ __('Quick Result Entry') }}</h3>
                <button onclick="closeQuickResultModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="quickResultForm" method="POST">
                @csrf
                <div class="space-y-6">
                    <!-- Match Info -->
                    <div class="bg-gray-700/30 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs" id="homeInitials">--</span>
                                </div>
                                <span class="text-white font-medium" id="homePlayerName">Player 1</span>
                            </div>
                            <input type="number" name="home_score" id="homeScoreInput" min="0" max="5" required
                                   class="w-20 text-center text-2xl font-bold bg-gray-600/50 border border-gray-500 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs" id="awayInitials">--</span>
                                </div>
                                <span class="text-white font-medium" id="awayPlayerName">Player 2</span>
                            </div>
                            <input type="number" name="away_score" id="awayScoreInput" min="0" max="5" required
                                   class="w-20 text-center text-2xl font-bold bg-gray-600/50 border border-gray-500 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Set Scores (Optional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-3">{{ __('Set Scores (Optional)') }}</label>
                        <div id="setScoresContainer" class="space-y-2">
                            <!-- Set score inputs will be dynamically added here -->
                        </div>
                        <button type="button" onclick="addSetScore()" class="mt-2 text-blue-400 hover:text-blue-300 text-sm">
                            + {{ __('Add Set Score') }}
                        </button>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="closeQuickResultModal()"
                                class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            {{ __('Save Result') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentMatchId = null;
        let isLeague = false;
        let setScoreCount = 0;

        function openQuickResultModal(matchId, homeName, awayName, league = false) {
            currentMatchId = matchId;
            isLeague = league;
            
            document.getElementById('homePlayerName').textContent = homeName;
            document.getElementById('awayPlayerName').textContent = awayName;
            document.getElementById('homeInitials').textContent = homeName.substring(0, 2).toUpperCase();
            document.getElementById('awayInitials').textContent = awayName.substring(0, 2).toUpperCase();
            
            document.getElementById('homeScoreInput').value = '';
            document.getElementById('awayScoreInput').value = '';
            document.getElementById('setScoresContainer').innerHTML = '';
            setScoreCount = 0;
            
            const form = document.getElementById('quickResultForm');
            if (isLeague) {
                form.action = `/leagues/matches/${matchId}/quick-result`;
            } else {
                form.action = `/competitions/matches/${matchId}/quick-result`;
            }
            
            document.getElementById('quickResultModal').classList.remove('hidden');
        }

        function closeQuickResultModal() {
            document.getElementById('quickResultModal').classList.add('hidden');
            currentMatchId = null;
        }

        function addSetScore() {
            setScoreCount++;
            const container = document.getElementById('setScoresContainer');
            const setDiv = document.createElement('div');
            setDiv.className = 'flex items-center gap-2';
            setDiv.innerHTML = `
                <span class="text-gray-400 text-sm w-16">Set ${setScoreCount}:</span>
                <input type="number" name="sets[${setScoreCount-1}][home]" min="0" placeholder="0"
                       class="w-20 text-center bg-gray-600/50 border border-gray-500 rounded px-2 py-1 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="text-gray-400">-</span>
                <input type="number" name="sets[${setScoreCount-1}][away]" min="0" placeholder="0"
                       class="w-20 text-center bg-gray-600/50 border border-gray-500 rounded px-2 py-1 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-300 ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(setDiv);
        }

        // Drag and Drop functionality for knockout bracket
        let draggedPlayer = null;
        let isBracketEditMode = false;

        function toggleGroupPhase() {
            const content = document.getElementById('group-phase-content');
            const icon = document.getElementById('group-phase-icon');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        function autoGenerateBracket() {
            if (confirm('{{ __("This will automatically generate the knockout bracket based on group standings. Continue?") }}')) {
                const organizationId = {{ $organization->id }};
                const competitionId = {{ $competition->id }};
                
                fetch(`/organizations/${organizationId}/competitions/${competitionId}/auto-generate-bracket`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error generating bracket');
                    }
                });
            }
        }
    </script>
</x-app-layout>