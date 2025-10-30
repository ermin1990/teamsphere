
<?php
    $knockoutMatches = isset($knockoutMatches) ? $knockoutMatches : collect();
    $isOwner = isset($isOwner) ? $isOwner : false;
    $organization = isset($organization) ? $organization : null;
    $competition = isset($competition) ? $competition : null;
?>

<?php if($knockoutMatches && $knockoutMatches->count() > 0): ?>
<div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 mb-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <h3 class="text-2xl font-bold text-white">🏆 Knockout Faza</h3>
        </div>
        
        <?php if($isOwner): ?>
            <div class="flex gap-2">
                
                <?php
                    $groupedByRound = $knockoutMatches->groupBy('round_number');
                    $currentRound = $groupedByRound->keys()->max();
                    $currentRoundMatches = $groupedByRound->get($currentRound);
                    $allMatchesComplete = $currentRoundMatches->every(function($match) {
                        // Bye matches are automatically completed when created
                        return in_array($match->status, ['completed', 'forfeited']);
                    });
                    $isFinale = $currentRoundMatches->count() == 1;
                ?>
                
                <?php if($allMatchesComplete && !$isFinale): ?>
                    <button type="button" onclick="confirmAdvanceRound(<?php echo e($currentRound); ?>)"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <span>⏭️</span>
                        <span>Generiši narednu rundu</span>
                    </button>
                <?php endif; ?>
                
                <button type="button" onclick="confirmResetKnockout()"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                    🔄 Resetuj
                </button>

                
            </div>
        <?php endif; ?>
    </div>

    
    <div class="overflow-x-auto pb-4">
        <div class="inline-flex gap-12 min-w-max items-center p-4">
            <?php
                $groupedByRound = $knockoutMatches->groupBy('round_number');
                $totalRounds = $groupedByRound->keys()->max();
            ?>

            <?php $__currentLoopData = $groupedByRound; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $round => $roundMatches): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex flex-col justify-around min-h-[400px] gap-4">
                    
                    <div class="text-center mb-4">
                        <div class="bg-blue-600/20 border border-blue-500 rounded-lg px-3 py-2">
                            <div class="text-blue-300 font-semibold text-sm">
                                <?php
                                    $matchCount = $roundMatches->count();
                                    if ($matchCount == 1) echo 'Finale';
                                    elseif ($matchCount == 2) echo 'Polufinale';
                                    elseif ($matchCount == 4) echo 'Četvrtfinale';
                                    elseif ($matchCount == 8) echo 'Osmina finala';
                                    else echo 'Runda ' . $round;
                                ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="space-y-3">
                        <?php $__currentLoopData = $roundMatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $match): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="w-56 bg-gray-700/30 rounded-lg border border-gray-600/50 overflow-hidden hover:border-blue-500/50 transition-all">
                                
                                <div class="bg-gray-800/50 px-3 py-2 border-b border-gray-600/30 flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-400">Meč <?php echo e($match->match_order); ?></span>
                                    <div class="flex items-center gap-2">
                                        <?php if($match->is_bye): ?>
                                            <span class="text-xs px-2 py-1 rounded-full bg-blue-600/20 text-blue-400">
                                                BYE
                                            </span>
                                        <?php endif; ?>
                                        <span class="text-xs px-2 py-1 rounded-full
                                            <?php if($match->status === 'completed'): ?> bg-green-600/20 text-green-400
                                            <?php elseif($match->status === 'live'): ?> bg-red-600/20 text-red-400 animate-pulse
                                            <?php else: ?> bg-gray-600/20 text-gray-400
                                            <?php endif; ?>">
                                            <?php if($match->status === 'completed'): ?> ✓
                                            <?php elseif($match->status === 'live'): ?> 🔴
                                            <?php else: ?> ⏳
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>

                                
                                <div class="p-3">
                                    
                                    <div class="flex justify-between items-center mb-2 p-2 rounded-lg
                                        <?php if($match->homePlayer && (!$match->awayPlayer || ($match->status === 'completed' && $match->home_score > $match->away_score))): ?> bg-green-500/10 border-l-4 border-l-green-500
                                        <?php else: ?> bg-gray-600/20
                                        <?php endif; ?>">
                                        <span class="text-white text-sm font-medium">
                                            <?php if($match->homePlayer): ?>
                                                <?php echo e($match->homePlayer->name); ?>

                                                <?php if($match->home_player_group && $match->home_player_position): ?>
                                                    <span class="text-gray-400 text-xs">(<?php echo e($match->home_player_group); ?><?php echo e($match->home_player_position); ?>)</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                TBD
                                            <?php endif; ?>
                                        </span>
                                        <?php if($match->status === 'completed' && $match->homePlayer && $match->awayPlayer): ?>
                                            <span class="text-base font-bold <?php echo e($match->home_score > $match->away_score ? 'text-green-400' : 'text-gray-400'); ?>">
                                                <?php echo e($match->home_score ?? '-'); ?>

                                            </span>
                                        <?php elseif($match->homePlayer && !$match->awayPlayer): ?>
                                            <span class="text-base font-bold text-green-400">1</span>
                                        <?php endif; ?>
                                    </div>

                                    
                                    <div class="flex justify-between items-center p-2 rounded-lg
                                        <?php if($match->awayPlayer && (!$match->homePlayer || ($match->status === 'completed' && $match->away_score > $match->home_score))): ?> bg-green-500/10 border-l-4 border-l-green-500
                                        <?php else: ?> bg-gray-600/20
                                        <?php endif; ?>">
                                        <span class="text-white text-sm font-medium">
                                            <?php if($match->awayPlayer): ?>
                                                <?php echo e($match->awayPlayer->name); ?>

                                                <?php if($match->away_player_group && $match->away_player_position): ?>
                                                    <span class="text-gray-400 text-xs">(<?php echo e($match->away_player_group); ?><?php echo e($match->away_player_position); ?>)</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                TBD
                                            <?php endif; ?>
                                        </span>
                                        <?php if($match->status === 'completed' && $match->homePlayer && $match->awayPlayer): ?>
                                            <span class="text-base font-bold <?php echo e($match->away_score > $match->home_score ? 'text-green-400' : 'text-gray-400'); ?>">
                                                <?php echo e($match->away_score ?? '-'); ?>

                                            </span>
                                        <?php elseif($match->awayPlayer && !$match->homePlayer): ?>
                                            <span class="text-base font-bold text-green-400">1</span>
                                        <?php endif; ?>
                                    </div>

                                    
                                    <?php if($match->status === 'completed' && isset($match->sets) && is_array($match->sets) && count($match->sets) > 0): ?>
                                    <div class="mt-3 pt-3 border-t border-gray-600/30">
                                        <div class="text-xs text-gray-400 mb-2 text-center">Setovi</div>
                                        <div class="flex justify-center gap-2">
                                            <?php $__currentLoopData = $match->sets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $set): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="flex flex-col items-center">
                                                    <div class="text-xs text-gray-500 mb-1"><?php echo e($index + 1); ?></div>
                                                    <div class="flex flex-col gap-0.5">
                                                        <?php
                                                            $homeScore = $set['home_score'] ?? $set['home'] ?? 0;
                                                            $awayScore = $set['away_score'] ?? $set['away'] ?? 0;
                                                        ?>
                                                        <span class="text-xs px-1.5 py-0.5 rounded text-center <?php echo e($homeScore > $awayScore ? 'bg-green-500/20 text-green-300 font-bold' : 'text-gray-400'); ?>">
                                                            <?php echo e($homeScore); ?>

                                                        </span>
                                                        <span class="text-xs px-1.5 py-0.5 rounded text-center <?php echo e($awayScore > $homeScore ? 'bg-green-500/20 text-green-300 font-bold' : 'text-gray-400'); ?>">
                                                            <?php echo e($awayScore); ?>

                                                        </span>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    
                                    <?php if(!$match->is_bye): ?>
                                        <div class="mt-3 flex gap-1 text-xs">
                                            <?php if($match->status === 'scheduled' || $match->status === 'pending'): ?>
                                                <button type="button"
                                                    onclick="openQuickResultModal(<?php echo e($match->id); ?>, '<?php echo e($match->homePlayer->name ?? 'TBD'); ?>', '<?php echo e($match->awayPlayer->name ?? 'TBD'); ?>')"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-1.5 py-0.5 rounded text-xs transition-colors">
                                                    ⚡
                                                </button>
                                                <a href="<?php echo e(route('organizations.competitions.matches.edit', [$organization, $competition, $match])); ?>"
                                                   class="bg-purple-600 hover:bg-purple-700 text-white px-1.5 py-0.5 rounded text-xs transition-colors text-center inline-block">
                                                    ✏️
                                                </a>
                                                <a href="<?php echo e(route('competitions.live-score', ['match' => $match->id])); ?>"
                                                   class="bg-red-600 hover:bg-red-700 text-white px-1.5 py-0.5 rounded text-xs transition-colors text-center inline-block">
                                                    🔴 Live
                                                </a>
                                            <?php elseif($match->status === 'completed'): ?>
                                                <button type="button"
                                                    onclick="openQuickEditModal('<?php echo e($match->id); ?>', '<?php echo e($match->homePlayer->name ?? 'TBD'); ?>', '<?php echo e($match->awayPlayer->name ?? 'TBD'); ?>', '<?php echo e($match->home_score ?? 0); ?>', '<?php echo e($match->away_score ?? 0); ?>', <?php echo e(json_encode($match->sets ?? [])); ?>)"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-1.5 py-0.5 rounded text-xs transition-colors">
                                                    ⚡
                                                </button>
                                                <a href="<?php echo e(route('organizations.competitions.matches.edit', [$organization, $competition, $match])); ?>"
                                                   class="bg-purple-600 hover:bg-purple-700 text-white px-1.5 py-0.5 rounded text-xs transition-colors text-center inline-block">
                                                    ✏️
                                                </a>
                                                <a href="<?php echo e(route('organizations.competitions.matches.show', [$organization, $competition, $match])); ?>"
                                                   class="bg-gray-600 hover:bg-gray-700 text-white px-1.5 py-0.5 rounded text-xs transition-colors text-center inline-block">
                                                    👁️
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<?php else: ?>
<div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 mb-6 text-center">
    <p class="text-gray-300">Knockout faza je resetovana - možete regenerisati bracket</p>
    <?php if($isOwner): ?>
        
        <?php if(!$competition->knockout_matches_count): ?>
            <div class="mt-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-3 text-yellow-400 inline-block text-sm">
                <p>⚠️ Prvo trebate postaviti <strong>Broj mečeva u eliminacionoj fazi</strong></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if (! $__env->hasRenderedOnce('9e44c898-5efe-4124-bd48-8357820a11d5')): $__env->markAsRenderedOnce('9e44c898-5efe-4124-bd48-8357820a11d5'); ?>
    
    <div id="quickEditModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-2xl p-6 max-w-lg w-full border border-gray-700 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-white">⚡ Brzi unos rezultata</h3>
                <button onclick="closeQuickEditModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="quickEditForm" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="quickEditMatchId" name="match_id">
                <input type="hidden" id="quickEditScrollPosition" name="scroll_position">
                <div class="space-y-6">
                    <!-- Match Info -->
                    <div class="bg-gray-700/30 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs" id="editHomeInitials">--</span>
                                </div>
                                <span class="text-white font-medium" id="editHomePlayerName">Player 1</span>
                            </div>
                            <input type="number" name="home_score" id="editHomeScoreInput" min="0" max="10" required
                                   class="w-20 text-center text-2xl font-bold bg-gray-600/50 border border-gray-500 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs" id="editAwayInitials">--</span>
                                </div>
                                <span class="text-white font-medium" id="editAwayPlayerName">Player 2</span>
                            </div>
                            <input type="number" name="away_score" id="editAwayScoreInput" min="0" max="10" required
                                   class="w-20 text-center text-2xl font-bold bg-gray-600/50 border border-gray-500 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Set Scores (Optional) -->
                    <div class="bg-gray-700/30 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-gray-300">Set Scores (Optional)</label>
                            <button type="button" onclick="addEditSetScore()" 
                                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-colors">
                                ➕ Add set score
                            </button>
                        </div>
                        <div id="editSetScoresContainer" class="space-y-2">
                            <!-- Set scores will be added here dynamically -->
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="closeQuickEditModal()"
                                class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            Odustani
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            Sačuvaj rezultat
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<script>
let editSetScoreCount = 0;

