{{-- Futsal Knockout Phase Section --}}
@php
    $rounds = $knockoutMatches->groupBy('round_number');
    $maxRound = $rounds->keys()->max();
@endphp

<div class="mb-8">
    <div class="w-full bg-gray-800/50 backdrop-blur-xl rounded-2xl p-4 sm:p-6 border border-gray-700/50 hover:border-gray-600/50 transition-all text-left">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <h3 class="text-lg sm:text-2xl font-bold text-white">🏆 Eliminaciona Faza</h3>
                @if($rounds->count() > 0 && $rounds->get($maxRound)->every(fn($match) => $match->status === 'completed'))
                    <span class="px-2 py-1 sm:px-3 text-xs rounded-full bg-green-600/20 text-green-400 self-start">
                        ✓ Završeno
                    </span>
                @else
                    <span class="px-2 py-1 sm:px-3 text-xs rounded-full bg-yellow-600/20 text-yellow-400 self-start">
                        ⏳ U toku
                    </span>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row gap-2 sm:gap-2 sm:ml-auto">
                {{-- Advance Round Button --}}
                @if($isOwner && $rounds->count() > 0)
                    @php
                        $currentRound = $rounds->keys()->sort()->last();
                        $currentRoundMatches = $rounds->get($currentRound);
                        $allCurrentRoundCompleted = $currentRoundMatches->every(fn($match) => $match->status === 'completed');
                        $hasNextRound = $rounds->has($currentRound + 1) || $currentRoundMatches->count() > 1;
                    @endphp

                    @if($allCurrentRoundCompleted && $hasNextRound && $currentRound < $maxRound)
                        <form method="POST" action="{{ route('organizations.competitions.futsal.advance-knockout', [$organization, $competition]) }}" class="inline">
                            @csrf
                            <input type="hidden" name="current_round" value="{{ $currentRound }}">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-3 py-2 sm:px-4 rounded-lg transition-colors font-semibold text-center">
                                ➡️ Sljedeća runda
                            </button>
                        </form>
                    @endif
                @endif

                {{-- Reset Button --}}
                @if($isOwner && $rounds->count() > 0)
                    <button type="button" onclick="confirmResetKnockout()"
                            class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-2 sm:px-4 rounded-lg transition-colors font-semibold text-center">
                        🔄 Resetuj knockout
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{-- Rounds Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($rounds as $roundNumber => $roundMatches)
                @php
                    $roundName = match($roundNumber) {
                        1 => $roundMatches->count() === 1 ? 'Finale' : 'Prvo kolo',
                        2 => $roundMatches->count() === 1 ? 'Finale' : 'Drugo kolo',
                        3 => 'Polufinale',
                        4 => 'Četvrtfinale',
                        default => "Kolo {$roundNumber}"
                    };
                @endphp

                <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700/50 shadow-xl overflow-hidden">
                    {{-- Round Header --}}
                    <div class="bg-gradient-to-r from-purple-600/20 to-pink-600/20 px-4 py-3 border-b border-gray-700/50">
                        <div class="flex items-center justify-between">
                            <h4 class="text-white font-bold text-base">{{ $roundName }}</h4>
                            <span class="text-gray-400 text-xs">
                                {{ $roundMatches->where('status', 'completed')->count() }}/{{ $roundMatches->count() }} mečeva
                            </span>
                        </div>
                    </div>

                    {{-- Matches in Round --}}
                    <div class="p-4 space-y-3">
                        @foreach($roundMatches as $match)
                            @include('organizations.competitions.futsal.partials.tournament.match-card', [
                                'match' => $match,
                                'competition' => $competition,
                                'organization' => $organization,
                                'isOwner' => $isOwner,
                                'isRefereeForMatch' => $isRefereeForMatch
                            ])
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function confirmResetKnockout() {
    if (confirm('Da li si siguran? Ovo će obrisati cijelu eliminacionu fazu.')) {
        // For futsal, we need to call the appropriate reset method
        // This would need to be implemented in the controller
        alert('Reset funkcionalnost će biti implementirana.');
    }
}
</script>