<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-1">🎯 Ručno postavljanje eliminacione faze</h1>
                        <p class="text-sm text-gray-400">{{ $competition->name }} - {{ $organization->name }}</p>
                    </div>
                    <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-lg transition-colors text-sm">
                        ← Nazad na turnir
                    </a>
                </div>
            </div>

            {{-- Instructions --}}
            <div class="mb-4 bg-blue-600/20 border border-blue-500/50 rounded-lg p-3">
                <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm text-blue-300 font-semibold mb-2">Postavljanje ždrijeba</p>
                        <div class="text-xs text-blue-300 space-y-1">
                            <p>• <strong>Povucite igrača</strong> sa lijeve strane na željenu poziciju u meču (desktop)</p>
                            <p>• Ili <strong>kliknite na igrača</strong> pa na poziciju u meču (mobilni uređaji)</p>
                            <p>• Mečevi su podijeljeni na dvije strane ždrijeba</p>
                            <p>• Pored svakog igrača piše <strong>iz koje grupe i sa koje pozicije</strong> dolazi</p>
                            <p>• Možete ostaviti prazne pozicije (automatski prolaz)</p>
                        </div>
                    </div>
                </div>
            </div>

            <form id="manualKnockoutForm" method="POST" action="{{ route('organizations.competitions.save-manual-knockout', [$organization, $competition]) }}">
                @csrf
                <input type="hidden" name="bracket_data" id="bracketDataInput">

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    
                    {{-- Available Players --}}
                    <div class="lg:col-span-1">
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700/50 shadow-xl p-3 sticky top-4">
                            <h3 class="text-sm font-semibold text-white mb-3">Igrači iz grupa</h3>
                            <div id="availablePlayers" class="space-y-1.5 max-h-[calc(100vh-250px)] overflow-y-auto pr-2">
                                @php
                                    $qualifiedPlayers = collect();
                                    foreach($competition->tournamentGroups as $group) {
                                        $standings = App\Models\Standing::where('competition_id', $competition->id)
                                            ->where('tournament_group_id', $group->id)
                                            ->with('player')
                                            ->orderBy('position', 'asc')
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
                                    <div class="player-item flex items-center justify-between p-2 bg-gray-700/30 rounded-lg border border-gray-600/30 hover:bg-gray-600/50 cursor-pointer transition-all"
                                         data-player-id="{{ $qualified['player']->id }}"
                                         data-player-name="{{ $qualified['player']->name }}"
                                         data-player-info="{{ $qualified['group'] }} - Pozicija {{ $qualified['position'] }}"
                                         draggable="true"
                                         onclick="selectPlayerForAssignment(this)">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-white text-sm font-medium truncate">{{ $qualified['player']->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $qualified['group'] }} - Pozicija {{ $qualified['position'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Knockout Bracket Slots --}}
                    <div class="lg:col-span-3">
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700/50 shadow-xl p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-base font-semibold text-white">Mečevi eliminacione faze</h3>
                                <div class="flex space-x-2">
                                    <div class="flex items-center space-x-2">
                                        <label for="matchCount" class="text-sm text-gray-300">Broj mečeva:</label>
                                        <input type="number" id="matchCount" min="1" max="16" value="4" 
                                               class="w-16 px-2 py-1 bg-gray-600/50 border border-gray-500 rounded-lg text-white text-sm text-center">
                                    </div>
                                    <button type="button" onclick="generateMatches()" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition-colors text-sm">
                                        Generiši mečeve
                                    </button>
                                </div>
                            </div>
                            
                            <div id="knockoutMatchesContainer" class="mb-4">
                                <div id="topBracket" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4 mb-4 md:mb-6">
                                    <!-- Top half matches -->
                                </div>
                                <div class="flex items-center justify-center mb-4 md:mb-6">
                                    <div class="w-full max-w-md h-px bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>
                                </div>
                                <div id="bottomBracket" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4">
                                    <!-- Bottom half matches -->
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-4 border-t border-gray-700">
                                <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" 
                                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors text-sm">
                                    Odustani
                                </a>
                                <button type="button" onclick="saveManualKnockout()" 
                                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold text-sm">
                                    💾 Sačuvaj i generiši eliminacionu fazu
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    {{-- Scripts --}}
    <script>
        let draggedPlayer = null;
        let selectedPlayer = null;

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            generateMatches();
            initializeDragAndDrop();
        });

        function initializeDragAndDrop() {
            document.querySelectorAll('.player-item').forEach(player => {
                player.addEventListener('dragstart', handleDragStart);
            });
        }

        function handleDragStart(e) {
            draggedPlayer = {
                id: this.dataset.playerId,
                name: this.dataset.playerName,
                info: this.dataset.playerInfo,
                element: this
            };
            e.dataTransfer.effectAllowed = 'move';
        }

        function allowDrop(e) {
            e.preventDefault();
            e.currentTarget.style.borderColor = 'rgb(59, 130, 246)';
        }

        function handleDragLeave(e) {
            e.currentTarget.style.borderColor = '';
        }

        function dropPlayer(e, matchIndex, position) {
            e.preventDefault();
            e.currentTarget.style.borderColor = '';

            if (!draggedPlayer) return;

            // Check if player is already assigned somewhere
            const existingAssignment = knockoutMatches.find(m => 
                (m.home && m.home.id === draggedPlayer.id) || 
                (m.away && m.away.id === draggedPlayer.id)
            );
            
            if (existingAssignment) {
                // Remove from old position
                if (existingAssignment.home && existingAssignment.home.id === draggedPlayer.id) {
                    existingAssignment.home = null;
                    updateSlotUI(knockoutMatches.indexOf(existingAssignment), 'home', null);
                }
                if (existingAssignment.away && existingAssignment.away.id === draggedPlayer.id) {
                    existingAssignment.away = null;
                    updateSlotUI(knockoutMatches.indexOf(existingAssignment), 'away', null);
                }
            }
            
            // Assign to new position
            knockoutMatches[matchIndex][position] = {
                id: draggedPlayer.id,
                name: draggedPlayer.name,
                info: draggedPlayer.info
            };
            
            // Update UI
            updateSlotUI(matchIndex, position, draggedPlayer);
            
            // Mark player as used
            draggedPlayer.element.classList.add('opacity-50', 'bg-blue-600/20');
            draggedPlayer = null;
        }

        // Generate matches based on user input
        function generateMatches() {
            const matchCount = parseInt(document.getElementById('matchCount').value);
            if (matchCount < 1 || matchCount > 16) {
                alert('Broj mečeva mora biti između 1 i 16');
                return;
            }
            
            const topContainer = document.getElementById('topBracket');
            const bottomContainer = document.getElementById('bottomBracket');
            topContainer.innerHTML = '';
            bottomContainer.innerHTML = '';
            knockoutMatches = [];
            
            const halfCount = Math.ceil(matchCount / 2);
            
            for (let i = 0; i < matchCount; i++) {
                knockoutMatches.push({
                    matchNumber: i + 1,
                    home: null,
                    away: null
                });
                
                const matchDiv = document.createElement('div');
                matchDiv.className = 'bg-gray-700/30 rounded-lg p-2 md:p-3 border border-gray-600/30';
                matchDiv.innerHTML = `
                    <div class="flex items-center justify-between mb-1 md:mb-2">
                        <h5 class="text-white font-semibold text-xs md:text-sm">Meč ${i + 1}</h5>
                    </div>
                    <div class="space-y-1 md:space-y-2">
                        <div class="match-slot p-1.5 md:p-2 bg-gray-600/30 rounded-lg min-h-[40px] md:min-h-[50px] border-2 border-transparent hover:border-blue-500/50 cursor-pointer transition-all flex items-center" 
                             data-match-index="${i}" 
                             data-position="home" 
                             ondrop="dropPlayer(event, ${i}, 'home')"
                             ondragover="allowDrop(event)"
                             ondragleave="handleDragLeave(event)"
                             onclick="assignPlayerToSlot(${i}, 'home')">
                            <span class="text-gray-400 text-xs">Kliknite da dodate igrača</span>
                        </div>
                        <div class="text-center text-gray-500 text-xs font-semibold">VS</div>
                        <div class="match-slot p-1.5 md:p-2 bg-gray-600/30 rounded-lg min-h-[40px] md:min-h-[50px] border-2 border-transparent hover:border-blue-500/50 cursor-pointer transition-all flex items-center" 
                             data-match-index="${i}" 
                             data-position="away" 
                             ondrop="dropPlayer(event, ${i}, 'away')"
                             ondragover="allowDrop(event)"
                             ondragleave="handleDragLeave(event)"
                             onclick="assignPlayerToSlot(${i}, 'away')">
                            <span class="text-gray-400 text-xs">Kliknite da dodate igrača</span>
                        </div>
                    </div>
                `;
                
                if (i < halfCount) {
                    topContainer.appendChild(matchDiv);
                } else {
                    bottomContainer.appendChild(matchDiv);
                }
            }
            
            // Clear all player selections
            document.querySelectorAll('.player-item').forEach(item => {
                item.classList.remove('opacity-50', 'pointer-events-none', 'bg-blue-600/20');
            });
        }

        // Select player from list
        function selectPlayerForAssignment(playerElement) {
            // Remove previous selection
            document.querySelectorAll('.player-item').forEach(item => {
                item.classList.remove('ring-2', 'ring-blue-500');
            });
            
            // Mark this player as selected
            playerElement.classList.add('ring-2', 'ring-blue-500');
            
            selectedPlayer = {
                id: playerElement.dataset.playerId,
                name: playerElement.dataset.playerName,
                info: playerElement.dataset.playerInfo,
                element: playerElement
            };
        }

        // Assign selected player to a match slot
        function assignPlayerToSlot(matchIndex, position) {
            if (!selectedPlayer) {
                alert('Prvo odaberite igrača sa lijeve strane');
                return;
            }
            
            // Check if player is already assigned somewhere
            const existingAssignment = knockoutMatches.find(m => 
                (m.home && m.home.id === selectedPlayer.id) || 
                (m.away && m.away.id === selectedPlayer.id)
            );
            
            if (existingAssignment) {
                // Remove from old position
                if (existingAssignment.home && existingAssignment.home.id === selectedPlayer.id) {
                    existingAssignment.home = null;
                    updateSlotUI(knockoutMatches.indexOf(existingAssignment), 'home', null);
                }
                if (existingAssignment.away && existingAssignment.away.id === selectedPlayer.id) {
                    existingAssignment.away = null;
                    updateSlotUI(knockoutMatches.indexOf(existingAssignment), 'away', null);
                }
            }
            
            // Assign to new position
            knockoutMatches[matchIndex][position] = {
                id: selectedPlayer.id,
                name: selectedPlayer.name,
                info: selectedPlayer.info
            };
            
            // Update UI
            updateSlotUI(matchIndex, position, selectedPlayer);
            
            // Mark player as used
            selectedPlayer.element.classList.add('opacity-50', 'bg-blue-600/20');
            selectedPlayer.element.classList.remove('ring-2', 'ring-blue-500');
            selectedPlayer = null;
        }

        // Update slot UI with player info
        function updateSlotUI(matchIndex, position, player) {
            const slot = document.querySelector(`.match-slot[data-match-index="${matchIndex}"][data-position="${position}"]`);
            if (!slot) return;
            
            if (player) {
                slot.innerHTML = `
                    <div class="flex-1">
                        <div class="text-white font-medium text-xs md:text-sm truncate">${player.name}</div>
                        <div class="text-xs text-gray-400 truncate">${player.info}</div>
                    </div>
                    <button type="button" 
                            onclick="clearSlot(${matchIndex}, '${position}'); event.stopPropagation();" 
                            class="text-red-400 hover:text-red-300 ml-1 md:ml-2 px-1 md:px-2 py-0.5 md:py-1 text-xs md:text-sm"
                            title="Ukloni igrača">
                        ❌
                    </button>
                `;
            } else {
                slot.innerHTML = '<span class="text-gray-400 text-xs">Kliknite da dodate igrača</span>';
            }
        }

        // Clear a slot
        function clearSlot(matchIndex, position) {
            const player = knockoutMatches[matchIndex][position];
            if (!player) return;
            
            // Re-enable player in list
            const playerElement = document.querySelector(`.player-item[data-player-id="${player.id}"]`);
            if (playerElement) {
                playerElement.classList.remove('opacity-50', 'bg-blue-600/20');
            }
            
            // Clear from data
            knockoutMatches[matchIndex][position] = null;
            
            // Update UI
            updateSlotUI(matchIndex, position, null);
        }

        // Save manual knockout
        function saveManualKnockout() {
            // Check if at least one match has both players
            const validMatches = knockoutMatches.filter(m => m.home || m.away);
            
            if (validMatches.length === 0) {
                alert('Molimo dodajte bar jednog igrača u neki meč');
                return;
            }

            // Convert to format expected by backend
            const matches = [];
            knockoutMatches.forEach((match, index) => {
                matches.push({
                    home_player_id: match.home ? parseInt(match.home.id) : null,
                    away_player_id: match.away ? parseInt(match.away.id) : null
                });
            });

            const bracketData = {
                matches: matches,
                playoffMatches: []
            };

            console.log('Saving bracket data:', bracketData);
            document.getElementById('bracketDataInput').value = JSON.stringify(bracketData);
            
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Čuva se...';

            document.getElementById('manualKnockoutForm').submit();
        }
    </script>

</x-app-layout>

```
