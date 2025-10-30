
<?php
    // Check if all group matches are completed
    $allGroupMatchesCompleted = $groupMatches->count() > 0 && $groupMatches->flatten()->every(function($match) {
        return $match->status === 'completed';
    });
?>

<div class="mb-8">
    <div class="w-full bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 hover:border-gray-600/50 transition-all text-left">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h3 class="text-2xl font-bold text-white">📋 Grupna Faza</h3>
                <?php if($allGroupMatchesCompleted): ?>
                    <span class="px-3 py-1 text-xs rounded-full bg-green-600/20 text-green-400">
                        ✓ Završeno
                    </span>
                <?php else: ?>
                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-600/20 text-yellow-400">
                        ⏳ U toku
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="flex gap-2">
                
                <?php if($isOwner && $allGroupMatchesCompleted && $knockoutMatches->count() === 0): ?>
                    <a href="<?php echo e(route('organizations.competitions.knockout-setup', [$organization, $competition])); ?>"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-2 rounded-lg transition-colors font-semibold">
                        🎯 Ručno Postavi Knockout
                    </a>
                    <form id="autoGenerateKnockoutForm" method="POST" action="<?php echo e(route('organizations.competitions.auto-generate-knockout', [$organization, $competition])); ?>" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <div class="flex gap-2 items-center">
                            <button type="submit" id="autoGenerateBtn" class="bg-gray-600 text-gray-400 text-xs px-4 py-2 rounded-lg cursor-not-allowed font-semibold" disabled title="Trenutno nije u funkciji">
                                ⚡ Automatski Generiši (nedostupno)
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            
                
                <?php if($isOwner && $groupMatches->count() > 0): ?>
                    <button type="button" onclick="confirmResetGroupPhase()" 
                            class="bg-red-600 hover:bg-red-700 text-white text-xs px-4 py-2 rounded-lg transition-colors font-semibold">
                        🔄 Resetuj grupnu fazu
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mt-4">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <?php $__currentLoopData = $competition->tournamentGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $matchesInGroup = $groupMatches->get($group->id, collect());
                    $standings = App\Models\Standing::where('competition_id', $competition->id)
                        ->where('tournament_group_id', $group->id)
                        ->with('player')
                        ->orderBy('points', 'desc')
                        ->orderByRaw('(sets_won - sets_lost) desc')
                        ->orderByRaw('(points_won - points_lost) desc')
                        ->orderBy('id')
                        ->get();
                ?>
                
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl border border-gray-700/50 shadow-xl overflow-hidden">
                    
                    <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 px-4 py-3 border-b border-gray-700/50">
                        <div class="flex items-center justify-between">
                            <h4 class="text-white font-bold text-base flex items-center space-x-2">
                                <span class="bg-gradient-to-r from-blue-500 to-purple-600 px-3 py-1 rounded-full text-xs">
                                    Grupa <?php echo e($group->name); ?>

                                </span>
                            </h4>
                            <span class="text-gray-400 text-xs">
                                <?php echo e($matchesInGroup->where('status', 'completed')->count()); ?>/<?php echo e($matchesInGroup->count()); ?> mečeva
                            </span>
                        </div>
                    </div>

                    
                    <div class="px-4 py-3 bg-gray-700/20">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-gray-400 border-b border-gray-700/50">
                                    <th class="text-left py-1 pr-2 font-medium">#</th>
                                    <th class="text-left py-1 font-medium">Igrač</th>
                                    <th class="text-center py-1 px-1 font-medium">M</th>
                                    <th class="text-center py-1 px-1 font-medium">P</th>
                                    <th class="text-center py-1 px-1 font-medium">I</th>
                                    <th class="text-center py-1 px-1 font-medium">S</th>
                                    <th class="text-center py-1 px-1 font-medium text-green-400">Bod</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $standings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $standing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="border-b border-gray-700/30 hover:bg-gray-700/30 transition-colors <?php echo e($index < $competition->players_advancing_per_group ? 'bg-green-900/30' : ''); ?>">
                                    <td class="py-2 pr-2 text-gray-400 font-mono"><?php echo e($index + 1); ?></td>
                                    <td class="py-2 text-white font-medium">
                                        <?php echo e($standing->player->name); ?>

                                        <?php if($standing->player->position): ?>
                                            <span class="text-gray-400 text-xs">(<?php echo e($standing->player->position); ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-2 px-1 text-center text-gray-300"><?php echo e($standing->played); ?></td>
                                    <td class="py-2 px-1 text-center text-green-400"><?php echo e($standing->won); ?></td>
                                    <td class="py-2 px-1 text-center text-red-400"><?php echo e($standing->lost); ?></td>
                                    <td class="py-2 px-1 text-center text-gray-300"><?php echo e($standing->sets_won); ?>-<?php echo e($standing->sets_lost); ?></td>
                                    <td class="py-2 px-1 text-center text-green-400 font-bold"><?php echo e($standing->points); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-gray-400">Nema podataka o bodovima</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <?php if($matchesInGroup->count() > 0): ?>
                    <div class="px-4 py-3 border-t border-gray-700/50">
                        <h5 class="text-gray-300 font-semibold text-xs mb-2">Mečevi</h5>
                        <?php
                            $matchesByRound = $matchesInGroup->groupBy('round_number');
                        ?>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $matchesByRound; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roundNumber => $roundMatches): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="space-y-1">
                                    <div class="text-gray-400 text-xs font-medium px-2 py-1 bg-gray-700/30 rounded">
                                        Kolo <?php echo e($roundNumber); ?>.
                                    </div>
                                    <div class="space-y-1">
                                        <?php $__currentLoopData = $roundMatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $match): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo $__env->make('organizations.competitions.partials.tournament.match-card', [
                                                'match' => $match,
                                                'competition' => $competition,
                                                'organization' => $organization,
                                                'isOwner' => $isOwner,
                                                'isRefereeForMatch' => $isRefereeForMatch
                                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<script>
function submitAutoGenerateForm(event) {
    event.preventDefault();
    
    const btn = document.getElementById('autoGenerateBtn');
    const form = document.getElementById('autoGenerateKnockoutForm');
    const originalText = btn.textContent;
    
    // Disable button and show loading
    btn.disabled = true;
    btn.textContent = '⏳ Generiši...';
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    
    // Submit form
    setTimeout(() => {
        form.submit();
    }, 300);
}

function confirmResetGroupPhase() {
    if (confirm('Da li si siguran? Ovo će obrisati svu grupnu fazu.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("organizations.competitions.reset-groups", [$organization, $competition])); ?>';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script><?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/competitions/partials/tournament/group-phase.blade.php ENDPATH**/ ?>