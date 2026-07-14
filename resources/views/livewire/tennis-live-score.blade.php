@php
    $pointLabel = function ($mine, $theirs) {
        if ($mine >= 3 && $theirs >= 3) {
            $diff = $mine - $theirs;
            if ($diff === 0) return '40';
            if ($diff === 1) return 'Ad';
            return '40'; // shouldn't be reached - game ends at diff 2
        }
        return [0 => '0', 1 => '15', 2 => '30', 3 => '40'][$mine] ?? '40';
    };
@endphp

<div class="space-y-6">
    @if($needsServerSelection)
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-8 border border-gray-700/50 text-center">
            <h3 class="text-xl font-bold text-white mb-6">Ko servira prvi?</h3>
            <div class="flex items-center justify-center gap-4">
                <button wire:click="selectFirstServer('home')" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-colors">
                    {{ $match->homePlayer->name ?? 'Domaći' }}
                </button>
                <span class="text-gray-500">ili</span>
                <button wire:click="selectFirstServer('away')" class="px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold transition-colors">
                    {{ $match->awayPlayer->name ?? 'Gost' }}
                </button>
            </div>
        </div>
    @else
        <!-- Sets won -->
        <div class="flex items-center justify-center gap-8 text-gray-400">
            <div class="text-center">
                <div class="text-4xl font-black text-white">{{ $homeSets }}</div>
                <div class="text-xs uppercase tracking-wider mt-1">Setova</div>
            </div>
            <div class="text-2xl font-bold text-gray-600">:</div>
            <div class="text-center">
                <div class="text-4xl font-black text-white">{{ $awaySets }}</div>
                <div class="text-xs uppercase tracking-wider mt-1">Setova</div>
            </div>
        </div>

        @if(count($completedSets) > 0)
            <div class="flex items-center justify-center gap-3 text-sm text-gray-400">
                @foreach($completedSets as $i => $set)
                    <span class="px-2 py-1 bg-gray-800/50 rounded-lg border border-gray-700/50">{{ $set['home'] }}-{{ $set['away'] }}</span>
                @endforeach
            </div>
        @endif

        @if($matchComplete)
            <div class="bg-green-500/10 border border-green-500/30 rounded-2xl p-6 text-center">
                <p class="text-green-400 font-bold text-lg">Meč završen</p>
                <p class="text-gray-400 text-sm mt-1">
                    {{ $homeSets > $awaySets ? ($match->homePlayer->name ?? 'Domaći') : ($match->awayPlayer->name ?? 'Gost') }} pobjeđuje
                </p>
                <button wire:click="resetMatch" wire:confirm="Sigurno želite resetovati meč?" class="mt-4 text-xs text-gray-500 hover:text-red-400 transition-colors">
                    Resetuj meč
                </button>
            </div>
        @else
            <!-- Current game / tiebreak -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                <div class="text-center mb-4">
                    <span class="text-xs uppercase tracking-widest text-gray-500">
                        {{ $inTiebreak ? 'Tie-break' : 'Trenutni gem' }} · Set {{ count($completedSets) + 1 }} · {{ $homeGames }}-{{ $awayGames }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Home -->
                    <button wire:click="addPoint('home')" class="group relative bg-blue-600/10 hover:bg-blue-600/20 border-2 border-blue-500/30 hover:border-blue-500/60 rounded-2xl p-8 transition-all">
                        @if($currentServer === 'home')
                            <span class="absolute top-3 right-3 w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                        @endif
                        <div class="text-sm text-gray-400 mb-2 truncate">{{ $match->homePlayer->name ?? 'Domaći' }}</div>
                        <div class="text-5xl font-black text-white">
                            {{ $inTiebreak ? $tiebreakHome : $pointLabel($homePoints, $awayPoints) }}
                        </div>
                    </button>

                    <!-- Away -->
                    <button wire:click="addPoint('away')" class="group relative bg-purple-600/10 hover:bg-purple-600/20 border-2 border-purple-500/30 hover:border-purple-500/60 rounded-2xl p-8 transition-all">
                        @if($currentServer === 'away')
                            <span class="absolute top-3 right-3 w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                        @endif
                        <div class="text-sm text-gray-400 mb-2 truncate">{{ $match->awayPlayer->name ?? 'Gost' }}</div>
                        <div class="text-5xl font-black text-white">
                            {{ $inTiebreak ? $tiebreakAway : $pointLabel($awayPoints, $homePoints) }}
                        </div>
                    </button>
                </div>

                <p class="text-center text-gray-500 text-xs mt-4">Tapni na igrača da mu dodaš poen</p>
            </div>

            <div class="text-center">
                <button wire:click="resetMatch" wire:confirm="Sigurno želite resetovati meč od početka?" class="text-xs text-gray-600 hover:text-red-400 transition-colors">
                    Resetuj meč
                </button>
            </div>
        @endif
    @endif
</div>
