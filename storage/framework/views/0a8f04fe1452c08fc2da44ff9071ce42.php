 <?php $__env->slot('header', null, []); ?> 
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                <?php echo e($competition->name); ?>

            </h2>
            <p class="text-gray-400 mt-1"><?php echo e($organization->name); ?></p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="px-3 py-1 text-sm rounded-full
                <?php if($competition->status === 'active'): ?> bg-green-500/20 text-green-400
                <?php elseif($competition->status === 'draft'): ?> bg-yellow-500/20 text-yellow-400
                <?php elseif($competition->status === 'completed'): ?> bg-blue-500/20 text-blue-400
                <?php else: ?> bg-red-500/20 text-red-400 <?php endif; ?>"
            >
                <?php if($competition->status === 'active'): ?> Aktivno
                <?php elseif($competition->status === 'draft'): ?> Nacrt
                <?php elseif($competition->status === 'completed'): ?> Završeno
                <?php else: ?> <?php echo e(ucfirst($competition->status)); ?> <?php endif; ?>
            </span>
            <?php if($competition->type === 'tournament'): ?>
            <span class="px-3 py-1 text-sm rounded-full bg-purple-500/20 text-purple-400">
                Turnir
            </span>
            <?php else: ?>
            <span class="px-3 py-1 text-sm rounded-full bg-blue-500/20 text-blue-400">
                Liga
            </span>
            <?php endif; ?>
        </div>
    </div>
 <?php $__env->endSlot(); ?>
<?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/competitions/partials/header.blade.php ENDPATH**/ ?>