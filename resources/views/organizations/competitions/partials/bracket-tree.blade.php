{{-- Bracket Tree Visualization Component --}}
<div class="bg-white rounded-lg shadow-sm p-4 mt-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-base font-semibold flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Ždrijeb eliminacione faze
        </h3>
        
        <div class="text-xs text-gray-600 flex gap-2">
            <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-green-50 border border-green-200 rounded">
                <span class="w-2 h-2 bg-green-500 rounded"></span>
                Pobjednici
            </span>
            <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-yellow-50 border border-yellow-200 rounded">
                <span class="w-2 h-2 bg-yellow-500 rounded"></span>
                Drugoplasirani
            </span>
        </div>
    </div>
    
    <p class="text-xs text-gray-600 mb-3">
        Ždrijeb se automatski ažurira dok birate igrače. A1 i B1 su u suprotnim polovinama (mogu se sresti samo u finalu).
    </p>
    
    <div id="bracketTreeContainer" class="overflow-x-auto">
        <div id="bracketTree" class="min-w-max"></div>
    </div>
</div>

<style>
.bracket-tree {
    display: flex;
    gap: 80px;
    padding: 20px;
    align-items: center;
}

.bracket-round {
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    min-height: 100%;
}

.bracket-round-title {
    text-align: center;
    font-weight: 600;
    font-size: 0.8rem;
    color: #4B5563;
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.bracket-match {
    display: flex;
    flex-direction: column;
    gap: 2px;
    margin: 10px 0;
    position: relative;
}

.bracket-player {
    background: white;
    border: 2px solid #E5E7EB;
    border-radius: 6px;
    padding: 8px 14px;
    min-width: 200px;
    font-size: 0.8rem;
    transition: all 0.2s;
    position: relative;
}

.bracket-player.winner-slot {
    border-color: #10B981;
    background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%);
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.15);
}

.bracket-player.runner-up-slot {
    border-color: #F59E0B;
    background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%);
    box-shadow: 0 2px 6px rgba(245, 158, 11, 0.15);
}

.bracket-player.bye-slot {
    border-color: #9CA3AF;
    background: #F9FAFB;
    color: #6B7280;
    font-style: italic;
}

.bracket-player.empty-slot {
    border-style: dashed;
    color: #9CA3AF;
    cursor: pointer;
}

.bracket-player.empty-slot:hover {
    border-color: #3B82F6;
    background: #EFF6FF;
}

.bracket-player:hover:not(.bye-slot) {
    border-color: #3B82F6;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
    transform: translateY(-1px);
    cursor: pointer;
}

.player-name {
    font-weight: 600;
    color: #111827;
    font-size: 0.8rem;
}

.player-group {
    font-size: 0.7rem;
    color: #6B7280;
    margin-top: 2px;
    font-weight: 500;
}

.bracket-connector {
    position: absolute;
    border: 1.5px solid #D1D5DB;
}

/* Vertical connectors between home and away */
.bracket-connector-vertical {
    left: 100%;
    top: 50%;
    width: 40px;
    height: 0;
    border-top: 1.5px solid #D1D5DB;
}

/* Horizontal connectors to next round */
.bracket-connector-horizontal {
    position: absolute;
    right: -80px;
    top: 50%;
    width: 80px;
    height: 2px;
    background: #D1D5DB;
}

.match-number {
    position: absolute;
    top: -16px;
    left: 0;
    font-size: 0.7rem;
    font-weight: 700;
    color: #6B7280;
}

/* Spacing adjustments based on round */
.bracket-round.round-1 .bracket-match {
    margin: 5px 0;
}

.bracket-round.round-2 .bracket-match {
    margin: 22px 0;
}

.bracket-round.round-3 .bracket-match {
    margin: 56px 0;
}

.bracket-round.round-4 .bracket-match {
    margin: 124px 0;
}

.bracket-round.finals .bracket-match {
    margin: 200px 0;
}
</style>

