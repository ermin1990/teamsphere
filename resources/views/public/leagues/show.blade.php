<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $competition->name }} - {{ $organization->name }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        function updateCompetitionMatches() {
            console.log('Updating competition matches...');
            fetch('{{ route("public.api.live-matches") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Filter matches for this specific competition
                        const competitionData = data.data.find(comp => comp.competition.id === {{ $competition->id }});
                        if (competitionData) {
                            updateLiveMatchesSection(competitionData.matches);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating competition matches:', error);
                });
        }

        function updateLiveMatchesSection(matchesData) {
            console.log('Updating league live matches section...');
            matchesData.forEach(match => {
                // Find match by ID in rounds section
                const roundMatchElements = document.querySelectorAll(`a[href*="/matches/${match.id}"]`);
                roundMatchElements.forEach(matchElement => {
                    console.log('Updating match', match.id);

                    // Update sets won indicators in white squares
                    const homeSetsSquare = matchElement.querySelector('.flex.items-center.justify-between:first-child .w-8.h-8.rounded.bg-white\\/20 .text-xs.font-bold, .flex.items-center.justify-between:first-child .w-8.h-8.rounded.bg-white\\/20 span');
                    const awaySetsSquare = matchElement.querySelector('.flex.items-center.justify-between:nth-child(2) .w-8.h-8.rounded.bg-white\\/20 .text-xs.font-bold, .flex.items-center.justify-between:nth-child(2) .w-8.h-8.rounded.bg-white\\/20 span');

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
                        const newScore = homeSetsWon;
                        if (match.status === 'in_progress') {
                            homeSetsSquare.innerHTML = `<span class="text-green-400">${newScore}</span>`;
                        } else if (match.status === 'completed') {
                            homeSetsSquare.innerHTML = newScore;
                        } else {
                            homeSetsSquare.innerHTML = `<span class="text-gray-500">0</span>`;
                        }
                    }

                    if (awaySetsSquare) {
                        const newScore = awaySetsWon;
                        if (match.status === 'in_progress') {
                            awaySetsSquare.innerHTML = `<span class="text-green-400">${newScore}</span>`;
                        } else if (match.status === 'completed') {
                            awaySetsSquare.innerHTML = newScore;
                        } else {
                            awaySetsSquare.innerHTML = `<span class="text-gray-500">0</span>`;
                        }
                    }

                    // Update set scores
                    if (match.sets && match.sets.length > 0) {
                        console.log('Updating set scores for match', match.id, 'sets:', match.sets);

                        // Find all player rows (flex items-center justify-between)
                        const playerRows = matchElement.querySelectorAll('.flex.items-center.justify-between');

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

                        console.debug('Updated set scores for match', match.id);
                    }

                    // Update current set scores in green boxes
                    const currentScoreContainer = matchElement.querySelector('.flex.flex-col.items-start.justify-center.space-y-1.pl-4');
                    console.log('Found score container:', currentScoreContainer);
                    if (currentScoreContainer) {
                        const scoreBoxes = currentScoreContainer.querySelectorAll('.w-8.h-8');
                        console.log('Found score boxes:', scoreBoxes.length, 'for match', match.id);
                        if (match.status === 'in_progress') {
                            // Update home score box
                            if (scoreBoxes[0]) {
                                scoreBoxes[0].className = 'w-8 h-8 bg-green-900/80 rounded-lg flex items-center justify-center';
                                const homeScoreDiv = scoreBoxes[0].querySelector('.text-sm.font-bold');
                                if (homeScoreDiv) {
                                    homeScoreDiv.textContent = match.home_score ?? 0;
                                    homeScoreDiv.className = 'text-sm font-bold text-green-300';
                                    console.log('Updated home score to:', match.home_score ?? 0);
                                }
                            }
                            // Update away score box
                            if (scoreBoxes[1]) {
                                scoreBoxes[1].className = 'w-8 h-8 bg-green-900/80 rounded-lg flex items-center justify-center';
                                const awayScoreDiv = scoreBoxes[1].querySelector('.text-sm.font-bold');
                                if (awayScoreDiv) {
                                    awayScoreDiv.textContent = match.away_score ?? 0;
                                    awayScoreDiv.className = 'text-sm font-bold text-green-300';
                                    console.log('Updated away score to:', match.away_score ?? 0);
                                }
                            }
                        } else {
                            // Set to inactive state
                            if (scoreBoxes[0]) {
                                scoreBoxes[0].className = 'w-8 h-8 bg-gray-700/50 rounded-lg flex items-center justify-center';
                                const homeScoreDiv = scoreBoxes[0].querySelector('.text-sm.font-bold');
                                if (homeScoreDiv) {
                                    homeScoreDiv.textContent = '-';
                                    homeScoreDiv.className = 'text-sm font-bold text-gray-500';
                                }
                            }
                            if (scoreBoxes[1]) {
                                scoreBoxes[1].className = 'w-8 h-8 bg-gray-700/50 rounded-lg flex items-center justify-center';
                                const awayScoreDiv = scoreBoxes[1].querySelector('.text-sm.font-bold');
                                if (awayScoreDiv) {
                                    awayScoreDiv.textContent = '-';
                                    awayScoreDiv.className = 'text-sm font-bold text-gray-500';
                                }
                            }
                        }
                    } else {
                        console.log('Score container not found for match', match.id);
                    }
                });
            });
        }

        function updateRoundsSection(matchesData) {
            // This function is now handled by updateLiveMatchesSection
        }

        // Update every 3 seconds
        setInterval(updateCompetitionMatches, 3000);

        // Initial update
        document.addEventListener('DOMContentLoaded', function() {
            updateCompetitionMatches();
        });
    </script>
