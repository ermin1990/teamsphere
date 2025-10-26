{{-- Knockout Phase Bracket --}}
@php
    $knockoutMatches = isset($knockoutMatches) ? $knockoutMatches : collect();
    $isOwner = isset($isOwner) ? $isOwner : false;
    $organization = isset($organization) ? $organization : null;
    $competition = isset($competition) ? $competition : null;
@endphp

@if($knockoutMatches && $knockoutMatches->count() > 0)
<div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 mb-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-white">🏆 Knockout Faza</h3>
        
        @if($isOwner && $knockoutMatches->count() > 0)
            <button type="button" onclick="confirmResetKnockout()"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                🔄 Resetuj
            </button>
        @endif
    </div>

    {{-- Bracket visualization --}}
    <div class="overflow-x-auto pb-4">
        <div class="inline-flex gap-12 min-w-max items-center p-4">
            @php
                $groupedByRound = $knockoutMatches->groupBy('round_number');
                $totalRounds = $groupedByRound->keys()->max();
            @endphp

            @foreach($groupedByRound as $round => $roundMatches)
                @if(is_iterable($roundMatches))
                <div class="flex flex-col justify-around min-h-[400px] gap-4">
                    {{-- Round title --}}
                    <div class="text-center mb-4">
                        <div class="bg-blue-600/20 border border-blue-500 rounded-lg px-3 py-2">
                            <div class="text-blue-300 font-semibold text-sm">
                                @php
                                    $matchCount = $roundMatches->count();
                                    if ($matchCount == 1) echo 'Finale';
                                    elseif ($matchCount == 2) echo 'Polufinale';
                                    elseif ($matchCount == 4) echo 'Četvrtfinale';
                                    elseif ($matchCount == 8) echo 'Osmina finala';
                                    else echo 'Runda ' . $round;
                                @endphp
                            </div>
                        </div>
                    </div>

                    {{-- Matches in round --}}
                    <div class="space-y-3">
                        @foreach($roundMatches as $match)
                            <div class="w-56 bg-gray-700/30 rounded-lg border border-gray-600/50 overflow-hidden hover:border-blue-500/50 transition-all">
                                {{-- Match header --}}
                                <div class="bg-gray-800/50 px-3 py-2 border-b border-gray-600/30">
                                    <span class="text-xs font-semibold text-gray-400">Meč {{ $match->match_order }}</span>
                                    <span class="ml-2 text-xs px-2 py-1 rounded-full
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

                                {{-- Players and score --}}
                                <div class="p-3">
                                    {{-- Home player --}}
                                    <div class="flex justify-between items-center mb-2 p-2 rounded-lg
                                        @if($match->status === 'completed' && $match->home_score > $match->away_score) bg-green-500/10 border-l-4 border-l-green-500
                                        @else bg-gray-600/20
                                        @endif">
                                        <span class="text-white text-sm font-medium">{{ $match->homePlayer->name ?? 'TBD' }}</span>
                                        @if($match->status === 'completed')
                                            <span class="text-base font-bold {{ $match->home_score > $match->away_score ? 'text-green-400' : 'text-gray-400' }}">
                                                {{ $match->home_score ?? '-' }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Away player --}}
                                    <div class="flex justify-between items-center p-2 rounded-lg
                                        @if($match->status === 'completed' && $match->away_score > $match->home_score) bg-green-500/10 border-l-4 border-l-green-500
                                        @else bg-gray-600/20
                                        @endif">
                                        <span class="text-white text-sm font-medium">{{ $match->awayPlayer->name ?? 'TBD' }}</span>
                                        @if($match->status === 'completed')
                                            <span class="text-base font-bold {{ $match->away_score > $match->home_score ? 'text-green-400' : 'text-gray-400' }}">
                                                {{ $match->away_score ?? '-' }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Actions --}}
                                    @if($match->status === 'scheduled' || $match->status === 'pending')
                                        <div class="mt-3 flex gap-1 text-xs">
                                            @if($isOwner)
                                                <button type="button" 
                                                    onclick="openQuickResult({{ $match->id }}, '{{ $match->homePlayer->name ?? 'TBD' }}', '{{ $match->awayPlayer->name ?? 'TBD' }}')"
                                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded transition-colors">
                                                    ⚡ Quick
                                                </button>
                                                <a href="{{ route('organizations.competitions.matches.edit', [$organization, $competition, $match]) }}"
                                                   class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-2 py-1 rounded transition-colors text-center">
                                                    ✏️
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@else
<div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 mb-6 text-center">
    <p class="text-gray-300">Knockout faza još nije kreirana</p>
    @if($isOwner)
        <div class="mt-4 flex gap-3 justify-center">
            <a href="{{ route('organizations.competitions.knockout-setup', [$organization, $competition]) }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                🎯 Ručno Postavi
            </a>
            <form method="POST" action="{{ route('organizations.competitions.auto-generate-knockout', [$organization, $competition]) }}" style="display: inline;">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    ⚡ Automatski Generiši
                </button>
            </form>
        </div>
    @endif
</div>
@endif

<script>
function confirmResetKnockout() {
    if (confirm('Da li si siguran? Ovo će obrisati svu knockout fazu.')) {
        // Add your reset logic here
    }
}

function openQuickResult(matchId, homePlayer, awayPlayer) {
    // Implement quick result modal
    console.log('Quick result for match', matchId);
}
</script>