<script>
function renderBracketTree() {
    const container = document.getElementById('bracketTree');
    if (!container) return;
    
    // Get current knockout assignments
    const matches = [];
    let matchIndex = 1;
    
    document.querySelectorAll('.knockout-slot').forEach((slot, index) => {
        if (slot.dataset.match.startsWith('playoff-')) return; // Skip playoff matches
        
        const matchNum = Math.floor(index / 2) + 1;
        const isHome = index % 2 === 0;
        
        if (!matches[matchNum - 1]) {
            matches[matchNum - 1] = { number: matchNum, home: null, away: null };
        }
        
        const playerData = slot.textContent.trim();
        const isBye = playerData === 'BYE';
        const isEmpty = playerData === 'Klikni za odabir...';
        
        if (isHome) {
            matches[matchNum - 1].home = {
                name: isEmpty ? 'TBD' : playerData,
                isBye: isBye,
                isEmpty: isEmpty,
                isWinner: slot.classList.contains('has-winner'),
                isRunnerUp: slot.classList.contains('has-runner-up')
            };
        } else {
            matches[matchNum - 1].away = {
                name: isEmpty ? 'TBD' : playerData,
                isBye: isBye,
                isEmpty: isEmpty,
                isWinner: slot.classList.contains('has-winner'),
                isRunnerUp: slot.classList.contains('has-runner-up')
            };
        }
    });
    
    // Determine rounds structure - SIMPLE APPROACH
    // In setup mode, show all matches as first round only
    const totalMatches = matches.length;
    
    // Determine the round name based on total match count
    let roundTitle = 'Eliminaciona faza';
    
    if (totalMatches === 1) {
        roundTitle = 'Finale';
    } else if (totalMatches === 2) {
        roundTitle = 'Polufinale';
    } else if (totalMatches <= 4) {
        roundTitle = 'Četvrtfinale';
    } else if (totalMatches <= 8) {
        roundTitle = 'Osmina finala';
    } else if (totalMatches <= 16) {
        roundTitle = 'Šesnaestina finala';
    }
    
    const rounds = [{
        round: 1,
        matches: matches,
        title: roundTitle
    }];
    
    // Render bracket tree
    let html = '<div class="bracket-tree">';
    
    rounds.forEach((round, roundIndex) => {
        html += `
            <div class="bracket-round round-${round.round}">
                <div class="bracket-round-title">${round.title}</div>
        `;
        
        round.matches.forEach((match, matchIndex) => {
            const homeSlot = document.querySelector(`.knockout-slot[data-match="${match.number}"][data-position="home"]`);
            const awaySlot = document.querySelector(`.knockout-slot[data-match="${match.number}"][data-position="away"]`);
            
            html += `
                <div class="bracket-match">
                    <div class="match-number">Meč ${match.number}</div>
                    ${renderPlayer(match.home, 'home', match.number, homeSlot)}
                    ${renderPlayer(match.away, 'away', match.number, awaySlot)}
                    ${roundIndex < rounds.length - 1 ? '<div class="bracket-connector-horizontal"></div>' : ''}
                </div>
            `;
        });
        
        html += '</div>';
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function renderPlayer(player, position, matchNumber, slotElement) {
    if (!player) return '<div class="bracket-player empty-slot">TBD</div>';
    
    let classes = ['bracket-player'];
    let onclick = '';
    
    if (player.isBye) {
        classes.push('bye-slot');
    } else if (player.isEmpty) {
        classes.push('empty-slot');
        // Make empty slots clickable to select from tree
        onclick = `onclick="selectSlotFromTree(${matchNumber}, '${position}')"`;
    } else {
        if (player.isWinner) classes.push('winner-slot');
        if (player.isRunnerUp) classes.push('runner-up-slot');
        // Make filled slots clickable to change selection
        onclick = `onclick="selectSlotFromTree(${matchNumber}, '${position}')"`;
    }
    
    let content = player.name;
    if (player.isBye) {
        content = 'BYE (Prolazi dalje)';
    }
    
    // Try to extract group info from player name
    if (!player.isBye && !player.isEmpty) {
        const match = player.name.match(/🥇|🥈/);
        if (match) {
            // Already has icon, just use as is
            content = `<div class="player-name">${player.name}</div>`;
        } else {
            content = `<div class="player-name">${content}</div>`;
        }
    }
    
    return `<div class="${classes.join(' ')}" ${onclick}>${content}</div>`;
}

function getRoundTitle(round, totalRounds, matchCount) {
    // Determine title based on distance from final
    const roundsFromEnd = totalRounds - round;
    
    if (roundsFromEnd === 0) return 'Finale';
    if (roundsFromEnd === 1) return 'Polufinale';
    if (roundsFromEnd === 2) return 'Četvrtfinale';
    if (roundsFromEnd === 3) return 'Osmina finala';
    if (roundsFromEnd === 4) return 'Šesnaestina finala';
    
    // If we're in early rounds with many matches, use match count
    if (matchCount >= 16) return 'Šesnaestina finala';
    if (matchCount >= 8) return 'Osmina finala';
    
    return `${round}. Runda`;
}

// Auto-render when JOOLA suggestion is applied
const originalApplyJoola = window.applyJoolaSuggestion;
if (originalApplyJoola) {
    window.applyJoolaSuggestion = function() {
        originalApplyJoola.call(this);
        setTimeout(() => renderBracketTree(), 100); // Delay to ensure DOM is updated
    };
}

// Also render when players are manually selected
const originalSelectPlayer = window.selectPlayerForKnockout;
if (originalSelectPlayer) {
    window.selectPlayerForKnockout = function(playerId) {
        originalSelectPlayer.call(this, playerId);
        setTimeout(() => renderBracketTree(), 50);
    };
}

// Initial render on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => renderBracketTree(), 200);
});
</script>
