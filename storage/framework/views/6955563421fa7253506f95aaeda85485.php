<?php $__env->startSection('admin-content'); ?>
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Izvještaji o Bagovima & Zahtjevi za Funkcijama
            </h2>
            <p class="text-gray-400 mt-2">Upravljajte povratnim informacijama i izvještajima korisnika</p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-400 text-sm">Ukupno Izvještaja</p>
                        <p class="text-2xl font-bold text-white"><?php echo e($stats['total']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-400 text-sm">Na Čekanju</p>
                        <p class="text-2xl font-bold text-white"><?php echo e($stats['pending']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-400 text-sm">U Pregledu</p>
                        <p class="text-2xl font-bold text-white"><?php echo e($stats['in_review']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="p-3 bg-green-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-400 text-sm">Riješeno</p>
                        <p class="text-2xl font-bold text-white"><?php echo e($stats['resolved']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 overflow-hidden">
            <div class="px-6 py-4 border-b border-white/10">
                <h3 class="text-lg font-semibold text-white">Svi Izvještaji</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tip</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Predmet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Korisnik</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Datum</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-white/5">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($report->type === 'bug'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
                                            🐛 Bag
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400">
                                            💡 Funkcija
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-white"><?php echo e(Str::limit($report->subject, 50)); ?></div>
                                    <div class="text-sm text-gray-400"><?php echo e(Str::limit($report->description, 80)); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-white"><?php echo e($report->user ? $report->user->name : ($report->name ?: 'Anonymous')); ?></div>
                                    <?php if($report->email): ?>
                                        <div class="text-sm text-gray-400"><?php echo e($report->email); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php switch($report->status):
                                        case ('pending'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400">
                                                Na Čekanju
                                            </span>
                                            <?php break; ?>
                                        <?php case ('in_review'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-500/20 text-purple-400">
                                                U Pregledu
                                            </span>
                                            <?php break; ?>
                                        <?php case ('resolved'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                                                Riješeno
                                            </span>
                                            <?php break; ?>
                                        <?php case ('closed'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400">
                                                Zatvoreno
                                            </span>
                                            <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    <?php echo e($report->created_at->format('M d, Y')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?php echo e(route('admin.bug-reports.show', $report)); ?>" class="text-blue-400 hover:text-blue-300">
                                        Pogledaj
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="mt-2 text-sm">Još nema izvještaja o bagovima.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($reports->hasPages()): ?>
                <div class="px-6 py-4 border-t border-white/10">
                    <?php echo e($reports->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/admin/bug-reports/index.blade.php ENDPATH**/ ?>