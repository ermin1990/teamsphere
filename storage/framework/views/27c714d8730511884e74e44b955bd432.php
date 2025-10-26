
<?php if($isOwner && $competition->status === 'draft'): ?>
<div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 backdrop-blur-xl rounded-xl p-6 border border-blue-500/30 shadow-xl mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-white mb-2">Postavi Takmičenje</h3>
            <p class="text-gray-300">Pratite ove korake da postavite vaše takmičenje</p>
        </div>
        <a href="<?php echo e(route('organizations.competitions.manage-players', [$organization, $competition])); ?>"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors font-semibold flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Upravljaj Igračima
        </a>
    </div>

    <!-- Setup Steps -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800/50 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 rounded-full <?php echo e($competition->players->count() > 0 ? 'bg-green-600' : 'bg-gray-600'); ?> flex items-center justify-center mr-3">
                    <?php if($competition->players->count() > 0): ?>
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    <?php else: ?>
                        <span class="text-white font-bold text-sm">1</span>
                    <?php endif; ?>
                </div>
                <h4 class="text-white font-semibold">Dodaj Igrače</h4>
            </div>
            <p class="text-gray-400 text-sm"><?php echo e($competition->players->count()); ?> igrača dodano</p>
        </div>

        <?php if($competition->type === 'tournament'): ?>
        <div class="bg-gray-800/50 rounded-lg p-4 <?php echo e($competition->tournamentGroups->count() > 0 ? 'cursor-pointer hover:bg-gray-700/50' : ''); ?> transition-colors"
             <?php if($competition->tournamentGroups->count() > 0): ?>
             onclick="window.location.href='<?php echo e(route('organizations.competitions.setup-groups', [$organization, $competition])); ?>'"
             <?php endif; ?>>
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-semibold text-white">Grupe</h4>
                <?php if($competition->tournamentGroups->count() > 0): ?>
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <?php else: ?>
                <a href="<?php echo e(route('organizations.competitions.setup-groups', [$organization, $competition])); ?>"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded transition-colors">
                    Postavi
                </a>
                <?php endif; ?>
            </div>
            <p class="text-gray-400 text-sm">
                <?php if($competition->tournamentGroups->count() > 0): ?>
                    <?php echo e($competition->tournamentGroups->count()); ?> grupa konfigurisano - Kliknite za uređivanje
                <?php else: ?>
                    <?php echo e(__('Organize into groups')); ?>

                <?php endif; ?>
            </p>
        </div>
        <?php endif; ?>

        <div class="bg-gray-800/50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full
                        <?php if(($competition->type === 'tournament' && $competition->players->count() > 0 && $competition->tournamentGroups->count() > 0) ||
                            ($competition->type === 'league' && $competition->players->count() > 0)): ?>
                            bg-green-600
                        <?php else: ?>
                            bg-gray-600
                        <?php endif; ?>
                        flex items-center justify-center mr-3">
                        <?php if(($competition->type === 'tournament' && $competition->players->count() > 0 && $competition->tournamentGroups->count() > 0) ||
                            ($competition->type === 'league' && $competition->players->count() > 0)): ?>
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        <?php else: ?>
                            <span class="text-white font-bold text-sm"><?php echo e($competition->type === 'tournament' ? '3' : '2'); ?></span>
                        <?php endif; ?>
                    </div>
                    <h4 class="text-white font-semibold"><?php echo e(__('Start Competition')); ?></h4>
                </div>
                <?php if(($competition->type === 'tournament' && $competition->players->count() > 0 && $competition->tournamentGroups->count() > 0) ||
                    ($competition->type === 'league' && $competition->players->count() > 0)): ?>
                <form method="POST" action="<?php echo e(route('organizations.competitions.start', [$organization, $competition])); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition-colors font-semibold">
                        🚀 <?php echo e(__('Start')); ?>

                    </button>
                </form>
                <?php endif; ?>
            </div>
            <p class="text-gray-400 text-sm">
                <?php if(($competition->type === 'tournament' && $competition->players->count() > 0 && $competition->tournamentGroups->count() > 0) ||
                    ($competition->type === 'league' && $competition->players->count() > 0)): ?>
                    <?php echo e(__('Ready to start!')); ?>

                <?php else: ?>
                    <?php echo e(__('Begin matches')); ?>

                <?php endif; ?>
            </p>
        </div>
    </div>
</div>
<?php endif; ?>
<?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/competitions/partials/setup-wizard.blade.php ENDPATH**/ ?>