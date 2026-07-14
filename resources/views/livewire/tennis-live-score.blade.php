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

<div>
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
        <!-- Compact status bar: sets, set history, current game indicator -->
        <div class="flex items-center justify-between gap-3 bg-gray-800/50 rounded-2xl px-4 py-2.5 border border-gray-700/50 mb-3 text-sm">
            <div class="flex items-center gap-2">
                <span class="text-2xl font-black text-white">{{ $homeSets }}</span>
                <span class="text-gray-600">:</span>
                <span class="text-2xl font-black text-white">{{ $awaySets }}</span>
                <span class="text-gray-500 text-xs uppercase tracking-wider ml-1">setova</span>
            </div>

            @if(count($completedSets) > 0)
                <div class="flex items-center gap-1.5 overflow-x-auto">
                    @foreach($completedSets as $set)
                        <span class="px-1.5 py-0.5 bg-gray-900/50 rounded text-xs text-gray-400 whitespace-nowrap">{{ $set['home'] }}-{{ $set['away'] }}</span>
                    @endforeach
                </div>
            @endif

            @unless($matchComplete)
                <span class="text-gray-500 text-xs whitespace-nowrap">
                    {{ $inTiebreak ? 'Tie-break' : 'Set ' . (count($completedSets) + 1) }} · {{ $homeGames }}-{{ $awayGames }}
                </span>
            @endunless
        </div>

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
            <!-- Players stacked, each a full-width tap zone -->
            <div class="flex flex-col gap-3" style="height: calc(100dvh - 220px); min-height: 420px;">
                <button wire:click="addPoint('home')" class="group relative flex-1 flex items-center justify-between px-6 sm:px-10 bg-blue-600/10 hover:bg-blue-600/20 active:bg-blue-600/30 border-2 border-blue-500/30 hover:border-blue-500/60 rounded-2xl transition-all">
                    @if($currentServer === 'home')
                        <span class="absolute top-4 right-4 w-3 h-3 rounded-full bg-yellow-400"></span>
                    @endif
                    <span class="text-lg sm:text-xl text-gray-300 font-medium truncate">{{ $match->homePlayer->name ?? 'Domaći' }}</span>
                    <span class="text-6xl sm:text-7xl font-black text-white">
                        {{ $inTiebreak ? $tiebreakHome : $pointLabel($homePoints, $awayPoints) }}
                    </span>
                </button>

                <button wire:click="addPoint('away')" class="group relative flex-1 flex items-center justify-between px-6 sm:px-10 bg-purple-600/10 hover:bg-purple-600/20 active:bg-purple-600/30 border-2 border-purple-500/30 hover:border-purple-500/60 rounded-2xl transition-all">
                    @if($currentServer === 'away')
                        <span class="absolute top-4 right-4 w-3 h-3 rounded-full bg-yellow-400"></span>
                    @endif
                    <span class="text-lg sm:text-xl text-gray-300 font-medium truncate">{{ $match->awayPlayer->name ?? 'Gost' }}</span>
                    <span class="text-6xl sm:text-7xl font-black text-white">
                        {{ $inTiebreak ? $tiebreakAway : $pointLabel($awayPoints, $homePoints) }}
                    </span>
                </button>
            </div>

            <div class="text-center mt-3">
                <button wire:click="resetMatch" wire:confirm="Sigurno želite resetovati meč od početka?" class="text-xs text-gray-600 hover:text-red-400 transition-colors">
                    Resetuj meč
                </button>
            </div>
        @endif
    @endif
</div>
