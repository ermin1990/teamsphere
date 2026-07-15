@php
    // Pass setStartTime as JS variable for timer
    $setStartTime = $setStartTime ?? ($match->current_set_started_at ?? $match->played_at);

    // Determine if this is a league or competition match and get the parent
    $parent = $match->league ?? $match->competition;
@endphp

<div>
        <div class="rounded-none sm:rounded-2xl p-2 sm:p-6 md:p-8 backdrop-blur-xl border-0 sm:border" style="background: var(--bg-card); border-color: var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">

            @if(!$firstServer)
        <div class="rounded-xl p-4 sm:p-6" style="background: var(--bg-hover); border: 1px solid var(--border-secondary);">
            <p class="text-sm mb-5 max-w-md mx-auto text-center" style="color: var(--text-tertiary);">
                Ovo je stranica na kojoj uživo pratiš meč i upisuješ poene, isto kao sudija na terenu.
                Nakon što izabereš ko servira prvi, dovoljno je da klikneš na igrača svaki put kad osvoji poen -
                setovi i konačan rezultat se automatski računaju i čuvaju.
            </p>

            @if($canManageLiveScore)
            <div class="max-w-sm mx-auto text-left mb-6 space-y-3">
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--text-tertiary);">Datum i vrijeme</label>
                    <input type="datetime-local" wire:model="playedAt"
                           class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none"
                           style="background: var(--bg-tertiary); border: 1px solid var(--border-primary); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--text-tertiary);">Teren</label>
                    @if(count($venues) > 0)
                        <select wire:model="venueId" class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none" style="background: var(--bg-tertiary); border: 1px solid var(--border-primary); color: var(--text-primary);">
                            <option value="">— nije odabran —</option>
                            @foreach($venues as $venue)
                                <option value="{{ $venue['id'] }}">{{ $venue['name'] }}</option>
                            @endforeach
                        </select>
                    @else
                        <p class="text-xs" style="color: var(--text-muted);">Nema unesenih terena za grad ove lige.</p>
                    @endif
                </div>
            </div>
            @endif

            <h3 class="text-lg sm:text-xl font-bold mb-4 text-center" style="color: var(--text-primary);">Ko servira prvi?</h3>
            @if($canManageLiveScore)
            <div class="space-y-3 max-w-md mx-auto">
                <button wire:click="selectHomeServer" class="w-full p-4 bg-blue-600 hover:bg-blue-700 rounded-xl text-white font-bold break-words">
                    @if($parent->is_team_based)
                        {{ $match->homeTeam->name }}
                    @else
                        {{ $match->homePlayer->name }}
                    @endif
                </button>

                <button wire:click="selectAwayServer" class="w-full p-4 bg-red-600 hover:bg-red-700 rounded-xl text-white font-bold break-words">
                    @if($parent->is_team_based)
                        {{ $match->awayTeam->name }}
                    @else
                        {{ $match->awayPlayer->name }}
                    @endif
                </button>

                <button wire:click="selectRandomServer" class="w-full p-4 bg-purple-600 hover:bg-purple-700 rounded-xl text-white font-bold">
                    🎲 Nasumično
                </button>
            </div>
            @else
            <p class="text-center" style="color: var(--text-tertiary);">⚠️ Nemaš dozvolu za upravljanje ovim mečom.</p>
            @endif
        </div>
        @endif

        @if($firstServer)
        <div class="flex justify-center mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-8 rounded-xl px-4 py-3" style="background: var(--bg-hover); border: 1px solid var(--border-secondary);">
                <div class="text-center">
                    <div class="text-xs sm:text-sm" style="color: var(--text-tertiary);">Set</div>
                    <div class="text-xl sm:text-2xl font-bold" style="color: var(--text-primary);">{{ $currentSet }}</div>
                </div>
                <div class="w-px h-8 hidden sm:block" style="background: var(--border-primary);"></div>
                <div class="text-center">
                    <div class="text-xs sm:text-sm" style="color: var(--text-tertiary);">Za pobjedu</div>
                    <div class="text-xl sm:text-2xl font-bold" style="color: #4ade80;">11</div>
                </div>
                @if($canManageLiveScore)
                <div class="w-px h-8 hidden sm:block" style="background: var(--border-primary);"></div>
                <div>
                    <button type="button" wire:click="resetServerSelection"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all whitespace-nowrap" style="background: rgba(249,115,22,0.15); border: 1px solid rgba(249,115,22,0.3); color: #fb923c;">
                        🔄 Promijeni servis
                    </button>
                </div>
                @endif
            </div>
        </div>

        @php
            $homeTappable = $match->status !== 'completed' && $canManageLiveScore;
            $awayTappable = $homeTappable;
            $homeName = $parent->is_team_based ? ($match->homeTeam->name ?? 'Domaći tim') : ($match->homePlayer->name ?? 'Domaći igrač');
            $awayName = $parent->is_team_based ? ($match->awayTeam->name ?? 'Gostujući tim') : ($match->awayPlayer->name ?? 'Gostujući igrač');
        @endphp

        <div class="flex flex-col gap-2 sm:gap-4 mb-6 sm:mb-8">
                {{-- Home / top block --}}
                <div class="relative rounded-xl sm:rounded-2xl overflow-hidden" style="background: {{ $currentServer === 'home' ? 'rgba(96,165,250,0.12)' : 'var(--bg-hover)' }}; border: 1px solid {{ $currentServer === 'home' ? 'rgba(96,165,250,0.4)' : 'var(--border-secondary)' }};">
                    <div class="flex items-center justify-between gap-2 px-3 sm:px-5 pt-3">
                        <div class="flex items-center gap-2 min-w-0">
                            @if($currentServer === 'home')
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-yellow-400 rounded-full animate-pulse shadow-lg shadow-yellow-400/50 shrink-0">
                                <div class="w-full h-full bg-yellow-300 rounded-full animate-ping"></div>
                            </div>
                            @endif
                            <span class="font-bold text-sm sm:text-lg md:text-xl break-words {{ $currentServer === 'home' ? 'text-blue-400' : '' }}" style="{{ $currentServer === 'home' ? '' : 'color: var(--text-primary);' }}">
                                {{ $homeName }}
                            </span>
                        </div>
                        <button type="button" @if($homeTappable) wire:click="subtractPoint('home')" @endif
                                class="shrink-0 px-2.5 py-1 rounded-lg font-semibold text-xs {{ !$homeTappable ? 'opacity-50 cursor-not-allowed' : '' }}"
                                style="background: var(--bg-tertiary); color: var(--text-primary);">
                            -1
                        </button>
                    </div>
                    @if($parent->is_team_based && $match->homeTeam)
                        <div class="text-xs sm:text-sm px-3 sm:px-5" style="color: var(--text-tertiary);">
                            @foreach($match->homeTeam->players as $player)
                                <div>{{ $player->name }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="w-full py-6 sm:py-10 text-center select-none {{ $homeTappable ? 'cursor-pointer active:scale-[0.99]' : '' }} transition-transform duration-150"
                         @if($homeTappable) wire:click="addPoint('home')" @endif>
                        <span class="text-7xl sm:text-8xl md:text-9xl font-bold {{ $currentServer === 'home' ? 'text-blue-400 drop-shadow-lg' : 'text-blue-400' }}">
                            {{ $homeScore }}
                        </span>
                        @if(!$canManageLiveScore)
                            <div class="text-xs text-yellow-400 mt-1">⚠️ Nemaš dozvolu</div>
                        @endif
                    </div>
                </div>

                {{-- Away / bottom block --}}
                <div class="relative rounded-xl sm:rounded-2xl overflow-hidden" style="background: {{ $currentServer === 'away' ? 'rgba(248,113,113,0.12)' : 'var(--bg-hover)' }}; border: 1px solid {{ $currentServer === 'away' ? 'rgba(248,113,113,0.4)' : 'var(--border-secondary)' }};">
                    <div class="flex items-center justify-between gap-2 px-3 sm:px-5 pt-3">
                        <div class="flex items-center gap-2 min-w-0">
                            @if($currentServer === 'away')
                            <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-yellow-400 rounded-full animate-pulse shadow-lg shadow-yellow-400/50 shrink-0">
                                <div class="w-full h-full bg-yellow-300 rounded-full animate-ping"></div>
                            </div>
                            @endif
                            <span class="font-bold text-sm sm:text-lg md:text-xl break-words {{ $currentServer === 'away' ? 'text-red-400' : '' }}" style="{{ $currentServer === 'away' ? '' : 'color: var(--text-primary);' }}">
                                {{ $awayName }}
                            </span>
                        </div>
                        <button type="button" @if($awayTappable) wire:click="subtractPoint('away')" @endif
                                class="shrink-0 px-2.5 py-1 rounded-lg font-semibold text-xs {{ !$awayTappable ? 'opacity-50 cursor-not-allowed' : '' }}"
                                style="background: var(--bg-tertiary); color: var(--text-primary);">
                            -1
                        </button>
                    </div>
                    @if($parent->is_team_based && $match->awayTeam)
                        <div class="text-xs sm:text-sm px-3 sm:px-5" style="color: var(--text-tertiary);">
                            @foreach($match->awayTeam->players as $player)
                                <div>{{ $player->name }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="w-full py-6 sm:py-10 text-center select-none {{ $awayTappable ? 'cursor-pointer active:scale-[0.99]' : '' }} transition-transform duration-150"
                         @if($awayTappable) wire:click="addPoint('away')" @endif>
                        <span class="text-7xl sm:text-8xl md:text-9xl font-bold {{ $currentServer === 'away' ? 'text-red-400 drop-shadow-lg' : 'text-red-400' }}">
                            {{ $awayScore }}
                        </span>
                        @if(!$canManageLiveScore)
                            <div class="text-xs text-yellow-400 mt-1">⚠️ Nemaš dozvolu</div>
                        @endif
                    </div>
                </div>
        </div>

        <div class="pt-5 sm:pt-6" style="border-top: 1px solid var(--border-secondary);" wire:key="sets-{{ $setsVersion }}">
            <h4 class="text-sm sm:text-base font-semibold mb-3 sm:mb-4 text-center" style="color: var(--text-primary);">Odigrani setovi</h4>
            <div class="flex flex-wrap justify-center gap-2 sm:gap-3">
                @forelse($sets as $index => $set)
                <div class="text-center px-3 py-2 sm:p-3 rounded-lg" style="background: var(--bg-tertiary); border: 1px solid var(--border-secondary);" wire:key="set-{{ $index }}-{{ $setsVersion }}">
                    <div class="text-xs mb-1" style="color: var(--text-tertiary);">Set {{ $index + 1 }}</div>
                    <div class="text-base sm:text-lg font-bold" style="color: var(--text-primary);">{{ $set['home_score'] ?? $set['home'] ?? 0 }} - {{ $set['away_score'] ?? $set['away'] ?? 0 }}</div>
                    <div class="text-xs" style="color: var(--text-tertiary);">
                        @if(is_numeric($setDurations[$index] ?? null))
                            @php
                                $duration = $setDurations[$index];
                                $minutes = floor($duration / 60);
                                $seconds = $duration % 60;
                                echo sprintf('%02d:%02d', $minutes, $seconds);
                            @endphp
                        @else
                            {{ $setDurations[$index] ?? '00:00' }}
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm" style="color: var(--text-muted);">Još nema odigranih setova.</p>
                @endforelse
            </div>
        </div>

        <div class="pt-5 sm:pt-6 mt-5 sm:mt-6" style="border-top: 1px solid var(--border-secondary);">
                <div class="flex flex-wrap justify-center gap-3">
                    @if($match->status !== 'completed' && $canManageLiveScore)
                    <button type="button" wire:click="undoPoint"
                            class="w-full sm:w-auto px-5 sm:px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl transition-colors font-semibold disabled:opacity-40 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                            @if(empty($pointHistory)) disabled @endif>
                        ↩️ Poništi poen
                    </button>
                    @if($this->canEndMatch())
                    <button type="button" wire:click="endMatch"
                            class="w-full sm:w-auto px-5 sm:px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-colors font-semibold">
                        🏁 Završi meč
                    </button>
                    @endif
                    @if(($match->league?->is_recreational ?? $match->competition?->is_recreational ?? false))
                    <button type="button" wire:click="forceFinishMatch"
                            wire:confirm="Završi meč sa trenutnim rezultatom? Ovo se ne može poništiti."
                            class="w-full sm:w-auto px-5 sm:px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition-colors font-semibold">
                        🏁 Završi meč sada
                    </button>
                    @endif
                    @elseif($match->status === 'completed')
                    <div class="text-center font-semibold" style="color: #4ade80;">
                        ✅ Meč je završen
                    </div>
                    @else
                    <div class="text-center font-semibold" style="color: var(--text-tertiary);">
                        👁️ Samo za pregled
                    </div>
                    @endif
                </div>
        </div>
        @endif
    </div>

    <div x-data="{ showModal: false, winner: '', homeSets: 0, awaySets: 0, setsToWin: 0, finalSets: [] }"
         x-show="showModal"
         x-on:match-won.window="showModal = true; winner = $event.detail.winner; homeSets = $event.detail.homeSets; awaySets = $event.detail.awaySets; setsToWin = $event.detail.setsToWin; finalSets = $event.detail.finalSets"
         x-on:match-not-finished.window="alert($event.detail.message)"
         x-on:keydown.escape.window="showModal = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="fixed inset-0 transition-opacity" x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>

            <div class="inline-block align-middle rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-md sm:w-full mx-4 lg:max-w-lg lg:mx-auto" style="background: var(--bg-card-solid); border: 1px solid var(--border-primary);" x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-500 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium" style="color: var(--text-primary);">
                                Meč završen!
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3" style="background: var(--bg-tertiary);">
                    <button type="button" wire:click="confirmMatchEnd" x-on:click="showModal = false"
                            class="w-full inline-flex justify-center rounded-xl shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:w-auto sm:text-sm">
                        Završi meč i sačuvaj rezultate
                    </button>
                    <button type="button" x-on:click="showModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-xl shadow-sm px-4 py-2 text-base font-medium sm:mt-0 sm:w-auto sm:text-sm"
                            style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                        Nastavi igrati
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Debug: Check if Livewire is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Livewire initialization check
    });
    
    // Listen for Livewire errors
    document.addEventListener('livewire:load', function() {
        Livewire.hook('message.failed', (message, component) => {
            console.error('Livewire message failed:', message, component);
        });
    });
    
    // Timer functionality removed - no longer needed
    
    // Listen for clear-local-storage event
    if (window.Livewire) {
        Livewire.on('clear-local-storage', (data) => {
            // Clear all localStorage data when match is reset
            localStorage.clear();
        });

        Livewire.on('start-timers', (data) => {
            // Timer functionality can be added here if needed
        });
    }
</script>
@endpush