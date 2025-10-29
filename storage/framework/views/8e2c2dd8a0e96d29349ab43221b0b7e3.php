
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

                
                <button type="button" onclick="directResetKnockout()"
                        class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                    🔄 Resetuj (bez confirm)
                </button>
                
                <form id="regenerateKnockoutForm" method="POST" action="<?php echo e(route('organizations.competitions.auto-generate-knockout', [$organization, $competition])); ?>" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="knockout_matches_count" value="<?php echo e($competition->knockout_matches_count ?? 8); ?>">
                    <button type="submit" onclick="return confirm('Da li želiš regenerisati knockout bracket?')" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        🎯 Regeneriši
                    </button>
                </form>
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

                                    
                                    <?php if(!$match->is_bye): ?>
                                        <div class="mt-3 flex gap-1 text-xs">
                                            <?php if($match->status === 'scheduled' || $match->status === 'pending'): ?>
                                                <button type="button"
                                                    onclick="openQuickResultModal(<?php echo e(json_encode((string)($match->id ?? ''))); ?>, <?php echo e(json_encode($match->homePlayer->name ?? 'TBD')); ?>, <?php echo e(json_encode($match->awayPlayer->name ?? 'TBD')); ?>)"
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
                                                    onclick="openQuickEditModal(<?php echo e(json_encode((string)($match->id ?? ''))); ?>, <?php echo e(json_encode($match->homePlayer->name ?? 'TBD')); ?>, <?php echo e(json_encode($match->awayPlayer->name ?? 'TBD')); ?>, <?php echo e(json_encode($match->home_score ?? 0)); ?>, <?php echo e(json_encode($match->away_score ?? 0)); ?>, <?php echo e(json_encode($match->sets ?? [])); ?>)"
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

<?php if (! $__env->hasRenderedOnce('ee48db6b-0275-4dfe-b2b4-6994ba61747c')): $__env->markAsRenderedOnce('ee48db6b-0275-4dfe-b2b4-6994ba61747c'); ?>
    
    <div id="quickResultModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">⚡ Brzi unos rezultata</h3>
                    <button onclick="closeQuickResultModal()" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="quickResultForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="quickMatchId" name="match_id">

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-white font-medium mb-2" id="homePlayerName">Igrač 1</div>
                                <input type="number" id="homeScore" name="home_score" min="0" max="7"
                                       class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white text-center"
                                       placeholder="0">
                                <div class="text-xs text-gray-400 mt-1">Setovi osvojeni</div>
                            </div>
                            <div class="text-center">
                                <div class="text-white font-medium mb-2" id="awayPlayerName">Igrač 2</div>
                                <input type="number" id="awayScore" name="away_score" min="0" max="7"
                                       class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white text-center"
                                       placeholder="0">
                                <div class="text-xs text-gray-400 mt-1">Setovi osvojeni</div>
                            </div>
                        </div>

                        <div class="text-center text-gray-400 text-sm">
                            Broj setova koje je svaki igrač osvojio
                        </div>

                        
                        <div id="setsContainer" class="space-y-2">
                            <div class="text-center text-gray-300 text-sm mb-2">Detalji setova (opciono)</div>
                            <div id="setsList"></div>
                            <button type="button" onclick="addSet()" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm">
                                + Dodaj set
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="closeQuickResultModal()"
                                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors">
                            Odustani
                        </button>
                        <button type="button" onclick="saveQuickResult()"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition-colors">
                            💾 Sačuvaj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    // ...existing code...
    </script>
