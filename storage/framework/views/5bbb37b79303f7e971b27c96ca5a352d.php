
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

    window.openQuickResultModal = function(matchId, homeName, awayName) {
        console.log('Opening quick result modal for match:', matchId, homeName, 'vs', awayName);
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

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('quickResultModal').classList.contains('hidden')) {
                closeQuickResultModal();
            }
        }
    });
</script>
<?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/competitions/partials/scripts.blade.php ENDPATH**/ ?>