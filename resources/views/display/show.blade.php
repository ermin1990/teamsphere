<!DOCTYPE html>
<html lang="bs" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uživo Prikaz - Team Sphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        
        .match-card {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .pulse-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }

        .score-animation {
            animation: scoreUpdate 0.5s ease-in-out;
        }

        @keyframes scoreUpdate {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        /* Optimized for 16 matches - 4x4 grid */
        @media (min-width: 1920px) {
            .match-card {
                font-size: 0.85rem;
            }
        }
        
        /* Compact mode for many matches */
        .compact-mode .match-card {
            padding: 1rem;
        }
        
        .compact-mode .sport-icon {
            font-size: 1.5rem;
        }
        
        .compact-mode .player-name {
            font-size: 0.875rem;
        }
        
        .compact-mode .score {
            font-size: 1.5rem;
        }
    </style>
</head>
<body class="h-full">
    <div class="min-h-screen p-4">
        <div class="max-w-[1920px] mx-auto">
            <!-- Header -->
            <div class="mb-4 text-center">
                <div class="flex items-center justify-center space-x-3 mb-2">
                    <div class="w-2 h-2 bg-red-500 rounded-full pulse-dot"></div>
                    <h1 class="text-3xl font-bold text-white">MEČEVI UŽIVO</h1>
                    <div class="w-2 h-2 bg-red-500 rounded-full pulse-dot"></div>
                </div>
                <p class="text-gray-400 text-sm" id="current-time"></p>
            </div>

            @if($matches->count() > 0)
                <!-- Matches Grid (4x4 for 16 matches) -->
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 {{ $matches->count() > 8 ? 'compact-mode' : '' }}" id="matches-grid">
                    @foreach($matches as $match)
                        <div class="match-card bg-gray-800/70 backdrop-blur-xl rounded-xl p-4 border-2 border-gray-700/50 hover:border-blue-500/50 transition-all duration-300 shadow-2xl" data-match-id="{{ $match->id }}">
                            <!-- Sport Icon & Competition -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-2 flex-1 min-w-0">
                                    <span class="text-2xl sport-icon">{{ $match->league->sport->icon }}</span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-white font-semibold text-xs truncate">{{ $match->competition->name ?? $match->league->name }}</p>
                                        <p class="text-gray-400 text-[10px] truncate">{{ $match->league->sport->name }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500 text-white animate-pulse ml-2">
                                    UŽIVO
                                </span>
                            </div>

                            <!-- Players/Teams -->
                            <div class="space-y-2">
                                <!-- Home -->
                                <div class="flex items-center justify-between bg-gray-700/50 rounded-lg p-2 transition-all duration-300 player-row-home">
                                    <div class="flex-1 min-w-0 pr-2">
                                        <p class="text-white font-bold text-sm truncate player-name">
                                            @if($match->league->is_team_based && $match->homeTeam)
                                                {{ $match->homeTeam->name }}
                                            @else
                                                {{ $match->homePlayer->name ?? 'TBD' }}
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-2xl font-bold text-white score score-home">{{ $match->home_score ?? 0 }}</span>
                                    </div>
                                </div>

                                <!-- Away -->
                                <div class="flex items-center justify-between bg-gray-700/50 rounded-lg p-2 transition-all duration-300 player-row-away">
                                    <div class="flex-1 min-w-0 pr-2">
                                        <p class="text-white font-bold text-sm truncate player-name">
                                            @if($match->league->is_team_based && $match->awayTeam)
                                                {{ $match->awayTeam->name }}
                                            @else
                                                {{ $match->awayPlayer->name ?? 'TBD' }}
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-2xl font-bold text-white score score-away">{{ $match->away_score ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Set History -->
                            @php
                                $sets = is_string($match->sets) ? json_decode($match->sets, true) : $match->sets;
                            @endphp
                            @if($sets && is_array($sets) && count($sets) > 0)
                                <div class="mt-3 pt-3 border-t border-gray-700/50">
                                    <p class="text-[10px] text-gray-400 mb-1.5 text-center font-semibold uppercase tracking-wide">Završeni Setovi</p>
                                    <div class="flex justify-center space-x-1.5 set-history">
                                        @foreach($sets as $index => $set)
                                            <div class="bg-gray-700/30 rounded-md p-1.5 text-center min-w-[50px]">
                                                <p class="text-[9px] text-gray-500 mb-0.5">Set {{ $index + 1 }}</p>
                                                <div class="text-sm font-bold text-white whitespace-nowrap">
                                                    <span class="{{ ($set['home_score'] ?? $set['home'] ?? 0) > ($set['away_score'] ?? $set['away'] ?? 0) ? 'text-green-400' : 'text-white' }}">
                                                        {{ $set['home_score'] ?? $set['home'] ?? 0 }}
                                                    </span>
                                                    <span class="text-gray-600 mx-0.5">-</span>
                                                    <span class="{{ ($set['away_score'] ?? $set['away'] ?? 0) > ($set['home_score'] ?? $set['home'] ?? 0) ? 'text-green-400' : 'text-white' }}">
                                                        {{ $set['away_score'] ?? $set['away'] ?? 0 }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Sets Score & Current Set -->
                            <div class="mt-3 pt-3 border-t border-gray-700/50">
                                @if($match->home_sets !== null && $match->away_sets !== null)
                                    <div class="flex items-center justify-center space-x-2 mb-1">
                                        <span class="text-[10px] text-gray-400">Setovi:</span>
                                        <span class="text-lg font-bold text-blue-400 sets-home">{{ $match->home_sets }}</span>
                                        <span class="text-gray-500 text-sm">-</span>
                                        <span class="text-lg font-bold text-blue-400 sets-away">{{ $match->away_sets }}</span>
                                    </div>
                                @endif

                                @if($match->current_set)
                                    <div class="text-center mb-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-600 text-white current-set">
                                            <span class="w-1 h-1 bg-white rounded-full mr-1 animate-pulse"></span>
                                            Set {{ $match->current_set }}
                                        </span>
                                    </div>
                                @endif
                                
                                <!-- Table and Referee Info -->
                                <div class="text-center space-y-1">
                                    @if($match->table)
                                    <div class="text-[10px] text-gray-400">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="font-semibold text-blue-400">{{ $match->table->name }}</span>
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- No Matches -->
                <div class="text-center py-20">
                    <div class="w-32 h-32 bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-8">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-4xl font-bold text-white mb-4">Nema Mečeva Uživo</h2>
                    <p class="text-gray-400 text-xl">Trenutno nema izabranih turnira sa mečevima uživo.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('current-time').textContent = now.toLocaleDateString('bs-BA', options);
        }
        
        updateTime();
        setInterval(updateTime, 1000);

        // Auto-refresh matches every 5 seconds
        setInterval(() => {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newGrid = doc.getElementById('matches-grid');
                    const currentGrid = document.getElementById('matches-grid');
                    
                    if (newGrid && currentGrid) {
                        // Get all current match cards
                        const currentMatches = Array.from(currentGrid.querySelectorAll('.match-card'));
                        const newMatches = Array.from(newGrid.querySelectorAll('.match-card'));
                        
                        // Update each match
                        newMatches.forEach(newMatch => {
                            const matchId = newMatch.dataset.matchId;
                            const currentMatch = currentMatches.find(m => m.dataset.matchId === matchId);
                            
                            if (currentMatch) {
                                // Update scores with animation
                                const newHomeScore = newMatch.querySelector('.score-home').textContent;
                                const newAwayScore = newMatch.querySelector('.score-away').textContent;
                                const currentHomeScore = currentMatch.querySelector('.score-home').textContent;
                                const currentAwayScore = currentMatch.querySelector('.score-away').textContent;
                                
                                if (newHomeScore !== currentHomeScore) {
                                    const homeScoreEl = currentMatch.querySelector('.score-home');
                                    const homeRowEl = currentMatch.querySelector('.player-row-home');
                                    homeScoreEl.textContent = newHomeScore;
                                    homeScoreEl.classList.add('score-animation');
                                    // Flash entire row green
                                    homeRowEl.classList.remove('bg-gray-700/50');
                                    homeRowEl.classList.add('bg-green-500/50');
                                    setTimeout(() => {
                                        homeScoreEl.classList.remove('score-animation');
                                        homeRowEl.classList.remove('bg-green-500/50');
                                        homeRowEl.classList.add('bg-gray-700/50');
                                    }, 500);
                                }
                                
                                if (newAwayScore !== currentAwayScore) {
                                    const awayScoreEl = currentMatch.querySelector('.score-away');
                                    const awayRowEl = currentMatch.querySelector('.player-row-away');
                                    awayScoreEl.textContent = newAwayScore;
                                    awayScoreEl.classList.add('score-animation');
                                    // Flash entire row green
                                    awayRowEl.classList.remove('bg-gray-700/50');
                                    awayRowEl.classList.add('bg-green-500/50');
                                    setTimeout(() => {
                                        awayScoreEl.classList.remove('score-animation');
                                        awayRowEl.classList.remove('bg-green-500/50');
                                        awayRowEl.classList.add('bg-gray-700/50');
                                    }, 500);
                                }
                                
                                // Update sets if they exist
                                const newSetsHome = newMatch.querySelector('.sets-home');
                                const newSetsAway = newMatch.querySelector('.sets-away');
                                if (newSetsHome && newSetsAway) {
                                    const currentSetsHome = currentMatch.querySelector('.sets-home');
                                    const currentSetsAway = currentMatch.querySelector('.sets-away');
                                    if (currentSetsHome) currentSetsHome.textContent = newSetsHome.textContent;
                                    if (currentSetsAway) currentSetsAway.textContent = newSetsAway.textContent;
                                }
                                
                                // Update set history
                                const newSetHistory = newMatch.querySelector('.set-history');
                                const currentSetHistory = currentMatch.querySelector('.set-history');
                                if (newSetHistory && currentSetHistory) {
                                    if (newSetHistory.innerHTML !== currentSetHistory.innerHTML) {
                                        currentSetHistory.innerHTML = newSetHistory.innerHTML;
                                        // Add animation to new set history
                                        currentSetHistory.classList.add('score-animation');
                                        setTimeout(() => currentSetHistory.classList.remove('score-animation'), 500);
                                    }
                                }
                                
                                // Update current set
                                const newCurrentSet = newMatch.querySelector('.current-set');
                                if (newCurrentSet) {
                                    const currentSetEl = currentMatch.querySelector('.current-set');
                                    if (currentSetEl) currentSetEl.textContent = newCurrentSet.textContent;
                                }
                            }
                        });
                        
                        // If number of matches changed, reload the page
                        if (currentMatches.length !== newMatches.length) {
                            location.reload();
                        }
                    } else if (!newGrid && currentGrid) {
                        // All matches ended, reload
                        location.reload();
                    }
                })
                .catch(error => {
                    // Silent error handling
                });
        }, 5000);
    </script>
</body>
</html>
