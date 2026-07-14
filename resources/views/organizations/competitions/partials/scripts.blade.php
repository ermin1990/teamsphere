{{-- Scripts - Placeholder --}}
<script>
    let currentMatchId = null;
    let setScoreCount = 0;

    // Restore scroll position on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Check for saved scroll position from match editing
        const scrollPosition = sessionStorage.getItem('scrollPosition');
        if (scrollPosition) {
            window.scrollTo(0, parseInt(scrollPosition));
            // Clear the saved position after restoring
            sessionStorage.removeItem('scrollPosition');
        }
    });

    window.openQuickResultModal = function(matchId, homeName, awayName, homeScore, awayScore, existingSets) {
        currentMatchId = matchId;

        // Set match ID in hidden input
        document.getElementById('quickMatchId').value = matchId;

        // Set scroll position
        const scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
        document.getElementById('scrollPosition').value = scrollPosition;

        document.getElementById('homePlayerName').textContent = homeName;
        document.getElementById('awayPlayerName').textContent = awayName;
        document.getElementById('homeInitials').textContent = (homeName || 'TBD').substring(0, 2).toUpperCase();
        document.getElementById('awayInitials').textContent = (awayName || 'TBD').substring(0, 2).toUpperCase();

        document.getElementById('homeScoreInput').value = (homeScore ?? '') === null ? '' : (homeScore ?? '');
        document.getElementById('awayScoreInput').value = (awayScore ?? '') === null ? '' : (awayScore ?? '');
        document.getElementById('setScoresContainer').innerHTML = '';
        setScoreCount = 0;

        // Ako mec vec ima setove (editovanje zavrsenog meca), popuni ih; inace
        // ostavi prazno - polja za setove ostaju opciona.
        if (Array.isArray(existingSets) && existingSets.length > 0) {
            existingSets.forEach(set => addSetScore(set.home, set.away));
        }

        const form = document.getElementById('quickResultForm');
        form.action = `/competitions/matches/${matchId}/quick-result`;

        document.getElementById('quickResultModal').classList.remove('hidden');
    };

    window.closeQuickResultModal = function() {
        document.getElementById('quickResultModal').classList.add('hidden');
        currentMatchId = null;
    };

    window.addSetScore = function(homeValue, awayValue) {
        setScoreCount++;
        const container = document.getElementById('setScoresContainer');
        const setDiv = document.createElement('div');
        setDiv.className = 'flex items-center gap-2';
        setDiv.innerHTML = `
            <span class="text-gray-400 text-sm w-16">Set ${setScoreCount}:</span>
            <input type="number" name="sets[${setScoreCount-1}][home]" min="0" placeholder="gemova"
                   value="${homeValue ?? ''}"
                   class="w-20 text-center bg-gray-600/50 border border-gray-500 rounded px-2 py-1 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <span class="text-gray-400">-</span>
            <input type="number" name="sets[${setScoreCount-1}][away]" min="0" placeholder="gemova"
                   value="${awayValue ?? ''}"
                   class="w-20 text-center bg-gray-600/50 border border-gray-500 rounded px-2 py-1 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-300 ml-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        container.appendChild(setDiv);
    };

    // Kad se unese konacan rezultat (npr. 2-1), ponudi da automatski napravi
    // taj broj praznih redova za unos gemova po setu - nije obavezno, samo
    // stedi klikanje na "Dodaj set" rucno. Ne dira redove koji vec postoje.
    window.syncSetScoreRows = function() {
        const home = parseInt(document.getElementById('homeScoreInput').value, 10);
        const away = parseInt(document.getElementById('awayScoreInput').value, 10);
        if (isNaN(home) || isNaN(away)) return;

        const desired = home + away;
        if (desired <= 0 || desired > 10) return;

        while (setScoreCount < desired) {
            addSetScore();
        }
    };

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('quickResultModal').classList.contains('hidden')) {
                closeQuickResultModal();
            }
        }
    });
</script>
