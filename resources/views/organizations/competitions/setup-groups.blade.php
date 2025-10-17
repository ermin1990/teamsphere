<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Postavi Grupe Turnira</h1>
                        <p class="text-gray-300">{{ $competition->name }}</p>
                    </div>
                    <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Nazad
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                <p class="text-green-400">{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                <p class="text-red-400">{{ session('error') }}</p>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                <ul class="list-disc list-inside text-red-400">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

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
                            <li>• Svaka grupa bi trebala imati 2-{{ $competition->players_per_group }} igrača</li>
                            <li>• Kliknite "Shuffle" da nasumično rasporedite nedodijeljene igrače</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form id="groupsForm" action="{{ route('organizations.competitions.save-groups', [$organization, $competition]) }}" method="POST">
                @csrf

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
                                @foreach($competition->players as $player)
                                <div class="player-item bg-gray-700/30 rounded-lg p-3 cursor-move hover:bg-gray-700/50 transition-colors" 
                                     data-player-id="{{ $player->id }}"
                                     data-player-name="{{ $player->name }}"
                                     draggable="true">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">{{ substr($player->name, 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-medium">{{ $player->name }}</p>
                                            </div>
                                        </div>
                                        <button type="button" 
                                                class="add-to-group-btn hidden bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                            Dodaj
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Groups -->
                    <div class="lg:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="groupsContainer">
                            @foreach($groups as $index => $group)
                            <div class="group-container bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl"
                                 data-group-index="{{ $index }}">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-semibold text-white">
                                        Grupa {{ $group['name'] }}
                                    </h3>
                                    <span class="group-count text-sm px-3 py-1 rounded-full bg-gray-700">
                                        <span class="current-count">0</span> igrača
                                    </span>
                                </div>

                                <div class="group-dropzone min-h-[300px] border-2 border-dashed border-gray-600 rounded-lg p-4 space-y-2"
                                     data-group-number="{{ $group['number'] }}">
                                    <!-- Players will be added here -->
                                </div>

                                <button type="button" 
                                        class="clear-group-btn mt-4 w-full bg-red-600/20 hover:bg-red-600/30 text-red-400 px-4 py-2 rounded-lg transition-colors text-sm">
                                    Očisti Grupu
                                </button>
                            </div>
                            @endforeach
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
        const playersPerGroup = {{ $competition->players_per_group }};
        const groupCount = {{ $competition->group_count }};
        let draggedElement = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateUnassignedCount();
            updateAllGroupCounts();
            updateSaveButton();
            initializeDragAndDrop();
            initializeSearch();
            initializeButtons();
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
                showNotification('Group is full!', 'error');
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
                    showNotification('No players to shuffle!', 'error');
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
                        
                        group.appendChild(clonedElement);
                        player.remove();
                        playerIndex++;
                    }

                    updateGroupCount(group.closest('.group-container'));
                });

                updateUnassignedCount();
                updateSaveButton();
                showNotification('Players shuffled!', 'success');
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
                if (!confirm('Are you sure you want to reset all groups?')) {
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
                showNotification('All groups cleared!', 'success');
            });
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
            const allGroupsValid = Array.from(document.querySelectorAll('.group-dropzone')).every(dropzone => {
                const playerCount = dropzone.querySelectorAll('.player-item').length;
                return playerCount >= 2 && playerCount <= playersPerGroup;
            });
            const allPlayersAssigned = document.querySelectorAll('#unassignedPlayers .player-item').length === 0;

            saveBtn.disabled = !(allGroupsValid && allPlayersAssigned);
            
            if (allGroupsValid && allPlayersAssigned) {
                saveBtn.textContent = '✓ Save Groups & Continue';
            } else if (!allGroupsValid) {
                saveBtn.textContent = 'Each group needs 2+ players';
            } else {
                saveBtn.textContent = 'Assign all players to groups';
            }
        }

        // Form submission
        document.getElementById('groupsForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const groups = [];
            document.querySelectorAll('.group-dropzone').forEach((dropzone, index) => {
                const playerIds = Array.from(dropzone.querySelectorAll('.player-item')).map(item => {
                    return item.dataset.playerId;
                });

                groups.push({
                    players: playerIds
                });
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

        // Notification helper
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } text-white`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</x-app-layout>
