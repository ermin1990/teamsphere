<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Live: {{ $match->homeTeam?->name ?? $match->homePlayer?->name ?? 'Home' }} vs {{ $match->awayTeam?->name ?? $match->awayPlayer?->name ?? 'Away' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let lastUpdated = null;
        
        function updateMatchData() {
            fetch('{{ route("public.api.match", $match) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateMatchDisplay(data.data);
                        lastUpdated = data.last_updated;
                        
                        // Update last updated time
                        const lastUpdatedElement = document.getElementById('last-updated-time');
                        if (lastUpdatedElement) {
                            const time = new Date(data.last_updated);
                            lastUpdatedElement.textContent = 'Last updated: ' + time.toLocaleTimeString();
                        }
                    }
                })
                .catch(error => {
                    // Silent error handling
                });
        }
        
        function updateMatchDisplay(matchData) {
            // Update home score
            const homeScoreElement = document.getElementById('home-score');
            if (homeScoreElement) {
                homeScoreElement.textContent = matchData.home_score;
            }
            
            // Update away score
            const awayScoreElement = document.getElementById('away-score');
            if (awayScoreElement) {
                awayScoreElement.textContent = matchData.away_score;
            }
            
            // Update sets if they exist
            if (matchData.sets && matchData.sets.length > 0) {
                updateSetsDisplay(matchData.sets);
            }
            
            // Update status
            const statusElement = document.getElementById('match-status');
            if (statusElement) {
                if (matchData.status === 'in_progress') {
                    statusElement.innerHTML = '<div class="text-green-400 font-semibold text-sm md:text-base">🔴 LIVE</div>';
                } else if (matchData.status === 'completed') {
                    statusElement.innerHTML = '<div class="text-green-400 font-semibold text-sm md:text-base">✅ COMPLETED</div>';
                } else {
                    statusElement.innerHTML = '';
                }
            }
        }
        
        function updateSetsDisplay(sets) {
            // Update sets breakdown for individual matches
            const setsContainer = document.getElementById('sets-breakdown');
            if (setsContainer && sets.length > 0) {
                let html = '<h3 class="text-lg md:text-xl font-bold text-center mb-4 bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">Set Scores</h3>';
                html += '<div class="overflow-x-auto"><table class="w-full text-center text-sm md:text-base">';
                html += '<thead><tr class="border-b border-gray-700">';
                html += '<th class="pb-2 md:pb-3 text-gray-400 font-medium text-xs md:text-sm">Set</th>';
                html += '<th class="pb-2 md:pb-3 text-blue-400 font-medium text-xs md:text-sm">{{ $match->homePlayer->name ?? "Home" }}</th>';
                html += '<th class="pb-2 md:pb-3 text-gray-400 font-medium text-xs md:text-sm">-</th>';
                html += '<th class="pb-2 md:pb-3 text-red-400 font-medium text-xs md:text-sm">{{ $match->awayPlayer->name ?? "Away" }}</th>';
                html += '</tr></thead><tbody>';
                
                sets.forEach((set, index) => {
                    html += '<tr class="border-b border-gray-700/50">';
                    html += '<td class="py-2 md:py-3 text-gray-300 font-medium text-xs md:text-sm">' + (index + 1) + '</td>';
                    html += '<td class="py-2 md:py-3 text-blue-400 font-bold text-sm md:text-lg">' + (set.home_score ?? set.home ?? 0) + '</td>';
                    html += '<td class="py-2 md:py-3 text-gray-400 text-xs md:text-sm">-</td>';
                    html += '<td class="py-2 md:py-3 text-red-400 font-bold text-sm md:text-lg">' + (set.away_score ?? set.away ?? 0) + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                setsContainer.innerHTML = html;
            }
        }
        
        // Update every 3 seconds
        setInterval(updateMatchData, 3000);
        
        // Initial update after a short delay
        setTimeout(updateMatchData, 1000);
    });
    </script>
</head>
<body class="antialiased bg-gray-900 text-white min-h-screen pb-16 md:pb-8">
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Menu (Desktop only) -->
            <nav class="hidden md:block bg-gray-800/50 backdrop-blur-xl rounded-2xl p-4 border border-gray-700/50 shadow-xl mb-6">
                <div class="flex items-center justify-center space-x-6 md:space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors text-sm md:text-base font-medium">
                        🏠 Home
                    </a>
                    <a href="{{ route('public.live-matches') }}" class="text-gray-300 hover:text-white transition-colors text-sm md:text-base font-medium">
                        📺 Live Matches
                    </a>
                    <a href="{{ route('public.leagues.index') }}" class="text-gray-300 hover:text-white transition-colors text-sm md:text-base font-medium">
                        🏆 Competitions
                    </a>
                </div>
            </nav>

            <!-- Header -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-4 border border-gray-700/50 shadow-xl mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                            🏓 Live Table Tennis Score
                        </h1>
                        <p class="text-gray-400 text-sm md:text-base mt-1">{{ $competition->name }} • Round {{ $match->round }}</p>
                    </div>
                    <div class="flex items-center space-x-2 md:space-x-4">
                        <a href="{{ route('public.matches.show', [$competition, $match]) }}"
                           class="px-3 py-1 md:px-4 md:py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors text-sm">
                            ← Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Live Score Display -->
            @livewire('public-live-score', ['match' => $match])

            <!-- Match Info -->
            <div class="mt-6 flex flex-col items-center gap-3 mb-8">
                @if($match->table)
                <div class="flex items-center gap-2 text-sm text-gray-300 bg-gray-700/30 px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span>Sto: {{ $match->table->name }}</span>
                </div>
                @endif
                
                @if($match->referee)
                <div class="flex items-center gap-2 text-sm text-gray-300 bg-gray-700/30 px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Sudija: {{ $match->referee->name }}</span>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-400 text-sm">
                <p>Powered by TeamSphere • {{ $organization->name }}</p>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu (Fixed Bottom) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-gray-800/95 backdrop-blur-xl border-t border-gray-700/50 shadow-2xl z-50">
        <div class="flex items-center justify-between py-3 px-4 w-full">
            <a href="{{ route('home') }}" class="flex flex-col items-center text-gray-300 hover:text-white transition-colors text-xs flex-1">
                <span class="text-lg">🏠</span>
                <span class="mt-1">Home</span>
            </a>
            <a href="{{ route('public.live-matches') }}" class="flex flex-col items-center text-gray-300 hover:text-white transition-colors text-xs flex-1">
                <span class="text-lg">📺</span>
                <span class="mt-1">Live</span>
            </a>
            <a href="{{ route('public.leagues.index') }}" class="flex flex-col items-center text-gray-300 hover:text-white transition-colors text-xs flex-1">
                <span class="text-lg">🏆</span>
                <span class="mt-1">Competitions</span>
            </a>
        </div>
    </nav>
</body>
</html>
