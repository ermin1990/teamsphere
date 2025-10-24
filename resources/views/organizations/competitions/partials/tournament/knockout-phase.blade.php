{{-- Knockout Phase --}}
@php
    $isOwner = $isOwner ?? false;
    $organization = $organization ?? null;
    $competition = $competition ?? null;
    $isRefereeForMatch = $isRefereeForMatch ?? function() { return false; };
@endphp
@if($knockoutMatches && $knockoutMatches->flatten()->count() > 0)
<div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 mb-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-white">🏆 Eliminaciona Faza</h3>
        
        <div class="flex items-center gap-3">
            @if($isOwner)
            <a href="{{ route('organizations.competitions.manual-knockout-setup', [$organization, $competition]) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                ✏️ Uredi raspored
            </a>
            @endif
            
            {{-- Reset Knockout Button --}}
            @if($isOwner && $knockoutMatches->flatten()->count() > 0)
                <button type="button" onclick="confirmResetKnockoutPhase()" 
                        class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg transition-colors font-semibold">
                    🔄 Resetuj eliminacionu fazu
                </button>
            @endif
        </div>
    </div>
    
    {{-- Bracket Tree Visualization --}}
    <div class="overflow-x-auto pb-4">
        <div class="flex justify-center items-center min-h-[500px] py-8">
            <div class="inline-flex gap-16 min-w-max items-center">
            @php
                // Matches are already grouped by round_number
                $matchesByRound = $knockoutMatches;
                $totalRounds = (int) $matchesByRound->keys()->max();
                
                // Function to get round name based on number of participants
                function getRoundName($roundNum, $totalRounds) {
                    $roundNum = (int) $roundNum;
                    $totalRounds = (int) $totalRounds;
                    $roundsFromFinal = $totalRounds - $roundNum;
                    
                    if ($roundNum == 0) return 'Playoff';
                    if ($roundsFromFinal == 0) return 'Finale';
                    if ($roundsFromFinal == 1) return 'Polufinale';
                    if ($roundsFromFinal == 2) return 'Četvrtfinale';
                    if ($roundsFromFinal == 3) return 'Osmina finala';
                    if ($roundsFromFinal == 4) return 'Šesnaestina finala';
                    if ($roundsFromFinal == 5) return 'Tridesetdvojka';
                    
                    return "Runda $roundNum";
                }
            @endphp
            
            @foreach($matchesByRound as $roundNum => $matches)
                <div class="flex flex-col justify-around min-h-[400px]">
                    {{-- Round Title --}}
                    <div class="text-center mb-4">
                        <div class="bg-blue-600/20 border border-blue-500 rounded-lg px-4 py-2">
                            <div class="text-blue-300 font-semibold text-sm">{{ getRoundName($roundNum, $totalRounds) }}</div>
                            <div class="text-blue-400 text-xs mt-1">{{ $matches->count() }} {{ $matches->count() == 1 ? 'meč' : 'mečeva' }}</div>
                        </div>
                    </div>
                    
                    {{-- Matches in this round --}}
                    <div class="flex flex-col gap-4" style="justify-content: space-around;">
                        @foreach($matches as $match)
                            <div class="relative bg-gray-700/30 rounded-lg border border-gray-600/50 hover:border-blue-500/50 transition-all min-w-[240px]">
                                {{-- Match Header --}}
                                <div class="bg-gray-800/50 px-2 py-1.5 border-b border-gray-600/30 flex items-center justify-between">
                                    <span class="text-[10px] font-semibold text-gray-300">Meč {{ $loop->parent->iteration }}.{{ $loop->iteration }}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full 
                                        @if($match->status === 'completed') bg-green-600/20 text-green-400
                                        @elseif($match->status === 'live') bg-red-600/20 text-red-400 animate-pulse
                                        @else bg-gray-600/20 text-gray-400
                                        @endif">
                                        @if($match->status === 'completed') ✓
                                        @elseif($match->status === 'live') 🔴
                                        @else ⏳
                                        @endif
                                    </span>
                                </div>
                                
                                {{-- Players --}}
                                <div class="p-2">
                                    @php
                                        // Get player's group and position info
                                        $homePlayerInfo = null;
                                        $awayPlayerInfo = null;
                                        
                                        if($match->homePlayer) {
                                            foreach($competition->tournamentGroups as $group) {
                                                $standings = is_array($group->standings) ? collect($group->standings) : $group->standings;
                                                $homeStanding = $standings->where('player_id', $match->home_player_id)->first();
                                                if($homeStanding) {
                                                    $position = null;
                                                    if(is_object($homeStanding)) {
                                                        $position = $homeStanding->position ?? null;
                                                    } elseif(is_array($homeStanding)) {
                                                        $position = $homeStanding['position'] ?? null;
                                                    }
                                                    
                                                    if($position !== null) {
                                                        $homePlayerInfo = [
                                                            'group' => $group->name,
                                                            'position' => $position
                                                        ];
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        
                                        if($match->awayPlayer) {
                                            foreach($competition->tournamentGroups as $group) {
                                                $standings = is_array($group->standings) ? collect($group->standings) : $group->standings;
                                                $awayStanding = $standings->where('player_id', $match->away_player_id)->first();
                                                if($awayStanding) {
                                                    $position = null;
                                                    if(is_object($awayStanding)) {
                                                        $position = $awayStanding->position ?? null;
                                                    } elseif(is_array($awayStanding)) {
                                                        $position = $awayStanding['position'] ?? null;
                                                    }
                                                    
                                                    if($position !== null) {
                                                        $awayPlayerInfo = [
                                                            'group' => $group->name,
                                                            'position' => $position
                                                        ];
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    
                                    {{-- Home Player --}}
                                    <div class="flex items-center justify-between p-1.5 rounded-lg mb-1
                                        @if($homePlayerInfo && $homePlayerInfo['position'] == 1)
                                            bg-green-500/10 border-l-4 border-l-green-500
                                        @elseif($homePlayerInfo && $homePlayerInfo['position'] == 2)
                                            bg-yellow-500/10 border-l-4 border-l-yellow-500
                                        @else
                                            bg-gray-600/20
                                        @endif
                                        @if($match->status === 'completed' && $match->winner_id === $match->home_player_id) 
                                            ring-2 ring-green-500
                                        @endif">
                                        <div class="flex-1 min-w-0">
                                            @if($match->homePlayer)
                                                <div class="flex items-center gap-1">
                                                    @if($homePlayerInfo)
                                                        <span class="text-[10px]">{{ $homePlayerInfo['position'] == 1 ? '🥇' : '🥈' }}</span>
                                                    @endif
                                                    <span class="text-white text-xs font-medium truncate block">{{ $match->homePlayer->name }}</span>
                                                </div>
                                                @if($homePlayerInfo)
                                                    <div class="text-[10px] text-gray-400 mt-0.5 ml-4">{{ $homePlayerInfo['group'] }}-{{ $homePlayerInfo['position'] }}</div>
                                                @endif
                                            @else
                                                <span class="text-gray-500 italic text-[10px]">TBD</span>
                                            @endif
                                        </div>
                                        @if($match->status === 'completed' && $match->home_score !== null)
                                            <span class="text-base font-bold ml-2
                                                @if($match->winner_id === $match->home_player_id) text-green-400
                                                @else text-gray-400
                                                @endif">
                                                {{ $match->home_score }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Away Player --}}
                                    <div class="flex items-center justify-between p-1.5 rounded-lg
                                        @if($awayPlayerInfo && $awayPlayerInfo['position'] == 1)
                                            bg-green-500/10 border-l-4 border-l-green-500
                                        @elseif($awayPlayerInfo && $awayPlayerInfo['position'] == 2)
                                            bg-yellow-500/10 border-l-4 border-l-yellow-500
                                        @else
                                            bg-gray-600/20
                                        @endif
                                        @if($match->status === 'completed' && $match->winner_id === $match->away_player_id) 
                                            ring-2 ring-green-500
                                        @endif">
                                        <div class="flex-1 min-w-0">
                                            @if($match->awayPlayer)
                                                <div class="flex items-center gap-1">
                                                    @if($awayPlayerInfo)
                                                        <span class="text-[10px]">{{ $awayPlayerInfo['position'] == 1 ? '🥇' : '🥈' }}</span>
                                                    @endif
                                                    <span class="text-white text-xs font-medium truncate block">{{ $match->awayPlayer->name }}</span>
                                                </div>
                                                @if($awayPlayerInfo)
                                                    <div class="text-[10px] text-gray-400 mt-0.5 ml-4">{{ $awayPlayerInfo['group'] }}-{{ $awayPlayerInfo['position'] }}</div>
                                                @endif
                                            @else
                                                <span class="text-gray-500 italic text-[10px]">TBD</span>
                                            @endif
                                        </div>
                                        @if($match->status === 'completed' && $match->away_score !== null)
                                            <span class="text-base font-bold ml-2
                                                @if($match->winner_id === $match->away_player_id) text-green-400
                                                @else text-gray-400
                                                @endif">
                                                {{ $match->away_score }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Match Actions --}}
                                    <div class="mt-2 flex gap-1">
                                        @if($match->status === 'scheduled' || $match->status === 'pending')
                                            @if($isOwner || $isRefereeForMatch($match))
                                                <a href="{{ $isRefereeForMatch($match) ? route('referee.competition.match.edit', [$match]) : route('organizations.competitions.matches.edit', [$organization, $competition, $match]) }}" 
                                                   class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-[10px] px-2 py-1 rounded transition-colors text-center">
                                                    ✏️ Edit
                                                </a>
                                                <a href="{{ $isRefereeForMatch($match) ? route('referee.competition.match.live', [$match]) : route('competitions.live-score', [$match->id]) }}" 
                                                   class="flex-1 bg-green-600 hover:bg-green-700 text-white text-[10px] px-2 py-1 rounded transition-colors text-center">
                                                    ▶️ Live
                                                </a>
                                                <button onclick="openQuickResultModal({{ $match->id }}, '{{ $match->homePlayer->name ?? 'TBD' }}', '{{ $match->awayPlayer->name ?? 'TBD' }}')"
                                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-2 py-1 rounded transition-colors text-center">
                                                    ⚡ Quick
                                                </button>
                                            @else
                                                <span class="flex-1 text-[10px] bg-yellow-600/20 text-yellow-400 px-2 py-1 rounded text-center">
                                                    Soon
                                                </span>
                                            @endif
                                        @elseif($match->status === 'in_progress')
                                            <span class="flex-1 text-[10px] bg-green-600/20 text-green-400 px-2 py-1 rounded text-center animate-pulse">
                                                🔴 Live
                                            </span>
                                            @if($isOwner || $isRefereeForMatch($match))
                                                <a href="{{ $isRefereeForMatch($match) ? route('referee.competition.match.live', [$match]) : route('competitions.live-score', [$match->id]) }}" 
                                                   class="flex-1 text-blue-400 hover:text-blue-300 text-[10px] text-center border border-blue-400/30 rounded py-1">
                                                    👁️ Watch
                                                </a>
                                            @endif
                                        @elseif($match->status === 'completed')
                                            <span class="flex-1 text-[10px] bg-gray-600/20 text-gray-400 px-2 py-1 rounded text-center">
                                                ✓ FT
                                            </span>
                                            @if($isOwner || $isRefereeForMatch($match))
                                                <a href="{{ $isRefereeForMatch($match) ? route('referee.competition.match.edit', [$match]) : route('organizations.competitions.matches.edit', [$organization, $competition, $match]) }}" 
                                                   class="flex-1 text-blue-400 hover:text-blue-300 text-[10px] text-center border border-blue-400/30 rounded py-1">
                                                    ✏️ Edit
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- Connector line to next round --}}
                                @if(!$loop->parent->last)
                                    <div class="absolute top-1/2 -right-16 w-16 h-0.5 bg-gradient-to-r from-blue-500/50 to-transparent -translate-y-1/2"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </div>
</div>
@endif
