<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    <?php echo e($organization->name); ?>

                </h2>
                <p class="text-gray-400 mt-1"><?php echo e($organization->description ?: 'Nema opisa'); ?></p>
            </div>
            <div class="flex items-center space-x-3">
                <?php if(isset($isOwner) && $isOwner): ?>
                <a href="<?php echo e(route('organizations.edit', $organization)); ?>" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Uredi
                </a>
                <a href="<?php echo e(route('organizations.users.index', $organization)); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    Upravljaj Korisnicima
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    ← Nazad na Kontrolnu Tablu
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Organization Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Leagues Count -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">Lige</p>
                            <p class="text-3xl font-bold text-white mt-1"><?php echo e($organization->leagues->count()); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-green-400 font-medium"><?php echo e($organization->leagues->count() > 0 ? '+' . $organization->leagues->count() : '0'); ?>%</span>
                            <span class="text-gray-500 ml-2">ukupno liga</span>
                        </div>
                    </div>
                </div>

                <!-- Competitions Count -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">Turniri</p>
                            <p class="text-3xl font-bold text-white mt-1"><?php echo e($organization->competitions->count()); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-green-400 font-medium"><?php echo e($organization->competitions->count() > 0 ? '+' . $organization->competitions->count() : '0'); ?>%</span>
                            <span class="text-gray-500 ml-2">ukupno turnira</span>
                        </div>
                    </div>
                </div>

                <!-- Teams Count -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">Igrači</p>
                            <p class="text-3xl font-bold text-white mt-1"><?php echo e(optional($organization->players)->count() ?? 0); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-green-400 font-medium"><?php echo e((optional($organization->players)->count() ?? 0) > 0 ? '+' . (optional($organization->players)->count() ?? 0) : '0'); ?>%</span>
                            <span class="text-gray-500 ml-2">ukupno igrača</span>
                        </div>
                    </div>
                </div>

                <!-- Matches Count -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm font-medium">Mečevi</p>
                            <p class="text-3xl font-bold text-white mt-1">0</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <span class="text-green-400 font-medium">+0%</span>
                            <span class="text-gray-500 ml-2">od prošlog mjeseca</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Competitions Section (Tournaments) -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Turniri</h3>
                    <?php if($organization->user_id === Auth::id() && $organization->canCreateMoreCompetitions()): ?>
                        <a href="<?php echo e(route('organizations.competitions.create', $organization)); ?>" class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-4 py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-purple-500/25 inline-block">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Kreiraj Turnir</span>
                            </span>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if($organization->competitions->count() > 0): ?>
                    <?php
                        $tournaments = $organization->competitions->where('type', 'tournament');
                    ?>

                    <?php if($tournaments->count() > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php $__currentLoopData = $tournaments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $competition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-gray-700/30 rounded-xl p-4 hover:bg-gray-600/30 transition-all duration-200 relative group">
                                <a href="<?php echo e(route('organizations.competitions.show', [$organization, $competition])); ?>" class="block">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                                            <span class="text-white font-bold text-sm"><?php echo e(substr($competition->name, 0, 2)); ?></span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-white font-semibold"><?php echo e($competition->name); ?></h4>
                                            <p class="text-gray-400 text-sm"><?php echo e($competition->sport->name); ?></p>
                                        </div>
                                    </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Učesnici:</span>
                                        <span class="text-white"><?php echo e($competition->max_participants); ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Status:</span>
                                        <span class="
                                            <?php if($competition->status === 'active'): ?> text-green-400
                                            <?php elseif($competition->status === 'draft'): ?> text-yellow-400
                                            <?php elseif($competition->status === 'completed'): ?> text-blue-400
                                            <?php else: ?> text-red-400 <?php endif; ?>">
                                            <?php echo e(ucfirst($competition->status)); ?>

                                        </span>
                                    </div>
                                </div>
                                </a>
                                
                                <?php if($competition->status === 'draft'): ?>
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <form action="<?php echo e(route('organizations.competitions.destroy', [$organization, $competition])); ?>" 
                                          method="POST" 
                                          onclick="event.stopPropagation();"
                                          onsubmit="return confirm('Are you sure you want to delete <?php echo e($competition->name); ?>?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="bg-red-500/20 hover:bg-red-500/30 text-red-400 p-2 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-white font-semibold mb-2">Još nema turnira</h4>
                            <p class="text-gray-400 mb-4">Kreirajte svoj prvi turnir</p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-white font-semibold mb-2">Još nema turnira</h4>
                        <p class="text-gray-400 mb-4">Kreirajte svoj prvi turnir</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tables Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Stolovi</h3>
                    <?php if($organization->user_id === Auth::id()): ?>
                        <a href="<?php echo e(route('organizations.tables.index', $organization)); ?>" class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-4 py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-purple-500/25">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Upravljaj Stolovima</span>
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Leagues Section - Coming Soon -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl opacity-50">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Lige</h3>
                    <span class="bg-gray-600 text-gray-400 px-4 py-2 rounded-xl">
                        <span class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Uskoro</span>
                        </span>
                    </span>
                </div>

                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-500/20 to-purple-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-white font-semibold text-lg mb-2">Lige dolaze uskoro!</h4>
                    <p class="text-gray-400">Radimo na implementaciji liga. Hvala na strpljenju.</p>
                </div>
            </div>

            <!-- Players Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Igrači</h3>
                    <?php if($organization->user_id === Auth::id()): ?>
                        <a href="<?php echo e(route('organizations.players.index', $organization)); ?>" class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-4 py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-emerald-500/25">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Upravljaj Igračima</span>
                            </span>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if((optional($organization->players)->count() ?? 0) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php $__currentLoopData = optional($organization->players)->take(6) ?? collect(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $player): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('organizations.players.show', ['organization' => $organization, 'player' => $player])); ?>" class="block">
                                <div class="bg-gray-700/30 rounded-xl p-4 hover:bg-gray-600/30 transition-all duration-200 transform hover:scale-[1.02] cursor-pointer">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                                            <span class="text-white font-bold text-sm"><?php echo e(substr($player->name, 0, 2)); ?></span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-white font-semibold"><?php echo e($player->name); ?></h4>
                                            <p class="text-gray-400 text-sm"><?php echo e($player->position ?: 'Nema kluba'); ?></p>
                                        </div>
                                        <?php if($player->jersey_number): ?>
                                            <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-2 py-1 rounded-lg text-xs font-bold">
                                                <?php echo e($player->jersey_number); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-400">Tip:</span>
                                            <span class="text-<?php echo e($player->user ? 'green' : 'yellow'); ?>-400"><?php echo e($player->user ? 'Registrovan' : 'Imenovan'); ?></span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-400">Status:</span>
                                            <span class="text-<?php echo e($player->is_active ? 'green' : 'red'); ?>-400"><?php echo e($player->is_active ? 'Aktivan' : 'Neaktivan'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php if((optional($organization->players)->count() ?? 0) > 6): ?>
                        <div class="mt-4 text-center">
                            <a href="<?php echo e(route('organizations.players.index', $organization)); ?>" class="text-emerald-400 hover:text-emerald-300 text-sm font-medium transition-colors">
                                Pogledaj sve <?php echo e(optional($organization->players)->count() ?? 0); ?> Igrači →
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-white font-semibold mb-2">Još nema igrača</h4>
                        <p class="text-gray-400 mb-4">Dodajte igrače da počnete graditi svoje timove i pratiti statistiku</p>
                        <?php if($organization->user_id === Auth::id()): ?>
                            <a href="<?php echo e(route('organizations.players.index', $organization)); ?>" class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-emerald-500/25 inline-block">
                                Dodajte Svojeg Prvog Igrača
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Friendly Matches -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Nedavni Prijateljski Mečevi</h3>
                    <div class="flex items-center space-x-3">
                        <?php if($organization->user_id === Auth::id()): ?>
                        <a href="<?php echo e(route('organizations.friendly-matches.index', $organization)); ?>" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-green-500/25">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Započni Novi Meč</span>
                            </span>
                        </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('organizations.friendly-matches.index', $organization)); ?>" class="text-green-400 hover:text-green-300 text-sm font-medium transition-colors">
                            Pogledaj Sve →
                        </a>
                    </div>
                </div>

                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('friendly-matches-list', ['organizationId' => $organization->id,'organization' => $organization,'limit' => 6]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1851412453-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>

            <!-- Organization Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Details -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Detalji Organizacije</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Naziv:</span>
                            <span class="text-white"><?php echo e($organization->name); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">URL Slug:</span>
                            <span class="text-white"><?php echo e($organization->url_slug); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Kreirano:</span>
                            <span class="text-white"><?php echo e($organization->created_at->format('M j, Y')); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Status:</span>
                            <span class="text-<?php echo e($organization->is_active ? 'green' : 'red'); ?>-400"><?php echo e($organization->is_active ? 'Aktivno' : 'Neaktivno'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Vlasnik:</span>
                            <span class="text-white"><?php echo e($organization->user->name); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Plan & Limits -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Plan i Ograničenja</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Vaš Plan:</span>
                            <span class="text-blue-400 font-medium"><?php echo e($organization->user->currentPlan() ? $organization->user->currentPlan()->name : 'Free'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Iskorištenih Liga:</span>
                            <span class="text-white"><?php echo e($organization->leagues->count()); ?>/<?php echo e($organization->user->currentPlan() ? $organization->user->currentPlan()->max_leagues_per_organization : '∞'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Iskorištenih Turnira:</span>
                            <span class="text-white"><?php echo e($organization->competitions->count()); ?>/<?php echo e($organization->user->currentPlan() ? $organization->user->currentPlan()->max_competitions_per_organization : '∞'); ?></span>
                        </div>
                        <div class="w-full bg-gray-600 rounded-full h-2 mt-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo e($organization->user->currentPlan() ? (($organization->leagues->count() / $organization->user->currentPlan()->max_leagues_per_organization) * 100) : 0); ?>%"></div>
                        </div>
                        <div class="pt-2">
                            <p class="text-gray-400 text-sm">Preostalih liga: <span class="text-white"><?php echo e($organization->getRemainingLeaguesCount()); ?></span></p>
                            <p class="text-gray-400 text-sm">Preostalih turnira: <span class="text-white"><?php echo e($organization->getRemainingCompetitionsCount()); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <?php if($organization->leagues->count() === 0): ?>
            <div class="bg-red-500/10 border border-red-500/20 rounded-2xl p-6">
                <h3 class="text-xl font-bold text-red-400 mb-4">Opasna Zona</h3>
                <p class="text-gray-400 mb-4">Jednom kada obrišete ovu organizaciju, nema povratka. Budite sigurni.</p>
                <form method="POST" action="<?php echo e(route('organizations.destroy', $organization)); ?>"                     onsubmit="return confirm('Da li ste sigurni da želite obrisati ovu organizaciju? Ova akcija se ne može poništiti.')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl transition-all duration-200">
                        Obriši Organizaciju
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/show.blade.php ENDPATH**/ ?>