<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-green-900 to-emerald-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">⚽ Postavi Grupe Turnira</h1>
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
            <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-green-400 font-medium mb-1">Kako postaviti grupe:</p>
                        <ul class="text-green-300 text-sm space-y-1">
                            <li>• Povucite timove sa liste s lijeva na grupe s desna</li>
                            <li>• Ili pretražite i kliknite dugme "Dodaj u Grupu"</li>
                            <li>• Svaka grupa bi trebala imati najmanje 2 tima</li>
                            <li>• Kliknite "Shuffle" da nasumično rasporedite nedodijeljene timove</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form id="groupsForm" action="{{ route('organizations.competitions.futsal.save-groups', [$organization, $competition]) }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Available Teams -->
                    <div class="lg:col-span-1">
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl sticky top-4">
                            <h3 class="text-xl font-semibold text-white mb-4">
                                Dostupni Timovi
                                <span id="unassignedCount" class="text-gray-400 text-sm ml-2"></span>
                            </h3>
                            
                            <!-- Search -->
                            <div class="mb-4">
                                <input type="text" 
                                       id="teamSearch" 
                                       placeholder="Pretraži timove..."
                                       class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>

                            <!-- Shuffle Button -->
                            <button type="button" 
                                    id="shuffleBtn"
                                    class="w-full mb-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                                🎲 Shuffle Preostale Timove
                            </button>

                            <!-- Teams List -->
                            <div id="availableTeams" class="space-y-2 max-h-[600px] overflow-y-auto">
                                @foreach($availableTeams as $team)
                                <div class="team-item bg-gray-700/30 rounded-lg p-3 cursor-move hover:bg-gray-700/50 transition-colors" 
                                     data-team-id="{{ $team->id }}"
                                     data-team-name="{{ $team->name }}"
                                     draggable="true">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            @if($team->logo)
                                            <img src="{{ Storage::url($team->logo) }}" 
                                                 alt="{{ $team->name }}" 
                                                 class="w-10 h-10 rounded-full object-cover">
                                            @else
                                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-lg">⚽</span>
                                            </div>
                                            @endif
                                            <div>
                                                <p class="text-white font-medium">{{ $team->name }}</p>
                                                <div class="flex items-center gap-1">
                                                    @if($team->primary_color)
                                                        <div class="w-3 h-3 rounded-full border border-white/20" style="background-color: {{ $team->primary_color }}"></div>
                                                    @endif
                                                    @if($team->secondary_color)
                                                        <div class="w-3 h-3 rounded-full border border-white/20" style="background-color: {{ $team->secondary_color }}"></div>
                                                    @endif
                                                    <span class="text-xs text-gray-400 ml-1">({{ $team->activePlayers->count() }} igrača)</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" 
                                                class="add-to-group-btn hidden bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs transition-colors">
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
                                        Grupa {{ $group->name }}
                                    </h3>
                                    <span class="group-count text-sm px-3 py-1 rounded-full bg-gray-700">
                                        <span class="current-count">{{ $group->futsalTeams->count() }}</span> timova
                                    </span>
                                </div>

                                <div class="group-dropzone min-h-[300px] border-2 border-dashed border-gray-600 rounded-lg p-4 space-y-2"
                                     data-group-name="{{ $group->name }}">
                                    @foreach($group->futsalTeams as $team)
                                    <div class="team-item bg-gray-700/30 rounded-lg p-3 cursor-move hover:bg-gray-700/50 transition-colors" 
                                         data-team-id="{{ $team->id }}"
                                         data-team-name="{{ $team->name }}"
                                         draggable="true">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                @if($team->logo)
                                                <img src="{{ Storage::url($team->logo) }}" 
                                                     alt="{{ $team->name }}" 
                                                     class="w-10 h-10 rounded-full object-cover">
                                                @else
                                                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                                    <span class="text-white font-bold text-lg">⚽</span>
                                                </div>
                                                @endif
                                                <div>
                                                    <p class="text-white font-medium">{{ $team->name }}</p>
                                                    <div class="flex items-center gap-1">
                                                        @if($team->primary_color)
                                                            <div class="w-3 h-3 rounded-full border border-white/20" style="background-color: {{ $team->primary_color }}"></div>
                                                        @endif
                                                        @if($team->secondary_color)
                                                            <div class="w-3 h-3 rounded-full border border-white/20" style="background-color: {{ $team->secondary_color }}"></div>
                                                        @endif
                                                        <span class="text-xs text-gray-400 ml-1">({{ $team->activePlayers->count() }} igrača)</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" 
                                                    class="remove-team-btn bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs transition-colors">
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

                        <!-- Round Robin Type Selection -->
                        <div class="mt-6 bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                            <h3 class="text-lg font-semibold text-white mb-4">Tip Round-Robin Sistema</h3>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <label class="flex items-center space-x-3 cursor-pointer flex-1">
                                    <input type="radio" 
                                           name="round_robin_type" 
                                           value="single" 
                                           checked
                                           class="w-4 h-4 text-green-600 focus:ring-green-500">
                                    <div>
                                        <span class="text-white font-medium">Single Round-Robin</span>
                                        <p class="text-xs text-gray-400">Svaki tim igra jednom protiv svakog</p>
                                    </div>
                                </label>
                                <label class="flex items-center space-x-3 cursor-pointer flex-1">
                                    <input type="radio" 
                                           name="round_robin_type" 
                                           value="double"
                                           class="w-4 h-4 text-green-600 focus:ring-green-500">
                                    <div>
                                        <span class="text-white font-medium">Double Round-Robin</span>
                                        <p class="text-xs text-gray-400">Svaki tim igra dvaput (home/away)</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Teams Advancing Per Group -->
                        <div class="mt-4 bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                            <h3 class="text-lg font-semibold text-white mb-4">Broj Timova Koji Prolaze iz Grupe</h3>
                            <div class="flex flex-wrap gap-2">
                                @for($i = 1; $i <= 4; $i++)
                                <label class="flex items-center space-x-2 cursor-pointer bg-gray-700/30 hover:bg-gray-700/50 px-4 py-2 rounded-lg transition-colors">
                                    <input type="radio" 
                                           name="teams_advancing_per_group" 
                                           value="{{ $i }}"
                                           {{ $i == 2 ? 'checked' : '' }}
                                           class="w-4 h-4 text-green-600 focus:ring-green-500">
                                    <span class="text-white">{{ $i }} {{ $i == 1 ? 'tim' : 'tima' }}</span>
                                </label>
                                @endfor
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="mt-6 flex flex-col sm:flex-row gap-2 sm:gap-4">
                            <button type="submit" 
                                    id="saveBtn"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-lg transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base">
                                Svaka grupa treba minimum 2 tima
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
        let groupCount = {{ count($groups) }};
        let draggedElement = null;
        const groupNames = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

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
            // Make team items draggable
            document.querySelectorAll('.team-item').forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragend', handleDragEnd);
            });

            // Make group dropzones accept drops
            document.querySelectorAll('.group-dropzone').forEach(zone => {
                zone.addEventListener('dragover', handleDragOver);
                zone.addEventListener('drop', handleDrop);
                zone.addEventListener('dragleave', handleDragLeave);
            });

            // Make available teams list accept drops (for returning teams)
            const availableTeams = document.getElementById('availableTeams');
            availableTeams.addEventListener('dragover', handleDragOver);
            availableTeams.addEventListener('drop', handleDropToAvailable);
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
                this.style.backgroundColor = 'rgba(34, 197, 94, 0.1)';
                this.style.borderColor = 'rgb(34, 197, 94)';
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

            // Check if team is already in this group
            const teamId = draggedElement.dataset.teamId;
            if (groupDropzone.querySelector(`[data-team-id="${teamId}"]`)) {
                return false;
            }

            // Move team to group
            const clonedElement = draggedElement.cloneNode(true);
            clonedElement.addEventListener('dragstart', handleDragStart);
            clonedElement.addEventListener('dragend', handleDragEnd);
            addRemoveTeamButton(clonedElement);
            
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

            // Return team to available list
            const clonedElement = draggedElement.cloneNode(true);
            clonedElement.addEventListener('dragstart', handleDragStart);
            clonedElement.addEventListener('dragend', handleDragEnd);
            
            // Remove remove button if it exists
            const removeBtn = clonedElement.querySelector('.remove-team-btn');
            if (removeBtn) removeBtn.remove();
            
            document.getElementById('availableTeams').appendChild(clonedElement);
            draggedElement.remove();

            updateUnassignedCount();
            updateAllGroupCounts();
            updateSaveButton();

            return false;
        }

        // Search
        function initializeSearch() {
            const searchInput = document.getElementById('teamSearch');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('#availableTeams .team-item').forEach(item => {
                    const teamName = item.dataset.teamName.toLowerCase();
                    if (teamName.includes(searchTerm)) {
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
                const availableTeams = Array.from(document.querySelectorAll('#availableTeams .team-item'));
                
                if (availableTeams.length === 0) {
                    showNotification('Nema timova za shuffle!', 'error');
                    return;
                }

                // Shuffle array
                for (let i = availableTeams.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [availableTeams[i], availableTeams[j]] = [availableTeams[j], availableTeams[i]];
                }

                // Distribute to groups evenly - round-robin style
                const groups = document.querySelectorAll('.group-dropzone');
                if (groups.length === 0) {
                    showNotification('Nema grupa za raspodjelu timova!', 'error');
                    return;
                }

                let groupIndex = 0;
                availableTeams.forEach((team, teamIndex) => {
                    const group = groups[groupIndex];
                    const groupContainer = group.closest('.group-container');
                    
                    const clonedElement = team.cloneNode(true);
                    clonedElement.addEventListener('dragstart', handleDragStart);
                    clonedElement.addEventListener('dragend', handleDragEnd);
                    addRemoveTeamButton(clonedElement);
                    
                    group.appendChild(clonedElement);
                    team.remove();
                    
                    // Move to next group (round-robin)
                    groupIndex = (groupIndex + 1) % groups.length;
                });

                updateAllGroupCounts();
                updateUnassignedCount();
                updateSaveButton();
                showNotification('Timovi su shuffle-ovani ravnomjerno po grupama!', 'success');
            });

            // Clear group buttons
            document.querySelectorAll('.clear-group-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const groupContainer = this.closest('.group-container');
                    const dropzone = groupContainer.querySelector('.group-dropzone');
                    const teams = Array.from(dropzone.querySelectorAll('.team-item'));

                    teams.forEach(team => {
                        const clonedElement = team.cloneNode(true);
                        clonedElement.addEventListener('dragstart', handleDragStart);
                        clonedElement.addEventListener('dragend', handleDragEnd);
                        
                        // Remove remove button
                        const removeBtn = clonedElement.querySelector('.remove-team-btn');
                        if (removeBtn) removeBtn.remove();
                        
                        document.getElementById('availableTeams').appendChild(clonedElement);
                        team.remove();
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
                    const teams = Array.from(dropzone.querySelectorAll('.team-item'));
                    
                    teams.forEach(team => {
                        const clonedElement = team.cloneNode(true);
                        clonedElement.addEventListener('dragstart', handleDragStart);
                        clonedElement.addEventListener('dragend', handleDragEnd);
                        
                        // Remove remove button
                        const removeBtn = clonedElement.querySelector('.remove-team-btn');
                        if (removeBtn) removeBtn.remove();
                        
                        document.getElementById('availableTeams').appendChild(clonedElement);
                        team.remove();
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

            // Remove team buttons (for existing teams)
            document.querySelectorAll('.remove-team-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const teamItem = this.closest('.team-item');
                    const groupContainer = this.closest('.group-container');
                    
                    // Move team back to available list
                    const clonedElement = teamItem.cloneNode(true);
                    clonedElement.addEventListener('dragstart', handleDragStart);
                    clonedElement.addEventListener('dragend', handleDragEnd);
                    
                    // Remove remove button
                    const removeBtn = clonedElement.querySelector('.remove-team-btn');
                    if (removeBtn) removeBtn.remove();
                    
                    document.getElementById('availableTeams').appendChild(clonedElement);
                    teamItem.remove();
                    
                    updateGroupCount(groupContainer);
                    updateUnassignedCount();
                    updateSaveButton();
                });
            });

            updateRemoveGroupButton();
        }

        function addNewGroup() {
            if (groupCount >= groupNames.length) {
                showNotification('Maksimalno ' + groupNames.length + ' grupa!', 'error');
                return;
            }

            const groupName = groupNames[groupCount];
            
            const groupHtml = `
                <div class="group-container bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl"
                     data-group-index="${groupCount}">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-white">
                            Grupa ${groupName}
                        </h3>
                        <span class="group-count text-sm px-3 py-1 rounded-full bg-gray-700">
                            <span class="current-count">0</span> timova
                        </span>
                    </div>

                    <div class="group-dropzone min-h-[300px] border-2 border-dashed border-gray-600 rounded-lg p-4 space-y-2"
                         data-group-name="${groupName}">
                        <!-- Teams will be added here -->
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
                const teams = Array.from(dropzone.querySelectorAll('.team-item'));

                teams.forEach(team => {
                    const clonedElement = team.cloneNode(true);
                    clonedElement.addEventListener('dragstart', handleDragStart);
                    clonedElement.addEventListener('dragend', handleDragEnd);
                    
                    // Remove remove button
                    const removeBtn = clonedElement.querySelector('.remove-team-btn');
                    if (removeBtn) removeBtn.remove();
                    
                    document.getElementById('availableTeams').appendChild(clonedElement);
                    team.remove();
                });

                updateGroupCount(groupContainer);
                updateUnassignedCount();
                updateSaveButton();
            });

            groupCount++;
            updateRemoveGroupButton();
            showNotification(`Grupa ${groupName} je dodana!`, 'success');
        }

        function removeLastGroup() {
            const groups = document.querySelectorAll('.group-container');
            if (groups.length <= 2) {
                showNotification('Morate imati barem 2 grupe!', 'error');
                return;
            }

            const lastGroup = groups[groups.length - 1];
            const dropzone = lastGroup.querySelector('.group-dropzone');
            const teams = Array.from(dropzone.querySelectorAll('.team-item'));

            // Move teams back to available list
            teams.forEach(team => {
                const clonedElement = team.cloneNode(true);
                clonedElement.addEventListener('dragstart', handleDragStart);
                clonedElement.addEventListener('dragend', handleDragEnd);
                
                // Remove remove button
                const removeBtn = clonedElement.querySelector('.remove-team-btn');
                if (removeBtn) removeBtn.remove();
                
                document.getElementById('availableTeams').appendChild(clonedElement);
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
            removeBtn.disabled = groups.length <= 2;
        }

        function addRemoveTeamButton(teamElement) {
            // Remove existing remove button if any
            const existingBtn = teamElement.querySelector('.remove-team-btn');
            if (existingBtn) existingBtn.remove();

            // Add new remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-team-btn bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs transition-colors';
            removeBtn.textContent = 'Ukloni';
            
            removeBtn.addEventListener('click', function() {
                const teamItem = this.closest('.team-item');
                const groupContainer = this.closest('.group-container');
                
                // Move team back to available list
                const clonedElement = teamItem.cloneNode(true);
                clonedElement.addEventListener('dragstart', handleDragStart);
                clonedElement.addEventListener('dragend', handleDragEnd);
                
                // Remove remove button
                const removeBtn = clonedElement.querySelector('.remove-team-btn');
                if (removeBtn) removeBtn.remove();
                
                document.getElementById('availableTeams').appendChild(clonedElement);
                teamItem.remove();
                
                updateGroupCount(groupContainer);
                updateUnassignedCount();
                updateSaveButton();
            });

            teamElement.querySelector('.flex.items-center.justify-between').appendChild(removeBtn);
        }

        // Update counts
        function updateGroupCount(groupContainer) {
            const dropzone = groupContainer.querySelector('.group-dropzone');
            const count = dropzone.querySelectorAll('.team-item').length;
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
            const count = document.querySelectorAll('#availableTeams .team-item').length;
            document.getElementById('unassignedCount').textContent = `(${count})`;
        }

        function updateSaveButton() {
            const saveBtn = document.getElementById('saveBtn');
            const groups = document.querySelectorAll('.group-container');
            let totalTeamsInGroups = 0;
            let validGroups = 0;
            
            // Check if all groups have at least 2 teams
            groups.forEach(group => {
                const teamCount = group.querySelectorAll('.group-dropzone .team-item').length;
                totalTeamsInGroups += teamCount;
                if (teamCount >= 2) {
                    validGroups++;
                }
            });

            // Allow saving if we have at least 2 valid groups
            saveBtn.disabled = validGroups < 2;
            
            if (validGroups >= 2) {
                saveBtn.textContent = `✓ Sačuvaj ${validGroups} grup${validGroups === 2 ? 'e' : 'a'} i nastavi`;
            } else {
                saveBtn.textContent = 'Svaka grupa treba minimum 2 tima (najmanje 2 grupe)';
            }
        }

        // Form submission
        document.getElementById('groupsForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const groups = [];
            document.querySelectorAll('.group-container').forEach((groupContainer) => {
                const groupName = groupContainer.querySelector('h3').textContent.replace('Grupa ', '').trim();
                const teamIds = Array.from(groupContainer.querySelectorAll('.group-dropzone .team-item')).map(item => {
                    return item.dataset.teamId;
                });

                // Only include groups that have at least 2 teams
                if (teamIds.length >= 2) {
                    groups.push({
                        name: groupName,
                        teams: teamIds
                    });
                }
            });

            // Validate we have at least 2 groups
            if (groups.length < 2) {
                showNotification('Morate imati barem 2 grupe sa najmanje 2 tima!', 'error');
                return;
            }

            // Add groups data to form
            groups.forEach((group, index) => {
                // Add group name
                const nameInput = document.createElement('input');
                nameInput.type = 'hidden';
                nameInput.name = `groups[${index}][name]`;
                nameInput.value = group.name;
                this.appendChild(nameInput);

                // Add team IDs
                group.teams.forEach((teamId, teamIndex) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `groups[${index}][teams][${teamIndex}]`;
                    input.value = teamId;
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
                type === 'error' ? 'bg-red-500' : 'bg-green-500'
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
