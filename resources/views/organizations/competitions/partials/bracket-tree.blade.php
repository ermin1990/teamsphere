{{-- Bracket Tree Visualization Component --}}
<div class="bg-white rounded-lg shadow-sm p-6 mt-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Eliminaciona faza - Ždrijeb (Tournament Bracket)
        </h3>
        
        <div class="text-sm text-gray-600">
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-50 border border-green-200 rounded-lg mr-2">
                <span class="w-3 h-3 bg-green-500 rounded"></span>
                Pobjednik grupe
            </span>
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-50 border border-yellow-200 rounded-lg">
                <span class="w-3 h-3 bg-yellow-500 rounded"></span>
                Drugoplasirani
            </span>
        </div>
    </div>
    
    <p class="text-sm text-gray-600 mb-4">
        Ždrijeb se automatski ažurira dok birate igrače iznad. Zeleno označeni su pobjednici grupa, žuto su drugoplasirani. 
        Obratite pažnju na raspodjelu prema JOOLA pravilima - A1 i B1 su postavljeni u suprotne polovine da se mogu sresti samo u finalu.
    </p>
    
    <div id="bracketTreeContainer" class="overflow-x-auto">
        <div id="bracketTree" class="min-w-max"></div>
    </div>
</div>

<style>
.bracket-tree {
    display: flex;
    gap: 60px;
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
    font-size: 0.875rem;
    color: #4B5563;
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.bracket-match {
    display: flex;
    flex-direction: column;
    gap: 2px;
    margin: 8px 0;
    position: relative;
}

.bracket-player {
    background: white;
    border: 1.5px solid #E5E7EB;
    border-radius: 6px;
    padding: 10px 16px;
    min-width: 180px;
    font-size: 0.875rem;
    transition: all 0.2s;
    position: relative;
}

.bracket-player.winner-slot {
    border-color: #10B981;
    background: #ECFDF5;
}

.bracket-player.runner-up-slot {
    border-color: #F59E0B;
    background: #FFFBEB;
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
}

.bracket-player:hover:not(.bye-slot):not(.empty-slot) {
    border-color: #3B82F6;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
    transform: translateY(-1px);
}

.player-name {
    font-weight: 500;
    color: #111827;
}

.player-group {
    font-size: 0.75rem;
    color: #6B7280;
    margin-top: 2px;
}

.bracket-connector {
    position: absolute;
    border: 1.5px solid #D1D5DB;
}

/* Vertical connectors between home and away */
.bracket-connector-vertical {
    left: 100%;
    top: 50%;
    width: 30px;
    height: 0;
    border-top: 1.5px solid #D1D5DB;
}

/* Horizontal connectors to next round */
.bracket-connector-horizontal {
    position: absolute;
    right: -60px;
    top: 50%;
    width: 60px;
    height: 2px;
    background: #D1D5DB;
}

.match-number {
    position: absolute;
    top: -18px;
    left: 0;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6B7280;
}

/* Spacing adjustments based on round */
.bracket-round.round-1 .bracket-match {
    margin: 4px 0;
}

.bracket-round.round-2 .bracket-match {
    margin: 20px 0;
}

.bracket-round.round-3 .bracket-match {
    margin: 52px 0;
}

.bracket-round.round-4 .bracket-match {
    margin: 116px 0;
}

.bracket-round.finals .bracket-match {
    margin: 180px 0;
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
                isWinner: slot.classList.contains('border-green-500'),
                isRunnerUp: slot.classList.contains('border-yellow-500')
            };
        } else {
            matches[matchNum - 1].away = {
                name: isEmpty ? 'TBD' : playerData,
                isBye: isBye,
                isEmpty: isEmpty,
                isWinner: slot.classList.contains('border-green-500'),
                isRunnerUp: slot.classList.contains('border-yellow-500')
            };
        }
    });
    
    // Determine rounds structure
    const totalMatches = matches.length;
    const rounds = [];
    let matchesInRound = Math.ceil(totalMatches / 2);
    let currentMatchIndex = 0;
    let roundNum = 1;
    
    // Calculate total rounds
    const totalRounds = Math.ceil(Math.log2(matches.length * 2));
    
    // Distribute matches into rounds
    for (let r = 0; r < totalRounds && currentMatchIndex < matches.length; r++) {
        const roundMatches = [];
        for (let m = 0; m < matchesInRound && currentMatchIndex < matches.length; m++) {
            roundMatches.push(matches[currentMatchIndex]);
            currentMatchIndex++;
        }
        
        if (roundMatches.length > 0) {
            rounds.push({
                round: roundNum,
                matches: roundMatches,
                title: getRoundTitle(roundNum, totalRounds, roundMatches.length)
            });
            roundNum++;
        }
        
        matchesInRound = Math.ceil(matchesInRound / 2);
    }
    
    // Render bracket tree
    let html = '<div class="bracket-tree">';
    
    rounds.forEach((round, roundIndex) => {
        html += `
            <div class="bracket-round round-${round.round}">
                <div class="bracket-round-title">${round.title}</div>
        `;
        
        round.matches.forEach((match, matchIndex) => {
            html += `
                <div class="bracket-match">
                    <div class="match-number">Meč ${match.number}</div>
                    ${renderPlayer(match.home, 'home')}
                    ${renderPlayer(match.away, 'away')}
                    ${roundIndex < rounds.length - 1 ? '<div class="bracket-connector-horizontal"></div>' : ''}
                </div>
            `;
        });
        
        html += '</div>';
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function renderPlayer(player, position) {
    if (!player) return '<div class="bracket-player empty-slot">TBD</div>';
    
    let classes = ['bracket-player'];
    if (player.isBye) classes.push('bye-slot');
    else if (player.isEmpty) classes.push('empty-slot');
    else if (player.isWinner) classes.push('winner-slot');
    else if (player.isRunnerUp) classes.push('runner-up-slot');
    
    let content = player.name;
    if (player.isBye) {
        content = 'BYE (Prolazi dalje)';
    }
    
    // Try to extract group info from player name
    let groupBadge = '';
    if (!player.isBye && !player.isEmpty) {
        const match = player.name.match(/\(Grupa ([A-Z])\)/);
        if (match) {
            const cleanName = player.name.replace(/\(Grupa [A-Z]\)/, '').trim();
            const groupLabel = player.isWinner ? '🥇 Grupa' : '🥈 Grupa';
            groupBadge = `<div class="player-group">${groupLabel} ${match[1]}</div>`;
            content = `<div class="player-name">${cleanName}</div>${groupBadge}`;
        } else {
            content = `<div class="player-name">${content}</div>`;
        }
    }
    
    return `<div class="${classes.join(' ')}">${content}</div>`;
}

function getRoundTitle(round, totalRounds, matchCount) {
    if (round === totalRounds) return 'Finale';
    if (round === totalRounds - 1) return 'Polufinale';
    if (round === totalRounds - 2) return 'Četvrtfinale';
    if (matchCount === 8) return 'Osmina finala';
    if (matchCount === 16) return 'Šesnaestina finala';
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