function openQuickEditModal(matchId, homeName, awayName, homeScore, awayScore, sets) {
    console.log('Opening quick edit modal for match:', matchId, homeName, 'vs', awayName);
    document.getElementById('quickEditMatchId').value = matchId;

    // Set scroll position
    const scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
    document.getElementById('quickEditScrollPosition').value = scrollPosition;

    document.getElementById('editHomePlayerName').textContent = homeName;
    document.getElementById('editAwayPlayerName').textContent = awayName;
    document.getElementById('editHomeInitials').textContent = (homeName || 'TBD').substring(0, 2).toUpperCase();
    document.getElementById('editAwayInitials').textContent = (awayName || 'TBD').substring(0, 2).toUpperCase();
    
    document.getElementById('editHomeScoreInput').value = homeScore || '';
    document.getElementById('editAwayScoreInput').value = awayScore || '';
    
    // Clear existing sets
    document.getElementById('editSetScoresContainer').innerHTML = '';
    editSetScoreCount = 0;
    
    // Load existing sets if any
    if (sets && Array.isArray(sets)) {
        sets.forEach(function(set, index) {
            editSetScoreCount++;
            const container = document.getElementById('editSetScoresContainer');
            const setDiv = document.createElement('div');
            setDiv.className = 'flex items-center gap-2';
            setDiv.innerHTML = `
                <span class="text-gray-400 text-sm w-16">Set ${editSetScoreCount}:</span>
                <input type="number" name="sets[${editSetScoreCount-1}][home]" min="0" value="${set.home || set.home_score || 0}"
                       class="w-20 text-center bg-gray-600/50 border border-gray-500 rounded px-2 py-1 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="text-gray-400">-</span>
                <input type="number" name="sets[${editSetScoreCount-1}][away]" min="0" value="${set.away || set.away_score || 0}"
                       class="w-20 text-center bg-gray-600/50 border border-gray-500 rounded px-2 py-1 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-300 ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(setDiv);
        });
    }
    
    const form = document.getElementById('quickEditForm');
    form.action = `/competitions/matches/${matchId}/quick-result`;
    
    document.getElementById('quickEditModal').classList.remove('hidden');
}

function closeQuickEditModal() {
    document.getElementById('quickEditModal').classList.add('hidden');
}

function addEditSetScore() {
    editSetScoreCount++;
    const container = document.getElementById('editSetScoresContainer');
    const setDiv = document.createElement('div');
    setDiv.className = 'flex items-center gap-2';
    setDiv.innerHTML = `
        <span class="text-gray-400 text-sm w-16">Set ${editSetScoreCount}:</span>
        <input type="number" name="sets[${editSetScoreCount-1}][home]" min="0" placeholder="0"
               class="w-20 text-center bg-gray-600/50 border border-gray-500 rounded px-2 py-1 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        <span class="text-gray-400">-</span>
        <input type="number" name="sets[${editSetScoreCount-1}][away]" min="0" placeholder="0"
               class="w-20 text-center bg-gray-600/50 border border-gray-500 rounded px-2 py-1 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-300 ml-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(setDiv);
}
</script>

<script>
function confirmResetKnockout() {
    console.log('confirmResetKnockout called');
    if (confirm('Da li si siguran? Ovo će obrisati svu knockout fazu.')) {
        console.log('User confirmed, creating form');
        // Create and submit a form to reset knockout
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("organizations.competitions.reset-knockout", [$organization, $competition])); ?>';
        
        console.log('Form action:', form.action);
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        console.log('Submitting form...');
        form.submit();
    } else {
        console.log('User cancelled');
    }
}

function directResetKnockout() {
    console.log('Direct reset called');
    // Create and submit a form to reset knockout without confirm
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo e(route("organizations.competitions.reset-knockout", [$organization, $competition])); ?>';
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '<?php echo e(csrf_token()); ?>';
    form.appendChild(csrfToken);
    
    console.log('Submitting form to:', form.action);
    document.body.appendChild(form);
    form.submit();
}

function submitAutoGenerateForm(event) {
    event.preventDefault();
    
    const btn = document.getElementById('autoGenerateBtn');
    const form = document.getElementById('autoGenerateKnockoutForm');
    
    // Show loading state
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = '⏳ Generiši...';
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    
    // Submit form
    setTimeout(() => {
        form.submit();
    }, 300);
}

function confirmAdvanceRound(currentRound) {
    console.log('confirmAdvanceRound called with round:', currentRound);
    if (confirm(`Da li želiš generirati sledeću rundu? Svi mečevi runde ${currentRound} su završeni.`)) {
        console.log('User confirmed, creating form');
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("organizations.competitions.advance-knockout-round", [$organization, $competition])); ?>';
        
        console.log('Form action:', form.action);
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrfToken);
        
        // Add current round
        const roundInput = document.createElement('input');
        roundInput.type = 'hidden';
        roundInput.name = 'current_round';
        roundInput.value = currentRound;
        form.appendChild(roundInput);
        
        document.body.appendChild(form);
        console.log('Submitting form...');
        form.submit();
    } else {
        console.log('User cancelled');
    }
}
</script><?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/competitions/partials/tournament/knockout-phase.blade.php ENDPATH**/ ?>