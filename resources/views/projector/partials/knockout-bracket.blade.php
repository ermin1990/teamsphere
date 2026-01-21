<!-- Knockout Bracket for Projector -->
@php
    // Group knockout matches by round
    $knockoutMatches = $competition->matches
        ->where('phase', 'knockout')
        ->sortBy(['round_number', 'match_order'])
        ->groupBy('round_number');
    
    // Determine max round specific to this competition to label correctly
    $maxRound = $knockoutMatches->keys()->max();
@endphp

@if($knockoutMatches->isNotEmpty())
<div class="h-full flex flex-col p-2">
    <!-- Header -->
    <div class="mb-4 text-center">
        <h2 class="text-3xl font-bold text-white flex items-center justify-center gap-3">
            <span class="text-4xl">🏆</span>
            Knockout Faza - {{ $competition->name }}
        </h2>
    </div>

    <!-- Bracket Columns -->
    <div class="flex-1 flex flex-row gap-4 h-full overflow-x-auto min-h-0">
        @foreach($knockoutMatches as $roundNumber => $matches)
        @php
             $roundsFromFinal = $maxRound - $roundNumber;
             $roundName = match($roundsFromFinal) {
                 0 => 'Finale',
                 1 => 'Polufinale',
                 2 => 'Četvrtfinale',
                 3 => 'Osmina finala',
                 4 => 'Šesnaestina finala',
                 default => 'Runda ' . $roundNumber
             };
        @endphp
        
        <div class="flex-1 flex flex-col min-w-[300px] h-full">
            <!-- Round Header -->
            <div class="bg-gray-800/90 rounded-t-xl p-3 text-center border-b border-gray-700">
                <h3 class="text-xl font-bold text-purple-400">{{ $roundName }}</h3>
                <span class="text-xs text-gray-400">{{ $matches->count() }} mečeva</span>
            </div>
            
            <!-- Matches Container -->
            <div class="flex-1 bg-gray-900/30 rounded-b-xl border border-gray-700 p-2 overflow-y-auto custom-scrollbar flex flex-col justify-around">
                @foreach($matches as $match)
                <div class="bg-gray-800 rounded-lg p-2 border border-gray-700/50 shadow-lg mb-2 last:mb-0 relative {{ $match->status === 'live' ? 'ring-2 ring-red-500/50' : '' }}">
                    <!-- Live Indicator Overlay -->
                    @if($match->status === 'live')
                    <div class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse shadow-lg z-10">
                        LIVE
                    </div>
                    @endif

                    <!-- Home Player -->
                    <div class="flex justify-between items-center mb-1 pb-1 border-b border-gray-700/50 {{ ($match->home_score ?? 0) > ($match->away_score ?? 0) ? 'bg-green-500/10' : '' }} rounded px-1">
                        <span class="text-white font-medium truncate text-sm flex-1 mr-2 {{ ($match->home_score ?? 0) > ($match->away_score ?? 0) ? 'text-green-400 font-bold' : '' }}">
                            @if($competition->is_team_based)
                                {{ $match->homeTeam ? $match->homeTeam->name : 'TBD' }}
                            @else
                                {{ $match->homePlayer ? $match->homePlayer->name : 'TBD' }}
                            @endif
                        </span>
                        <span class="text-lg font-bold {{ ($match->home_score ?? 0) > ($match->away_score ?? 0) ? 'text-green-400' : 'text-gray-400' }}">
                            {{ $match->home_score ?? 0 }}
                        </span>
                    </div>

                    <!-- Away Player -->
                    <div class="flex justify-between items-center {{ ($match->away_score ?? 0) > ($match->home_score ?? 0) ? 'bg-green-500/10' : '' }} rounded px-1">
                        <span class="text-white font-medium truncate text-sm flex-1 mr-2 {{ ($match->away_score ?? 0) > ($match->home_score ?? 0) ? 'text-green-400 font-bold' : '' }}">
                            @if($competition->is_team_based)
                                {{ $match->awayTeam ? $match->awayTeam->name : 'TBD' }}
                            @else
                                {{ $match->awayPlayer ? $match->awayPlayer->name : 'TBD' }}
                            @endif
                        </span>
                        <span class="text-lg font-bold {{ ($match->away_score ?? 0) > ($match->home_score ?? 0) ? 'text-green-400' : 'text-gray-400' }}">
                            {{ $match->away_score ?? 0 }}
                        </span>
                    </div>

                    <!-- Groups Reference -->
                    @if($roundsFromFinal > 1 && ($match->home_player_group || $match->away_player_group))
                    <div class="mt-1 flex justify-between text-[10px] text-gray-500">
                        <span>{{ $match->home_player_group ? $match->home_player_group : '' }}</span>
                        <span>{{ $match->away_player_group ? $match->away_player_group : '' }}</span>
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
