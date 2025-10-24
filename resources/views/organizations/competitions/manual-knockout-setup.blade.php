<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">🎯 Ručno postavljanje eliminacione faze</h1>
                        <p class="text-gray-400">{{ $competition->name }} - {{ $organization->name }}</p>
                    </div>
                    <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        ← Nazad na turnir
                    </a>
                </div>
            </div>

            {{-- JOOLA System Info --}}
            <div class="mb-6 bg-blue-600/20 border border-blue-500/50 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-base text-blue-300 font-semibold mb-3">JOOLA Turnirski Sistem</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-blue-300">
                            <div>
                                <p class="font-semibold mb-2">Automatska pravila:</p>
                                <ul class="space-y-1 text-xs">
                                    <li>✓ <strong>Grupa 1 pobjednik</strong> → Pozicija A (seedovan)</li>
                                    <li>✓ <strong>Grupa 2 pobjednik</strong> → Pozicija B (seedovan, suprotna strana)</li>
                                    <li>✓ <strong>Ostali pobjednici</strong> → Raspoređeni na C, D, E... pozicije</li>
                                    <li>✓ <strong>Drugoplasirani</strong> → Ne igraju protiv pobjednika svoje grupe</li>
                                    <li>✓ <strong>BYE sistem</strong> → Ako nije 2ⁿ broj, seedovi dobijaju prednost</li>
                                </ul>
                            </div>
                            <div>
                                <p class="font-semibold mb-2">Kako koristiti:</p>
                                <ul class="space-y-1 text-xs">
                                    <li>• <strong>Prihvati prijedlog</strong> - Resetuj na JOOLA parove</li>
                                    <li>• <strong>Ručna izmjena</strong> - Klikni prazan slot (plavi okvir)</li>
                                    <li>• <strong>Odabir igrača</strong> - Klikni igrača sa lijeve strane</li>
                                    <li>• <strong>Zamjena</strong> - Klikni popunjen slot (zeleni okvir), pa izaberi drugog</li>
                                    <li>• <strong>Playoff mečevi</strong> - Dodaj mečeve za razigravanje (3. mjesto, itd.)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="manualKnockoutForm" method="POST" action="{{ route('organizations.competitions.save-manual-bracket', [$organization, $competition]) }}">
                @csrf
                <input type="hidden" name="bracket_data" id="bracketDataInput">

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    
                    {{-- Available Players --}}
                    <div class="lg:col-span-1">
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700/50 shadow-xl p-4 sticky top-4">
                            <h3 class="text-lg font-semibold text-white mb-4">Dostupni igrači</h3>
                            <div id="availablePlayers" class="space-y-2 max-h-[calc(100vh-250px)] overflow-y-auto pr-2">
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
                                    <div class="player-item flex items-center p-3 bg-gray-700/30 rounded-lg border border-gray-600/30 hover:bg-gray-600/50 cursor-pointer transition-all"
                                         data-player-id="{{ $qualified['player']->id }}"
                                         data-player-name="{{ $qualified['player']->name }}"
                                         data-player-group="{{ $qualified['group'] }}"
                                         data-player-position="{{ $qualified['position'] }}"
                                         onclick="selectPlayerForKnockout({{ $qualified['player']->id }}, '{{ $qualified['player']->name }}', '{{ $qualified['group'] }}', {{ $qualified['position'] }}, this)">
                                        <div class="flex items-center space-x-3 flex-1">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-white font-bold text-sm">{{ substr($qualified['player']->name, 0, 2) }}</span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-white font-medium truncate">{{ $qualified['player']->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $qualified['group'] }}-{{ $qualified['position'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Knockout Bracket Slots --}}
                    <div class="lg:col-span-3">
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700/50 shadow-xl p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-semibold text-white">Mečevi eliminacione faze</h3>
                                <div class="flex space-x-3">
                                    <button type="button" onclick="applyJoolaSuggestion()" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors font-semibold">
                                        ✓ Prihvati JOOLA prijedlog
                                    </button>
                                    <button type="button" onclick="addPlayoffMatch()" 
                                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors">
                                        ➕ Dodaj Playoff meč
                                    </button>
                                </div>
                            </div>
                            
                            <div id="knockoutMatchesContainer" class="space-y-4 mb-6">
                                {{-- Matches will be dynamically generated --}}
                            </div>

                            <div class="flex justify-between items-center pt-6 border-t border-gray-700">
                                <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" 
                                   class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                                    Odustani
                                </a>
                                <button type="button" onclick="saveManualKnockout()" 
                                        class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold text-lg">
                                    💾 Sačuvaj i generiši eliminacionu fazu
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

            {{-- Bracket Tree Visualization --}}
            @include('organizations.competitions.partials.bracket-tree')

        </div>
    </div>

    {{-- Scripts --}}
    <script>
        let knockoutPlayers = [];
        let knockoutMatchCount = 0;
        let playoffMatchCount = 0;
        let qualifiedPlayersData = [];
        let selectedSlot = null;

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Collect qualified players data
            const playerElements = document.querySelectorAll('#availablePlayers .player-item');
            playerElements.forEach(el => {
                const playerId = el.dataset.playerId;
                const playerName = el.dataset.playerName;
                const group = el.dataset.playerGroup;
                const position = parseInt(el.dataset.playerPosition);
                
                qualifiedPlayersData.push({
                    id: playerId,
                    name: playerName,
                    group: group,
                    position: position
                });
            });

            // Calculate total and generate initial bracket
            const totalQualified = qualifiedPlayersData.length;
            generateKnockoutSlots(totalQualified);
            
            // Auto-apply JOOLA suggestion
            applyJoolaSuggestion();
        });

        // Generate knockout match slots
        function generateKnockoutSlots(playerCount) {
            const container = document.getElementById('knockoutMatchesContainer');
            container.innerHTML = '';
            
            knockoutMatchCount = Math.floor(playerCount / 2);
            
            // Determine round name
            let roundName = 'Eliminacija';
            if (knockoutMatchCount === 8) roundName = 'Osmina finala';
            else if (knockoutMatchCount === 4) roundName = 'Četvrtfinale';
            else if (knockoutMatchCount === 2) roundName = 'Polufinale';
            else if (knockoutMatchCount === 1) roundName = 'Finale';
            
            for (let i = 1; i <= knockoutMatchCount; i++) {
                const matchDiv = document.createElement('div');
                matchDiv.className = 'bg-gray-700/30 rounded-xl p-4 border border-gray-600/30';
                matchDiv.dataset.matchIndex = i;
                matchDiv.innerHTML = `
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-white font-semibold">Meč ${i}</h5>
                        <span class="text-sm text-gray-400">${roundName}</span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 p-3 bg-gray-600/30 rounded-lg min-h-[60px] knockout-slot cursor-pointer hover:bg-gray-600/50 transition-colors" 
                             data-match="${i}" data-position="home" 
                             onclick="toggleSlotSelection(this)">
                            <span class="text-gray-400 text-sm">Klikni za odabir igrača...</span>
                        </div>
                        <div class="text-center text-gray-500 text-sm font-semibold">VS</div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-600/30 rounded-lg min-h-[60px] knockout-slot cursor-pointer hover:bg-gray-600/50 transition-colors" 
                             data-match="${i}" data-position="away" 
                             onclick="toggleSlotSelection(this)">
                            <span class="text-gray-400 text-sm">Klikni za odabir igrača...</span>
                        </div>
                    </div>
                `;
                container.appendChild(matchDiv);
            }
        }

        // Toggle slot selection
        function toggleSlotSelection(slotElement) {
            if (selectedSlot === slotElement) {
                slotElement.classList.remove('ring-2', 'ring-blue-500', 'ring-green-500');
                selectedSlot = null;
                return;
            }
            
            document.querySelectorAll('.knockout-slot').forEach(slot => {
                slot.classList.remove('ring-2', 'ring-blue-500', 'ring-green-500');
            });
            
            const hasPlayer = slotElement.querySelector('.font-bold');
            if (hasPlayer) {
                slotElement.classList.add('ring-2', 'ring-green-500');
            } else {
                slotElement.classList.add('ring-2', 'ring-blue-500');
            }
            selectedSlot = slotElement;
        }

        // Select player for knockout position
        function selectPlayerForKnockout(playerId, playerName, group, position, element) {
            if (!selectedSlot) {
                alert('Prvo odaberite poziciju u eliminacionoj fazi (kliknite na prazan slot)');
                return;
            }

            const existingAssignment = knockoutPlayers.find(p => p.playerId === playerId);
            if (existingAssignment) {
                knockoutPlayers = knockoutPlayers.filter(p => p.playerId !== playerId);
                const oldSlot = document.querySelector(`.knockout-slot[data-match="${existingAssignment.match}"][data-position="${existingAssignment.position}"]`);
                if (oldSlot && !oldSlot.dataset.match.startsWith('playoff-')) {
                    oldSlot.innerHTML = '<span class="text-gray-400 text-sm">Klikni za odabir igrača...</span>';
                }
            }
            
            const slotMatch = selectedSlot.dataset.match;
            const slotPosition = selectedSlot.dataset.position;
            const existingInSlot = knockoutPlayers.find(p => p.match == slotMatch && p.position === slotPosition);
            
            if (existingInSlot) {
                const oldPlayerElement = document.querySelector(`#availablePlayers .player-item[data-player-id="${existingInSlot.playerId}"]`);
                if (oldPlayerElement) {
                    oldPlayerElement.classList.remove('opacity-50', 'pointer-events-none');
                }
                knockoutPlayers = knockoutPlayers.filter(p => !(p.match == slotMatch && p.position === slotPosition));
            }

            const match = selectedSlot.dataset.match;
            const positionSlot = selectedSlot.dataset.position;
            
            selectedSlot.innerHTML = `
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold">${playerName.substring(0, 2)}</span>
                </div>
                <div class="flex-1">
                    <div class="text-white font-semibold">${playerName}</div>
                    <div class="text-xs text-gray-400">${group}-${position}</div>
                </div>
            `;
            selectedSlot.classList.remove('ring-2', 'ring-blue-500', 'ring-green-500');
            
            knockoutPlayers.push({
                playerId: playerId,
                playerName: playerName,
                match: match,
                position: positionSlot,
                isPlayoff: false
            });

            element.classList.add('opacity-50', 'pointer-events-none');
            selectedSlot = null;
        }

        // Apply JOOLA suggestion
        function applyJoolaSuggestion() {
            knockoutPlayers = [];
            
            document.querySelectorAll('.knockout-slot').forEach(slot => {
                if (!slot.dataset.match.startsWith('playoff-')) {
                    slot.innerHTML = '<span class="text-gray-400 text-sm">Klikni za odabir igrača...</span>';
                    slot.classList.remove('ring-2', 'ring-blue-500', 'ring-green-500');
                }
            });
            
            document.querySelectorAll('#availablePlayers .player-item').forEach(el => {
                el.classList.remove('opacity-50', 'pointer-events-none');
            });
            
            const sortedPlayers = [...qualifiedPlayersData].sort((a, b) => {
                if (a.position !== b.position) return a.position - b.position;
                return a.group.localeCompare(b.group);
            });
            
            const winners = sortedPlayers.filter(p => p.position === 1);
            const runnersUp = sortedPlayers.filter(p => p.position === 2);
            
            const totalPlayers = winners.length + runnersUp.length;
            const nextPowerOf2 = Math.pow(2, Math.ceil(Math.log2(totalPlayers)));
            const byeCount = nextPowerOf2 - totalPlayers;
            
            console.log(`Total: ${totalPlayers}, Next 2^n: ${nextPowerOf2}, BYE slots: ${byeCount}`);
            
            let bracketPositions = Array(nextPowerOf2).fill(null);
            let availableWinners = [...winners];
            let availableRunnersUp = [...runnersUp];
            
            // JOOLA seeding
            if (availableWinners.length > 0) {
                bracketPositions[0] = availableWinners.shift();
                console.log('Seed A:', bracketPositions[0].name);
            }
            
            if (availableWinners.length > 0) {
                const bottomSeedPosition = Math.floor(nextPowerOf2 / 2);
                bracketPositions[bottomSeedPosition] = availableWinners.shift();
                console.log(`Seed B (position ${bottomSeedPosition}):`, bracketPositions[bottomSeedPosition].name);
            }
            
            const halfBracket = nextPowerOf2 / 2;
            const quarterBracket = nextPowerOf2 / 4;
            
            const winnerPositions = [
                quarterBracket,
                quarterBracket + halfBracket,
                Math.floor(quarterBracket / 2),
                Math.floor(quarterBracket / 2) + halfBracket,
                Math.floor(quarterBracket * 3 / 2),
                Math.floor(quarterBracket * 3 / 2) + halfBracket
            ];
            
            let posIndex = 0;
            while (availableWinners.length > 0 && posIndex < winnerPositions.length) {
                const pos = winnerPositions[posIndex];
                if (!bracketPositions[pos]) {
                    bracketPositions[pos] = availableWinners.shift();
                }
                posIndex++;
            }
            
            for (let i = 0; i < bracketPositions.length && availableWinners.length > 0; i++) {
                if (bracketPositions[i] === null) {
                    bracketPositions[i] = availableWinners.shift();
                }
            }
            
            for (let i = 0; i < bracketPositions.length && availableRunnersUp.length > 0; i++) {
                if (bracketPositions[i] === null) {
                    const opponentIndex = nextPowerOf2 - 1 - i;
                    const opponent = bracketPositions[opponentIndex];
                    
                    if (opponent) {
                        const safeRunner = availableRunnersUp.find(r => r.group !== opponent.group);
                        if (safeRunner) {
                            bracketPositions[i] = safeRunner;
                            availableRunnersUp = availableRunnersUp.filter(r => r.id !== safeRunner.id);
                        } else {
                            bracketPositions[i] = availableRunnersUp.shift();
                        }
                    } else {
                        bracketPositions[i] = availableRunnersUp.shift();
                    }
                }
            }
            
            let matchIndex = 1;
            for (let i = 0; i < halfBracket; i++) {
                const homeIndex = i;
                const awayIndex = nextPowerOf2 - 1 - i;
                
                const homePlayer = bracketPositions[homeIndex];
                const awayPlayer = bracketPositions[awayIndex];
                
                if (homePlayer || awayPlayer) {
                    if (homePlayer) {
                        assignPlayerToSlot(homePlayer, matchIndex, 'home');
                    }
                    if (awayPlayer) {
                        assignPlayerToSlot(awayPlayer, matchIndex, 'away');
                    }
                    matchIndex++;
                }
            }
            
            console.log('Applied JOOLA suggestion');
        }

        function assignPlayerToSlot(player, matchIndex, position) {
            const slot = document.querySelector(`.knockout-slot[data-match="${matchIndex}"][data-position="${position}"]`);
            if (!slot) return;
            
            slot.innerHTML = `
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold">${player.name.substring(0, 2)}</span>
                </div>
                <div class="flex-1">
                    <div class="text-white font-semibold">${player.name}</div>
                    <div class="text-xs text-gray-400">${player.group}-${player.position}</div>
                </div>
            `;
            
            knockoutPlayers.push({
                playerId: player.id,
                playerName: player.name,
                match: matchIndex,
                position: position,
                isPlayoff: false
            });
            
            const playerElement = document.querySelector(`#availablePlayers .player-item[data-player-id="${player.id}"]`);
            if (playerElement) {
                playerElement.classList.add('opacity-50', 'pointer-events-none');
            }
        }

        function addPlayoffMatch() {
            playoffMatchCount++;
            const container = document.getElementById('knockoutMatchesContainer');
            
            const matchDiv = document.createElement('div');
            matchDiv.className = 'bg-yellow-600/20 rounded-xl p-4 border-2 border-yellow-500/50 mt-4';
            matchDiv.dataset.playoffIndex = playoffMatchCount;
            matchDiv.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h5 class="text-yellow-400 font-semibold">🏆 Playoff Meč ${playoffMatchCount}</h5>
                    <button type="button" onclick="removePlayoffMatch(this)" class="text-red-400 hover:text-red-300 text-sm">
                        ❌ Ukloni
                    </button>
                </div>
                <div class="mb-3">
                    <input type="text" placeholder="Naziv (npr. Meč za 3. mjesto)" 
                           class="w-full px-3 py-2 bg-gray-600/50 border border-gray-500 rounded-lg text-white"
                           id="playoff-name-${playoffMatchCount}">
                </div>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3 p-3 bg-gray-600/30 rounded-lg min-h-[60px] knockout-slot cursor-pointer hover:bg-gray-600/50 transition-colors" 
                         data-match="playoff-${playoffMatchCount}" data-position="home" 
                         onclick="toggleSlotSelection(this)">
                        <span class="text-gray-400 text-sm">Klikni za odabir igrača...</span>
                    </div>
                    <div class="text-center text-gray-500 text-sm font-semibold">VS</div>
                    <div class="flex items-center space-x-3 p-3 bg-gray-600/30 rounded-lg min-h-[60px] knockout-slot cursor-pointer hover:bg-gray-600/50 transition-colors" 
                         data-match="playoff-${playoffMatchCount}" data-position="away" 
                         onclick="toggleSlotSelection(this)">
                        <span class="text-gray-400 text-sm">Klikni za odabir igrača...</span>
                    </div>
                </div>
            `;
            container.appendChild(matchDiv);
        }

        function removePlayoffMatch(button) {
            const matchDiv = button.closest('[data-playoff-index]');
            matchDiv.remove();
        }

        function saveManualKnockout() {
            const totalSlots = knockoutMatchCount * 2;
            const mainBracketPlayers = knockoutPlayers.filter(p => !p.isPlayoff);
            
            if (mainBracketPlayers.length < totalSlots) {
                alert(`Molimo popunite sve pozicije u eliminacionoj fazi (${mainBracketPlayers.length}/${totalSlots})`);
                return;
            }

            const bracketData = {
                matches: knockoutPlayers,
                playoffMatches: []
            };

            document.querySelectorAll('[data-playoff-index]').forEach((playoffDiv, index) => {
                const playoffIndex = playoffDiv.dataset.playoffIndex;
                const playoffName = document.getElementById(`playoff-name-${playoffIndex}`).value || `Playoff ${playoffIndex}`;
                
                const homePlayers = knockoutPlayers.filter(p => p.match === `playoff-${playoffIndex}` && p.position === 'home');
                const awayPlayers = knockoutPlayers.filter(p => p.match === `playoff-${playoffIndex}` && p.position === 'away');
                
                if (homePlayers.length > 0 && awayPlayers.length > 0) {
                    bracketData.playoffMatches.push({
                        name: playoffName,
                        home_player_id: homePlayers[0].playerId,
                        away_player_id: awayPlayers[0].playerId
                    });
                }
            });

            document.getElementById('bracketDataInput').value = JSON.stringify(bracketData);
            
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Čuva se...';

            document.getElementById('manualKnockoutForm').submit();
        }
    </script>

</x-app-layout>
