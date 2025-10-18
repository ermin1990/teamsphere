<div>
    @if($knockoutMatches->count() > 0)
        <!-- Quick Mode Toggle -->
        <div class="bg-gray-700/30 rounded-lg p-4 border border-gray-600/30 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-white font-medium">{{ __('Quick Mode') }}</h4>
                    <p class="text-sm text-gray-400">{{ __('Enable quick player assignment and auto-assign features') }}</p>
                </div>
                <div class="relative">
                    <input type="checkbox"
                           wire:model.live="quickMode"
                           wire:change="toggleQuickMode"
                           class="sr-only">
                    
                    <!-- Custom Toggle Switch -->
                    <div class="w-16 h-8 bg-gray-600 rounded-full relative cursor-pointer transition-colors duration-300 {{ $quickMode ? 'bg-green-600' : 'bg-gray-600' }}"
                         onclick="document.querySelector('input[wire\\:model\\.live=quickMode]').click();">
                        
                        <!-- Slider -->
                        <div class="absolute top-1 w-6 h-6 bg-white rounded-full shadow-md transform transition-transform duration-300 {{ $quickMode ? 'translate-x-8' : 'translate-x-1' }}">
                            <!-- Icon inside slider -->
                            <div class="flex items-center justify-center w-full h-full">
                                @if($quickMode)
                                    <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-3 h-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Background Labels -->
                        <div class="absolute inset-0 flex items-center justify-between px-2 text-xs font-medium">
                            <span class="text-gray-400 {{ $quickMode ? 'opacity-0' : 'opacity-100' }} transition-opacity duration-300">OFF</span>
                            <span class="text-white {{ $quickMode ? 'opacity-100' : 'opacity-0' }} transition-opacity duration-300">ON</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($quickMode)
                <div class="mt-4 pt-4 border-t border-gray-600/30">
                    <button wire:click="autoAssignPlayers"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                        Automatski dodijeli igrače
                    </button>
                    <p class="text-xs text-gray-400 mt-2">Automatski će dodijeliti igrače preostalim praznim pozicijama u utakmicama.</p>
                </div>
            @endif
        </div>

        <div class="space-y-8">
            @foreach($knockoutMatches->sortKeysDesc() as $roundNumber => $roundMatches)
                <div>
                    <h4 class="text-xl font-bold text-center mb-6 text-white">
                        Runda {{ $roundNumber }}
                    </h4>
                    <div class="grid gap-4" style="grid-template-columns: repeat({{ $roundMatches->count() }}, minmax(0, 1fr));">
                        @foreach($roundMatches as $match)
                            <div class="bg-gray-700/40 rounded-xl border-2 border-gray-600/50 overflow-hidden">
                                <!-- Match Header -->
                                <div class="bg-gray-700/60 px-3 py-2 border-b border-gray-600/50">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-400">Utakmica #{{ $match->id }}</span>
                                        @if($match->status === 'in_progress')
                                            <span class="text-xs text-green-400 animate-pulse">🔴 UŽIVO</span>
                                        @elseif($match->status === 'completed')
                                            <span class="text-xs text-gray-400">✓</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Players Selection -->
                                <div class="p-4 space-y-3">
                                    <!-- Home Player -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Domaći igrač</label>
                                        <select wire:model.live="selectedPlayers.{{ $match->id }}.home_player_id"
                                                wire:change="updatePlayer({{ $match->id }}, 'home', $event.target.value)"
                                                class="w-full bg-gray-700/60 text-white rounded px-3 py-2 border border-gray-600/50">
                                            <option value="">-- Odaberite igrača --</option>
                                            @foreach($availablePlayers as $player)
                                                <option value="{{ $player->id }}"
                                                        @if(($selectedPlayers[$match->id]['home_player_id'] ?? null) == $player->id) selected @endif>
                                                    {{ $player->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Away Player -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-2">Gostujući igrač</label>
                                        <select wire:model.live="selectedPlayers.{{ $match->id }}.away_player_id"
                                                wire:change="updatePlayer({{ $match->id }}, 'away', $event.target.value)"
                                                class="w-full bg-gray-700/60 text-white rounded px-3 py-2 border border-gray-600/50">
                                            <option value="">-- Odaberite igrača --</option>
                                            @foreach($availablePlayers as $player)
                                                <option value="{{ $player->id }}"
                                                        @if(($selectedPlayers[$match->id]['away_player_id'] ?? null) == $player->id) selected @endif>
                                                    {{ $player->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Confirm Button -->
                                    <div class="pt-2">
                                        <button wire:click="confirmPlayers({{ $match->id }})"
                                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                                            Potvrdi igrače
                                        </button>
                                    </div>

                                    <!-- Current Players Display -->
                                    @if($match->homePlayer || $match->awayPlayer)
                                        <div class="mt-3 p-3 bg-gray-800/50 rounded-lg">
                                            <div class="text-xs text-gray-400 mb-2">Trenutno postavljeni igrači:</div>
                                            <div class="space-y-1 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-blue-400">Domaći:</span>
                                                    <span class="text-white">{{ $match->homePlayer->name ?? 'Nije postavljen' }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-red-400">Gostujući:</span>
                                                    <span class="text-white">{{ $match->awayPlayer->name ?? 'Nije postavljen' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="mt-4 p-4 bg-green-600/20 border border-green-500/30 rounded-lg">
                <div class="text-green-400 font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mt-4 p-4 bg-red-600/20 border border-red-500/30 rounded-lg">
                <div class="text-red-400 font-semibold">{{ session('error') }}</div>
            </div>
        @endif
    @else
        <div class="text-center text-gray-400 py-8">
            Nema utakmica u eliminacionoj fazi.
        </div>
    @endif
</div>
