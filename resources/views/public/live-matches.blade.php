<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Live Matches - TeamSphere</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        let lastUpdate = null;

        function updateLiveMatches() {
            fetch('{{ route("public.api.live-matches") }}')
                .then(response => {
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update total count in header
                        const headerElement = document.querySelector('p.text-gray-400');
                        if (headerElement) {
                            const countText = headerElement.innerHTML.replace(/\d+/, data.total_live_matches);
                            headerElement.innerHTML = countText;
                        }

                    // Update matches
                        updateMatchesDisplay(data.data);

                        // Update last updated time
                        lastUpdate = data.last_updated;
                    }
                })
                .catch(error => {
                });
        }

        function updateMatchesDisplay(competitionsData) {
            competitionsData.forEach(compData => {
                const matches = compData.matches;
                matches.forEach(match => {
                    // Find any anchor whose href ends with /{match.id}
                    const matchAnchors = document.querySelectorAll(`a[href$="/${match.id}"]`);
                    if (!matchAnchors || matchAnchors.length === 0) {
                        return;
                    }
                    const matchLink = matchAnchors[0];

                    // Update set scores
                    if (match.sets && match.sets.length > 0) {
                        // Find all player rows (flex items-center justify-between)
                        const playerRows = matchLink.querySelectorAll('.flex.items-center.justify-between');

                        // Home player (first row)
                        if (playerRows[0]) {
                            const homeSetSpans = playerRows[0].querySelectorAll('.flex.gap-1.ml-4 .w-6.text-center span');
                            homeSetSpans.forEach((span, index) => {
                                if (index < match.sets.length) {
                                    const set = match.sets[index];
                                    const homeScore = set.home_score ?? 0;
                                    const awayScore = set.away_score ?? 0;
                                    span.textContent = homeScore;
                                    span.className = `text-xs px-1 py-0.5 rounded ${homeScore > awayScore ? 'bg-green-900/60 text-green-300 font-bold' : 'text-gray-400'}`;
                                } else {
                                    span.textContent = '-';
                                    span.className = 'text-xs px-1 py-0.5 rounded text-gray-600';
                                }
                            });
                        }

                        // Away player (second row)
                        if (playerRows[1]) {
                            const awaySetSpans = playerRows[1].querySelectorAll('.flex.gap-1.ml-4 .w-6.text-center span');
                            awaySetSpans.forEach((span, index) => {
                                if (index < match.sets.length) {
                                    const set = match.sets[index];
                                    const homeScore = set.home_score ?? 0;
                                    const awayScore = set.away_score ?? 0;
                                    span.textContent = awayScore;
                                    span.className = `text-xs px-1 py-0.5 rounded ${awayScore > homeScore ? 'bg-green-900/60 text-green-300 font-bold' : 'text-gray-400'}`;
                                } else {
                                    span.textContent = '-';
                                    span.className = 'text-xs px-1 py-0.5 rounded text-gray-600';
                                }
                            });
                        }
                    }

                    // Update sets won indicators in white squares - calculate from sets data
                    const homeSetsSquare = matchLink.querySelector('.flex.items-center.justify-between:first-child .w-8.h-8.rounded.bg-white\\/20 .text-xs.font-bold, .flex.items-center.justify-between:first-child .w-8.h-8.rounded.bg-white\\/20 span');
                    const awaySetsSquare = matchLink.querySelector('.flex.items-center.justify-between:nth-child(2) .w-8.h-8.rounded.bg-white\\/20 .text-xs.font-bold, .flex.items-center.justify-between:nth-child(2) .w-8.h-8.rounded.bg-white\\/20 span');

                    // Calculate sets won by counting sets where player has higher score
                    let homeSetsWon = 0;
                    let awaySetsWon = 0;

                    if (match.sets && match.sets.length > 0) {
                        match.sets.forEach(set => {
                            const homeScore = set.home_score ?? 0;
                            const awayScore = set.away_score ?? 0;
                            if (homeScore > awayScore) {
                                homeSetsWon++;
                            } else if (awayScore > homeScore) {
                                awaySetsWon++;
                            }
                        });
                    }

                    if (homeSetsSquare) {
                        if (match.status === 'in_progress') {
                            homeSetsSquare.innerHTML = `<span class="text-green-400">${homeSetsWon}</span>`;
                        } else if (match.status === 'completed') {
                            homeSetsSquare.innerHTML = homeSetsWon;
                        } else {
                            homeSetsSquare.innerHTML = `<span class="text-gray-500">0</span>`;
                        }
                    }

                    if (awaySetsSquare) {
                        if (match.status === 'in_progress') {
                            awaySetsSquare.innerHTML = `<span class="text-green-400">${awaySetsWon}</span>`;
                        } else if (match.status === 'completed') {
                            awaySetsSquare.innerHTML = awaySetsWon;
                        } else {
                            awaySetsSquare.innerHTML = `<span class="text-gray-500">0</span>`;
                        }
                    }

                    // Update current set scores in green boxes
                    const currentScoreContainer = matchLink.querySelector('.flex.flex-col.items-start.justify-center.space-y-1.pl-4');
                    if (currentScoreContainer) {
                        const scoreBoxes = currentScoreContainer.querySelectorAll('.w-8.h-8');
                        if (match.status === 'in_progress') {
                            // Update home score box - green and glowing for live match
                            if (scoreBoxes[0]) {
                                scoreBoxes[0].className = 'w-8 h-8 bg-green-900/80 rounded-lg flex items-center justify-center';
                                const homeScoreDiv = scoreBoxes[0].querySelector('.text-sm.font-bold') || scoreBoxes[0].querySelector('div');
                                if (homeScoreDiv) {
                                    homeScoreDiv.textContent = match.home_score ?? 0;
                                    homeScoreDiv.className = 'text-sm font-bold text-green-300';
                                }
                            }
                            // Update away score box - green and glowing for live match
                            if (scoreBoxes[1]) {
                                scoreBoxes[1].className = 'w-8 h-8 bg-green-900/80 rounded-lg flex items-center justify-center';
                                const awayScoreDiv = scoreBoxes[1].querySelector('.text-sm.font-bold') || scoreBoxes[1].querySelector('div');
                                if (awayScoreDiv) {
                                    awayScoreDiv.textContent = match.away_score ?? 0;
                                    awayScoreDiv.className = 'text-sm font-bold text-green-300';
                                }
                            }
                        } else if (match.status === 'completed') {
                            // Match finished - hide current set boxes (show nothing)
                            if (scoreBoxes[0]) {
                                scoreBoxes[0].className = 'w-8 h-8 bg-transparent rounded-lg flex items-center justify-center';
                                const homeScoreDiv = scoreBoxes[0].querySelector('.text-sm.font-bold') || scoreBoxes[0].querySelector('div');
                                if (homeScoreDiv) {
                                    homeScoreDiv.textContent = '';
                                    homeScoreDiv.className = 'text-sm font-bold text-transparent';
                                }
                            }
                            if (scoreBoxes[1]) {
                                scoreBoxes[1].className = 'w-8 h-8 bg-transparent rounded-lg flex items-center justify-center';
                                const awayScoreDiv = scoreBoxes[1].querySelector('.text-sm.font-bold') || scoreBoxes[1].querySelector('div');
                                if (awayScoreDiv) {
                                    awayScoreDiv.textContent = '';
                                    awayScoreDiv.className = 'text-sm font-bold text-transparent';
                                }
                            }
                        } else {
                            // Set to inactive state (scheduled/cancelled)
                            if (scoreBoxes[0]) {
                                scoreBoxes[0].className = 'w-8 h-8 bg-gray-700/50 rounded-lg flex items-center justify-center';
                                const homeScoreDiv = scoreBoxes[0].querySelector('.text-sm.font-bold') || scoreBoxes[0].querySelector('div');
                                if (homeScoreDiv) {
                                    homeScoreDiv.textContent = '-';
                                    homeScoreDiv.className = 'text-sm font-bold text-gray-500';
                                }
                            }
                            if (scoreBoxes[1]) {
                                scoreBoxes[1].className = 'w-8 h-8 bg-gray-700/50 rounded-lg flex items-center justify-center';
                                const awayScoreDiv = scoreBoxes[1].querySelector('.text-sm.font-bold') || scoreBoxes[1].querySelector('div');
                                if (awayScoreDiv) {
                                    awayScoreDiv.textContent = '-';
                                    awayScoreDiv.className = 'text-sm font-bold text-gray-500';
                                }
                            }
                        }
                    }
                });
            });
        }

        // Update every 3 seconds
        setInterval(updateLiveMatches, 3000);

        // Initial update
        document.addEventListener('DOMContentLoaded', function() {
            updateLiveMatches();
        });
    </script>
