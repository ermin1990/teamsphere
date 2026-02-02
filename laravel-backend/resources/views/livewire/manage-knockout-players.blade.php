<div>
    @if($knockoutMatches->count() > 0)
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
