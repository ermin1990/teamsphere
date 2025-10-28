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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Postavi Grupe Turnira</h1>
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

            <?php if(session('error')): ?>
            <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                <p class="text-red-400"><?php echo e(session('error')); ?></p>
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

            <!-- Info Box -->
            <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-blue-400 font-medium mb-1">Kako postaviti grupe:</p>
                        <ul class="text-blue-300 text-sm space-y-1">
                            <li>• Povucite igrače sa liste s lijeva na grupe s desna</li>
                            <li>• Ili pretražite i kliknite dugme "Dodaj u Grupu"</li>
                            <li>• Svaka grupa bi trebala imati 2-<?php echo e($competition->players_per_group); ?> igrača</li>
                            <li>• Kliknite "Shuffle" da nasumično rasporedite nedodijeljene igrače</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form id="groupsForm" action="<?php echo e(route('organizations.competitions.save-groups', [$organization, $competition])); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Available Players -->
                    <div class="lg:col-span-1">
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl sticky top-4">
                            <h3 class="text-xl font-semibold text-white mb-4">
                                Dostupni Igrači
                                <span id="unassignedCount" class="text-gray-400 text-sm ml-2"></span>
                            </h3>
                            
                            <!-- Search -->
                            <div class="mb-4">
                                <input type="text" 
                                       id="playerSearch" 
                                       placeholder="Pretraži igrače..."
                                       class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Shuffle Button -->
                            <button type="button" 
                                    id="shuffleBtn"
                                    class="w-full mb-4 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                                🎲 Shuffle Preostale Igrače
                            </button>

                            <!-- Players List -->
                            <div id="availablePlayers" class="space-y-2 max-h-[600px] overflow-y-auto">
                                <?php $__currentLoopData = $availablePlayers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $player): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="player-item bg-gray-700/30 rounded-lg p-3 cursor-move hover:bg-gray-700/50 transition-colors" 
                                     data-player-id="<?php echo e($player->id); ?>"
                                     data-player-name="<?php echo e($player->name); ?>"
                                     draggable="true">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-sm"><?php echo e(substr($player->name, 0, 2)); ?></span>
                                            </div>
                                            <div>
                                                <p class="text-white font-medium"><?php echo e($player->name); ?></p>
                                                <?php if($player->position): ?>
                                                    <p class="text-xs text-gray-400">(<?php echo e($player->position); ?>)</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <button type="button" 
                                                class="add-to-group-btn hidden bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                            Dodaj
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Groups -->
                    <div class="lg:col-span-2">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-white">Grupe Turnira</h3>
                            <div class="flex space-x-2">
                                <button type="button" 
                                        id="addGroupBtn"
                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg transition-colors text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Dodaj Grupu
                                </button>
                                <button type="button" 
                                        id="removeGroupBtn"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg transition-colors text-sm flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                    Ukloni Grupu
                                </button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="groupsContainer">
                            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="group-container bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl"
                                 data-group-index="<?php echo e($index); ?>">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-semibold text-white">
                                        Grupa <?php echo e($group['name']); ?>

                                    </h3>
                                    <span class="group-count text-sm px-3 py-1 rounded-full bg-gray-700">
                                        <span class="current-count"><?php echo e(count($group['players'])); ?></span> igrača
                                    </span>
                                </div>

                                <div class="group-dropzone min-h-[300px] border-2 border-dashed border-gray-600 rounded-lg p-4 space-y-2"
                                     data-group-number="<?php echo e($group['number']); ?>">
                                    <?php $__currentLoopData = $group['players']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $player): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="player-item bg-gray-700/30 rounded-lg p-3 cursor-move hover:bg-gray-700/50 transition-colors" 
                                         data-player-id="<?php echo e($player['id']); ?>"
                                         data-player-name="<?php echo e($player['name']); ?>"
                                         draggable="true">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm"><?php echo e(substr($player['name'], 0, 2)); ?></span>
                                                </div>
                                                <div>
                                                    <p class="text-white font-medium"><?php echo e($player['name']); ?></p>
                                                    <?php if(isset($player['position']) && $player['position']): ?>
                                                        <p class="text-xs text-gray-400">(<?php echo e($player['position']); ?>)</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <button type="button" 
                                                    class="remove-player-btn bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                                Ukloni
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                                <button type="button" 
                                        class="clear-group-btn mt-4 w-full bg-red-600/20 hover:bg-red-600/30 text-red-400 px-4 py-2 rounded-lg transition-colors text-sm">
                                    Očisti Grupu
                                </button>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <!-- Save Button -->
                        <div class="mt-6 flex space-x-4">
                            <button type="submit" 
                                    id="saveBtn"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                                Sačuvaj Grupe i Nastavi
                            </button>
                            <button type="button" 
                                    id="resetBtn"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors">
                                Resetuj Sve
                            </button>
                        </div>
                    </div>

                </div>

            </form>

        </div>
    </div>

    <script>
        const playersPerGroup = <?php echo e($competition->players_per_group); ?>;
        let groupCount = <?php echo e(count($groups)); ?>;
        let draggedElement = null;
        let nextGroupNumber = <?php echo e(count($groups) + 1); ?>;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateUnassignedCount();
            updateAllGroupCounts();
            updateSaveButton();
            initializeDragAndDrop();
            initializeSearch();
            initializeButtons();
            initializeGroupManagement();
        });

        // Drag and Drop
        function initializeDragAndDrop() {
            // Make player items draggable
            document.querySelectorAll('.player-item').forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragend', handleDragEnd);
            });

            // Make group dropzones accept drops
            document.querySelectorAll('.group-dropzone').forEach(zone => {
                zone.addEventListener('dragover', handleDragOver);
                zone.addEventListener('drop', handleDrop);
                zone.addEventListener('dragleave', handleDragLeave);
            });

            // Make available players list accept drops (for returning players)
            const availablePlayers = document.getElementById('availablePlayers');
            availablePlayers.addEventListener('dragover', handleDragOver);
            availablePlayers.addEventListener('drop', handleDropToAvailable);
        }

        function handleDragStart(e) {
            draggedElement = this;
            this.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
        }

        function handleDragEnd(e) {
            this.style.opacity = '1';
        }

        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';
            
            if (this.classList.contains('group-dropzone')) {
                this.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';
                this.style.borderColor = 'rgb(59, 130, 246)';
            }
            return false;
        }

        function handleDragLeave(e) {
            if (this.classList.contains('group-dropzone')) {
                this.style.backgroundColor = '';
                this.style.borderColor = '';
            }
        }

        function handleDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }
            
            this.style.backgroundColor = '';
            this.style.borderColor = '';

            const groupDropzone = this.closest('.group-dropzone');
            const currentCount = groupDropzone.querySelectorAll('.player-item').length;

            // Check if group is full
            if (currentCount >= playersPerGroup) {
                showNotification('Grupa je puna!', 'error');
                return false;
            }

            // Check if player is already in this group
            const playerId = draggedElement.dataset.playerId;
            if (groupDropzone.querySelector(`[data-player-id="${playerId}"]`)) {
                return false;
            }

            // Move player to group
            const clonedElement = draggedElement.cloneNode(true);
            clonedElement.addEventListener('dragstart', handleDragStart);
            clonedElement.addEventListener('dragend', handleDragEnd);
            addRemovePlayerButton(clonedElement);
            
            groupDropzone.appendChild(clonedElement);
            draggedElement.remove();

            updateUnassignedCount();
            updateGroupCount(groupDropzone.closest('.group-container'));
            updateSaveButton();

            return false;
        }

        function handleDropToAvailable(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }

            // Return player to available list
            const clonedElement = draggedElement.cloneNode(true);
            clonedElement.addEventListener('dragstart', handleDragStart);
            clonedElement.addEventListener('dragend', handleDragEnd);
            
            document.getElementById('availablePlayers').appendChild(clonedElement);
            draggedElement.remove();

            updateUnassignedCount();
            updateAllGroupCounts();
            updateSaveButton();

            return false;
        }

        // Search
        function initializeSearch() {
            const searchInput = document.getElementById('playerSearch');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('#availablePlayers .player-item').forEach(item => {
                    const playerName = item.dataset.playerName.toLowerCase();
                    if (playerName.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Buttons
        function initializeButtons() {
            // Shuffle button
            document.getElementById('shuffleBtn').addEventListener('click', function() {
                const availablePlayers = Array.from(document.querySelectorAll('#availablePlayers .player-item'));
                
                if (availablePlayers.length === 0) {
                    showNotification('Nema igrača za shuffle!', 'error');
                    return;
                }

                // Shuffle array
                for (let i = availablePlayers.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [availablePlayers[i], availablePlayers[j]] = [availablePlayers[j], availablePlayers[i]];
                }

                // Distribute to groups
                const groups = document.querySelectorAll('.group-dropzone');
                let playerIndex = 0;

                groups.forEach(group => {
                    const currentCount = group.querySelectorAll('.player-item').length;
                    const neededPlayers = playersPerGroup - currentCount;

                    for (let i = 0; i < neededPlayers && playerIndex < availablePlayers.length; i++) {
                        const player = availablePlayers[playerIndex];
                        const clonedElement = player.cloneNode(true);
                        clonedElement.addEventListener('dragstart', handleDragStart);
                        clonedElement.addEventListener('dragend', handleDragEnd);
                        addRemovePlayerButton(clonedElement);
                        
                        group.appendChild(clonedElement);
                        player.remove();
                        playerIndex++;
                    }

                    updateGroupCount(group.closest('.group-container'));
                });

                updateUnassignedCount();
                updateSaveButton();
                showNotification('Igrači su shuffle-ovani!', 'success');
            });

            // Clear group buttons
            document.querySelectorAll('.clear-group-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const groupContainer = this.closest('.group-container');
                    const dropzone = groupContainer.querySelector('.group-dropzone');
                    const players = Array.from(dropzone.querySelectorAll('.player-item'));

                    players.forEach(player => {
                        const clonedElement = player.cloneNode(true);
                        clonedElement.addEventListener('dragstart', handleDragStart);
                        clonedElement.addEventListener('dragend', handleDragEnd);
                        
                        document.getElementById('availablePlayers').appendChild(clonedElement);
                        player.remove();
                    });

                    updateGroupCount(groupContainer);
                    updateUnassignedCount();
                    updateSaveButton();
                });
            });

            // Reset all button
            document.getElementById('resetBtn').addEventListener('click', function() {
                if (!confirm('Da li ste sigurni da želite resetovati sve grupe?')) {
                    return;
                }

                document.querySelectorAll('.group-dropzone').forEach(dropzone => {
                    const players = Array.from(dropzone.querySelectorAll('.player-item'));
                    players.forEach(player => {
                        const clonedElement = player.cloneNode(true);
                        clonedElement.addEventListener('dragstart', handleDragStart);
                        clonedElement.addEventListener('dragend', handleDragEnd);
                        
                        document.getElementById('availablePlayers').appendChild(clonedElement);
                        player.remove();
                    });
                });

                updateAllGroupCounts();
                updateUnassignedCount();
                updateSaveButton();
                showNotification('Sve grupe su očišćene!', 'success');
            });
        }

        // Group Management
        function initializeGroupManagement() {
            // Add group button
            document.getElementById('addGroupBtn').addEventListener('click', function() {
                addNewGroup();
            });

            // Remove group button
            document.getElementById('removeGroupBtn').addEventListener('click', function() {
                removeLastGroup();
            });

            // Remove player buttons (for existing players)
            document.querySelectorAll('.remove-player-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const playerItem = this.closest('.player-item');
                    const groupContainer = this.closest('.group-container');
                    
                    // Move player back to available list
                    const clonedElement = playerItem.cloneNode(true);
                    clonedElement.addEventListener('dragstart', handleDragStart);
                    clonedElement.addEventListener('dragend', handleDragEnd);
                    
                    document.getElementById('availablePlayers').appendChild(clonedElement);
                    playerItem.remove();
                    
                    updateGroupCount(groupContainer);
                    updateUnassignedCount();
                    updateSaveButton();
                });
            });
        }

        function addNewGroup() {
            const groupName = String.fromCharCode(65 + groupCount); // A, B, C, etc.
            
            const groupHtml = `
                <div class="group-container bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl"
                     data-group-index="${groupCount}">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-white">
                            Grupa ${groupName}
                        </h3>
                        <span class="group-count text-sm px-3 py-1 rounded-full bg-gray-700">
                            <span class="current-count">0</span> igrača
                        </span>
                    </div>

                    <div class="group-dropzone min-h-[300px] border-2 border-dashed border-gray-600 rounded-lg p-4 space-y-2"
                         data-group-number="${nextGroupNumber}">
                        <!-- Players will be added here -->
                    </div>

                    <button type="button" 
                            class="clear-group-btn mt-4 w-full bg-red-600/20 hover:bg-red-600/30 text-red-400 px-4 py-2 rounded-lg transition-colors text-sm">
                        Očisti Grupu
                    </button>
                </div>
            `;

            document.getElementById('groupsContainer').insertAdjacentHTML('beforeend', groupHtml);
            
            const newGroup = document.getElementById('groupsContainer').lastElementChild;
            
            // Initialize drag and drop for new group
            const dropzone = newGroup.querySelector('.group-dropzone');
            dropzone.addEventListener('dragover', handleDragOver);
            dropzone.addEventListener('drop', handleDrop);
            dropzone.addEventListener('dragleave', handleDragLeave);
            
            // Initialize clear button for new group
            const clearBtn = newGroup.querySelector('.clear-group-btn');
            clearBtn.addEventListener('click', function() {
                const groupContainer = this.closest('.group-container');
                const dropzone = groupContainer.querySelector('.group-dropzone');
                const players = Array.from(dropzone.querySelectorAll('.player-item'));

                players.forEach(player => {
                    const clonedElement = player.cloneNode(true);
                    clonedElement.addEventListener('dragstart', handleDragStart);
                    clonedElement.addEventListener('dragend', handleDragEnd);
                    
                    document.getElementById('availablePlayers').appendChild(clonedElement);
                    player.remove();
                });

                updateGroupCount(groupContainer);
                updateUnassignedCount();
                updateSaveButton();
            });

            groupCount++;
            nextGroupNumber++;
            updateRemoveGroupButton();
            showNotification(`Grupa ${groupName} je dodana!`, 'success');
        }

        function removeLastGroup() {
            const groups = document.querySelectorAll('.group-container');
            if (groups.length <= 1) {
                showNotification('Morate imati barem jednu grupu!', 'error');
                return;
            }

            const lastGroup = groups[groups.length - 1];
            const dropzone = lastGroup.querySelector('.group-dropzone');
            const players = Array.from(dropzone.querySelectorAll('.player-item'));

            // Move players back to available list
            players.forEach(player => {
                const clonedElement = player.cloneNode(true);
                clonedElement.addEventListener('dragstart', handleDragStart);
                clonedElement.addEventListener('dragend', handleDragEnd);
                
                document.getElementById('availablePlayers').appendChild(clonedElement);
            });

            lastGroup.remove();
            groupCount--;
            updateRemoveGroupButton();
            updateUnassignedCount();
            updateSaveButton();
            showNotification('Posljednja grupa je uklonjena!', 'success');
        }

        function updateRemoveGroupButton() {
            const removeBtn = document.getElementById('removeGroupBtn');
            const groups = document.querySelectorAll('.group-container');
            removeBtn.disabled = groups.length <= 1;
        }

        function addRemovePlayerButton(playerElement) {
            // Remove existing remove button if any
            const existingBtn = playerElement.querySelector('.remove-player-btn');
            if (existingBtn) existingBtn.remove();

            // Add new remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-player-btn bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs transition-colors';
            removeBtn.textContent = 'Ukloni';
            
            removeBtn.addEventListener('click', function() {
                const playerItem = this.closest('.player-item');
                const groupContainer = this.closest('.group-container');
                
                // Move player back to available list
                const clonedElement = playerItem.cloneNode(true);
                clonedElement.addEventListener('dragstart', handleDragStart);
                clonedElement.addEventListener('dragend', handleDragEnd);
                
                document.getElementById('availablePlayers').appendChild(clonedElement);
                playerItem.remove();
                
                updateGroupCount(groupContainer);
                updateUnassignedCount();
                updateSaveButton();
            });

            playerElement.querySelector('.flex.items-center.justify-between').appendChild(removeBtn);
        }

        // Update counts
        function updateGroupCount(groupContainer) {
            const dropzone = groupContainer.querySelector('.group-dropzone');
            const count = dropzone.querySelectorAll('.player-item').length;
            const currentCountSpan = groupContainer.querySelector('.current-count');
            currentCountSpan.textContent = count;

            // Update styling based on count
            const countBadge = groupContainer.querySelector('.group-count');
            if (count >= 2 && count <= playersPerGroup) {
                countBadge.classList.remove('bg-gray-700', 'bg-red-600');
                countBadge.classList.add('bg-green-600');
            } else if (count > playersPerGroup) {
                countBadge.classList.remove('bg-gray-700', 'bg-green-600');
                countBadge.classList.add('bg-red-600');
            } else {
                countBadge.classList.remove('bg-green-600', 'bg-red-600');
                countBadge.classList.add('bg-gray-700');
            }
        }

        function updateAllGroupCounts() {
            document.querySelectorAll('.group-container').forEach(updateGroupCount);
        }

        function updateUnassignedCount() {
            const count = document.querySelectorAll('#availablePlayers .player-item').length;
            document.getElementById('unassignedCount').textContent = `(${count})`;
        }

        function updateSaveButton() {
            const saveBtn = document.getElementById('saveBtn');
            const groups = document.querySelectorAll('.group-container');
            let totalPlayersInGroups = 0;
            let validGroups = 0;
            
            // Check if all groups have at least 2 players
            groups.forEach(group => {
                const playerCount = group.querySelectorAll('.group-dropzone .player-item').length;
                totalPlayersInGroups += playerCount;
                if (playerCount >= 2) {
                    validGroups++;
                }
            });

            // Allow saving if we have at least one valid group
            saveBtn.disabled = validGroups === 0;
            
            if (validGroups > 0) {
                saveBtn.textContent = `✓ Sačuvaj ${validGroups} grup${validGroups === 1 ? 'u' : 'e'} i nastavi`;
            } else {
                saveBtn.textContent = 'Svaka grupa treba minimum 2 igrača';
            }
        }

        // Form submission
        document.getElementById('groupsForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const groups = [];
            document.querySelectorAll('.group-container').forEach((groupContainer, index) => {
                const playerIds = Array.from(groupContainer.querySelectorAll('.group-dropzone .player-item')).map(item => {
                    return item.dataset.playerId;
                });

                // Only include groups that have players
                if (playerIds.length > 0) {
                    groups.push({
                        players: playerIds
                    });
                }
            });

            // Add groups data to form
            groups.forEach((group, index) => {
                group.players.forEach((playerId, playerIndex) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `groups[${index}][players][${playerIndex}]`;
                    input.value = playerId;
                    this.appendChild(input);
                });
            });

            this.submit();
        });

        // Notification helper (bottom-center, non-shifting)
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
<?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/competitions/setup-groups.blade.php ENDPATH**/ ?>