</head>
<body class="antialiased bg-gray-900 text-white min-h-screen pb-16 md:pb-8">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Menu (Desktop only) -->
            <nav class="hidden md:block bg-gray-800/50 backdrop-blur-xl rounded-2xl p-4 border border-gray-700/50 shadow-xl mb-6">
                <div class="flex items-center justify-center space-x-6 md:space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors text-sm md:text-base font-medium">
                        🏠 Home
                    </a>
                    <a href="{{ route('public.live-matches') }}" class="text-blue-400 font-semibold text-sm md:text-base">
                        📺 Live Matches
                    </a>
                    <a href="{{ route('public.leagues.index') }}" class="text-gray-300 hover:text-white transition-colors text-sm md:text-base font-medium">
                        🏆 Competitions
                    </a>
                </div>
            </nav>

            <!-- Header -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent mb-2">
                        🏓 Live Matches
                    </h1>
                    <p class="text-gray-400">Watch live table tennis matches from all leagues</p>
                    <div class="mt-4 text-sm text-gray-500">
                        Real-time updates • {{ $liveMatches->count() }} live matches
                    </div>
                </div>
            </div>

            @if($liveMatches->count() > 0)
                <!-- Group matches by competition -->
                @php
                    $matchesByCompetition = $liveMatches->groupBy(function($match) {
                        return $match->competition->id;
                    });
                @endphp

                <div class="space-y-6 md:space-y-8" id="live-matches-container">
                    @foreach($matchesByCompetition as $competitionId => $competitionMatches)
                    @php
                        $competition = $competitionMatches->first()->competition;
                    @endphp

                    <!-- Competition Header -->
                    <div class="bg-gray-800/30 backdrop-blur-xl rounded-xl p-4 md:p-6 border border-gray-700/30 shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-lg md:text-xl font-bold text-white">
                                    {{ $competition->name }}
                                </h2>
                                <p class="text-sm text-gray-400">
                                    {{ $competition->organization->name }} • {{ $competition->sport->name }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-green-400 font-semibold">
                                    🔴 {{ $competitionMatches->count() }} LIVE
                                </div>
                            </div>
                        </div>

                        <!-- Matches Table -->
                        <div class="bg-gray-800/30 rounded-lg overflow-hidden border border-gray-700/30">
                            <!-- Table Header - Removed -->
                            <!-- <div class="grid grid-cols-[3fr_120px] gap-0 bg-gray-700/50 border-b border-gray-600/30">
                                <div class="p-4">
                                    <span class="text-sm font-semibold text-gray-300 uppercase tracking-wider">Players</span>
                                </div>
                                <div class="p-4 text-center">
                                    <span class="text-sm font-semibold text-gray-300 uppercase tracking-wider">Score</span>
                                </div>
                            </div> -->

                            @foreach($competitionMatches as $match)
                            <a href="{{ route('public.matches.show', [$match->competition, $match]) }}"
                               class="block hover:bg-gray-700/20 transition-colors duration-200 border-b border-gray-700/20 last:border-b-0 group">
                                <div class="grid grid-cols-[3fr_120px] gap-0 items-center p-4">
                                    <!-- Players Column -->
                                    <div class="space-y-4">
                                        <!-- Home Player -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <!-- Sets won indicator -->
                                                <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                                    @php
                                                        $homeSetsWon = 0;
                                                        if(isset($match->sets) && is_array($match->sets)) {
                                                            foreach($match->sets as $set) {
                                                                if(($set['home_score'] ?? 0) > ($set['away_score'] ?? 0)) {
                                                                    $homeSetsWon++;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @if($match->status === 'completed')
                                                        {{ $homeSetsWon }}
                                                    @elseif($match->status === 'in_progress')
                                                        <span class="text-green-400">{{ $homeSetsWon }}</span>
                                                    @else
                                                        <span class="text-gray-500">0</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs md:text-sm font-semibold text-white truncate">
                                                    @if($match->competition->is_team_based)
                                                        {{ $match->homeTeam?->name ?? 'Home Team' }}
                                                    @else
                                                        {{ $match->homePlayer?->name ?? 'Home Player' }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex gap-1 ml-4">
                                                @for($i = 1; $i <= 5; $i++)
                                                <div class="w-6 text-center {{ $i < 5 ? 'border-r border-gray-600/30' : '' }}">
                                                    @if(isset($match->sets) && isset($match->sets[$i-1]))
                                                        <span class="text-xs px-1 py-0.5 rounded {{ $match->sets[$i-1]['home_score'] > $match->sets[$i-1]['away_score'] ? 'bg-green-900/60 text-green-300 font-bold' : 'text-gray-400' }}">
                                                            {{ $match->sets[$i-1]['home_score'] ?? 0 }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs px-1 py-0.5 rounded text-gray-600">-</span>
                                                    @endif
                                                </div>
                                                @endfor
                                            </div>
                                        </div>

                                        <!-- Away Player -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <!-- Sets won indicator -->
                                                <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                                    @php
                                                        $awaySetsWon = 0;
                                                        if(isset($match->sets) && is_array($match->sets)) {
                                                            foreach($match->sets as $set) {
                                                                if(($set['away_score'] ?? 0) > ($set['home_score'] ?? 0)) {
                                                                    $awaySetsWon++;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @if($match->status === 'completed')
                                                        {{ $awaySetsWon }}
                                                    @elseif($match->status === 'in_progress')
                                                        <span class="text-green-400">{{ $awaySetsWon }}</span>
                                                    @else
                                                        <span class="text-gray-500">0</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs md:text-sm font-semibold text-white truncate">
                                                    @if($match->competition->is_team_based)
                                                        {{ $match->awayTeam?->name ?? 'Away Team' }}
                                                    @else
                                                        {{ $match->awayPlayer?->name ?? 'Away Player' }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex gap-1 ml-4">
                                                @for($i = 1; $i <= 5; $i++)
                                                <div class="w-6 text-center {{ $i < 5 ? 'border-r border-gray-600/30' : '' }}">
                                                    @if(isset($match->sets) && isset($match->sets[$i-1]))
                                                        <span class="text-xs px-1 py-0.5 rounded {{ $match->sets[$i-1]['away_score'] > $match->sets[$i-1]['home_score'] ? 'bg-green-900/60 text-green-300 font-bold' : 'text-gray-400' }}">
                                                            {{ $match->sets[$i-1]['away_score'] ?? 0 }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs px-1 py-0.5 rounded text-gray-600">-</span>
                                                    @endif
                                                </div>
                                                @endfor
                                            </div>
                                        </div>

                                        <!-- Match Status - Removed live indicator -->
                                    </div>

                                    <!-- Current Set Score Column -->
                                    <div class="flex flex-col items-start justify-center space-y-1 pl-4">
                                        @if($match->status === 'in_progress')
                                            <div class="flex flex-col items-center space-y-1">
                                                <div class="w-8 h-8 bg-green-900/80 rounded-lg flex items-center justify-center">
                                                    <div class="text-sm font-bold text-green-300">
                                                        {{ $match->home_score ?? 0 }}
                                                    </div>
                                                </div>
                                                <div class="w-8 h-8 bg-green-900/80 rounded-lg flex items-center justify-center">
                                                    <div class="text-sm font-bold text-green-300">
                                                        {{ $match->away_score ?? 0 }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center space-y-1">
                                                <div class="w-8 h-8 bg-gray-700/50 rounded-lg flex items-center justify-center">
                                                    <div class="text-sm font-bold text-gray-500">-</div>
                                                </div>
                                                <div class="w-8 h-8 bg-gray-700/50 rounded-lg flex items-center justify-center">
                                                    <div class="text-sm font-bold text-gray-500">-</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- No Live Matches -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-12 border border-gray-700/50 shadow-xl text-center">
                    <div class="text-6xl mb-4">🏓</div>
                    <h2 class="text-2xl font-bold text-white mb-4">No Live Matches</h2>
                    <p class="text-gray-400 mb-6">There are currently no live matches being played. Check back later!</p>
                    <div class="text-sm text-gray-500">
                        Live matches will appear here automatically when games start.
                    </div>
                </div>
            @endif

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-400 text-sm">
                <p>Powered by TeamSphere • Real-time match updates</p>
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