<?php endif; ?>

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
    if (confirm(`Da li želiš generirati sledeću rundu? Svi mečevi runde ${currentRound} su završeni.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("organizations.competitions.advance-knockout-round", [$organization, $competition])); ?>';
        
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
        form.submit();
    }
}

let currentSets = [];

function openQuickResultModal(matchId, homePlayer, awayPlayer) {
    // Fallback for null/undefined matchId
    if (matchId === null || matchId === undefined) matchId = '';
    matchId = String(matchId);
    console.log('Opening modal with:', { matchId, homePlayer, awayPlayer });
    document.getElementById('quickMatchId').value = matchId;
    document.getElementById('homePlayerName').textContent = homePlayer;
    document.getElementById('awayPlayerName').textContent = awayPlayer;
    document.getElementById('homeScore').value = '';
    document.getElementById('awayScore').value = '';
    currentSets = [];
    document.getElementById('setsList').innerHTML = '';
    document.getElementById('setsContainer').classList.remove('hidden'); // Show sets container immediately
    document.getElementById('quickResultModal').classList.remove('hidden');
}

function openQuickEditModal(matchId, homePlayer, awayPlayer, homeScore, awayScore, sets) {
    // Fallback for null/undefined matchId
    if (matchId === null || matchId === undefined) matchId = '';
    matchId = String(matchId);
    console.log('Opening edit modal with:', { matchId, homePlayer, awayPlayer, homeScore, awayScore, sets });
    
    // Set basic match info
    document.getElementById('quickMatchId').value = matchId;
    document.getElementById('homePlayerName').textContent = homePlayer;
    document.getElementById('awayPlayerName').textContent = awayPlayer;
    document.getElementById('homeScore').value = homeScore || '';
    document.getElementById('awayScore').value = awayScore || '';
    
    // Clear existing sets
    currentSets = [];
    document.getElementById('setsList').innerHTML = '';
    
    // Load existing sets
    if (sets && Array.isArray(sets) && sets.length > 0) {
        sets.forEach((set, index) => {
            const setNumber = index + 1;
            const setDiv = document.createElement('div');
            setDiv.className = 'flex gap-2 items-center';
            setDiv.innerHTML = `
                <span class="text-gray-400 text-sm w-12">Set ${setNumber}:</span>
                <input type="number" name="sets[${setNumber}][home]" min="0" max="21" value="${set.home || set.home_score || 0}"
                       class="flex-1 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white text-center text-sm">
                <span class="text-gray-400">-</span>
                <input type="number" name="sets[${setNumber}][away]" min="0" max="21" value="${set.away || set.away_score || 0}"
                       class="flex-1 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white text-center text-sm">
                <button type="button" onclick="removeSet(${index})" class="text-red-400 hover:text-red-300 text-sm px-2">×</button>
            `;
            document.getElementById('setsList').appendChild(setDiv);
            currentSets.push(setNumber);
        });
    }
    
    document.getElementById('setsContainer').classList.remove('hidden');
    document.getElementById('quickResultModal').classList.remove('hidden');
}

function closeQuickResultModal() {
    document.getElementById('quickResultModal').classList.add('hidden');
}

function addSet() {
    const setNumber = currentSets.length + 1;
    const setDiv = document.createElement('div');
    setDiv.className = 'flex gap-2 items-center';
    setDiv.innerHTML = `
        <span class="text-gray-400 text-sm w-12">Set ${setNumber}:</span>
        <input type="number" name="sets[${setNumber}][home]" min="0" max="21" 
               class="flex-1 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white text-center text-sm" placeholder="0">
        <span class="text-gray-400">-</span>
        <input type="number" name="sets[${setNumber}][away]" min="0" max="21" 
               class="flex-1 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white text-center text-sm" placeholder="0">
        <button type="button" onclick="removeSet(${setNumber - 1})" class="text-red-400 hover:text-red-300 text-sm px-2">×</button>
    `;
    document.getElementById('setsList').appendChild(setDiv);
    currentSets.push(setNumber);
    document.getElementById('setsContainer').classList.remove('hidden');
}

function removeSet(index) {
    currentSets.splice(index, 1);
    const setsList = document.getElementById('setsList');
    setsList.innerHTML = '';
    currentSets.forEach((setNum, i) => {
        const setDiv = document.createElement('div');
        setDiv.className = 'flex gap-2 items-center';
        setDiv.innerHTML = `
            <span class="text-gray-400 text-sm w-12">Set ${i + 1}:</span>
            <input type="number" name="sets[${i + 1}][home]" min="0" max="21" 
                   class="flex-1 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white text-center text-sm" placeholder="0">
            <span class="text-gray-400">-</span>
            <input type="number" name="sets[${i + 1}][away]" min="0" max="21" 
                   class="flex-1 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white text-center text-sm" placeholder="0">
            <button type="button" onclick="removeSet(${i})" class="text-red-400 hover:text-red-300 text-sm px-2">×</button>
        `;
        setsList.appendChild(setDiv);
    });
}

function saveQuickResult() {
    const matchId = document.getElementById('quickMatchId').value;
    const homeScore = document.getElementById('homeScore').value;
    const awayScore = document.getElementById('awayScore').value;

    console.log('Saving result:', { matchId, homeScore, awayScore });

    if (!matchId || matchId.trim() === '') {
        alert('Greška: ID meča nije pronađen');
        return;
    }

    if (!homeScore || !awayScore) {
        alert('Molimo unesite rezultate za oba igrača');
        return;
    }

    // Collect set data
    const sets = [];
    currentSets.forEach((setNum, index) => {
        const homeSetScore = document.querySelector(`input[name="sets[${setNum}][home]"]`)?.value;
        const awaySetScore = document.querySelector(`input[name="sets[${setNum}][away]"]`)?.value;
        if (homeSetScore && awaySetScore) {
            sets.push({
                home: parseInt(homeSetScore),
                away: parseInt(awaySetScore)
            });
        }
    });

    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/competitions/matches/' + matchId + '/quick-result';
    form.style.display = 'none';

    console.log('Form action:', form.action);

    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '<?php echo e(csrf_token()); ?>';
    form.appendChild(csrfToken);

    // Add match data
    const matchIdField = document.createElement('input');
    matchIdField.type = 'hidden';
    matchIdField.name = 'match_id';
    matchIdField.value = matchId;
    form.appendChild(matchIdField);

    const homeScoreField = document.createElement('input');
    homeScoreField.type = 'hidden';
    homeScoreField.name = 'home_score';
    homeScoreField.value = homeScore;
    form.appendChild(homeScoreField);

    const awayScoreField = document.createElement('input');
    awayScoreField.type = 'hidden';
    awayScoreField.name = 'away_score';
    awayScoreField.value = awayScore;
    form.appendChild(awayScoreField);

    // Add sets data as individual fields (not JSON string)
    sets.forEach((set, index) => {
        const homeSetField = document.createElement('input');
        homeSetField.type = 'hidden';
        homeSetField.name = `sets[${index}][home]`;
        homeSetField.value = set.home;
        form.appendChild(homeSetField);

        const awaySetField = document.createElement('input');
        awaySetField.type = 'hidden';
        awaySetField.name = `sets[${index}][away]`;
        awaySetField.value = set.away;
        form.appendChild(awaySetField);
    });

    document.body.appendChild(form);
    form.submit();
}

</script>

<?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/competitions/partials/tournament/knockout-phase.blade.php ENDPATH**/ ?>