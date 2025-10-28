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
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Postavke Takmičenja</h1>
                        <p class="text-gray-300"><?php echo e($competition->name); ?></p>
                    </div>
                    <a href="<?php echo e(route('organizations.competitions.show', [$organization, $competition])); ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Nazad
                    </a>
                </div>
            </div>

            <?php if(session('success')): ?>
            <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                <p class="text-green-400"><?php echo e(session('success')); ?></p>
            </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
            <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                <ul class="list-disc list-inside text-red-400">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Public Visibility Toggle -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <form method="POST" action="<?php echo e(route('organizations.competitions.update', [$organization, $competition])); ?>" class="space-y-3">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    
                    <input type="hidden" name="is_public" value="0">
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-white font-medium">Javna Vidljivost</h4>
                            <p class="text-sm text-gray-400">Učini ovo takmičenje vidljivim na javnoj web stranici</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   name="is_public"
                                   value="1"
                                   <?php echo e($competition->isPublic() ? 'checked' : ''); ?>

                                   class="sr-only peer"
                                   onchange="this.form.submit()">
                            <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Quick Presets -->
                        <!-- Quick Presets -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <h3 class="text-xl font-semibold text-white mb-4">Brzi Predlošci</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button onclick="applyPreset('standard')" 
                            <?php echo e($competition->status !== 'draft' ? 'disabled' : ''); ?>

                            class="<?php echo e($competition->status !== 'draft' ? 'bg-gray-600/20 border-gray-500 cursor-not-allowed' : 'bg-blue-600/20 hover:bg-blue-600/30 border-blue-500'); ?> border-2 text-white p-4 rounded-lg transition-colors text-left">
                        <h4 class="font-semibold mb-2">� Standard (11 poena)</h4>
                        <p class="text-sm text-gray-300">Najbolji od 3, 11 poena, završetak pri 10</p>
                    </button>
                    <button onclick="applyPreset('extended')" 
                            <?php echo e($competition->status !== 'draft' ? 'disabled' : ''); ?>

                            class="<?php echo e($competition->status !== 'draft' ? 'bg-gray-600/20 border-gray-500 cursor-not-allowed' : 'bg-purple-600/20 hover:bg-purple-600/30 border-purple-500'); ?> border-2 text-white p-4 rounded-lg transition-colors text-left">
                        <h4 class="font-semibold mb-2">🎯 Produženo (15 poena)</h4>
                        <p class="text-sm text-gray-300">Najbolji od 3, 15 poena, završetak pri 14</p>
                    </button>
                    <button onclick="applyPreset('classic')" 
                            <?php echo e($competition->status !== 'draft' ? 'disabled' : ''); ?>

                            class="<?php echo e($competition->status !== 'draft' ? 'bg-gray-600/20 border-gray-500 cursor-not-allowed' : 'bg-green-600/20 hover:bg-green-600/30 border-green-500'); ?> border-2 text-white p-4 rounded-lg transition-colors text-left">
                        <h4 class="font-semibold mb-2">⚡ Classic (21 pts)</h4>
                        <p class="text-sm text-gray-300">Best of 3, 21 points, deuce at 20</p>
                    </button>
                </div>
            </div>

            <form action="<?php echo e(route('organizations.competitions.update-settings', [$organization, $competition])); ?>" method="POST" onsubmit="return validateSettingsForm(event)">
                <?php echo csrf_field(); ?>

                <fieldset <?php echo e($competition->status !== 'draft' ? 'disabled' : ''); ?>>

                <!-- Match Format -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Format Meča</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Sets to Win -->
                        <div>
                            <label for="sets_to_win" class="block text-sm font-medium text-white mb-2">
                                Setova za Pobjedu <span class="text-red-400">*</span>
                            </label>
                            <select id="sets_to_win" name="sets_to_win" required
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1" <?php echo e(old('sets_to_win', $competition->sets_to_win ?? 3) == 1 ? 'selected' : ''); ?>>1 (Najbolji od 1)</option>
                                <option value="2" <?php echo e(old('sets_to_win', $competition->sets_to_win ?? 3) == 2 ? 'selected' : ''); ?>>2 (Najbolji od 3)</option>
                                <option value="3" <?php echo e(old('sets_to_win', $competition->sets_to_win ?? 3) == 3 ? 'selected' : ''); ?>>3 (Najbolji od 5)</option>
                                <option value="4" <?php echo e(old('sets_to_win', $competition->sets_to_win ?? 3) == 4 ? 'selected' : ''); ?>>4 (Najbolji od 7)</option>
                            </select>
                            <p class="text-gray-400 text-xs mt-1">Broj setova koji igrač treba da osvoji za pobjedu u meču</p>
                        </div>

                        <!-- Points per Set -->
                        <div>
                            <label for="points_per_set" class="block text-sm font-medium text-white mb-2">
                                Poena po Setu <span class="text-red-400">*</span>
                            </label>
                            <select id="points_per_set" name="points_per_set" required
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="7" <?php echo e(old('points_per_set', $competition->points_per_set ?? 11) == 7 ? 'selected' : ''); ?>>7 poena</option>
                                <option value="11" <?php echo e(old('points_per_set', $competition->points_per_set ?? 11) == 11 ? 'selected' : ''); ?>>11 poena (Standard)</option>
                                <option value="15" <?php echo e(old('points_per_set', $competition->points_per_set ?? 11) == 15 ? 'selected' : ''); ?>>15 poena</option>
                                <option value="21" <?php echo e(old('points_per_set', $competition->points_per_set ?? 11) == 21 ? 'selected' : ''); ?>>21 poen (Klasično)</option>
                            </select>
                            <p class="text-gray-400 text-xs mt-1">Poeni potrebni za pobjedu u setu</p>
                        </div>

                        <!-- Deuce At -->
                        <div>
                            <label for="deuce_at" class="block text-sm font-medium text-white mb-2">
                                Deuce na
                            </label>
                            <input type="number" id="deuce_at" name="deuce_at" 
                                   value="<?php echo e(old('deuce_at', $competition->deuce_at ?? 10)); ?>"
                                   min="5" max="20"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Poeni na kojima počinje deuce</p>
                        </div>

                        <!-- Must Win by Two -->
                        <div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="must_win_by_two" name="must_win_by_two" value="1"
                                       <?php echo e(old('must_win_by_two', $competition->must_win_by_two ?? true) ? 'checked' : ''); ?>

                                       class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-white font-medium">Mora pobijediti sa dva poena</span>
                                    <p class="text-gray-400 text-xs">Igrač mora pobijediti sa dva poena razlike</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Tiebreak Settings -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Postavke Tiebreak-a</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Has Tiebreak -->
                        <div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="has_tiebreak" name="has_tiebreak" value="1"
                                       <?php echo e(old('has_tiebreak', $competition->has_tiebreak ?? false) ? 'checked' : ''); ?>

                                       onchange="toggleTiebreakPoints()"
                                       class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-white font-medium">Koristi tiebreak u finalnom setu</span>
                                    <p class="text-gray-400 text-xs">Omogući tiebreak u finalnom setu kada je rezultat jednak</p>
                                </div>
                            </label>
                        </div>

                        <!-- Tiebreak Points -->
                        <div id="tiebreakPointsDiv">
                            <label for="tiebreak_points" class="block text-sm font-medium text-white mb-2">
                                Tiebreak Poeni
                            </label>
                            <input type="number" id="tiebreak_points" name="tiebreak_points" 
                                   value="<?php echo e(old('tiebreak_points', $competition->tiebreak_points ?? 7)); ?>"
                                   min="5" max="15"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Poeni potrebni za pobjedu u tiebreak-u</p>
                        </div>
                    </div>
                </div>

                <!-- Tournament Settings -->
                <?php if($competition->type === 'tournament'): ?>
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Postavke Turnira</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Players Advancing per Group -->
                        <div>
                            <label for="players_advancing_per_group" class="block text-sm font-medium text-white mb-2">
                                Igrača koji Napreduju po Grupi <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="players_advancing_per_group" name="players_advancing_per_group" 
                                   value="<?php echo e(old('players_advancing_per_group', $competition->players_advancing_per_group ?? 2)); ?>"
                                   min="1" max="4" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Broj igrača koji prolaze u eliminacionu fazu iz svake grupe</p>
                        </div>
                    </div>

                    <!-- Manual Knockout Selection -->
                    <div class="mt-6">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" id="manual_knockout_selection" name="manual_knockout_selection" value="1"
                                   <?php echo e(old('manual_knockout_selection', $competition->manual_knockout_selection ?? true) ? 'checked' : ''); ?>

                                   class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-white font-medium">Ručno Odabiranje za Eliminacionu Faz</span>
                                <p class="text-gray-400 text-xs">Administrator ručno odabire igrače koji prolaze u eliminacionu fazu</p>
                            </div>
                        </label>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Group Stage Scoring -->
                <?php if($competition->type === 'tournament'): ?>
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Bodovanje Grupne Faze</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Points for Win -->
                        <div>
                            <label for="points_for_win" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Pobjedu <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_win" name="points_for_win" 
                                   value="<?php echo e(old('points_for_win', $competition->points_for_win ?? 2)); ?>"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za pobjedu u meču</p>
                        </div>

                        <!-- Points for Draw -->
                        <div>
                            <label for="points_for_draw" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Neriješeno <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_draw" name="points_for_draw" 
                                   value="<?php echo e(old('points_for_draw', $competition->points_for_draw ?? 1)); ?>"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za neriješeni meč</p>
                        </div>

                        <!-- Points for Loss -->
                        <div>
                            <label for="points_for_loss" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Poraz <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_loss" name="points_for_loss" 
                                   value="<?php echo e(old('points_for_loss', $competition->points_for_loss ?? 0)); ?>"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za izgubljeni meč</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- League Scoring -->
                <?php if($competition->type === 'league'): ?>
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Bodovanje Lige</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Points for Win -->
                        <div>
                            <label for="points_for_win" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Pobjedu <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_win" name="points_for_win" 
                                   value="<?php echo e(old('points_for_win', $competition->points_for_win ?? 2)); ?>"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za pobjedu u meču</p>
                        </div>

                        <!-- Points for Draw -->
                        <div>
                            <label for="points_for_draw" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Neriješeno <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_draw" name="points_for_draw" 
                                   value="<?php echo e(old('points_for_draw', $competition->points_for_draw ?? 1)); ?>"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za neriješeni meč</p>
                        </div>

                        <!-- Points for Loss -->
                        <div>
                            <label for="points_for_loss" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Poraz <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_loss" name="points_for_loss" 
                                   value="<?php echo e(old('points_for_loss', $competition->points_for_loss ?? 0)); ?>"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za izgubljeni meč</p>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-green-500/10 border border-green-500/20 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-green-400 text-sm">
                                    Ovi bodovi određuju plasman u ligi. Uobičajene postavke:
                                </p>
                                <ul class="text-green-300 text-xs mt-2 space-y-1">
                                    <li>• Standard: 2 za pobjedu, 1 za neriješeno, 0 za poraz</li>
                                    <li>• Samo pobjede: 3 za pobjedu, 0 za neriješeno, 0 za poraz</li>
                                    <li>• Svi bodovi: 2 za pobjedu, 1 za neriješeno, 0 za poraz</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                </div>

                <!-- Save Button -->
                <div class="flex space-x-4">
                    <button type="submit" 
                            <?php echo e($competition->status !== 'draft' ? 'disabled' : ''); ?>

                            class="flex-1 <?php echo e($competition->status !== 'draft' ? 'bg-gray-600 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'); ?> text-white px-6 py-3 rounded-lg transition-colors font-semibold">
                        Sačuvaj Postavke
                    </button>
                    <a href="<?php echo e(route('organizations.competitions.show', [$organization, $competition])); ?>"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors text-center">
                        Otkaži
                    </a>
                </div>

                </fieldset>
            </form>

        </div>
    </div>

    <script>
        // Presets
        const presets = {
            standard: {
                sets_to_win: 2,
                points_per_set: 11,
                deuce_at: 10,
                must_win_by_two: true,
                points_for_win: 2,
                points_for_draw: 1,
                points_for_loss: 0,
                has_tiebreak: false,
                tiebreak_points: 7
            },
            extended: {
                sets_to_win: 2,
                points_per_set: 15,
                deuce_at: 14,
                must_win_by_two: true,
                points_for_win: 2,
                points_for_draw: 1,
                points_for_loss: 0,
                has_tiebreak: false,
                tiebreak_points: 7
            },
            classic: {
                sets_to_win: 2,
                points_per_set: 21,
                deuce_at: 20,
                must_win_by_two: true,
                points_for_win: 3,
                points_for_draw: 0,
                points_for_loss: 0,
                has_tiebreak: false,
                tiebreak_points: 7
            }
        };

        function applyPreset(presetName) {
            const preset = presets[presetName];
            
            document.getElementById('sets_to_win').value = preset.sets_to_win;
            document.getElementById('points_per_set').value = preset.points_per_set;
            document.getElementById('deuce_at').value = preset.deuce_at;
            document.getElementById('must_win_by_two').checked = preset.must_win_by_two;
            document.getElementById('has_tiebreak').checked = preset.has_tiebreak;
            document.getElementById('tiebreak_points').value = preset.tiebreak_points;
            
            if (document.getElementById('points_for_win')) {
                document.getElementById('points_for_win').value = preset.points_for_win;
                document.getElementById('points_for_draw').value = preset.points_for_draw;
                document.getElementById('points_for_loss').value = preset.points_for_loss;
            }

            toggleTiebreakPoints();
            
            // Show notification
            showNotification(`Applied ${presetName.charAt(0).toUpperCase() + presetName.slice(1)} preset!`, 'success');
        }

        function toggleTiebreakPoints() {
            const hasTiebreak = document.getElementById('has_tiebreak').checked;
            const tiebreakDiv = document.getElementById('tiebreakPointsDiv');
            
            if (hasTiebreak) {
                tiebreakDiv.style.display = 'block';
            } else {
                tiebreakDiv.style.display = 'none';
            }
        }

        function showNotification(message, type = 'info') {
            let container = document.getElementById('toast-container-bottom-center');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container-bottom-center';
                container.className = 'fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 flex flex-col items-center gap-3 pointer-events-none';
                document.body.appendChild(container);
            }

            const notification = document.createElement('div');
            notification.className = `pointer-events-auto max-w-xl w-full px-6 py-3 rounded-lg shadow-lg transition-opacity duration-300 ease-out ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } text-white opacity-0`;
            notification.textContent = message;

            container.appendChild(notification);
            requestAnimationFrame(() => { notification.classList.remove('opacity-0'); notification.classList.add('opacity-100'); });

            setTimeout(() => {
                notification.classList.remove('opacity-100');
                notification.classList.add('opacity-0');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleTiebreakPoints();
            
            // Auto-update deuce_at when points_per_set changes
            document.getElementById('points_per_set').addEventListener('change', function() {
                const points = parseInt(this.value);
                document.getElementById('deuce_at').value = points - 1;
            });
        });

        // Validate settings form submission
        function validateSettingsForm(event) {
            const competitionStatus = '<?php echo e($competition->status); ?>';
            
            if (competitionStatus !== 'draft') {
                // Check if only knockout_matches_count is being changed
                const knockoutInput = document.getElementById('knockout_matches_count');
                const originalValue = '<?php echo e($competition->knockout_matches_count ?? 7); ?>';
                const currentValue = knockoutInput.value;
                
                // Check if any other inputs have changed (simple check)
                const allInputs = event.target.querySelectorAll('input:not([type="hidden"]), select');
                let otherFieldsChanged = false;
                
                allInputs.forEach(input => {
                    if (input.id !== 'knockout_matches_count' && input.name !== '_token') {
                        // For simplicity, just check if knockout_matches_count is the only field that might have changed
                        // In a real scenario, you'd want to track original values
                    }
                });
                
                // Allow submission if only knockout_matches_count changed
                if (currentValue !== originalValue) {
                    // Confirm with user
                    if (!confirm('Da li ste sigurni da želite promijeniti broj mečeva u eliminacionoj fazi? Ovo može uticati na već planirane mečeve.')) {
                        return false;
                    }
                    return true;
                }
                
                // Prevent submission for other changes
                showNotification('Postavke se mogu mijenjati samo u draft fazi turnira.', 'error');
                return false;
            }
            
            return true;
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/competitions/settings.blade.php ENDPATH**/ ?>