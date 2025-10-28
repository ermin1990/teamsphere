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
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    <?php echo e($organization->name); ?>

                </h2>
                <p class="text-gray-400 mt-1">Kreiraj Novo Takmičenje</p>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                <form action="<?php echo e(route('organizations.competitions.store', $organization)); ?>" method="POST" id="competitionForm" class="space-y-8">
                    <?php echo csrf_field(); ?>

                    <!-- Competition Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-white mb-2">
                            Naziv Takmičenja <span class="text-red-400">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="<?php echo e(old('name')); ?>"
                               required
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Unesite naziv takmičenja">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-white mb-2">
                            Opis
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="3"
                                  class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                  placeholder="Opcionalni opis takmičenja"><?php echo e(old('description')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Sport Selection -->
                    <div>
                        <label for="sport_id" class="block text-sm font-medium text-white mb-2">
                            Sport <span class="text-red-400">*</span>
                        </label>
                        <select id="sport_id"
                                name="sport_id"
                                required
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">Odaberite sport</option>
                            <?php $__currentLoopData = $sports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($sport->id); ?>" <?php echo e(old('sport_id') == $sport->id ? 'selected' : ''); ?>>
                                    <?php echo e($sport->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['sport_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Competition Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-white mb-2">
                            Format Takmičenja <span class="text-red-400">*</span>
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" id="tournament" name="type" value="tournament" <?php echo e(old('type', 'tournament') === 'tournament' ? 'checked' : ''); ?>

                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2" onchange="toggleCompetitionType()">
                                <label for="tournament" class="ml-3 text-sm font-medium text-white">
                                    Turnir - Grupna faza + eliminaciona faza
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="knockout" name="type" value="knockout" <?php echo e(old('type') === 'knockout' ? 'checked' : ''); ?>

                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2" onchange="toggleCompetitionType()" disabled>
                                <label for="knockout" class="ml-3 text-sm font-medium text-gray-500">
                                    Samo Eliminacija - Direktno eliminaciono takmičenje <span class="text-xs text-gray-400">(uskoro)</span>
                                </label>
                            </div>
                        </div>
                        <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Team vs Individual -->
                    <div id="playerFormatSection">
                        <label class="block text-sm font-medium text-white mb-2">
                            Format Igrača <span class="text-red-400">*</span>
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" id="individual_based" name="is_team_based" value="0" <?php echo e(old('is_team_based', '0') === '0' ? 'checked' : ''); ?>

                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2">
                                <label for="individual_based" class="ml-3 text-sm font-medium text-white">
                                    Individualno takmičenje
                                </label>
                            </div>
                        </div>
                        <?php $__errorArgs = ['is_team_based'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Tournament Settings -->
                    <div id="tournamentSettings" class="space-y-6" style="display: none;">
                        <h3 class="text-lg font-semibold text-white border-b border-gray-600 pb-2">Postavke Turnira</h3>

                        <!-- Players Advancing Per Group -->
                        <div>
                            <label for="players_advancing_per_group" class="block text-sm font-medium text-white mb-2">
                                Igrača Koji Napreduju Po Grupi <span class="text-red-400">*</span>
                            </label>
                            <input type="number"
                                   id="players_advancing_per_group"
                                   name="players_advancing_per_group"
                                   value="<?php echo e(old('players_advancing_per_group', 2)); ?>"
                                   min="1"
                                   max="4"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-400">Koliko igrača napreduje iz svake grupe u eliminacionu fazu (1-4)</p>
                            <?php $__errorArgs = ['players_advancing_per_group'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Advancement Method -->
                        <div>
                            <label for="advancement_method" class="block text-sm font-medium text-white mb-2">
                                Metoda Napredovanja <span class="text-red-400">*</span>
                            </label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" id="automatic" name="advancement_method" value="automatic" <?php echo e(old('advancement_method', 'automatic') === 'automatic' ? 'checked' : ''); ?>

                                           class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2">
                                    <label for="automatic" class="ml-3 text-sm font-medium text-white">
                                        Automatski - Najbolji igrači napreduju na osnovu bodova
                                    </label>
                                </div>
                            </div>
                            <?php $__errorArgs = ['advancement_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Manual Knockout Selection -->
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">
                                Odabir Igrača za Eliminacionu Fazu
                            </label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="manual_knockout_selection" name="manual_knockout_selection" value="1" <?php echo e(old('manual_knockout_selection') ? 'checked' : ''); ?>

                                           class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2 rounded">
                                    <label for="manual_knockout_selection" class="ml-3 text-sm font-medium text-white">
                                        Omogući ručni odabir igrača za eliminacionu fazu
                                    </label>
                                </div>
                                <p class="text-xs text-gray-400 ml-6">Ako je omogućeno, organizator će ručno birati igrače za svaki meč u eliminacionoj fazi umjesto automatskog generisanja</p>
                            </div>
                            <?php $__errorArgs = ['manual_knockout_selection'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Competition Duration -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-white mb-2">
                                    Datum Početka <span class="text-red-400">*</span>
                                </label>
                                <input type="date"
                                       id="start_date"
                                       name="start_date"
                                       value="<?php echo e(old('start_date', now()->format('Y-m-d'))); ?>"
                                       required
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-white mb-2">
                                    Datum Završetka
                                </label>
                                <input type="date"
                                       id="end_date"
                                       name="end_date"
                                       value="<?php echo e(old('end_date')); ?>"
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-600">
                        <a href="<?php echo e(route('organizations.show', $organization)); ?>"
                           class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors font-semibold">
                            Otkaži
                        </a>
                        <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg transition-all duration-200 font-semibold shadow-lg hover:shadow-xl">
                            Kreiraj Takmičenje
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleCompetitionType() {
            const tournamentRadio = document.getElementById('tournament');
            const knockoutRadio = document.getElementById('knockout');
            const tournamentSettings = document.getElementById('tournamentSettings');
            const playerFormatSection = document.getElementById('playerFormatSection');

            if (tournamentRadio.checked) {
                tournamentSettings.style.display = 'block';
                playerFormatSection.style.display = 'none';
                // Make tournament fields required
                document.getElementById('max_participants').required = false;
                document.getElementById('group_count').required = false;
                document.getElementById('players_per_group').required = false;
                document.getElementById('players_advancing_per_group').required = true;
                document.getElementById('advancement_method').required = true;
                // Set is_team_based to 0 for tournaments
                document.getElementById('individual_based').checked = true;
            } else if (knockoutRadio.checked) {
                tournamentSettings.style.display = 'none';
                playerFormatSection.style.display = 'none';
                // Remove required from tournament fields
                document.getElementById('max_participants').required = false;
                document.getElementById('group_count').required = false;
                document.getElementById('players_per_group').required = false;
                document.getElementById('players_advancing_per_group').required = false;
                document.getElementById('advancement_method').required = false;
                // Set is_team_based to 0 for knockout
                document.getElementById('individual_based').checked = true;
            } else {
                tournamentSettings.style.display = 'none';
                playerFormatSection.style.display = 'block';
                // Remove required from tournament fields
                document.getElementById('max_participants').required = false;
                document.getElementById('group_count').required = false;
                document.getElementById('players_per_group').required = false;
                document.getElementById('players_advancing_per_group').required = false;
                document.getElementById('advancement_method').required = false;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleCompetitionType();
        });
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
<?php endif; ?><?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/competitions/create.blade.php ENDPATH**/ ?>