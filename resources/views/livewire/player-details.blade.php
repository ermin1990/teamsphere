@php use Carbon\Carbon; @endphp
<div class="max-w-4xl mx-auto">
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
        <div class="text-center mb-6">
            <h1 class="text-4xl font-bold text-white mb-2">{{ $player->name }}</h1>
            <p class="text-gray-400">{{ $organization->name }}</p>
        </div>

        <!-- General Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
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
                <div class="text-2xl font-bold text-yellow-400">{{ $stats['sets_w'] }}-{{ $stats['sets_l'] }}</div>
                <div class="text-sm text-gray-400">Setovi</div>
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
            <div class="space-y-2">
                @foreach($stats['opponents'] as $opponent)
                <div class="bg-gray-700/50 rounded-lg p-4 flex justify-between items-center">
                    <span class="text-white">{{ $opponent['name'] }}</span>
                    <div class="flex space-x-4 text-sm">
                        <span class="text-green-400">{{ $opponent['wins'] }}W</span>
                        <span class="text-red-400">{{ $opponent['losses'] }}L</span>
                        <span class="text-blue-400">{{ $opponent['wins'] + $opponent['losses'] > 0 ? round($opponent['wins'] / ($opponent['wins'] + $opponent['losses']) * 100) : 0 }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Matches History -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
        <h3 class="text-xl font-bold text-white mb-6">Historija mečeva</h3>
        @if($matches->isEmpty())
        <p class="text-gray-400 text-center py-8">Nema odigranih mečeva.</p>
        @else
        <div class="space-y-4">
            @foreach($matches as $match)
            <div class="bg-gray-700/50 rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <div class="text-sm text-gray-400">{{ Carbon::parse($match->completed_at)->format('d.m.Y H:i') }}</div>
                    <div class="text-sm text-green-400">{{ $match->winner_name }} pobjeđuje</div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex-1 text-center">
                        <div class="text-lg font-bold {{ $match->home_player_id == $player->id ? ($match->sets[count($match->sets)-1]['home_score'] > $match->sets[count($match->sets)-1]['away_score'] ? 'text-green-400' : 'text-red-400') : 'text-white' }}">
                            {{ $match->home_player_name }}
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-2xl font-bold {{ $match->sets[count($match->sets)-1]['home_score'] > $match->sets[count($match->sets)-1]['away_score'] ? 'text-green-400' : 'text-white' }}">
                            {{ $match->sets[count($match->sets)-1]['home_score'] }}
                        </span>
                        <span class="text-xl text-gray-400">-</span>
                        <span class="text-2xl font-bold {{ $match->sets[count($match->sets)-1]['away_score'] > $match->sets[count($match->sets)-1]['home_score'] ? 'text-green-400' : 'text-white' }}">
                            {{ $match->sets[count($match->sets)-1]['away_score'] }}
                        </span>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="text-lg font-bold {{ $match->away_player_id == $player->id ? ($match->sets[count($match->sets)-1]['away_score'] > $match->sets[count($match->sets)-1]['home_score'] ? 'text-green-400' : 'text-red-400') : 'text-white' }}">
                            {{ $match->away_player_name }}
                        </div>
                    </div>
                </div>
                <div class="mt-2 text-center text-sm text-gray-400">
                    Setovi: {{ collect($match->sets)->map(fn($s) => $s['home_score'].'-'.$s['away_score'])->implode(' | ') }}
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>