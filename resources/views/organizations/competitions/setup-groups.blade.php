<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Postavi Grupe Turnira</h1>
                        <p class="text-gray-300">{{ $competition->name }}</p>
                    </div>
                    <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors text-center sm:w-auto w-full">
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
                            <li>• Svaka grupa bi trebala imati najmanje 2 igrača</li>
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
                                @foreach($availablePlayers as $player)
                                <div class="player-item bg-gray-700/30 rounded-lg p-3 cursor-move hover:bg-gray-700/50 transition-colors" 
                                     data-player-id="{{ $player->id }}"
                                     data-player-name="{{ $player->name }}"
                                     data-player-position="{{ $player->position ?? 'UN' }}"
                                     draggable="true">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">{{ $loop->index + 1 }}</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-medium">{{ $player->name }}</p>
                                                @if($player->position)
                                                    <p class="text-xs text-gray-400">({{ $player->position }})</p>
                                                @endif
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
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                            <h3 class="text-lg font-semibold text-white text-center sm:text-left">Grupe Turnira</h3>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <button type="button" 
                                        id="addGroupBtn"
                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg transition-colors text-sm flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Dodaj Grupu
                                </button>
                                <button type="button" 
                                        id="removeGroupBtn"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg transition-colors text-sm flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                    Ukloni Grupu
                                </button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="groupsContainer">
                            @foreach($groups as $index => $group)
                            <div class="group-container bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl"
                                 data-group-index="{{ $index }}">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-semibold text-white">
                                        Grupa {{ $group['name'] }}
                                    </h3>
                                    <span class="group-count text-sm px-3 py-1 rounded-full bg-gray-700">
                                        <span class="current-count">{{ count($group['players']) }}</span> igrača
                                    </span>
                                </div>

                                <div class="group-dropzone min-h-[300px] border-2 border-dashed border-gray-600 rounded-lg p-4 space-y-2"
                                     data-group-number="{{ $group['number'] }}">
                                    @foreach($group['players'] as $player)
                                    <div class="player-item bg-gray-700/30 rounded-lg p-3 cursor-move hover:bg-gray-700/50 transition-colors" 
                                         data-player-id="{{ $player['id'] }}"
                                         data-player-name="{{ $player['name'] }}"
                                         draggable="true">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm">{{ $group['name'] }}-{{ $loop->index + 1 }}</span>
                                                </div>
                                                <div>
                                                    <p class="text-white font-medium">{{ $player['name'] }}</p>
                                                    @if(isset($player['position']) && $player['position'])
                                                        <p class="text-xs text-gray-400">({{ $player['position'] }})</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <button type="button" 
                                                    class="remove-player-btn bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                                Ukloni
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <button type="button" 
                                        class="clear-group-btn mt-4 w-full bg-red-600/20 hover:bg-red-600/30 text-red-400 px-4 py-2 rounded-lg transition-colors text-sm">
                                    Očisti Grupu
                                </button>
                            </div>
                            @endforeach
                        </div>

                        <!-- Save Button -->
                        <div class="mt-6 flex flex-col sm:flex-row gap-2 sm:gap-4">
                            <button type="submit" 
                                    id="saveBtn"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-lg transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base">
                                Svaka grupa treba minimum 2 igrača
                            </button>
                            <button type="button" 
                                    id="resetBtn"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-lg transition-colors text-sm sm:text-base">
                                Resetuj Sve
                            </button>
                        </div>
                    </div>

                </div>

            </form>

        </div>
    </div>

    <script>
        const playersPerGroup = 100; // No limit on players per group
        let groupCount = {{ count($groups) }};
        let draggedElement = null;
        let nextGroupNumber = {{ count($groups) + 1 }};

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
            const groupContainer = groupDropzone.closest('.group-container');
            const groupName = groupContainer.querySelector('h3').textContent.replace('Grupa ', '');

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
            
            // Update avatar to show group position
            const currentPlayerCount = groupDropzone.querySelectorAll('.player-item').length;
            const avatarSpan = clonedElement.querySelector('.w-10.h-10 span');
            if (avatarSpan) {
                avatarSpan.textContent = groupName + '-' + (currentPlayerCount + 1);
            }
            
            groupDropzone.appendChild(clonedElement);
            draggedElement.remove();

            updateUnassignedCount();
            updateGroupCount(groupContainer);
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
            
            // Update avatar back to position number
            const avatarSpan = clonedElement.querySelector('.w-10.h-10 span');
            if (avatarSpan) {
                const availablePlayers = document.querySelectorAll('#availablePlayers .player-item');
                const position = Array.from(availablePlayers).indexOf(clonedElement) + 1;
                avatarSpan.textContent = position.toString();
            }
            
            // Remove remove button if it exists
            const removeBtn = clonedElement.querySelector('.remove-player-btn');
            if (removeBtn) removeBtn.remove();
            
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

                // Distribute to groups evenly - round-robin style
                const groups = document.querySelectorAll('.group-dropzone');
                if (groups.length === 0) {
                    showNotification('Nema grupa za raspodjelu igrača!', 'error');
                    return;
                }

                let groupIndex = 0;
                availablePlayers.forEach((player, playerIndex) => {
                    const group = groups[groupIndex];
                    const groupContainer = group.closest('.group-container');
                    const groupName = groupContainer.querySelector('h3').textContent.replace('Grupa ', '');
                    
                    const clonedElement = player.cloneNode(true);
                    clonedElement.addEventListener('dragstart', handleDragStart);
                    clonedElement.addEventListener('dragend', handleDragEnd);
                    addRemovePlayerButton(clonedElement);
                    
                    // Update avatar to show group position
                    const currentPlayerCount = group.querySelectorAll('.player-item').length;
                    const avatarSpan = clonedElement.querySelector('.w-10.h-10 span');
                    if (avatarSpan) {
                        avatarSpan.textContent = groupName + '-' + (currentPlayerCount + 1);
                    }
                    
                    group.appendChild(clonedElement);
                    player.remove();
                    
                    // Move to next group (round-robin)
                    groupIndex = (groupIndex + 1) % groups.length;
                });

                updateAllGroupCounts();
                updateUnassignedCount();
                updateSaveButton();
                showNotification('Igrači su shuffle-ovani ravnomjerno po grupama!', 'success');
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
                        
                        // Update avatar back to position number
                        const avatarSpan = clonedElement.querySelector('.w-10.h-10 span');
                        if (avatarSpan) {
                            const availablePlayers = document.querySelectorAll('#availablePlayers .player-item');
                            const position = Array.from(availablePlayers).length + 1; // Next position
                            avatarSpan.textContent = position.toString();
                        }
                        
                        // Remove remove button
                        const removeBtn = clonedElement.querySelector('.remove-player-btn');
                        if (removeBtn) removeBtn.remove();
                        
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
                    let positionCounter = document.querySelectorAll('#availablePlayers .player-item').length + 1;
                    
                    players.forEach(player => {
                        const clonedElement = player.cloneNode(true);
                        clonedElement.addEventListener('dragstart', handleDragStart);
                        clonedElement.addEventListener('dragend', handleDragEnd);
                        
                        // Update avatar back to position number
                        const avatarSpan = clonedElement.querySelector('.w-10.h-10 span');
                        if (avatarSpan) {
                            avatarSpan.textContent = positionCounter.toString();
                            positionCounter++;
                        }
                        
                        // Remove remove button
                        const removeBtn = clonedElement.querySelector('.remove-player-btn');
                        if (removeBtn) removeBtn.remove();
                        
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
                    
                    // Update avatar back to position number
                    const avatarSpan = clonedElement.querySelector('.w-10.h-10 span');
                    if (avatarSpan) {
                        const availablePlayers = document.querySelectorAll('#availablePlayers .player-item');
                        const position = Array.from(availablePlayers).length + 1; // Next position
                        avatarSpan.textContent = position.toString();
                    }
                    
                    // Remove remove button
                    const removeBtn = clonedElement.querySelector('.remove-player-btn');
                    if (removeBtn) removeBtn.remove();
                    
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
                    
                    // Update avatar back to position number
                    const avatarSpan = clonedElement.querySelector('.w-10.h-10 span');
                    if (avatarSpan) {
                        const availablePlayers = document.querySelectorAll('#availablePlayers .player-item');
                        const position = Array.from(availablePlayers).length + 1; // Next position
                        avatarSpan.textContent = position.toString();
                    }
                    
                    // Remove remove button
                    const removeBtn = clonedElement.querySelector('.remove-player-btn');
                    if (removeBtn) removeBtn.remove();
                    
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
            let positionCounter = document.querySelectorAll('#availablePlayers .player-item').length + 1;
            players.forEach(player => {
                const clonedElement = player.cloneNode(true);
                clonedElement.addEventListener('dragstart', handleDragStart);
                clonedElement.addEventListener('dragend', handleDragEnd);
                
                // Update avatar back to position number
                const avatarSpan = clonedElement.querySelector('.w-10.h-10 span');
                if (avatarSpan) {
                    avatarSpan.textContent = positionCounter.toString();
                    positionCounter++;
                }
                
                // Remove remove button
                const removeBtn = clonedElement.querySelector('.remove-player-btn');
                if (removeBtn) removeBtn.remove();
                
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
                
                // Update avatar back to position number
                const avatarSpan = clonedElement.querySelector('.w-10.h-10 span');
                if (avatarSpan) {
                    const availablePlayers = document.querySelectorAll('#availablePlayers .player-item');
                    const position = Array.from(availablePlayers).length + 1; // Next position
                    avatarSpan.textContent = position.toString();
                }
                
                // Remove remove button
                const removeBtn = clonedElement.querySelector('.remove-player-btn');
                if (removeBtn) removeBtn.remove();
                
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
            if (count >= 2) {
                countBadge.classList.remove('bg-gray-700', 'bg-red-600');
                countBadge.classList.add('bg-green-600');
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
</x-app-layout>
