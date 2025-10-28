<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-600 rounded-full">
                                <span class="text-white font-bold">1</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-white font-semibold">Dodaj Igrače</h3>
                                <p class="text-gray-400 text-sm">Odaberite učesnike</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 h-1 bg-gray-700 mx-4"></div>
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 bg-gray-700 rounded-full">
                                <span class="text-gray-400 font-bold">2</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-400 font-semibold">Postavi Grupe</h3>
                                <p class="text-gray-500 text-sm">Organizujte učesnike</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 h-1 bg-gray-700 mx-4"></div>
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 bg-gray-700 rounded-full">
                                <span class="text-gray-400 font-bold">3</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-400 font-semibold">Započni Takmičenje</h3>
                                <p class="text-gray-500 text-sm">Započni mečeve</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Content - Add Players (2 columns) -->
                <div class="lg:col-span-2">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('add-player-to-competition', ['organization' => $organization, 'competition' => $competition]);

$__html = app('livewire')->mount($__name, $__params, 'add-player-' . $competition->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

                    <!-- Current Participants -->
                    <div class="mt-6 bg-gray-800/50 backdrop-blur-xl rounded-xl p-6 border border-gray-700/50 shadow-xl"
                         id="participants-list">
                        <h3 class="text-xl font-bold text-white mb-4">
                            Trenutni Učesnici
                            <span class="text-gray-400 text-sm ml-2">(<?php echo e($competition->players->count()); ?>/<?php echo e($competition->max_participants ?? '∞'); ?>)</span>
                        </h3>

                        <!--[if BLOCK]><![endif]--><?php if($competition->players->count() > 0): ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $competition->players; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $player): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center justify-between bg-gray-700/30 rounded-lg p-3 hover:bg-gray-700/50 transition-colors">
                                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-white font-bold text-sm"><?php echo e(substr($player->name, 0, 2)); ?></span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-white font-medium truncate"><?php echo e($player->name); ?></p>
                                                <!--[if BLOCK]><![endif]--><?php if($player->position): ?>
                                                    <p class="text-gray-400 text-xs truncate">(<?php echo e($player->position); ?>)</p>
                                                <?php elseif($player->email): ?>
                                                    <p class="text-gray-400 text-xs truncate"><?php echo e($player->email); ?></p>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>
                                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('remove-player-from-competition', ['organization' => $organization, 'competition' => $competition, 'player' => $player]);

$__html = app('livewire')->mount($__name, $__params, 'remove-player-' . $player->id, $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-400">Još nema dodanih igrača. Koristite obrazac iznad da dodate igrače.</p>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- Sidebar - Info & Actions (1 column) -->
                <div class="space-y-6">

                    <!-- Competition Info -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-5 border border-gray-700/50 shadow-xl">
                        <h3 class="text-base font-bold text-white mb-3">Informacije o Takmičenju</h3>
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">Tip</span>
                                <span class="text-white text-xs font-medium"><?php echo e(ucfirst($competition->type)); ?></span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">Format</span>
                                <span class="text-white text-xs font-medium"><?php echo e($competition->is_team_based ? 'Tim' : 'Individualno'); ?></span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">Sport</span>
                                <span class="text-white text-xs font-medium"><?php echo e($competition->sport->name); ?></span>
                            </div>
                            <!--[if BLOCK]><![endif]--><?php if($competition->type === 'tournament'): ?>
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">Grupe</span>
                                <span class="text-white text-xs font-medium"><?php echo e($competition->group_count); ?></span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">Po Grupi</span>
                                <span class="text-white text-xs font-medium"><?php echo e($competition->players_per_group); ?></span>
                            </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <!-- Next Step Card -->
                    <?php
                        $hasEnoughPlayers = $competition->players->count() >= ($competition->type === 'tournament' ? 4 : 2);
                    ?>

                    <!--[if BLOCK]><![endif]--><?php if($hasEnoughPlayers): ?>
                        <div class="bg-gradient-to-r from-green-600/20 to-emerald-600/20 backdrop-blur-xl rounded-xl p-5 border border-green-500/30 shadow-xl">
                            <div class="flex items-start mb-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-base font-bold text-white">Spremni za Sledeći Korak!</h3>
                                    <p class="text-gray-300 text-sm mt-1">Imate dovoljno igrača da nastavite.</p>
                                </div>
                            </div>

                            <!--[if BLOCK]><![endif]--><?php if($competition->type === 'tournament'): ?>
                                <a href="<?php echo e(route('organizations.competitions.setup-groups', [$organization, $competition])); ?>"
                                   class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-3 rounded-lg transition-colors font-semibold">
                                    Nastavi na Postavljanje Grupa →
                                </a>
                            <?php else: ?>
                                <form method="POST" action="<?php echo e(route('organizations.competitions.start', [$organization, $competition])); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition-colors font-semibold">
                                        <?php echo e(__('Start League')); ?> →
                                    </button>
                                </form>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php else: ?>
                        <div class="bg-gradient-to-r from-yellow-600/20 to-orange-600/20 backdrop-blur-xl rounded-xl p-5 border border-yellow-500/30 shadow-xl">
                            <div class="flex items-start mb-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-base font-bold text-white">Dodaj više igrača</h3>
                                <p class="text-gray-300 text-sm mt-1">
                                    <!--[if BLOCK]><![endif]--><?php if($competition->type === 'tournament'): ?>
                                        Potrebno je najmanje 4 igrača za početak.
                                    <?php else: ?>
                                        Potrebno je najmanje 2 igrača za početak.
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </p>
                                <div class="mt-3 flex items-center justify-between bg-gray-800/50 rounded-lg px-3 py-2">
                                    <span class="text-gray-400 text-xs">Napredak</span>
                                    <span class="text-white font-bold"><?php echo e($competition->players->count()); ?>/<?php echo e($competition->type === 'tournament' ? 4 : 2); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!-- Quick Actions -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-5 border border-gray-700/50 shadow-xl">
                        <h3 class="text-base font-bold text-white mb-3"><?php echo e(__('Quick Actions')); ?></h3>
                        <div class="space-y-2">
                            <a href="<?php echo e(route('organizations.competitions.bulk-import', [$organization, $competition])); ?>"
                               class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center text-sm px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                                📄 <?php echo e(__('Bulk Import Players')); ?>

                            </a>
                            <a href="<?php echo e(route('organizations.competitions.show', [$organization, $competition])); ?>"
                               class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center text-sm px-4 py-2 rounded-lg transition-colors">
                                ← <?php echo e(__('Back to Competition')); ?>

                            </a>
                            <a href="<?php echo e(route('organizations.competitions.settings', [$organization, $competition])); ?>"
                               class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center text-sm px-4 py-2 rounded-lg transition-colors">
                                ⚙️ <?php echo e(__('Settings')); ?>

                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/livewire/manage-players.blade.php ENDPATH**/ ?>