{{-- Quick Result Modal --}}
<div id="quickResultModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-2xl p-6 max-w-lg w-full border border-gray-700 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-white">⚡ {{ __('Quick Result Entry') }}</h3>
            <button onclick="closeQuickResultModal()" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="quickResultForm" method="POST">
            @csrf
            <div class="space-y-6">
                <!-- Match Info -->
                <div class="bg-gray-700/30 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-xs" id="homeInitials">--</span>
                            </div>
                            <span class="text-white font-medium" id="homePlayerName">Player 1</span>
                        </div>
                        <input type="number" name="home_score" id="homeScoreInput" min="0" max="5" required
                               class="w-20 text-center text-2xl font-bold bg-gray-600/50 border border-gray-500 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-xs" id="awayInitials">--</span>
                            </div>
                            <span class="text-white font-medium" id="awayPlayerName">Player 2</span>
                        </div>
                        <input type="number" name="away_score" id="awayScoreInput" min="0" max="5" required
                               class="w-20 text-center text-2xl font-bold bg-gray-600/50 border border-gray-500 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Set Scores (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-3">{{ __('Set Scores (Optional)') }}</label>
                    <div id="setScoresContainer" class="space-y-2">
                        <!-- Set score inputs will be dynamically added here -->
                    </div>
                    <button type="button" onclick="addSetScore()" class="mt-2 text-blue-400 hover:text-blue-300 text-sm">
                        + {{ __('Add Set Score') }}
                    </button>
                </div>

                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="closeQuickResultModal()"
                            class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        {{ __('Save Result') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Manual Knockout Setup Modal --}}
<div id="manualKnockoutModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-gray-800 rounded-2xl p-5 max-w-4xl w-full border border-gray-700 shadow-xl my-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-white">🎯 Ručno postavljanje eliminacione faze</h3>
            <button onclick="closeManualKnockoutModal()" class="text-gray-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- JOOLA System Info -->
        <div class="mb-4 bg-blue-600/20 border border-blue-500/50 rounded-lg p-3">
            <div class="flex items-start space-x-2">
                <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-sm text-blue-300 font-semibold mb-2">JOOLA Turnirski Sistem</p>
                    <ul class="text-xs text-blue-300 space-y-1">
                        <li>✓ <strong>Grupa 1 pobjednik</strong> → Pozicija A (seedovan)</li>
                        <li>✓ <strong>Grupa 2 pobjednik</strong> → Pozicija B (seedovan)</li>
                        <li>✓ <strong>Ostali pobjednici</strong> → Raspoređeni na C, D, E... pozicije</li>
                        <li>✓ <strong>Drugoplasirani</strong> → Ne igraju protiv pobjednika svoje grupe</li>
                        <li>✓ <strong>BYE sistem</strong> → Ako nije 2ⁿ broj, seedovi dobijaju prednost</li>
                    </ul>
                    <div class="mt-2 pt-2 border-t border-blue-400/30">
                        <p class="text-xs text-blue-200">
                            <strong>Izmjena:</strong> Klikni slot (plavi=prazan, zeleni=popunjen), pa odaberi igrača
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Available Players -->
            <div class="md:col-span-1 bg-gray-700/30 rounded-lg p-3">
                <h4 class="text-sm font-semibold text-white mb-3">Dostupni igrači</h4>
                <div id="availablePlayers" class="space-y-2 max-h-80 overflow-y-auto pr-2">
                    @php
                        $qualifiedPlayers = collect();
                        foreach($competition->tournamentGroups as $group) {
                            $standings = App\Models\Standing::where('competition_id', $competition->id)
                                ->where('tournament_group_id', $group->id)
                                ->with('player')
                                ->orderBy('points', 'desc')
                                ->orderByRaw('(sets_won - sets_lost) desc')
                                ->orderByRaw('(points_won - points_lost) desc')
                                ->limit($competition->players_advancing_per_group ?? 2)
                                ->get();
                            foreach($standings as $standing) {
                                if($standing->player) {
                                    $qualifiedPlayers->push([
                                        'player' => $standing->player,
                                        'group' => $group->name,
                                        'position' => $standing->position
                                    ]);
                                }
                            }
                        }
                    @endphp
                    
                    @foreach($qualifiedPlayers as $qualified)
                        <div class="player-item flex items-center justify-between p-2 bg-gray-600/30 rounded-lg border border-gray-500/30 hover:bg-gray-600/50 cursor-pointer transition-colors"
                             data-player-id="{{ $qualified['player']->id }}"
                             data-player-name="{{ $qualified['player']->name }}"
                             onclick="selectPlayerForKnockout({{ $qualified['player']->id }}, '{{ $qualified['player']->name }}', this)">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-xs">{{ substr($qualified['player']->name, 0, 2) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-white font-medium text-sm truncate">{{ $qualified['player']->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $qualified['group'] }}-{{ $qualified['position'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Knockout Bracket Slots -->
            <div class="md:col-span-2">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-white">Mečevi eliminacione faze</h4>
                    <div class="flex space-x-2">
                        <button onclick="applyJoolaSuggestion()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg transition-colors">
                            ✓ Prihvati prijedlog
                        </button>
                        <button onclick="addPlayoffMatch()" class="bg-yellow-600 hover:bg-yellow-700 text-white text-xs px-3 py-1.5 rounded-lg transition-colors">
                            ➕ Playoff
                        </button>
                    </div>
                </div>
                
                <div id="knockoutMatchesContainer" class="space-y-2 max-h-80 overflow-y-auto pr-2">
                    <!-- Matches will be dynamically generated -->
                </div>

                <div class="mt-4 flex justify-end space-x-2">
                    <button onclick="closeManualKnockoutModal()" 
                            class="px-4 py-2 text-sm bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Odustani
                    </button>
                    <button onclick="saveManualKnockout()" 
                            class="px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold">
                        💾 Sačuvaj
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

