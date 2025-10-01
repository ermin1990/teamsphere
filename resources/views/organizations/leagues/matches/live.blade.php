<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                    Live Match Scoring
                </h2>
                <p class="text-gray-400 mt-1">{{ $league->name }} • Round {{ $match->round }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span id="match-status" class="px-3 py-1 text-sm rounded-full
                    @if($match->status === 'completed') bg-green-500/20 text-green-400
                    @elseif($match->status === 'in_progress') bg-yellow-500/20 text-yellow-400
                    @else bg-gray-500/20 text-gray-400 @endif"
                >
                    {{ ucfirst(str_replace('_', ' ', $match->status)) }}
                </span>
                <a href="{{ route('organizations.leagues.matches.show', [$organization, $league, $match]) }}"
                   class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    ← Back to Match
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Main Score Display -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                        <div class="text-center mb-8">
                            <div class="text-sm text-gray-400 mb-6">{{ $league->sport->name }} • Round {{ $match->round }}</div>

                            <div class="grid grid-cols-2 gap-8 items-center">
                                <!-- Home Participant -->
                                <div class="text-center">
                                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-3xl font-bold text-white">
                                            @if($league->is_team_based)
                                                {{ substr($match->homeTeam->name ?? 'TBD', 0, 2) }}
                                            @else
                                                {{ substr($match->homePlayer->name ?? 'TBD', 0, 2) }}
                                            @endif
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-bold text-white mb-2">
                                        @if($league->is_team_based)
                                            {{ $match->homeTeam->name ?? 'TBD' }}
                                        @else
                                            {{ $match->homePlayer->name ?? 'TBD' }}
                                        @endif
                                    </h3>
                                    <div class="text-6xl font-bold text-blue-400 mb-4" id="home-score">{{ $match->home_score ?? 0 }}</div>
                                    <button type="button" class="score-btn px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-semibold"
                                            data-player="home" data-action="add">
                                        + Point
                                    </button>
                                </div>

                                <!-- VS and Current Set -->
                                <div class="text-center">
                                    <div class="text-4xl font-bold text-gray-400 mb-4">VS</div>
                                    <div class="text-sm text-gray-400 mb-2">Current Set</div>
                                    <div class="text-2xl font-bold text-white" id="current-set">1</div>
                                </div>

                                <!-- Away Participant -->
                                <div class="text-center">
                                    <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-3xl font-bold text-white">
                                            @if($league->is_team_based)
                                                {{ substr($match->awayTeam->name ?? 'TBD', 0, 2) }}
                                            @else
                                                {{ substr($match->awayPlayer->name ?? 'TBD', 0, 2) }}
                                            @endif
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-bold text-white mb-2">
                                        @if($league->is_team_based)
                                            {{ $match->awayTeam->name ?? 'TBD' }}
                                        @else
                                            {{ $match->awayPlayer->name ?? 'TBD' }}
                                        @endif
                                    </h3>
                                    <div class="text-6xl font-bold text-red-400 mb-4" id="away-score">{{ $match->away_score ?? 0 }}</div>
                                    <button type="button" class="score-btn px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-semibold"
                                            data-player="away" data-action="add">
                                        + Point
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Set Scores -->
                        <div class="border-t border-gray-700 pt-6">
                            <h4 class="text-lg font-semibold text-white mb-4 text-center">Set Scores</h4>
                            <div id="sets-display" class="grid grid-cols-5 gap-4 text-center">
                                <!-- Sets will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Control Panel -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h4 class="text-lg font-semibold text-white mb-4">Quick Actions</h4>
                        <div class="space-y-3">
                            <button type="button" id="undo-last" class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                                ↶ Undo Last Point
                            </button>
                            <button type="button" id="complete-match" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                ✅ Complete Match
                            </button>
                            <div class="space-y-2">
                                <button type="button" id="forfeit-home" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm">
                                    🚫 {{ $league->is_team_based ? ($match->homeTeam->name ?? 'Home') : ($match->homePlayer->name ?? 'Home') }} Forfeits
                                </button>
                                <button type="button" id="forfeit-away" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm">
                                    🚫 {{ $league->is_team_based ? ($match->awayTeam->name ?? 'Away') : ($match->awayPlayer->name ?? 'Away') }} Forfeits
                                </button>
                            </div>
                            <button type="button" id="pause-match" class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors">
                                ⏸️ Pause Match
                            </button>
                        </div>
                    </div>

                    <!-- Set Management -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h4 class="text-lg font-semibold text-white mb-4">Set Management</h4>
                        <div class="space-y-3">
                            <button type="button" id="new-set" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                                🎯 Start New Set
                            </button>
                            <div class="text-sm text-gray-400 text-center">
                                Current Set: <span id="current-set-display">1</span>
                            </div>
                        </div>
                    </div>

                    <!-- Match Log -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h4 class="text-lg font-semibold text-white mb-4">Match Log</h4>
                        <div id="match-log" class="space-y-2 max-h-64 overflow-y-auto">
                            <div class="text-sm text-gray-400 text-center py-4">
                                Match started at {{ $match->played_at ? $match->played_at->format('H:i:s') : now()->format('H:i:s') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSet = 1;
        let sets = {{ $match->sets ? json_encode($match->sets) : '[]' }};
        let homeScore = {{ $match->home_score ?? 0 }};
        let awayScore = {{ $match->away_score ?? 0 }};
        let matchLog = [];
        let csrfToken = '{{ csrf_token() }}';

        // Initialize sets array if empty
        if (sets.length === 0) {
            sets = [{ home: 0, away: 0 }];
        }

        function updateDisplay() {
            document.getElementById('home-score').textContent = homeScore;
            document.getElementById('away-score').textContent = awayScore;
            document.getElementById('current-set').textContent = currentSet;
            document.getElementById('current-set-display').textContent = currentSet;

            // Update sets display
            const setsContainer = document.getElementById('sets-display');
            setsContainer.innerHTML = '';

            sets.forEach((set, index) => {
                const setDiv = document.createElement('div');
                setDiv.className = `p-3 rounded-lg ${index + 1 === currentSet ? 'bg-blue-600/20 border border-blue-500/50' : 'bg-gray-700/50'}`;
                setDiv.innerHTML = `
                    <div class="text-xs text-gray-400 mb-1">Set ${index + 1}</div>
                    <div class="text-lg font-bold text-white">${set.home || 0} - ${set.away || 0}</div>
                `;
                setsContainer.appendChild(setDiv);
            });

            // Update match log
            const logContainer = document.getElementById('match-log');
            logContainer.innerHTML = '<div class="text-sm text-gray-400 text-center py-4">Match started</div>';

            matchLog.slice(-10).forEach(log => {
                const logDiv = document.createElement('div');
                logDiv.className = 'text-sm text-gray-300 py-1';
                logDiv.textContent = log;
                logContainer.appendChild(logDiv);
            });
        }

        function addToLog(message) {
            matchLog.push(`${new Date().toLocaleTimeString()}: ${message}`);
            updateDisplay();
        }

        function sendScoreUpdate(action = 'update_score', forfeitedBy = null) {
            const data = {
                home_score: homeScore,
                away_score: awayScore,
                sets: sets,
                action: action
            };

            if (forfeitedBy) {
                data.forfeited_by = forfeitedBy;
            }

            fetch('{{ route("organizations.leagues.matches.live-score", [$organization, $league, $match]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Score updated:', data);
            })
            .catch(error => {
                console.error('Error updating score:', error);
            });
        }

        // Score buttons
        document.querySelectorAll('.score-btn').forEach(button => {
            button.addEventListener('click', function() {
                const player = this.dataset.player;
                const action = this.dataset.action;

                if (action === 'add') {
                    if (player === 'home') {
                        homeScore++;
                        sets[currentSet - 1].home = (sets[currentSet - 1].home || 0) + 1;
                        addToLog(`Point to ${player === 'home' ? '{{ $match->homeTeam->name ?? $match->homePlayer->name ?? "Home" }}' : '{{ $match->awayTeam->name ?? $match->awayPlayer->name ?? "Away" }}'}`);
                    } else {
                        awayScore++;
                        sets[currentSet - 1].away = (sets[currentSet - 1].away || 0) + 1;
                        addToLog(`Point to ${player === 'home' ? '{{ $match->homeTeam->name ?? $match->homePlayer->name ?? "Home" }}' : '{{ $match->awayTeam->name ?? $match->awayPlayer->name ?? "Away" }}'}`);
                    }
                    sendScoreUpdate();
                }

                updateDisplay();
            });
        });

        // Undo last point
        document.getElementById('undo-last').addEventListener('click', function() {
            if (matchLog.length > 0) {
                const lastLog = matchLog[matchLog.length - 1];
                if (lastLog.includes('Point to')) {
                    if (lastLog.includes('{{ $match->homeTeam->name ?? $match->homePlayer->name ?? "Home" }}')) {
                        homeScore = Math.max(0, homeScore - 1);
                        sets[currentSet - 1].home = Math.max(0, (sets[currentSet - 1].home || 0) - 1);
                    } else {
                        awayScore = Math.max(0, awayScore - 1);
                        sets[currentSet - 1].away = Math.max(0, (sets[currentSet - 1].away || 0) - 1);
                    }
                    matchLog.pop();
                    addToLog('Point undone');
                    sendScoreUpdate();
                }
            }
        });

        // Complete match
        document.getElementById('complete-match').addEventListener('click', function() {
            if (confirm('Are you sure you want to complete this match?')) {
                sendScoreUpdate('complete_match');
                addToLog('Match completed');
                setTimeout(() => {
                    window.location.href = '{{ route("organizations.leagues.matches.show", [$organization, $league, $match]) }}';
                }, 1000);
            }
        });

        // Pause match
        document.getElementById('pause-match').addEventListener('click', function() {
            if (confirm('Are you sure you want to pause this match?')) {
                sendScoreUpdate('pause_match');
                addToLog('Match paused');
                document.getElementById('match-status').textContent = 'Scheduled';
                document.getElementById('match-status').className = 'px-3 py-1 text-sm rounded-full bg-gray-500/20 text-gray-400';
            }
        });

        // New set
        document.getElementById('new-set').addEventListener('click', function() {
            currentSet++;
            sets.push({ home: 0, away: 0 });
            addToLog(`Started set ${currentSet}`);
            sendScoreUpdate();
            updateDisplay();
        });

        // Forfeit buttons
        document.getElementById('forfeit-home').addEventListener('click', function() {
            if (confirm('Are you sure {{ $league->is_team_based ? ($match->homeTeam->name ?? "Home Team") : ($match->homePlayer->name ?? "Home Player") }} wants to forfeit?')) {
                sendScoreUpdate('forfeit_match', 'home');
                addToLog('{{ $league->is_team_based ? ($match->homeTeam->name ?? "Home Team") : ($match->homePlayer->name ?? "Home Player") }} forfeited');
                setTimeout(() => {
                    window.location.href = '{{ route("organizations.leagues.matches.show", [$organization, $league, $match]) }}';
                }, 1000);
            }
        });

        document.getElementById('forfeit-away').addEventListener('click', function() {
            if (confirm('Are you sure {{ $league->is_team_based ? ($match->awayTeam->name ?? "Away Team") : ($match->awayPlayer->name ?? "Away Player") }} wants to forfeit?')) {
                sendScoreUpdate('forfeit_match', 'away');
                addToLog('{{ $league->is_team_based ? ($match->awayTeam->name ?? "Away Team") : ($match->awayPlayer->name ?? "Away Player") }} forfeited');
                setTimeout(() => {
                    window.location.href = '{{ route("organizations.leagues.matches.show", [$organization, $league, $match]) }}';
                }, 1000);
            }
        });

        // Initialize display
        updateDisplay();
    </script>
</x-app-layout>