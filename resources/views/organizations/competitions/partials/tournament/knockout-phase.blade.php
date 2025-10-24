{{-- Knockout Phase --}}
@if($knockoutMatches->count() > 0)
<div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 mb-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-white">🏆 Eliminaciona Faza</h3>
        
        @if($isOwner)
        <a href="{{ route('organizations.competitions.manual-knockout-setup', [$organization, $competition]) }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors text-sm">
            ✏️ Uredi raspored
        </a>
        @endif
    </div>
    
    @php
        // Group matches by round
        $matchesByRound = $knockoutMatches->groupBy('round');
        $roundNames = [
            1 => 'Prva runda',
            2 => 'Druga runda',
            'quarterfinals' => 'Četvrtfinale',
            'semifinals' => 'Polufinale',
            'final' => 'Finale',
            'third_place' => 'Meč za 3. mjesto'
        ];
    @endphp
    
    @foreach($matchesByRound as $round => $matches)
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-sm">
                    {{ $matches->count() }}
                </span>
                {{ $roundNames[$round] ?? "Runda $round" }}
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($matches as $match)
                    <div class="bg-gray-700/30 rounded-xl border border-gray-600/50 overflow-hidden hover:border-blue-500/50 transition-all">
                        {{-- Match Header --}}
                        <div class="bg-gray-800/50 px-4 py-2 border-b border-gray-600/30">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-300">
                                    Meč {{ $loop->iteration }}
                                </span>
                                <span class="text-xs px-2 py-1 rounded-full 
                                    @if($match->status === 'completed') bg-green-600/20 text-green-400
                                    @elseif($match->status === 'live') bg-red-600/20 text-red-400 animate-pulse
                                    @else bg-gray-600/20 text-gray-400
                                    @endif">
                                    @if($match->status === 'completed') ✓ Završeno
                                    @elseif($match->status === 'live') 🔴 Live
                                    @else Zakazano
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        {{-- Players --}}
                        <div class="p-4">
                            {{-- Home Player --}}
                            <div class="flex items-center justify-between p-3 bg-gray-600/20 rounded-lg mb-2
                                @if($match->status === 'completed' && $match->winner_id === $match->home_player_id) 
                                    ring-2 ring-green-500 bg-green-500/10
                                @endif">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    @if($match->homePlayer)
                                        <span class="text-white font-medium truncate">
                                            {{ $match->homePlayer->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 italic text-sm">TBD</span>
                                    @endif
                                </div>
                                @if($match->status === 'completed' && $match->home_score !== null)
                                    <span class="text-lg font-bold 
                                        @if($match->winner_id === $match->home_player_id) text-green-400
                                        @else text-gray-400
                                        @endif">
                                        {{ $match->home_score }}
                                    </span>
                                @endif
                            </div>
                            
                            {{-- VS Divider --}}
                            <div class="text-center text-gray-500 text-xs font-semibold my-1">VS</div>
                            
                            {{-- Away Player --}}
                            <div class="flex items-center justify-between p-3 bg-gray-600/20 rounded-lg
                                @if($match->status === 'completed' && $match->winner_id === $match->away_player_id) 
                                    ring-2 ring-green-500 bg-green-500/10
                                @endif">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    @if($match->awayPlayer)
                                        <span class="text-white font-medium truncate">
                                            {{ $match->awayPlayer->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 italic text-sm">TBD</span>
                                    @endif
                                </div>
                                @if($match->status === 'completed' && $match->away_score !== null)
                                    <span class="text-lg font-bold 
                                        @if($match->winner_id === $match->away_player_id) text-green-400
                                        @else text-gray-400
                                        @endif">
                                        {{ $match->away_score }}
                                    </span>
                                @endif
                            </div>
                            
                            {{-- Match Actions --}}
                            @if($isOwner && $match->homePlayer && $match->awayPlayer)
                                <div class="flex gap-2 mt-4">
                                    @if($match->status !== 'completed')
                                        <button onclick="quickResultModal({{ $match->id }})" 
                                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                                            ⚡ Rezultat
                                        </button>
                                    @endif
                                    <a href="{{ route('organizations.competitions.matches.show', [$organization, $competition, $match]) }}" 
                                       class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg text-sm transition-colors text-center">
                                        👁️ Detalji
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endif
