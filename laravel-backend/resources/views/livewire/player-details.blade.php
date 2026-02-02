@php
    use Carbon\Carbon;
    // Ensure $matchesBySport is always defined as an array
    $matchesBySport = $matchesBySport ?? [];
@endphp
<div class="max-w-4xl mx-auto">
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
        <div class="text-center mb-6">
            <h1 class="text-4xl font-bold text-white mb-2">{{ $player->name }}</h1>
            <p class="text-gray-400">{{ $organization->name }}</p>
        </div>

        <!-- General Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-400">{{ $stats['mp'] }}</div>
                <div class="text-sm text-gray-400">Mečevi</div>
            </div>
            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-400">{{ $stats['w'] }}</div>
                <div class="text-sm text-gray-400">Pobjede</div>
            </div>
            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-red-400">{{ $stats['l'] }}</div>
                <div class="text-sm text-gray-400">Porazi</div>
            </div>
            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-purple-400">{{ $stats['mp'] > 0 ? round(($stats['w'] / $stats['mp']) * 100, 1) : 0 }}%</div>
                <div class="text-sm text-gray-400">Uspješnost</div>
            </div>
            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-yellow-400">{{ $stats['sets_w'] }}-{{ $stats['sets_l'] }}</div>
                <div class="text-sm text-gray-400">Setovi</div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                <div class="text-xl font-bold text-cyan-400">{{ $stats['sets_w'] > 0 ? round(($stats['sets_w'] / ($stats['sets_w'] + $stats['sets_l'])) * 100, 1) : 0 }}%</div>
                <div class="text-sm text-gray-400">Uspješnost setova</div>
            </div>
            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                <div class="text-xl font-bold text-orange-400">{{ round(($stats['sets_w'] + $stats['sets_l']) / max($stats['mp'], 1), 1) }}</div>
                <div class="text-sm text-gray-400">Prosjek setova po meču</div>
            </div>
            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                <div class="text-xl font-bold text-pink-400">{{ count($stats['opponents']) }}</div>
                <div class="text-sm text-gray-400">Različitih protivnika</div>
            </div>
            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                <div class="text-xl font-bold {{ $stats['win_streak'] > 0 ? 'text-green-400' : ($stats['loss_streak'] > 0 ? 'text-red-400' : 'text-gray-400') }}">
                    @if($stats['win_streak'] > 0)
                        +{{ $stats['win_streak'] }}
                    @elseif($stats['loss_streak'] > 0)
                        -{{ $stats['loss_streak'] }}
                    @else
                        0
                    @endif
                </div>
                <div class="text-sm text-gray-400">Trenutni niz</div>
            </div>
        </div>

        <!-- Streaks -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-400">{{ $stats['longest_win_streak'] }}</div>
                <div class="text-sm text-gray-400">Najduži pobjednički niz</div>
            </div>
            <div class="bg-gray-700/50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-red-400">{{ $stats['longest_loss_streak'] }}</div>
                <div class="text-sm text-gray-400">Najduži poraženi niz</div>
            </div>
        </div>

        <!-- Opponents -->
        @if(!empty($stats['opponents']))
        <div class="mb-8">
            <h3 class="text-xl font-bold text-white mb-4">Protivnici</h3>
            <div class="grid gap-3">
                @foreach(collect($stats['opponents'])->sortByDesc(function($opp) { return $opp['wins'] + $opp['losses']; }) as $opponent)
                @php
                    $totalGames = $opponent['wins'] + $opponent['losses'];
                    $winRate = $totalGames > 0 ? round(($opponent['wins'] / $totalGames) * 100, 1) : 0;
                @endphp
                <div class="bg-gray-700/50 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-white font-semibold">{{ $opponent['name'] }}</span>
                        <span class="text-sm px-2 py-1 rounded {{ $winRate >= 50 ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $winRate }}% pobjeda
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-4 text-sm">
                            <span class="text-green-400">{{ $opponent['wins'] }} pobjeda</span>
                            <span class="text-red-400">{{ $opponent['losses'] }} poraza</span>
                            <span class="text-blue-400">{{ $totalGames }} ukupno</span>
                        </div>
                        <div class="w-32 bg-gray-600 rounded-full h-2">
                            <div class="bg-green-400 h-2 rounded-full" style="width: {{ $winRate }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Recent Form -->
    @if(count($matches) > 0)
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
        <h3 class="text-xl font-bold text-white mb-6">Nedavna forma (zadnjih 10 mečeva)</h3>
        <div class="flex flex-wrap gap-2 mb-4">
            @php
                $recentMatches = collect($matches)->take(10);
                $form = [];
                foreach($recentMatches as $match) {
                    if($match['winner'] === $player->name || str_contains($match['winner'], $player->name)) {
                        $form[] = 'W';
                    } else {
                        $form[] = 'L';
                    }
                }
            @endphp
            @foreach($form as $result)
                <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $result === 'W' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                    {{ $result }}
                </span>
            @endforeach
        </div>
        <p class="text-gray-400 text-sm">
            @php
                $recentWins = collect($form)->filter(fn($r) => $r === 'W')->count();
                $recentTotal = count($form);
            @endphp
            {{ $recentWins }} pobjeda od {{ $recentTotal }} mečeva ({{ $recentTotal > 0 ? round(($recentWins / $recentTotal) * 100, 1) : 0 }}%)
        </p>
    </div>
    @endif

    <!-- Matches History by Sport -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
        @livewire('player-match-history', ['playerId' => $player->id, 'organizationId' => $organization->id])
    </div>
</div>