</head>
<body class="antialiased bg-gray-900 text-white min-h-screen pb-16 md:pb-8 px-4">
    <div class="py-12">
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
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
                        {{ $competition->name }}
                    </h1>
                    <p class="text-gray-400">{{ $organization->name }} • {{ $competition->sport->name }}</p>
                </div>
            </div>

            <!-- Competition Content -->
            @if($competition->type === 'league')
                <!-- League Tabs -->
                <div class="mb-6 md:mb-8">
                    <div class="border-b border-gray-700">
                        <nav class="-mb-px flex space-x-6 md:space-x-8">
                            <button onclick="showLeagueTab('standings')" id="standings-tab"
                                    class="tab-button border-b-2 py-2 px-1 text-sm md:text-base font-medium transition-colors border-blue-500 text-blue-400">
                                🏆 Tabela
                            </button>
                            <button onclick="showLeagueTab('matches')" id="matches-tab"
                                    class="tab-button border-b-2 py-2 px-1 text-sm md:text-base font-medium transition-colors border-transparent text-gray-400 hover:text-gray-300">
                                🎯 Mečevi
                            </button>
                        </nav>
                    </div>

                    <!-- Standings Tab Content -->
                    <div id="standings-content" class="tab-content mt-4 md:mt-6">
                        @if($competition->standings && $competition->standings->count() > 0)
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-3 md:p-5 border border-gray-700/50 shadow-xl">
                            <!-- Table Header -->
                            <div class="grid grid-cols-12 gap-2 mb-2 text-xs text-gray-400 font-medium px-2">
                                <div class="col-span-6"></div>
                                <div class="col-span-1 text-center">Pob</div>
                                <div class="col-span-1 text-center">Rem</div>
                                <div class="col-span-1 text-center">Por</div>
                                <div class="col-span-1 text-center">Set ±</div>
                                <div class="col-span-2 text-center">Bod</div>
                            </div>

                            <!-- Table Rows -->
                            <div class="space-y-1">
                                @foreach($competition->standings as $standing)
                                <div class="grid grid-cols-12 gap-2 items-center py-2 px-2 bg-gray-700/20 rounded text-xs md:text-sm">
                                    <div class="col-span-6 flex items-center space-x-2">
                                        <span class="font-bold text-gray-400 w-6 text-center">{{ $standing->position }}</span>
                                        <span class="text-white font-medium truncate">{{ $standing->participant->name }}</span>
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <span class="text-green-400 font-bold">{{ $standing->won }}</span>
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <span class="text-yellow-400 font-bold">{{ $standing->drawn }}</span>
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <span class="text-red-400 font-bold">{{ $standing->lost }}</span>
                                    </div>
                                    <div class="col-span-1 text-center">
                                        <span class="text-cyan-400 font-bold">{{ $standing->sets_won - $standing->sets_lost }}</span>
                                    </div>
                                    <div class="col-span-2 text-center">
                                        <span class="text-blue-400 font-bold">{{ $standing->points }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 md:p-12 border border-gray-700/50 shadow-xl text-center">
                            <div class="text-4xl md:text-6xl mb-4">🏆</div>
                            <h3 class="text-lg md:text-xl font-semibold text-white mb-2">Tabela još nije dostupna</h3>
                            <p class="text-gray-400 text-sm md:text-base">Tabela će se pojaviti kada liga počne.</p>
                        </div>
                        @endif
                    </div>

                    <!-- Matches Tab Content -->
                    <div id="matches-content" class="tab-content mt-4 md:mt-6 hidden">
                        @php
                            $matchesByRound = $competition->matches->sortBy('round')->groupBy('round');
                        @endphp
                        @if($matchesByRound->count() > 0)
                        <div class="space-y-3 md:space-y-5">
                            @foreach($matchesByRound as $round => $roundMatches)
                            <div>
                                <h4 class="text-xs md:text-base font-semibold text-center mb-3 md:mb-4 text-gray-400 uppercase tracking-wider">
                                    Kolo {{ $round }}
                                </h4>
                                <div class="space-y-1 md:space-y-3">
                                    @foreach($roundMatches->sortByDesc('scheduled_at') as $match)
                                    <a href="{{ route('public.matches.show', [$competition, $match]) }}"
                                       class="block bg-gray-700/20 hover:bg-gray-700/40 rounded-md p-3 transition-all duration-200 hover:scale-[1.01]">
                                        <div class="grid grid-cols-[3fr_120px] gap-0 items-center p-3">
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
                                                                } elseif ($match->status === 'completed') {
                                                                    $homeSetsWon = $match->home_score;
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
                                                            @if($competition->is_team_based)
                                                                {{ $match->homeTeam?->name ?? 'Home Team' }}
                                                            @else
                                                                {{ $match->homePlayer?->name ?? 'Home Player' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-1 ml-4">
                                                        @php
                                                            $displaySets = isset($match->sets) && is_array($match->sets) && count($match->sets) > 0 ? $match->sets : [];
                                                        @endphp
                                                        @for($i = 1; $i <= 5; $i++)
                                                        <div class="w-6 text-center {{ $i < 5 ? 'border-r border-gray-600/30' : '' }}">
                                                            @if(isset($displaySets[$i-1]))
                                                                <span class="text-xs px-1 py-0.5 rounded {{ $displaySets[$i-1]['home_score'] > $displaySets[$i-1]['away_score'] ? 'bg-green-900/60 text-green-300 font-bold' : 'text-gray-400' }}">
                                                                    {{ $displaySets[$i-1]['home_score'] ?? 0 }}
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
                                                                } elseif ($match->status === 'completed') {
                                                                    $awaySetsWon = $match->away_score;
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
                                                            @if($competition->is_team_based)
                                                                {{ $match->awayTeam?->name ?? 'Away Team' }}
                                                            @else
                                                                {{ $match->awayPlayer?->name ?? 'Away Player' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-1 ml-4">
                                                        @for($i = 1; $i <= 5; $i++)
                                                        <div class="w-6 text-center {{ $i < 5 ? 'border-r border-gray-600/30' : '' }}">
                                                            @if(isset($displaySets[$i-1]))
                                                                <span class="text-xs px-1 py-0.5 rounded {{ $displaySets[$i-1]['away_score'] > $displaySets[$i-1]['home_score'] ? 'bg-green-900/60 text-green-300 font-bold' : 'text-gray-400' }}">
                                                                    {{ $displaySets[$i-1]['away_score'] ?? 0 }}
                                                                </span>
                                                            @else
                                                                <span class="text-xs px-1 py-0.5 rounded text-gray-600">-</span>
                                                            @endif
                                                        </div>
                                                        @endfor
                                                    </div>
                                                </div>
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
                                                @elseif($match->status === 'completed')
                                                    <div class="flex flex-col items-center space-y-1">
                                                        <div class="w-8 h-8 bg-green-900/80 rounded-lg flex items-center justify-center">
                                                            <div class="text-sm font-bold text-green-300">
                                                                {{ $homeSetsWon }}
                                                            </div>
                                                        </div>
                                                        <div class="w-8 h-8 bg-green-900/80 rounded-lg flex items-center justify-center">
                                                            <div class="text-sm font-bold text-green-300">
                                                                {{ $awaySetsWon }}
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

                                        @if($match->scheduled_at)
                                        <div class="text-center text-xs md:text-sm text-gray-500 mt-1 md:mt-2">
                                            {{ $match->scheduled_at->format('d.m. H:i') }}
                                        </div>
                                        @endif
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 md:p-12 border border-gray-700/50 shadow-xl text-center">
                            <div class="text-4xl md:text-6xl mb-4">🎯</div>
                            <h3 class="text-lg md:text-xl font-semibold text-white mb-2">Nema mečeva još</h3>
                            <p class="text-gray-400 text-sm md:text-base">Mečevi će se pojaviti kada liga počne.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <script>
                    function showLeagueTab(tabName) {
                        // Hide all tab contents
                        document.querySelectorAll('.tab-content').forEach(content => {
                            content.classList.add('hidden');
                        });

                        // Remove active state from all tabs
                        document.querySelectorAll('.tab-button').forEach(button => {
                            button.classList.remove('border-blue-500', 'text-blue-400');
                            button.classList.add('border-transparent', 'text-gray-400');
                        });

                        // Show selected tab content
                        document.getElementById(tabName + '-content').classList.remove('hidden');

                        // Set active state for selected tab
                        document.getElementById(tabName + '-tab').classList.remove('border-transparent', 'text-gray-400');
                        document.getElementById(tabName + '-tab').classList.add('border-blue-500', 'text-blue-400');
                    }
                </script>
            @elseif($competition->type === 'tournament')
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    @include('public.leagues._tournament')
                </div>
            @endif

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
