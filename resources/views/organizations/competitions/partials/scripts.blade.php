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

    window.openQuickResultModal = function(matchId, homeName, awayName, homeScore, awayScore, existingSets, playedAt, venueId) {
        currentMatchId = matchId;

        // Set match ID in hidden input
        document.getElementById('quickMatchId').value = matchId;

        // Set scroll position
        const scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
        document.getElementById('scrollPosition').value = scrollPosition;

        document.getElementById('homePlayerName').textContent = homeName;
        document.getElementById('awayPlayerName').textContent = awayName;
        document.getElementById('forfeitHomeOption').textContent = homeName;
        document.getElementById('forfeitAwayOption').textContent = awayName;

        document.getElementById('homeScoreInput').value = (homeScore ?? '') === null ? '' : (homeScore ?? '');
        document.getElementById('awayScoreInput').value = (awayScore ?? '') === null ? '' : (awayScore ?? '');
        document.getElementById('quickPlayedAt').value = playedAt || '';
        const venueSelect = document.getElementById('quickVenueId');
        if (venueSelect) venueSelect.value = venueId || '';
        document.getElementById('setScoresContainer').innerHTML = '';
        setScoreCount = 0;

        // Reset WO/forfeit state - always starts unchecked, score fields visible
        document.getElementById('quickForfeitToggle').checked = false;
        document.getElementById('quickForfeitedBy').value = '';
        toggleForfeitFields();

        // Ako mec vec ima setove (editovanje zavrsenog meca), popuni ih; inace
        // ostavi prazno - polja za setove ostaju opciona.
        if (Array.isArray(existingSets) && existingSets.length > 0) {
            existingSets.forEach(set => addSetScore(set.home, set.away));
        }

        const form = document.getElementById('quickResultForm');
        form.action = `/competitions/matches/${matchId}/quick-result`;

        document.getElementById('quickResultModal').classList.remove('hidden');
    };

    // Toggles between manual score entry and WO/forfeit mode - a forfeited
    // match has no sets to play, so the score section is hidden and its
    // inputs stop being required, while the "who forfeited" select becomes
    // the required field instead.
    window.toggleForfeitFields = function() {
        const isForfeit = document.getElementById('quickForfeitToggle').checked;
        const scoreSection = document.getElementById('quickScoreSection');
        const setsSection = document.getElementById('quickSetsSection');
        const forfeitSelectWrap = document.getElementById('quickForfeitSelectWrap');
        const forfeitedBySelect = document.getElementById('quickForfeitedBy');
        const homeScoreInput = document.getElementById('homeScoreInput');
        const awayScoreInput = document.getElementById('awayScoreInput');

        scoreSection.classList.toggle('hidden', isForfeit);
        setsSection.classList.toggle('hidden', isForfeit);
        forfeitSelectWrap.classList.toggle('hidden', !isForfeit);

        homeScoreInput.required = !isForfeit;
        awayScoreInput.required = !isForfeit;
        forfeitedBySelect.required = isForfeit;

        if (!isForfeit) {
            forfeitedBySelect.value = '';
        }
    };

    window.closeQuickResultModal = function() {
        document.getElementById('quickResultModal').classList.add('hidden');
        currentMatchId = null;
    };

    window.addSetScore = function(homeValue, awayValue) {
        setScoreCount++;
        const container = document.getElementById('setScoresContainer');
        const setDiv = document.createElement('div');
        setDiv.className = 'flex items-center gap-2 bg-gray-800 p-1.5 rounded-xl border border-white/5';
        setDiv.innerHTML = `
            <span class="text-[9px] font-black text-gray-500 w-5 pl-1 shrink-0">${setScoreCount}</span>
            <div class="flex items-center gap-1.5 flex-1">
                <input type="number" name="sets[${setScoreCount-1}][home]" min="0" placeholder="0"
                       value="${homeValue ?? ''}"
                       class="w-full bg-gray-900/60 border-none rounded-lg p-1.5 text-center font-black text-white outline-none text-xs focus:ring-2 focus:ring-blue-500/40">
                <span class="text-gray-700 font-black text-[10px] shrink-0">:</span>
                <input type="number" name="sets[${setScoreCount-1}][away]" min="0" placeholder="0"
                       value="${awayValue ?? ''}"
                       class="w-full bg-gray-900/60 border-none rounded-lg p-1.5 text-center font-black text-white outline-none text-xs focus:ring-2 focus:ring-blue-500/40">
            </div>
            <button type="button" onclick="this.closest('.flex').remove()" class="text-gray-500 hover:text-red-400 transition-colors shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
