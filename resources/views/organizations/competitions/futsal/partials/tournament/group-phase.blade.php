{{-- Futsal Group Phase Section --}}
@php
    // Check if all group matches are completed
    $allGroupMatchesCompleted = $groupMatches->count() > 0 && $groupMatches->flatten()->every(function($match) {
        return $match->status === 'completed';
    });
@endphp

<div class="mb-8">
    <div class="w-full bg-gray-800/50 backdrop-blur-xl rounded-2xl p-4 sm:p-6 border border-gray-700/50 hover:border-gray-600/50 transition-all text-left">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <h3 class="text-lg sm:text-2xl font-bold text-white">📋 Grupna Faza</h3>
                @if($allGroupMatchesCompleted)
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
                {{-- Knockout buttons (show when groups are completed) --}}
                @if($isOwner && $allGroupMatchesCompleted && $knockoutMatches->count() === 0)
                    <a href="{{ route('organizations.competitions.futsal.generate-knockout', [$organization, $competition]) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-2 sm:px-4 rounded-lg transition-colors font-semibold text-center">
                        🎯 Generiši Knockout
                    </a>
                @endif

                {{-- Reset Button --}}
                @if($isOwner && $groupMatches->count() > 0)
                    <button type="button" onclick="confirmResetGroupPhase()"
                            class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-2 sm:px-4 rounded-lg transition-colors font-semibold text-center">
                        🔄 Resetuj grupnu fazu
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{-- Groups Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            @foreach($competition->tournamentGroups as $group)
                @php
                    $matchesInGroup = $groupMatches->get($group->id, collect());
                    $standings = App\Models\FutsalStanding::where('competition_id', $competition->id)
                        ->where('tournament_group_id', $group->id)
                        ->with('futsalTeam')
                        ->orderBy('points', 'desc')
                        ->orderByRaw('(goals_for - goals_against) desc')
                        ->orderBy('goals_for', 'desc')
                        ->orderBy('id')
                        ->get();
                @endphp

                <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700/50 shadow-xl overflow-hidden">
                    {{-- Group Header --}}
                    <div class="bg-gradient-to-r from-green-600/20 to-emerald-600/20 px-4 py-3 border-b border-gray-700/50">
                        <div class="flex items-center justify-between">
                            <h4 class="text-white font-bold text-base flex items-center space-x-2">
                                <span class="bg-gradient-to-r from-green-500 to-emerald-600 px-3 py-1 rounded-full text-xs">
                                    Grupa {{ $group->name }}
                                </span>
                            </h4>
                            <span class="text-gray-400 text-xs">
                                {{ $matchesInGroup->where('status', 'completed')->count() }}/{{ $matchesInGroup->count() }} mečeva
                            </span>
                        </div>
                    </div>

                    {{-- Standings Table --}}
                    <div class="px-4 py-3 bg-gray-700/20">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-gray-400 border-b border-gray-700/50">
                                    <th class="text-left py-1 pr-2 font-medium">#</th>
                                    <th class="text-left py-1 font-medium">Tim</th>
                                    <th class="text-center py-1 px-1 font-medium">M</th>
                                    <th class="text-center py-1 px-1 font-medium">P</th>
                                    <th class="text-center py-1 px-1 font-medium">I</th>
                                    <th class="text-center py-1 px-1 font-medium">G</th>
                                    <th class="text-center py-1 px-1 font-medium text-green-400">Bod</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($standings as $index => $standing)
                                <tr class="border-b border-gray-700/30 hover:bg-gray-700/30 transition-colors {{ $index < $competition->players_advancing_per_group ? 'bg-green-900/30' : '' }}">
                                    <td class="py-2 pr-2 text-gray-400 font-mono">{{ $index + 1 }}</td>
                                    <td class="py-2 text-white font-medium">
                                        {{ $standing->futsalTeam->name }}
                                    </td>
                                    <td class="py-2 px-1 text-center text-gray-300">{{ $standing->played }}</td>
                                    <td class="py-2 px-1 text-center text-green-400">{{ $standing->won }}</td>
                                    <td class="py-2 px-1 text-center text-red-400">{{ $standing->lost }}</td>
                                    <td class="py-2 px-1 text-center text-gray-300">{{ $standing->goals_for }}-{{ $standing->goals_against }}</td>
                                    <td class="py-2 px-1 text-center text-green-400 font-bold">{{ $standing->points }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-gray-400">Nema podataka o bodovima</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Matches in Group --}}
                    @if($matchesInGroup->count() > 0)
                    <div class="px-4 py-3 border-t border-gray-700/50">
                        <h5 class="text-gray-300 font-semibold text-xs mb-2">Mečevi</h5>
                        <div class="space-y-3">
                            @foreach($matchesInGroup as $roundNumber => $roundMatches)
                                <div class="space-y-1">
                                    <div class="text-gray-400 text-xs font-medium px-2 py-1 bg-gray-700/30 rounded">
                                        Kolo {{ $roundNumber }}
                                    </div>
                                    <div class="space-y-1">
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
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function confirmResetGroupPhase() {
    if (confirm('Da li si siguran? Ovo će obrisati svu grupnu fazu.')) {
        // For futsal, we need to call the appropriate reset method
        // This would need to be implemented in the controller
        alert('Reset funkcionalnost će biti implementirana.');
    }
}
</script>