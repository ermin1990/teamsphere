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
    $canManageLiveScore = $this->canManageLiveScore();
@endphp

<div>
    @if($needsServerSelection)
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 sm:p-8 border border-gray-700/50 text-center">
            <p class="text-sm text-gray-400 mb-5 max-w-md mx-auto">
                Ovo je stranica na kojoj uživo pratiš meč i upisuješ poene, isto kao sudija na terenu.
                Nakon što izabereš ko servira prvi, dovoljno je da klikneš na igrača svaki put kad osvoji poen -
                gemovi, setovi i konačan rezultat se automatski računaju i čuvaju.
            </p>

            @if($canManageLiveScore)
            <div class="max-w-sm mx-auto text-left mb-6 space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Datum i vrijeme</label>
                    <input type="datetime-local" wire:model="playedAt"
                           class="w-full px-3 py-2 rounded-lg text-sm bg-gray-900/50 border border-gray-700 text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Teren</label>
                    @if(count($venues) > 0)
                        <select wire:model="venueId" class="w-full px-3 py-2 rounded-lg text-sm bg-gray-900/50 border border-gray-700 text-white focus:outline-none focus:border-blue-500">
                            <option value="">— nije odabran —</option>
                            @foreach($venues as $venue)
                                <option value="{{ $venue['id'] }}">{{ $venue['name'] }}</option>
                            @endforeach
                        </select>
                    @else
                        <p class="text-xs text-gray-500">Nema unesenih terena za grad ove lige.</p>
                    @endif
                </div>
            </div>
            @endif

            <h3 class="text-xl font-bold text-white mb-6">Ko servira prvi?</h3>
            @if($canManageLiveScore)
            <div class="flex items-center justify-center gap-4">
                <button wire:click="selectFirstServer('home')" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-colors">
                    {{ $match->homePlayer->name ?? 'Domaći' }}
                </button>
                <span class="text-gray-500">ili</span>
                <button wire:click="selectFirstServer('away')" class="px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold transition-colors">
                    {{ $match->awayPlayer->name ?? 'Gost' }}
                </button>
            </div>
            @else
            <p class="text-gray-400">⚠️ Nemaš dozvolu za upravljanje ovim mečom.</p>
            @endif
        </div>
    @else
        @if($matchComplete)
            <div class="bg-green-500/10 border border-green-500/30 rounded-2xl p-6 text-center">
                <p class="text-green-400 font-bold text-lg">Meč završen</p>
                <p class="text-gray-400 text-sm mt-1">
                    {{ $homeSets > $awaySets ? ($match->homePlayer->name ?? 'Domaći') : ($match->awayPlayer->name ?? 'Gost') }} pobjeđuje
                </p>
                @if($canManageLiveScore)
                <button wire:click="resetMatch" wire:confirm="Sigurno želite resetovati meč?" class="mt-4 text-xs text-gray-500 hover:text-red-400 transition-colors">
                    Resetuj meč
                </button>
                @endif
            </div>
        @else
            <!-- Players stacked, each a full-width tap zone -->
            <div class="flex flex-col gap-2 sm:gap-3">
                <button wire:click="addPoint('home')" @disabled(!$canManageLiveScore) class="group relative flex items-center justify-between px-4 sm:px-8 py-7 sm:py-9 bg-blue-600/10 hover:bg-blue-600/20 active:bg-blue-600/30 border-2 border-blue-500/30 hover:border-blue-500/60 rounded-2xl transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                    @if($currentServer === 'home')
                        <span class="absolute top-3 right-3 w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                    @endif
                    <span class="min-w-0 flex-1 pr-3 text-left text-2xl sm:text-3xl text-gray-200 font-bold break-words leading-tight">
                        {{ $match->homePlayer->name ?? 'Domaći' }}
                        <span class="text-gray-400 font-black">({{ $homeSets }})</span>
                    </span>
                    <span class="shrink-0 text-6xl sm:text-7xl font-black text-white leading-none">
                        {{ $inTiebreak ? $tiebreakHome : $pointLabel($homePoints, $awayPoints) }}
                    </span>
                </button>

                <button wire:click="addPoint('away')" @disabled(!$canManageLiveScore) class="group relative flex items-center justify-between px-4 sm:px-8 py-7 sm:py-9 bg-purple-600/10 hover:bg-purple-600/20 active:bg-purple-600/30 border-2 border-purple-500/30 hover:border-purple-500/60 rounded-2xl transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                    @if($currentServer === 'away')
                        <span class="absolute top-3 right-3 w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                    @endif
                    <span class="min-w-0 flex-1 pr-3 text-left text-2xl sm:text-3xl text-gray-200 font-bold break-words leading-tight">
                        {{ $match->awayPlayer->name ?? 'Gost' }}
                        <span class="text-gray-400 font-black">({{ $awaySets }})</span>
                    </span>
                    <span class="shrink-0 text-6xl sm:text-7xl font-black text-white leading-none">
                        {{ $inTiebreak ? $tiebreakAway : $pointLabel($awayPoints, $homePoints) }}
                    </span>
                </button>
            </div>

            @if($canManageLiveScore)
            <div class="flex justify-center mt-2 sm:mt-3">
                <button type="button" wire:click="undoPoint"
                        wire:loading.attr="disabled"
                        @if(empty($pointHistory)) disabled @endif
                        class="px-4 py-2 rounded-xl text-xs font-semibold bg-yellow-500 hover:bg-yellow-600 text-white transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    ↩️ Poništi poen
                </button>
            </div>
            @endif

            @if(!$canManageLiveScore)
                <p class="text-center text-xs text-yellow-400 mt-2">⚠️ Nemaš dozvolu za mijenjanje rezultata</p>
            @endif

            <!-- Sets - completed ones with winner's name/score in green, plus the
                 set currently in progress (updates live after every game) -->
            <div class="rounded-2xl px-4 py-3 bg-gray-800/50 border border-gray-700/50 mt-3 text-sm space-y-1.5">
                @foreach($completedSets as $index => $set)
                    @php $homeWonSet = $set['home'] > $set['away']; @endphp
                    <div class="text-center text-xs text-gray-500 uppercase tracking-wider {{ $index > 0 ? 'pt-1.5 border-t border-gray-700/50' : '' }}">Set {{ $index + 1 }}</div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="truncate {{ $homeWonSet ? 'text-green-400 font-bold' : 'text-gray-400' }}">{{ $match->homePlayer->name ?? 'Domaći' }}</span>
                        <span class="shrink-0 font-semibold whitespace-nowrap">
                            <span class="{{ $homeWonSet ? 'text-green-400' : 'text-gray-300' }}">{{ $set['home'] }}</span>
                            <span class="text-gray-600"> - </span>
                            <span class="{{ !$homeWonSet ? 'text-green-400' : 'text-gray-300' }}">{{ $set['away'] }}</span>
                        </span>
                        <span class="truncate text-right {{ !$homeWonSet ? 'text-green-400 font-bold' : 'text-gray-400' }}">{{ $match->awayPlayer->name ?? 'Gost' }}</span>
                    </div>
                @endforeach

                <div class="text-center text-xs text-blue-400 uppercase tracking-wider {{ count($completedSets) > 0 ? 'pt-1.5 border-t border-gray-700/50' : '' }}">
                    Set {{ count($completedSets) + 1 }} (u toku){{ $inTiebreak ? ' · tie-break' : '' }}
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span class="truncate text-gray-300">{{ $match->homePlayer->name ?? 'Domaći' }}</span>
                    <span class="shrink-0 font-semibold whitespace-nowrap text-white">
                        {{ $inTiebreak ? $tiebreakHome : $homeGames }} - {{ $inTiebreak ? $tiebreakAway : $awayGames }}
                    </span>
                    <span class="truncate text-right text-gray-300">{{ $match->awayPlayer->name ?? 'Gost' }}</span>
                </div>
            </div>

            @if($canManageLiveScore)
            <div class="text-center mt-3 flex items-center justify-center gap-4">
                @if($match->competition?->is_recreational)
                    <button wire:click="finishMatchNow" wire:confirm="Završi meč sa trenutnim rezultatom? Ovo se ne može poništiti." class="text-xs font-semibold text-purple-400 hover:text-purple-300 transition-colors">
                        🏁 Završi meč sada
                    </button>
                @endif
                <button wire:click="resetMatch" wire:confirm="Sigurno želite resetovati meč od početka?" class="text-xs text-gray-600 hover:text-red-400 transition-colors">
                    Resetuj meč
                </button>
            </div>
            @endif
        @endif
    @endif
</div>
