<div>
    <!-- Match Setup (always show when no first server selected) -->
    @if(!$firstServer)
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
        <h3 class="text-xl font-semibold text-white mb-6 text-center">Match Setup</h3>
        <div class="max-w-md mx-auto">
            <label class="block text-sm font-medium text-gray-300 mb-3">Who serves first?</label>
            <div class="space-y-3">
                <button type="button" wire:click="selectFirstServer('home')"
                        class="w-full p-4 bg-blue-600/20 hover:bg-blue-600/30 border-2 border-blue-500/30 hover:border-blue-500/50 rounded-lg transition-all">
                    <div class="text-center">
                        <div class="text-lg font-bold text-blue-400 mb-1">
                            @if($match->league->is_team_based)
                                {{ $match->homeTeam->name ?? 'Home Team' }}
                            @else
                                {{ $match->homePlayer->name ?? 'Home Player' }}
                            @endif
                        </div>
                        <div class="text-sm text-gray-400">Serves First</div>
                    </div>
                </button>
                <button type="button" wire:click="selectFirstServer('away')"
                        class="w-full p-4 bg-red-600/20 hover:bg-red-600/30 border-2 border-red-500/30 hover:border-red-500/50 rounded-lg transition-all">
                    <div class="text-center">
                        <div class="text-lg font-bold text-red-400 mb-1">
                            @if($match->league->is_team_based)
                                {{ $match->awayTeam->name ?? 'Away Team' }}
                            @else
                                {{ $match->awayPlayer->name ?? 'Away Player' }}
                            @endif
                        </div>
                        <div class="text-sm text-gray-400">Serves First</div>
                    </div>
                </button>
                <button type="button" wire:click="selectRandomServer"
                        class="w-full p-4 bg-purple-600/20 hover:bg-purple-600/30 border-2 border-purple-500/30 hover:border-purple-500/50 rounded-lg transition-all">
                    <div class="text-center">
                        <div class="text-lg font-bold text-purple-400 mb-1">
                            🎲 Random
                        </div>
                        <div class="text-sm text-gray-400">Random Server</div>
                    </div>
                </button>
            </div>
            <div class="mt-6 text-center text-gray-400 text-sm">
                Select who serves first to start the match
            </div>
        </div>
    </div>
    @endif

    <!-- Live Scoring Interface -->
    @if($firstServer)
        <!-- Match Timer -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center space-x-4 mb-4">
                <div class="text-4xl font-mono text-white" id="match-timer">00:00:00</div>
                @if(!$matchStartTime)
                <button type="button" wire:click="startTimer"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold">
                    ▶️ Start Timer
                </button>
                @endif
            </div>
            <div class="text-sm text-gray-400">Match Time</div>
        </div>

        <!-- Players Side by Side -->
        <div class="grid grid-cols-2 gap-4 md:gap-8 mb-8">
            <!-- Player 1 (Home) -->
            <div class="text-center">
                <div class="mb-4">
                    <h3 class="text-xl font-bold transition-all duration-300 {{ $currentServer === 'home' ? 'text-blue-400 animate-pulse drop-shadow-lg' : 'text-white' }} mb-2">
                        @if($match->league->is_team_based)
                            {{ $match->homeTeam->name ?? 'Home Team' }}
                        @else
                            {{ $match->homePlayer->name ?? 'Home Player' }}
                        @endif
                    </h3>
                </div>

                <!-- Score Display -->
                <div class="relative mb-6">
                    <div class="text-8xl md:text-9xl font-bold transition-all duration-300 mb-2 {{ $match->status === 'completed' ? '' : 'cursor-pointer hover:text-blue-300 active:scale-95 select-none' }} {{ $currentServer === 'home' ? 'text-blue-400 animate-pulse drop-shadow-lg' : 'text-blue-400' }}"
                         @if($match->status !== 'completed') wire:click="addPoint('home')" @endif>
                        {{ $homeScore }}
                    </div>
                    <div class="text-lg font-semibold text-white">
                        @if($match->league->is_team_based)
                            {{ $match->homeTeam->name ?? 'Home Team' }}
                        @else
                            {{ $match->homePlayer->name ?? 'Home Player' }}
                        @endif
                    </div>
                    <!-- Serving Indicator -->
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-400 rounded-full transition-opacity duration-300 shadow-lg shadow-yellow-400/50"
                         style="opacity: {{ $currentServer === 'home' ? '1' : '0' }}">
                        <div class="w-full h-full bg-yellow-300 rounded-full animate-ping"></div>
                    </div>
                </div>

                <!-- Score Buttons -->
                <div class="flex justify-center">
                    <button type="button" @if($match->status !== 'completed') wire:click="subtractPoint('home')" @endif
                            class="px-4 py-2 bg-gray-600 {{ $match->status === 'completed' ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-700' }} text-white rounded-lg transition-all duration-200 font-semibold text-sm">
                        -1
                    </button>
                </div>
            </div>

            <!-- Player 2 (Away) -->
            <div class="text-center">
                <div class="mb-4">
                    <h3 class="text-xl font-bold transition-all duration-300 {{ $currentServer === 'away' ? 'text-red-400 animate-pulse drop-shadow-lg' : 'text-white' }} mb-2">
                        @if($match->league->is_team_based)
                            {{ $match->awayTeam->name ?? 'Away Team' }}
                        @else
                            {{ $match->awayPlayer->name ?? 'Away Player' }}
                        @endif
                    </h3>
                </div>

                <!-- Score Display -->
                <div class="relative mb-6">
                    <div class="text-8xl md:text-9xl font-bold transition-all duration-300 mb-2 {{ $match->status === 'completed' ? '' : 'cursor-pointer hover:text-red-300 active:scale-95 select-none' }} {{ $currentServer === 'away' ? 'text-red-400 animate-pulse drop-shadow-lg' : 'text-red-400' }}"
                         @if($match->status !== 'completed') wire:click="addPoint('away')" @endif>
                        {{ $awayScore }}
                    </div>
                    <div class="text-lg font-semibold text-white">
                        @if($match->league->is_team_based)
                            {{ $match->awayTeam->name ?? 'Away Team' }}
                        @else
                            {{ $match->awayPlayer->name ?? 'Away Player' }}
                        @endif
                    </div>
                    <!-- Serving Indicator -->
                    <div class="absolute -top-2 -left-2 w-6 h-6 bg-yellow-400 rounded-full transition-opacity duration-300 shadow-lg shadow-yellow-400/50"
                         style="opacity: {{ $currentServer === 'away' ? '1' : '0' }}">
                        <div class="w-full h-full bg-yellow-300 rounded-full animate-ping"></div>
                    </div>
                </div>

                <!-- Score Buttons -->
                <div class="flex justify-center">
                    <button type="button" @if($match->status !== 'completed') wire:click="subtractPoint('away')" @endif
                            class="px-4 py-2 bg-gray-600 {{ $match->status === 'completed' ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-700' }} text-white rounded-lg transition-all duration-200 font-semibold text-sm">
                        -1
                    </button>
                </div>
            </div>
        </div>

        <!-- Set Information -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center space-x-8 bg-gray-700/30 rounded-lg p-4">
                <div>
                    <div class="text-sm text-gray-400">Current Set</div>
                    <div class="text-2xl font-bold text-white">{{ $currentSet }}</div>
                </div>
                <div class="w-px h-8 bg-gray-600"></div>
                <div>
                    <div class="text-sm text-gray-400">Set Time</div>
                    <div class="text-xl font-mono text-white" id="set-timer">00:00</div>
                </div>
                <div class="w-px h-8 bg-gray-600"></div>
                <div>
                    <div class="text-sm text-gray-400">To Win</div>
                    <div class="text-2xl font-bold text-green-400">11</div>
                </div>
            </div>
        </div>

        <!-- Set Scores -->
        <div class="border-t border-gray-700 pt-6">
            <h4 class="text-lg font-semibold text-white mb-4 text-center">Completed Sets</h4>
            <div class="flex justify-center space-x-4">
                @foreach($sets as $index => $set)
                <div class="text-center p-3 bg-gray-700/30 rounded-lg">
                    <div class="text-sm text-gray-400 mb-1">Set {{ $index + 1 }}</div>
                    <div class="text-lg font-bold text-white">{{ $set['home_score'] ?? $set['home'] ?? 0 }} - {{ $set['away_score'] ?? $set['away'] ?? 0 }}</div>
                    <div class="text-xs text-gray-400">{{ $setTimes[$index] ?? '00:00' }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Match Controls -->
        <div class="border-t border-gray-700 pt-6 mt-6">
            <div class="flex justify-center space-x-4">
                @if($match->status !== 'completed')
                <button type="button" wire:click="togglePause"
                        class="px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors font-semibold">
                    {{ $matchPaused ? '▶️ Resume Match' : '⏸️ Pause Match' }}
                </button>
                <button type="button" wire:click="endMatch"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold">
                    🏁 End Match
                </button>
                @else
                <div class="text-center text-green-400 font-semibold">
                    ✅ Match Completed
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Real-time timer updates
        let matchStartTime = {{ $matchStartTime ? $matchStartTime->timestamp * 1000 : 'null' }};
        let setStartTime = {{ $setStartTime ? $setStartTime->timestamp * 1000 : 'null' }};
        let matchPaused = {{ $matchPaused ? 'true' : 'false' }};

        // Listen for start-timers event from Livewire
        document.addEventListener('livewire:updated', () => {
            @this.on('start-timers', () => {
                matchStartTime = Date.now();
                setStartTime = Date.now();
                matchPaused = false;
                updateTimers();
            });

            @this.on('set-changed', () => {
                setStartTime = Date.now();
                updateTimers();
            });
        });

        function updateTimers() {
            const matchCompleted = {{ $match->status === 'completed' ? 'true' : 'false' }};

            if (!matchPaused && matchStartTime && !matchCompleted) {
                const elapsed = Math.floor((Date.now() - matchStartTime) / 1000);
                const matchTimer = document.getElementById('match-timer');
                if (matchTimer) {
                    matchTimer.textContent = formatTime(elapsed);
                }
            }

            if (!matchPaused && setStartTime && !matchCompleted) {
                const setElapsed = Math.floor((Date.now() - setStartTime) / 1000);
                const setTimer = document.getElementById('set-timer');
                if (setTimer) {
                    setTimer.textContent = formatSetTime(setElapsed);
                }
            }
        }

        function formatTime(seconds) {
            const hrs = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        function formatSetTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        // Update timers every second
        setInterval(updateTimers, 1000);
        updateTimers(); // Initial update

        // Listen for Livewire updates to refresh timer variables
        document.addEventListener('livewire:updated', () => {
            // Update setStartTime when it changes
            const newSetStartTime = {{ $setStartTime ? $setStartTime->timestamp * 1000 : 'null' }};
            if (newSetStartTime !== setStartTime) {
                setStartTime = newSetStartTime;
                updateTimers();
            }
        });
    </script>

    <!-- Match End Confirmation Modal -->
    <div x-data="{ showModal: false, winner: '', homeSets: 0, awaySets: 0, setsToWin: 0, finalSets: [] }"
         x-show="showModal"
         x-on:match-won.window="showModal = true; winner = $event.detail.winner; homeSets = $event.detail.homeSets; awaySets = $event.detail.awaySets; setsToWin = $event.detail.setsToWin; finalSets = $event.detail.finalSets"
         x-on:keydown.escape.window="showModal = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-500 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-white">
                                Match Completed!
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-300">
                                    <span x-text="winner === 'home' ? '{{ $match->league->is_team_based ? $match->homeTeam->name ?? 'Home Team' : $match->homePlayer->name ?? 'Home Player' }}' : '{{ $match->league->is_team_based ? $match->awayTeam->name ?? 'Away Team' : $match->awayPlayer->name ?? 'Away Player' }}'"></span>
                                    wins the match with <span x-text="winner === 'home' ? homeSets : awaySets"></span> sets to <span x-text="winner === 'away' ? homeSets : awaySets"></span>!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="confirmMatchEnd" x-on:click="showModal = false"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        End Match & Save Results
                    </button>
                    <button type="button" x-on:click="showModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Continue Playing
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
