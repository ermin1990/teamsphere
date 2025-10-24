{{-- Scripts - Placeholder --}}
<script>
    let currentMatchId = null;
    let setScoreCount = 0;

    window.openQuickResultModal = function(matchId, homeName, awayName) {
        console.log('Opening quick result modal for match:', matchId, homeName, 'vs', awayName);
        currentMatchId = matchId;

        document.getElementById('homePlayerName').textContent = homeName;
        document.getElementById('awayPlayerName').textContent = awayName;
        document.getElementById('homeInitials').textContent = (homeName || 'TBD').substring(0, 2).toUpperCase();
        document.getElementById('awayInitials').textContent = (awayName || 'TBD').substring(0, 2).toUpperCase();
        
        document.getElementById('homeScoreInput').value = '';
        document.getElementById('awayScoreInput').value = '';
        document.getElementById('setScoresContainer').innerHTML = '';
        setScoreCount = 0;
        
        const form = document.getElementById('quickResultForm');
        form.action = `/competitions/matches/${matchId}/quick-result`;
        
        document.getElementById('quickResultModal').classList.remove('hidden');
    };

    window.closeQuickResultModal = function() {
        document.getElementById('quickResultModal').classList.add('hidden');
        currentMatchId = null;
    };

    window.addSetScore = function() {
        setScoreCount++;
        const container = document.getElementById('setScoresContainer');
        const setDiv = document.createElement('div');
        setDiv.className = 'flex items-center gap-2';
        setDiv.innerHTML = `
            <span class="text-gray-400 text-sm w-16">Set ${setScoreCount}:</span>
            <input type="number" name="sets[${setScoreCount-1}][home]" min="0" placeholder="0"
                   class="w-20 text-center bg-gray-600/50 border border-gray-500 rounded px-2 py-1 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <span class="text-gray-400">-</span>
            <input type="number" name="sets[${setScoreCount-1}][away]" min="0" placeholder="0"
                   class="w-20 text-center bg-gray-600/50 border border-gray-500 rounded px-2 py-1 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-300 ml-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        container.appendChild(setDiv);
    };

    // Knockout Phase Functions
    window.autoGenerateBracket = function() {
        if (!confirm('Da li želite da automatski generišete eliminacionu fazu prema JOOLA pravilima?\n\nOvo će kreirati parove na osnovu plasmana iz grupne faze.')) {
            return;
        }

        // Show loading state
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Generiše se...';

        // Make AJAX request
        fetch(`/organizations/{{ $competition->organization_id }}/competitions/{{ $competition->id }}/auto-generate-bracket`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Reload to show the bracket
            } else {
                alert('Greška: ' + (data.message || 'Nije moguće generisati eliminacionu fazu'));
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Došlo je do greške prilikom generisanja eliminacione faze');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    };

    let knockoutPlayers = [];
    let knockoutMatchCount = 0;
    let playoffMatchCount = 0;
    let qualifiedPlayersData = []; // Store all qualified players with their positions

    window.showManualKnockoutSetup = function() {
        knockoutPlayers = [];
        knockoutMatchCount = 0;
        playoffMatchCount = 0;
        qualifiedPlayersData = [];
        
        // Calculate number of qualified players
        const groupsCount = {{ $competition->tournamentGroups->count() }};
        const advancingPerGroup = {{ $competition->players_advancing_per_group ?? 2 }};
        const totalQualified = groupsCount * advancingPerGroup;
        
        // Collect qualified players data
        const playerElements = document.querySelectorAll('#availablePlayers .player-item');
        playerElements.forEach(el => {
            const playerId = el.dataset.playerId;
            const playerName = el.dataset.playerName;
            const groupInfo = el.querySelector('.text-gray-400').textContent; // e.g., "A-1"
            const [group, position] = groupInfo.split('-');
            
            qualifiedPlayersData.push({
                id: playerId,
                name: playerName,
                group: group,
                position: parseInt(position)
            });
        });
        
        // Generate initial bracket (Round of X)
        generateKnockoutSlots(totalQualified);
        
        // Auto-apply JOOLA suggestion
        applyJoolaSuggestion();
        
        // Show modal
        document.getElementById('manualKnockoutModal').classList.remove('hidden');
    };

    window.closeManualKnockoutModal = function() {
        document.getElementById('manualKnockoutModal').classList.add('hidden');
    };

    window.applyJoolaSuggestion = function() {
        // Clear current assignments
        knockoutPlayers = [];
        
        // Reset all slots
        document.querySelectorAll('.knockout-slot').forEach(slot => {
            if (!slot.dataset.match.startsWith('playoff-')) {
                slot.innerHTML = '<span class="text-gray-400 text-xs">Klikni za odabir...</span>';
                slot.classList.remove('ring-2', 'ring-blue-500', 'ring-green-500');
            }
        });
        
        // Reset player availability
        document.querySelectorAll('#availablePlayers .player-item').forEach(el => {
            el.classList.remove('opacity-50', 'pointer-events-none');
        });
        
        // Sort players by position (1st place, then 2nd place, etc.)
        const sortedPlayers = [...qualifiedPlayersData].sort((a, b) => {
            if (a.position !== b.position) return a.position - b.position;
            return a.group.localeCompare(b.group);
        });
        
        // Separate winners and runners-up
        const winners = sortedPlayers.filter(p => p.position === 1);
        const runnersUp = sortedPlayers.filter(p => p.position === 2);
        
        console.log('Winners:', winners);
        console.log('Runners-up:', runnersUp);
        
        // JOOLA SYSTEM IMPLEMENTATION
        const totalPlayers = winners.length + runnersUp.length;
        const nextPowerOf2 = Math.pow(2, Math.ceil(Math.log2(totalPlayers)));
        const byeCount = nextPowerOf2 - totalPlayers;
        
        console.log(`Total: ${totalPlayers}, Next 2^n: ${nextPowerOf2}, BYE slots: ${byeCount}`);
        
        // Step 1: Seed positions (fixed)
        let bracketPositions = Array(nextPowerOf2).fill(null);
        let availableWinners = [...winners];
        let availableRunnersUp = [...runnersUp];
        
        // JOOLA Rule 1: Group 1 winner → Position A (index 0) - TOP of bracket
        if (availableWinners.length > 0) {
            bracketPositions[0] = availableWinners.shift(); // A1
            console.log('Seed A (position 0):', bracketPositions[0].name, '- Grupa', bracketPositions[0].group);
        }
        
        // JOOLA Rule 1: Group 2 winner → Position B (BOTTOM half, not position 1!)
        // For proper separation, B should be at position (nextPowerOf2 / 2)
        // This ensures they can only meet in the FINAL
        if (availableWinners.length > 0) {
            const bottomSeedPosition = Math.floor(nextPowerOf2 / 2);
            bracketPositions[bottomSeedPosition] = availableWinners.shift(); // B1
            console.log(`Seed B (position ${bottomSeedPosition}):`, bracketPositions[bottomSeedPosition].name, '- Grupa', bracketPositions[bottomSeedPosition].group);
        }
        
        // Step 2: Distribute remaining winners (avoid early collision with seeds)
        // Place them strategically so they don't meet seeds until later rounds
        // Key positions for proper bracket balance:
        const halfBracket = nextPowerOf2 / 2;
        const quarterBracket = nextPowerOf2 / 4;
        
        const winnerPositions = [
            quarterBracket,                    // Position C - quarter mark
            quarterBracket + halfBracket,      // Position D - three-quarter mark
            Math.floor(quarterBracket / 2),    // Position E - eighth mark
            Math.floor(quarterBracket / 2) + halfBracket,  // Position F
            Math.floor(quarterBracket * 3 / 2),            // Position G
            Math.floor(quarterBracket * 3 / 2) + halfBracket // Position H
        ];
        
        let posIndex = 0;
        while (availableWinners.length > 0 && posIndex < winnerPositions.length) {
            const pos = winnerPositions[posIndex];
            if (!bracketPositions[pos]) {
                bracketPositions[pos] = availableWinners.shift();
                console.log(`Winner position ${pos}:`, bracketPositions[pos].name, '- Grupa', bracketPositions[pos].group);
            }
            posIndex++;
        }
        
        // If still have winners left, place them in any empty slot
        for (let i = 0; i < bracketPositions.length && availableWinners.length > 0; i++) {
            if (bracketPositions[i] === null) {
                bracketPositions[i] = availableWinners.shift();
                console.log(`Extra winner position ${i}:`, bracketPositions[i].name, '- Grupa', bracketPositions[i].group);
            }
        }
        
        // Step 3: Place runners-up in remaining positions
        // JOOLA Rule 3: Avoid same group opponents in first round
        for (let i = 0; i < bracketPositions.length && availableRunnersUp.length > 0; i++) {
            if (bracketPositions[i] === null) {
                // Find opponent position (bracket pairing)
                const opponentIndex = getOpponentIndex(i, nextPowerOf2);
                const opponent = bracketPositions[opponentIndex];
                
                if (opponent) {
                    // Try to avoid same group
                    const safeRunner = availableRunnersUp.find(r => r.group !== opponent.group);
                    if (safeRunner) {
                        bracketPositions[i] = safeRunner;
                        availableRunnersUp = availableRunnersUp.filter(r => r.id !== safeRunner.id);
                    } else {
                        // No choice, place any
                        bracketPositions[i] = availableRunnersUp.shift();
                    }
                } else {
                    // No opponent yet, place any
                    bracketPositions[i] = availableRunnersUp.shift();
                }
            }
        }
        
        // Step 4: Fill BYE slots (remaining nulls get BYE - they auto-advance)
        // Prioritize giving BYEs to seeds (positions 0 and last are already filled)
        
        // Step 5: Create matches from bracket positions
        // JOOLA Standard bracket pairing: A vs H, B vs G, C vs F, D vs E
        // This means: 1-8, 2-7, 3-6, 4-5 for 8 players
        // For 16: 1-16, 2-15, 3-14, 4-13, 5-12, 6-11, 7-10, 8-9
        let matchIndex = 1;
        // halfBracket already declared above, reusing
        
        for (let i = 0; i < halfBracket; i++) {
            // JOOLA pairing formula: position i pairs with position (totalSlots - 1 - i)
            const homeIndex = i;
            const awayIndex = nextPowerOf2 - 1 - i;
            
            const homePlayer = bracketPositions[homeIndex];
            const awayPlayer = bracketPositions[awayIndex];
            
            // Only create match if at least one player exists (skip BYE vs BYE)
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
        
        console.log('Bracket positions:', bracketPositions);
        console.log('\n=== FIRST ROUND MATCHES (JOOLA Format: A vs H, B vs G...) ===');
        for (let i = 0; i < halfBracket; i++) {
            const home = bracketPositions[i];
            const away = bracketPositions[nextPowerOf2 - 1 - i];
            const homeLabel = String.fromCharCode(65 + i); // A, B, C, D...
            const awayLabel = String.fromCharCode(65 + (nextPowerOf2 - 1 - i));
            const homeDesc = home ? `${home.name} (${home.group}${home.position})` : 'BYE';
            const awayDesc = away ? `${away.name} (${away.group}${away.position})` : 'BYE';
            console.log(`  Match ${i+1}: ${homeLabel} ${homeDesc} vs ${awayLabel} ${awayDesc}`);
        }
        console.log('\nAssigned players:', knockoutPlayers);
    };
    
    // Helper: Get opponent bracket index for first round
    // JOOLA System: A vs H, B vs G, C vs F, D vs E
    // Formula: position i pairs with (totalSlots - 1 - i)
    function getOpponentIndex(index, totalSlots) {
        return totalSlots - 1 - index;
    }

    window.assignPlayerToSlot = function(player, matchIndex, position) {
        const slot = document.querySelector(`.knockout-slot[data-match="${matchIndex}"][data-position="${position}"]`);
        if (!slot) return;
        
        slot.innerHTML = `
            <div class="w-7 h-7 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold text-xs">${player.name.substring(0, 2)}</span>
            </div>
            <span class="text-white font-medium text-sm truncate">${player.name}</span>
        `;
        
        // Store assignment
        knockoutPlayers.push({
            playerId: player.id,
            playerName: player.name,
            match: matchIndex,
            position: position,
            isPlayoff: false
        });
        
        // Mark player as used
        const playerElement = document.querySelector(`#availablePlayers .player-item[data-player-id="${player.id}"]`);
        if (playerElement) {
            playerElement.classList.add('opacity-50', 'pointer-events-none');
        }
    };

    window.generateKnockoutSlots = function(playerCount) {
        const container = document.getElementById('knockoutMatchesContainer');
        container.innerHTML = '';
        
        knockoutMatchCount = Math.floor(playerCount / 2);
        
        // Determine round name
        let roundName = 'Eliminacija';
        if (knockoutMatchCount === 8) roundName = 'Osmina';
        else if (knockoutMatchCount === 4) roundName = 'Četvrtfinale';
        else if (knockoutMatchCount === 2) roundName = 'Polufinale';
        else if (knockoutMatchCount === 1) roundName = 'Finale';
        
        for (let i = 1; i <= knockoutMatchCount; i++) {
            const matchDiv = document.createElement('div');
            matchDiv.className = 'bg-gray-700/30 rounded-lg p-2.5 border border-gray-500/30';
            matchDiv.dataset.matchIndex = i;
            matchDiv.innerHTML = `
                <div class="flex items-center justify-between mb-2">
                    <h5 class="text-white font-medium text-sm">Meč ${i}</h5>
                    <span class="text-xs text-gray-400">${roundName}</span>
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center space-x-2 p-2 bg-gray-600/30 rounded-lg min-h-[40px] knockout-slot" 
                         data-match="${i}" data-position="home" 
                         onclick="toggleSlotSelection(this)">
                        <span class="text-gray-400 text-xs">Klikni za odabir...</span>
                    </div>
                    <div class="text-center text-gray-500 text-xs">vs</div>
                    <div class="flex items-center space-x-2 p-2 bg-gray-600/30 rounded-lg min-h-[40px] knockout-slot" 
                         data-match="${i}" data-position="away" 
                         onclick="toggleSlotSelection(this)">
                        <span class="text-gray-400 text-xs">Klikni za odabir...</span>
                    </div>
                </div>
            `;
            container.appendChild(matchDiv);
        }
    };

    let selectedSlot = null;

    window.toggleSlotSelection = function(slotElement) {
        // If clicking the same slot again, deselect it
        if (selectedSlot === slotElement) {
            slotElement.classList.remove('ring-2', 'ring-blue-500', 'ring-green-500');
            selectedSlot = null;
            return;
        }
        
        // Deselect all slots
        document.querySelectorAll('.knockout-slot').forEach(slot => {
            slot.classList.remove('ring-2', 'ring-blue-500', 'ring-green-500');
        });
        
        // Select this slot
        const hasPlayer = slotElement.querySelector('.font-bold'); // Check if slot has player
        if (hasPlayer) {
            slotElement.classList.add('ring-2', 'ring-green-500'); // Green for occupied slots
        } else {
            slotElement.classList.add('ring-2', 'ring-blue-500'); // Blue for empty slots
        }
        selectedSlot = slotElement;
    };

    window.selectPlayerForKnockout = function(playerId, playerName, element) {
        if (!selectedSlot) {
            alert('Prvo odaberite poziciju u eliminacionoj fazi (kliknite na prazan slot)');
            return;
        }

        // Check if player already assigned
        const existingAssignment = knockoutPlayers.find(p => p.playerId === playerId);
        if (existingAssignment) {
            // Remove from old position
            knockoutPlayers = knockoutPlayers.filter(p => p.playerId !== playerId);
            
            // Clear old slot
            const oldSlot = document.querySelector(`.knockout-slot[data-match="${existingAssignment.match}"][data-position="${existingAssignment.position}"]`);
            if (oldSlot && !oldSlot.dataset.match.startsWith('playoff-')) {
                oldSlot.innerHTML = '<span class="text-gray-400 text-xs">Klikni za odabir...</span>';
            }
        }
        
        // Check if selected slot already has a player
        const slotMatch = selectedSlot.dataset.match;
        const slotPosition = selectedSlot.dataset.position;
        const existingInSlot = knockoutPlayers.find(p => p.match == slotMatch && p.position === slotPosition);
        
        if (existingInSlot) {
            // Free up the player that was in this slot
            const oldPlayerElement = document.querySelector(`#availablePlayers .player-item[data-player-id="${existingInSlot.playerId}"]`);
            if (oldPlayerElement) {
                oldPlayerElement.classList.remove('opacity-50', 'pointer-events-none');
            }
            // Remove from assignments
            knockoutPlayers = knockoutPlayers.filter(p => !(p.match == slotMatch && p.position === slotPosition));
        }

        // Add player to slot
        const match = selectedSlot.dataset.match;
        const position = selectedSlot.dataset.position;
        
        selectedSlot.innerHTML = `
            <div class="w-7 h-7 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold text-xs">${playerName.substring(0, 2)}</span>
            </div>
            <span class="text-white font-medium text-sm truncate">${playerName}</span>
        `;
        selectedSlot.classList.remove('ring-2', 'ring-blue-500');
        
        // Store assignment
        knockoutPlayers.push({
            playerId: playerId,
            playerName: playerName,
            match: match,
            position: position,
            isPlayoff: false
        });

        // Mark player as used in available list
        element.classList.add('opacity-50', 'pointer-events-none');
        
        selectedSlot = null;
    };

    window.addPlayoffMatch = function() {
        playoffMatchCount++;
        const container = document.getElementById('knockoutMatchesContainer');
        
        const matchDiv = document.createElement('div');
        matchDiv.className = 'bg-yellow-600/20 rounded-lg p-2.5 border-2 border-yellow-500/50 mt-3';
        matchDiv.dataset.playoffIndex = playoffMatchCount;
        matchDiv.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <h5 class="text-yellow-400 font-medium text-sm">🏆 Playoff ${playoffMatchCount}</h5>
                <button onclick="removePlayoffMatch(this)" class="text-red-400 hover:text-red-300 text-xs">
                    ❌ Ukloni
                </button>
            </div>
            <div class="mb-2">
                <input type="text" placeholder="Naziv (npr. 3. mjesto)" 
                       class="w-full px-2 py-1.5 text-sm bg-gray-600/50 border border-gray-500 rounded-lg text-white"
                       id="playoff-name-${playoffMatchCount}">
            </div>
            <div class="space-y-1.5">
                <div class="flex items-center space-x-2 p-2 bg-gray-600/30 rounded-lg min-h-[40px] knockout-slot" 
                     data-match="playoff-${playoffMatchCount}" data-position="home" 
                     onclick="toggleSlotSelection(this)">
                    <span class="text-gray-400 text-xs">Klikni za odabir...</span>
                </div>
                <div class="text-center text-gray-500 text-xs">vs</div>
                <div class="flex items-center space-x-2 p-2 bg-gray-600/30 rounded-lg min-h-[40px] knockout-slot" 
                     data-match="playoff-${playoffMatchCount}" data-position="away" 
                     onclick="toggleSlotSelection(this)">
                    <span class="text-gray-400 text-xs">Klikni za odabir...</span>
                </div>
            </div>
        `;
        container.appendChild(matchDiv);
    };

    window.removePlayoffMatch = function(button) {
        const matchDiv = button.closest('[data-playoff-index]');
        matchDiv.remove();
    };

    window.saveManualKnockout = function() {
        // Validate all main bracket slots are filled
        const totalSlots = knockoutMatchCount * 2;
        const mainBracketPlayers = knockoutPlayers.filter(p => !p.isPlayoff);
        
        if (mainBracketPlayers.length < totalSlots) {
            alert(`Molimo popunite sve pozicije u eliminacionoj fazi (${mainBracketPlayers.length}/${totalSlots})`);
            return;
        }

        // Prepare data
        const bracketData = {
            matches: knockoutPlayers,
            playoffMatches: []
        };

        // Add playoff matches
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

        // Show loading
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Čuva se...';

        // Make AJAX request
        fetch(`/organizations/{{ $competition->organization_id }}/competitions/{{ $competition->id }}/save-manual-bracket`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(bracketData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Greška: ' + (data.message || 'Nije moguće sačuvati eliminacionu fazu'));
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Došlo je do greške prilikom čuvanja eliminacione faze');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    };

    // Reset Group Phase
    window.confirmResetGroupPhase = function() {
        if (confirm('⚠️ Da li ste sigurni da želite resetovati grupnu fazu?\n\nOvo će:\n- Obrisati sve rezultate grupnih mečeva\n- Resetovati tabele\n- Obrisati knockout fazu\n\nOva akcija se ne može poništiti!')) {
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '⏳ Resetujem...';
            
            fetch(`/organizations/{{ $organization->id }}/competitions/{{ $competition->id }}/reset-group-phase`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Greška: ' + (data.message || 'Nepoznata greška'));
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Došlo je do greške prilikom resetovanja grupne faze');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    };

    // Reset Knockout Phase
    window.confirmResetKnockoutPhase = function() {
        if (confirm('⚠️ Da li ste sigurni da želite resetovati eliminacionu fazu?\n\nOvo će:\n- Obrisati sve mečeve eliminacione faze\n- Obrisati sve rezultate\n- Vrati turnir u grupnu fazu\n\nOva akcija se ne može poništiti!')) {
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '⏳ Resetujem...';
            
            fetch(`/organizations/{{ $organization->id }}/competitions/{{ $competition->id }}/reset-knockout-phase`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Greška: ' + (data.message || 'Nepoznata greška'));
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Došlo je do greške prilikom resetovanja eliminacione faze');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    };

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('quickResultModal').classList.contains('hidden')) {
                closeQuickResultModal();
            }
            if (!document.getElementById('manualKnockoutModal').classList.contains('hidden')) {
                closeManualKnockoutModal();
            }
        }
    });
</script>
