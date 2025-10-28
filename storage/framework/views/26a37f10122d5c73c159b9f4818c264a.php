
<?php
    $isCompleted = $match->status === 'completed';
    $matchUrl = $isCompleted ? route('organizations.competitions.matches.show', [$organization, $competition, $match]) : null;
?>

<div class="bg-gray-700/20 rounded-lg p-2 <?php echo e($isCompleted ? 'hover:bg-gray-700/50 hover:border-blue-500/30 cursor-pointer' : 'hover:bg-gray-700/40'); ?> transition-all border border-gray-600/10 <?php echo e($isCompleted ? 'group' : ''); ?>"
     <?php if($isCompleted): ?> onclick="window.location.href='<?php echo e($matchUrl); ?>'" <?php endif; ?>>
    <div class="flex items-center justify-between gap-2">
        <!-- Players and Scores -->
        <div class="flex-1 min-w-0">
            <!-- Home Player -->
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center space-x-1.5 min-w-0 flex-1">
                    <div class="w-5 h-5 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-[9px]"><?php echo e(substr($match->homePlayer->name ?? 'TBD', 0, 2)); ?></span>
                    </div>
                    <span class="text-white text-xs truncate">
                        <?php echo e($match->homePlayer->name ?? 'TBD'); ?>

                        <?php if($match->homePlayer && $match->homePlayer->position): ?>
                            <span class="text-gray-400 text-[10px]">(<?php echo e($match->homePlayer->position); ?>)</span>
                        <?php endif; ?>
                    </span>
                </div>
                <span class="text-lg font-bold ml-2 flex-shrink-0
                    <?php if($match->status === 'completed' && $match->home_score > $match->away_score && $match->homePlayer): ?> text-green-400
                    <?php elseif($match->status === 'completed'): ?> text-gray-500
                    <?php else: ?> text-white <?php endif; ?>">
                    <?php echo e($match->status !== 'scheduled' ? ($match->home_score ?? 0) : '-'); ?>

                </span>
            </div>
            
            <!-- Away Player -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-1.5 min-w-0 flex-1">
                    <div class="w-5 h-5 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-[9px]"><?php echo e(substr($match->awayPlayer->name ?? 'TBD', 0, 2)); ?></span>
                    </div>
                    <span class="text-white text-xs truncate">
                        <?php echo e($match->awayPlayer->name ?? 'TBD'); ?>

                        <?php if($match->awayPlayer && $match->awayPlayer->position): ?>
                            <span class="text-gray-400 text-[10px]">(<?php echo e($match->awayPlayer->position); ?>)</span>
                        <?php endif; ?>
                    </span>
                </div>
                <span class="text-lg font-bold ml-2 flex-shrink-0
                    <?php if($match->status === 'completed' && $match->away_score > $match->home_score && $match->awayPlayer): ?> text-green-400
                    <?php elseif($match->status === 'completed'): ?> text-gray-500
                    <?php else: ?> text-white <?php endif; ?>">
                    <?php echo e($match->status !== 'scheduled' ? ($match->away_score ?? 0) : '-'); ?>

                </span>
            </div>

            <!-- Set Scores -->
            <?php if($match->status === 'completed' && $match->sets && is_array($match->sets) && count($match->sets) > 0): ?>
            <div class="flex gap-1 mt-1">
                <?php $__currentLoopData = $match->sets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $set): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-gray-600/40 px-1.5 py-0.5 rounded text-[10px] text-gray-300">
                    <?php echo e(($set['home_score'] ?? $set['home'] ?? 0)); ?>-<?php echo e(($set['away_score'] ?? $set['away'] ?? 0)); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Actions -->
        <div class="flex flex-col gap-1 flex-shrink-0">
            <?php if($match->status === 'scheduled'): ?>
                <?php if($isOwner || $isRefereeForMatch($match)): ?>
                <a href="<?php echo e($isRefereeForMatch($match) ? route('referee.competition.match.edit', [$match]) : route('organizations.competitions.matches.edit', [$organization, $competition, $match])); ?>" 
                   class="bg-purple-600 hover:bg-purple-700 text-white text-[10px] px-2 py-1 rounded transition-colors text-center whitespace-nowrap">
                    ✏️ <?php echo e(__('Edit')); ?>

                </a>
                <a href="<?php echo e($isRefereeForMatch($match) ? route('referee.competition.match.live', [$match]) : route('competitions.live-score', [$match->id])); ?>" 
                   class="bg-green-600 hover:bg-green-700 text-white text-[10px] px-2 py-1 rounded transition-colors text-center whitespace-nowrap">
                    ▶️ <?php echo e(__('Live')); ?>

                </a>
                <button onclick="openQuickResultModal(<?php echo e($match->id); ?>, '<?php echo e($match->homePlayer->name ?? 'TBD'); ?>', '<?php echo e($match->awayPlayer->name ?? 'TBD'); ?>')"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-2 py-1 rounded transition-colors text-center whitespace-nowrap">
                    ⚡ Quick
                </button>
                <?php else: ?>
                <span class="text-[10px] bg-yellow-600/20 text-yellow-400 px-2 py-1 rounded text-center whitespace-nowrap">
                    <?php echo e(__('Soon')); ?>

                </span>
                <?php endif; ?>
            <?php elseif($match->status === 'in_progress'): ?>
                <span class="text-[10px] bg-green-600/20 text-green-400 px-2 py-1 rounded text-center whitespace-nowrap animate-pulse">
                    🔴 <?php echo e(__('Live')); ?>

                </span>
                <?php if($isOwner || $isRefereeForMatch($match)): ?>
                <a href="<?php echo e($isRefereeForMatch($match) ? route('referee.competition.match.live', [$match]) : route('competitions.live-score', [$match->id])); ?>" 
                   class="text-blue-400 hover:text-blue-300 text-[10px] text-center whitespace-nowrap">
                    👁️ <?php echo e(__('Watch')); ?>

                </a>
                <?php endif; ?>
            <?php elseif($match->status === 'completed'): ?>
                <a href="<?php echo e($matchUrl); ?>" 
                   onclick="event.stopPropagation()"
                   class="text-[10px] bg-gray-600/20 text-gray-400 px-2 py-1 rounded text-center whitespace-nowrap hover:bg-gray-600/40 hover:text-gray-300 transition-colors block">
                    👁️ Detalji
                </a>
                <?php if($isOwner || $isRefereeForMatch($match)): ?>
                <a href="<?php echo e($isRefereeForMatch($match) ? route('referee.competition.match.edit', [$match]) : route('organizations.competitions.matches.edit', [$organization, $competition, $match])); ?>" 
                   onclick="event.stopPropagation()"
                   class="text-blue-400 hover:text-blue-300 text-[10px] text-center whitespace-nowrap">
                    ✏️ <?php echo e(__('Edit')); ?>

                </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/competitions/partials/tournament/match-card.blade.php ENDPATH**/ ?>