<!-- Knockout Bracket for Projector -->
@php
    // Group knockout matches by round
    // 1 = Final, 2 = Semifinal, 4 = Quarterfinal, 8 = Round of 16
    // Display order: Round of 16 (8) LEFT → Quarterfinal (4) → Semifinal (2) → Final (1) RIGHT
    $knockoutMatches = $competition->matches
        ->where('phase', 'knockout')
        ->sortBy('match_order')
        ->groupBy(function($match) {
            return (int)$match->round_number;
        })
        ->sortKeys(); // Sort in ascending order: 1, 2, 3... (Start to Final)
    
    // Determine max round specific to this competition to label correctly
    $maxRound = $knockoutMatches->keys()->last();
@endphp

@if($knockoutMatches->isNotEmpty())
<div class="h-full flex flex-col p-6 max-w-full overflow-hidden">
    <!-- Zoom Controls -->
    <div class="flex justify-center mb-2 opacity-20 hover:opacity-100 transition-opacity duration-300">
        <div class="flex items-center gap-1 scale-75 transform origin-center">
            <button type="button" onclick="window.changeZoom(-0.05)" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-base font-bold backdrop-blur-sm border border-gray-600/50" title="Smanji Prikaz">−</button>
            <button type="button" onclick="window.resetZoom()" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-xs font-bold backdrop-blur-sm border border-gray-600/50" title="Resetuj Prikaz">🔄</button>
            <button type="button" onclick="window.changeZoom(0.05)" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-base font-bold backdrop-blur-sm border border-gray-600/50" title="Povećaj Prikaz">+</button>
            
            <div class="w-px h-6 bg-gray-600/50 mx-1"></div>

            <button type="button" onclick="window.changeWidth(-20)" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-xs font-bold backdrop-blur-sm border border-gray-600/50" title="Uže Kolone">⬅️</button>
            <button type="button" onclick="window.resetWidth()" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-xs font-bold backdrop-blur-sm border border-gray-600/50" title="Resetuj Širinu">↔️</button>
            <button type="button" onclick="window.changeWidth(20)" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-xs font-bold backdrop-blur-sm border border-gray-600/50" title="Šire Kolone">➡️</button>

            <div class="w-px h-6 bg-gray-600/50 mx-1"></div>

            <button type="button" onclick="window.changePlayerFont(-1)" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-xs font-bold backdrop-blur-sm border border-gray-600/50" title="Manji Font Igrača">A-</button>
            <button type="button" onclick="window.resetPlayerFont()" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-xs font-bold backdrop-blur-sm border border-gray-600/50" title="Resetuj Font">A</button>
            <button type="button" onclick="window.changePlayerFont(1)" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-xs font-bold backdrop-blur-sm border border-gray-600/50" title="Veći Font Igrača">A+</button>
        </div>
    </div>
    
    <!-- Tournament Header -->
    <div class="mb-4 text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-white flex items-center justify-center gap-3">
            <span class="text-3xl md:text-4xl">🏆</span>
            Eliminaciona faza - {{ $competition->name }}
        </h2>
    </div>
    
    <!-- Bracket Columns -->
    <div class="flex-1 flex flex-row h-full overflow-x-auto custom-scrollbar knockout-bracket-container" style="gap: 15px; justify-content: start;">
        @foreach($knockoutMatches as $roundNumber => $matches)
        @php
             // Logic: Max round is always the final
             $distanceToFinal = $maxRound - (int)$roundNumber;
             
             $roundName = match($distanceToFinal) {
                 0 => 'Finale',
                 1 => 'Polufinale',
                 2 => 'Četvrtfinale',
                 3 => 'Osmina finala',
                 4 => 'Šesnaestina finala',
                 default => 'Runda ' . $roundNumber
             };
        @endphp
        
        <div class="flex-1 flex flex-col knockout-column h-full transition-all duration-300" style="gap: 5px; min-width: 250px;">
            <div class="text-center mb-4">
                <h3 class="text-lg font-bold text-amber-400 uppercase tracking-widest border-b border-amber-400/30 pb-2">
                    {{ $roundName }}
                </h3>
            </div>

            <!-- Matches Container -->
            <div class="flex-1 flex flex-col justify-center gap-2" style="gap: 5px;">
                @foreach($matches as $match)
                <div class="block bg-gray-800/40 backdrop-blur-md rounded-lg border border-gray-700/50 shadow-xl transition-all duration-200 hover:scale-[1.02] knockout-match relative {{ $match->status === 'live' ? 'bg-red-900/20 border-red-500/50' : '' }}" 
                     data-match-id="{{ $match->id }}"
                     data-home-player="{{ $match->home_player_id }}"
                     data-away-player="{{ $match->away_player_id }}"
                     style="padding-top: 3px; margin-top: 3px; margin-bottom: 3px;">
                    
                    <!-- Live Indicator Overlay -->
                    @if($match->status === 'live')
                    <div class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse shadow-lg z-10">
                        LIVE
                    </div>
                    @endif

                    <!-- Match Players container -->
                    <div class="px-3 md:px-4" style="padding-bottom: 3px;">
                        @php
                            $homeWinner = $match->status === 'completed' && ($match->home_score ?? 0) > ($match->away_score ?? 0);
                            $awayWinner = $match->status === 'completed' && ($match->away_score ?? 0) > ($match->home_score ?? 0);
                            
                            $homeName = $competition->is_team_based 
                                ? ($match->homeTeam ? $match->homeTeam->name : 'TBD') 
                                : ($match->homePlayer ? $match->homePlayer->name : 'TBD');
                            $awayName = $competition->is_team_based 
                                ? ($match->awayTeam ? $match->awayTeam->name : 'TBD') 
                                : ($match->awayPlayer ? $match->awayPlayer->name : 'TBD');
                        @endphp

                        <!-- Home Player Row -->
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2 flex-1 min-w-0 player-container" data-player-id="{{ $match->home_player_id }}">
                                <div class="player-name font-semibold truncate {{ $homeWinner ? 'text-green-500 font-bold' : 'text-gray-300' }}">
                                    {{ $homeName }}
                                </div>
                            </div>
                            <div class="flex-shrink-0 ml-2">
                                <div class="w-6 h-6 {{ $homeWinner ? 'bg-green-900/80' : 'bg-gray-800' }} rounded flex items-center justify-center border border-white/5 badge-box">
                                    <div class="text-xs font-bold text-white badge-number">
                                        {{ $match->home_score ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Away Player Row -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 flex-1 min-w-0 player-container" data-player-id="{{ $match->away_player_id }}">
                                <div class="player-name font-semibold truncate {{ $awayWinner ? 'text-green-500 font-bold' : 'text-gray-300' }}">
                                    {{ $awayName }}
                                </div>
                            </div>
                            <div class="flex-shrink-0 ml-2">
                                <div class="w-6 h-6 {{ $awayWinner ? 'bg-green-900/80' : 'bg-gray-800' }} rounded flex items-center justify-center border border-white/5 badge-box">
                                    <div class="text-xs font-bold text-white badge-number">
                                        {{ $match->away_score ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Groups Reference (Subtle) -->
                    @if((int)$roundNumber > 2 && ($match->home_player_group || $match->away_player_group))
                    <div class="mt-1 flex justify-between text-[8px] text-gray-500/50 px-4 pb-1 uppercase tracking-tighter">
                        <span>{{ $match->home_player_group ?: '' }}</span>
                        <span>{{ $match->away_player_group ?: '' }}</span>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-2xl">
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-white flex items-center gap-3">
            <span class="text-4xl">🏆</span>
            Knockout Faza - {{ $competition->name }}
        </h2>
    </div>
    <div class="text-center py-12">
        <div class="text-6xl mb-4">🏆</div>
        <h3 class="text-2xl font-semibold text-white mb-2">Knockout faza još nije počela</h3>
        <p class="text-gray-400">Mečevi će se pojaviti kada knockout faza bude aktivna.</p>
    </div>
</div>
@endif
