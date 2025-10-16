@php
    // Pass setStartTime as JS variable for timer
    $setStartTime = $setStartTime ?? ($match->current_set_started_at ?? $match->played_at);
@endphp

<div>
        <div class="contents">
            
            <!-- Debug Info -->
            <div class="bg-red-900/50 p-4 mb-4 rounded-lg text-sm">
                <strong>DEBUG:</strong> firstServer={{ $firstServer ? 'true' : 'false' }} | 
                match_status={{ $match->status }} | 
                is_owner={{ $isOrganizationOwner ? 'true' : 'false' }}
            </div>
            
            @if(!$firstServer)
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
            <h3 class="text-xl font-bold mb-6 text-center">Match Setup</h3>
            <div class="max-w-md mx-auto">
                <label class="block text-sm font-medium text-gray-300 mb-3">Who serves first?</label>
                <div class="space-y-3">
                    <button type="button" 
                            wire:click="selectFirstServer('home')" 
                            wire:loading.attr="disabled"
                            onclick="console.log('Home button clicked'); window.Livewire.find('{{ $_instance->id }}').call('selectFirstServer', 'home')"
                            class="w-full p-4 bg-blue-600/20 hover:bg-blue-600/30 border-2 border-blue-500/30 hover:border-blue-500/50 rounded-lg transition-all disabled:opacity-50">
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-400 mb-1">
                                @if($match->league->is_team_based)
                                    {{ $match->homeTeam->name ?? 'Home Team' }}
                                @else
                                    {{ $match->homePlayer->name ?? 'Home Player' }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-400">Serves First</div>
                            <div class="text-xs text-blue-300 mt-1">wire:click="selectFirstServer('home')"</div>
                        </div>
                    </button>
                    <button type="button" 
                            wire:click="selectFirstServer('away')" 
                            wire:loading.attr="disabled"
                            onclick="console.log('Away button clicked'); window.Livewire.find('{{ $_instance->id }}').call('selectFirstServer', 'away')"
                            class="w-full p-4 bg-red-600/20 hover:bg-red-600/30 border-2 border-red-500/30 hover:border-red-500/50 rounded-lg transition-all disabled:opacity-50">
                        <div class="text-center">
                            <div class="text-lg font-bold text-red-400 mb-1">
                                @if($match->league->is_team_based)
                                    {{ $match->awayTeam->name ?? 'Away Team' }}
                                @else
                                    {{ $match->awayPlayer->name ?? 'Away Player' }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-400">Serves First</div>
                            <div class="text-xs text-red-300 mt-1">wire:click="selectFirstServer('away')"</div>
                        </div>
                    </button>
                                        <button type="button" 
                            wire:click="selectFirstServer('random')" 
                            wire:loading.attr="disabled"
                            onclick="console.log('Random button clicked'); window.Livewire.find('{{ $_instance->id }}').call('selectFirstServer', 'random')"
                            class="w-full p-4 bg-purple-600/20 hover:bg-purple-600/30 border-2 border-purple-500/30 hover:border-purple-500/50 rounded-lg transition-all disabled:opacity-50">
                        <div class="text-center">
                            <div class="text-lg font-bold text-purple-400 mb-1">🎲 Random</div>
                            <div class="text-sm text-gray-400">Random Selection</div>
                            <div class="text-xs text-purple-300 mt-1">wire:click="selectFirstServer('random')"</div>
                        </div>
                    </button>
                </div>
                <div class="mt-6 text-center text-gray-400 text-sm">
                    Select who serves first to start the match
                </div>
            </div>
        </div>
        @endif
        
        @if($firstServer)
        <div class="text-center mb-8">
            <div class="inline-flex items-center space-x-8 bg-gray-700/30 rounded-lg p-4">
                <div>
                    <div class="text-sm text-gray-400">Current Set</div>
                    <div class="text-2xl font-bold text-white">{{ $currentSet }}</div>
                </div>
                <div class="w-px h-8 bg-gray-600"></div>
                <div>
                    <div class="text-sm text-gray-400">To Win</div>
                    <div class="text-2xl font-bold text-green-400">11</div>
                </div>
                <div class="w-px h-8 bg-gray-600"></div>
                <div>
                    <button type="button" wire:click="resetServerSelection"
                            class="px-3 py-1 bg-orange-600/20 hover:bg-orange-600/30 border border-orange-500/30 hover:border-orange-500/50 rounded text-orange-400 text-sm font-medium transition-all">
                        🔄 Change Server
                    </button>
                    <div class="text-xs text-orange-300 mt-1">wire:click="resetServerSelection"</div>
                </div>
            </div>
        </div>
        @endif
        @if($firstServer)
        <div class="contents">
            <div class="grid grid-cols-2 gap-4 md:gap-8 mb-8">
                <div class="text-center">
                    <div class="mb-4">
                        <h3 class="text-2xl font-bold transition-all duration-300 mb-2 flex items-center justify-center gap-2">
                            @if($currentServer === 'home')
                            <div class="w-4 h-4 bg-yellow-400 rounded-full animate-pulse shadow-lg shadow-yellow-400/50">
                                <div class="w-full h-full bg-yellow-300 rounded-full animate-ping"></div>
                            </div>
                            @endif
                            <span class="{{ $currentServer === 'home' ? 'text-blue-400 animate-pulse drop-shadow-lg' : 'text-white' }}">
                                @if($match->league->is_team_based)
                                    {{ $match->homeTeam->name ?? 'Home Team' }}
                                @else
                                    {{ $match->homePlayer->name ?? 'Home Player' }}
                                @endif
                            </span>
                        </h3>
                        @if($match->league->is_team_based && $match->homeTeam)
                            <div class="text-sm text-gray-400 mb-4">
                                @foreach($match->homeTeam->players as $player)
                                    <div>{{ $player->name }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="relative mb-6">
                        <div class="text-8xl md:text-9xl font-bold transition-all duration-300 mb-2 {{ $match->status === 'completed' ? '' : 'cursor-pointer hover:text-blue-300 active:scale-95 select-none' }} {{ $currentServer === 'home' ? 'text-blue-400 animate-pulse drop-shadow-lg' : 'text-blue-400' }}"
                             @if($match->status !== 'completed') wire:click="addPoint('home')" @endif
                             style="position: relative; z-index: 10;">
                            {{ $homeScore }}
                        </div>
                        @if(!$isOrganizationOwner)
                            <div class="text-xs text-yellow-400 mt-1">⚠️ Samo vlasnik organizacije može mijenjati rezultat</div>
                        @endif
                        <div class="text-xs text-gray-500 mt-1">Status: {{ $match->status }} | Owner: {{ $isOrganizationOwner ? 'Yes' : 'No' }}</div>
                        <div class="text-xs text-blue-300 mt-1">wire:click="addPoint('home')"</div>
                    </div>

                    <div class="flex justify-center">
                        <button type="button" @if($match->status !== 'completed' && $isOrganizationOwner) wire:click="subtractPoint('home')" @endif
                                class="px-4 py-2 bg-gray-600 {{ $match->status === 'completed' || !$isOrganizationOwner ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-700' }} text-white rounded-lg transition-all duration-200 font-semibold text-sm">
                            -1
                        </button>
                    </div>
                </div>

                <div class="text-center">
                    <div class="mb-4">
                        <h3 class="text-2xl font-bold transition-all duration-300 mb-2 flex items-center justify-center gap-2">
                            <span class="{{ $currentServer === 'away' ? 'text-red-400 animate-pulse drop-shadow-lg' : 'text-white' }}">
                                @if($match->league->is_team_based)
                                    {{ $match->awayTeam->name ?? 'Away Team' }}
                                @else
                                    {{ $match->awayPlayer->name ?? 'Away Player' }}
                                @endif
                            </span>
                            @if($currentServer === 'away')
                            <div class="w-4 h-4 bg-yellow-400 rounded-full animate-pulse shadow-lg shadow-yellow-400/50">
                                <div class="w-full h-full bg-yellow-300 rounded-full animate-ping"></div>
                            </div>
                            @endif
                        </h3>
                        @if($match->league->is_team_based && $match->awayTeam)
                            <div class="text-sm text-gray-400 mb-4">
                                @foreach($match->awayTeam->players as $player)
                                    <div>{{ $player->name }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="relative mb-6">
                        <div class="text-8xl md:text-9xl font-bold transition-all duration-300 mb-2 {{ $match->status === 'completed' ? '' : 'cursor-pointer hover:text-red-300 active:scale-95 select-none' }} {{ $currentServer === 'away' ? 'text-red-400 animate-pulse drop-shadow-lg' : 'text-red-400' }}"
                             @if($match->status !== 'completed') wire:click="addPoint('away')" @endif
                             style="position: relative; z-index: 10;">
                            {{ $awayScore }}
                        </div>
                        @if(!$isOrganizationOwner)
                            <div class="text-xs text-yellow-400 mt-1">⚠️ Samo vlasnik organizacije može mijenjati rezultat</div>
                        @endif
                        <div class="text-xs text-gray-500 mt-1">Status: {{ $match->status }} | Owner: {{ $isOrganizationOwner ? 'Yes' : 'No' }}</div>
                        <div class="text-xs text-red-300 mt-1">wire:click="addPoint('away')"</div>
                    </div>

                    <div class="flex justify-center">
                        <button type="button" @if($match->status !== 'completed' && $isOrganizationOwner) wire:click="subtractPoint('away')" @endif
                                class="px-4 py-2 bg-gray-600 {{ $match->status === 'completed' || !$isOrganizationOwner ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-700' }} text-white rounded-lg transition-all duration-200 font-semibold text-sm">
                            -1
                        </button>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 pt-6" wire:key="sets-{{ $setsVersion }}">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">Completed Sets</h4>
                <div class="flex justify-center space-x-4">
                    @foreach($sets as $index => $set)
                    <div class="text-center p-3 bg-gray-700/30 rounded-lg" wire:key="set-{{ $index }}-{{ $setsVersion }}">
                        <div class="text-sm text-gray-400 mb-1">Set {{ $index + 1 }}</div>
                        <div class="text-lg font-bold text-white">{{ $set['home_score'] ?? $set['home'] ?? 0 }} - {{ $set['away_score'] ?? $set['away'] ?? 0 }}</div>
                        <div class="text-xs text-gray-400">
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
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-700 pt-6 mt-6">
                <div class="flex justify-center space-x-4 mb-4">
                    @if($match->status !== 'completed' && $isOrganizationOwner)
                    <button type="button" wire:click="undoPoint"
                            class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors font-semibold"
                            wire:loading.attr="disabled"
                            @if(empty($pointHistory)) disabled @endif>
                        ↩️ Undo
                    </button>
                    <div class="text-xs text-yellow-300 mt-1">wire:click="undoPoint"</div>
                    <button type="button" wire:click="togglePause"
                            class="px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors font-semibold">
                        {{ $matchPaused ? '▶️ Resume Timer' : '⏸️ Pause Timer' }}
                    </button>
                    <div class="text-xs text-yellow-300 mt-1">wire:click="togglePause"</div>
                    <button type="button" wire:click="endMatch"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold">
                        🏁 End Match
                    </button>
                    <div class="text-xs text-green-300 mt-1">wire:click="endMatch"</div>
                    @elseif($match->status === 'completed')
                    <div class="text-center text-green-400 font-semibold">
                        ✅ Match Completed
                    </div>
                    @else
                    <div class="text-center text-gray-400 font-semibold">
                        👁️ View Only Mode
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <div x-data="{ showModal: false, winner: '', homeSets: 0, awaySets: 0, setsToWin: 0, finalSets: [] }"
         x-show="showModal"
         x-on:match-won.window="showModal = true; winner = $event.detail.winner; homeSets = $event.detail.homeSets; awaySets = $event.detail.awaySets; setsToWin = $event.detail.setsToWin; finalSets = $event.detail.finalSets"
         x-on:keydown.escape.window="showModal = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-500 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-white">
                                Match Completed!
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-300">
                                    <span x-text="winner === 'home' ? '{{ $match->league->is_team_based ? $match->homeTeam->name ?? 'Home Team' : $match->homePlayer->name ?? 'Home Player' }}' : '{{ $match->league->is_team_based ? $match->awayTeam->name ?? 'Away Team' : $match->awayPlayer->name ?? 'Away Player' }}'"></span>
                                    wins the match with <span x-text="winner === 'home' ? homeSets : awaySets"></span> sets to <span x-text="winner === 'away' ? homeSets : awaySets"></span>!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="confirmMatchEnd" x-on:click="showModal = false"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        End Match & Save Results
                    </button>
                    <div class="text-xs text-green-300 mt-1">wire:click="confirmMatchEnd"</div>
                    <button type="button" x-on:click="showModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Continue Playing
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
        console.log('Livewire loaded:', typeof window.Livewire !== 'undefined');
        console.log('Livewire object:', window.Livewire);
        
        // Log all Livewire components
        if (window.Livewire && window.Livewire.all) {
            console.log('Livewire components:', window.Livewire.all());
        }
    });
    
    // Listen for Livewire errors
    document.addEventListener('livewire:load', function() {
        console.log('Livewire loaded event fired');
        
        Livewire.hook('message.failed', (message, component) => {
            console.error('Livewire message failed:', message, component);
        });
        
        Livewire.hook('element.updating', (fromEl, toEl, component) => {
            console.log('Livewire updating element:', fromEl, toEl);
        });
    });
    
    // Timer functionality removed - no longer needed
    
    // Listen for clear-local-storage event
    if (window.Livewire) {
        Livewire.on('clear-local-storage', (data) => {
            // Clear all localStorage data when match is reset
            localStorage.clear();
            console.log('Cleared all localStorage data for match reset');
        });
    }
</script>
@endpush