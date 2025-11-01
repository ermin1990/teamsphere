@extends('layouts.public')

@section('title', $competition->name . ' - ' . $organization->name)

@section('content')
    <!-- Header -->
    <div class="backdrop-blur-xl rounded-2xl p-4 md:p-8 shadow-xl mb-8 border" style="background: var(--bg-card); border-color: var(--border-primary); box-shadow: 0 10px 25px var(--shadow-primary);">
        <!-- Mobile Layout -->
        <div class="block md:hidden">
            <div class="text-center">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
                    {{ $competition->name }}
                </h1>
                <p class="text-sm mb-4" style="color: var(--text-tertiary);">{{ $organization->name }} • {{ $competition->sport->name }}</p>
                <div class="flex items-center justify-center gap-2">
                    <span class="px-3 py-1 text-xs rounded-full font-medium"
                         style="background: var(--accent-blue); color: var(--accent-blue-solid);">
                        @if($competition->status === 'completed')
                            Završeno
                        @elseif($competition->status === 'in_progress')
                            U tijeku
                        @else
                            Planirano
                        @endif
                    </span>
                    <span class="px-3 py-1 text-xs rounded-full font-medium"
                         style="background: var(--accent-purple); color: var(--accent-purple-solid);">
                        @if($competition->type === 'tournament')
                            Turnir
                        @else
                            Liga
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Desktop Layout -->
        <div class="hidden md:flex md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
                    {{ $competition->name }}
                </h1>
                <p class="text-base" style="color: var(--text-tertiary);">{{ $organization->name }} • {{ $competition->sport->name }}</p>
            </div>
            <div class="flex items-center gap-4">
                @if($competition->type === 'tournament')
                <a href="{{ route('public.leagues.tournament.pdf', $competition->slug) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors"
                   style="color: var(--accent-blue); background: var(--bg-tertiary); border: 1px solid var(--border-primary);">
                    📄 PDF Export
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
                @endif
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 text-sm rounded-full font-medium"
                         style="background: var(--accent-blue); color: var(--accent-blue-solid);">
                        @if($competition->status === 'completed')
                            Završeno
                        @elseif($competition->status === 'in_progress')
                            U tijeku
                        @else
                            Planirano
                        @endif
                    </span>
                    <span class="px-3 py-1 text-sm rounded-full font-medium"
                         style="background: var(--accent-purple); color: var(--accent-purple-solid);">
                        @if($competition->type === 'tournament')
                            Turnir
                        @else
                            Liga
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Competition Content -->
    @if($competition->type === 'league')
        <!-- League Tabs -->
        <div class="mb-6 md:mb-8">
            <div class="border-b" style="border-color: var(--border-primary);">
                <nav class="-mb-px flex space-x-6 md:space-x-8">
                    <button onclick="showLeagueTab('standings')" id="standings-tab"
                            class="tab-button border-b-2 py-2 px-1 text-sm md:text-base font-medium transition-colors" style="border-color: var(--accent-blue); color: var(--accent-blue);">
                        🏆 Tabela
                    </button>
                    <button onclick="showLeagueTab('matches')" id="matches-tab"
                            class="tab-button border-b-2 py-2 px-1 text-sm md:text-base font-medium transition-colors border-transparent hover:text-blue-400" style="color: var(--text-tertiary);">
                        🎯 Mečevi
                    </button>
                </nav>
            </div>

            <!-- Standings Tab Content -->
            <div id="standings-content" class="tab-content mt-4 md:mt-6">
                @if($competition->standings && $competition->standings->count() > 0)
                <div class="backdrop-blur-xl rounded-xl p-3 md:p-5 shadow-xl border" style="background: var(--bg-card); border-color: var(--border-primary); box-shadow: 0 10px 25px var(--shadow-primary);">
                    <div class="px-4 py-3" style="background: var(--bg-tertiary);">
                        <table class="w-full text-xs">
                            <thead>
                                <tr style="color: var(--text-tertiary); border-bottom: 1px solid var(--border-secondary);">
                                    <th class="text-left py-1 pr-2 font-medium">#</th>
                                    <th class="text-left py-1 font-medium">Igrač</th>
                                    <th class="text-center py-1 px-1 font-medium">M</th>
                                    <th class="text-center py-1 px-1 font-medium">P</th>
                                    <th class="text-center py-1 px-1 font-medium">I</th>
                                    <th class="text-center py-1 px-1 font-medium">S</th>
                                    <th class="text-center py-1 px-1 font-medium" style="color: var(--accent-green-solid);">Bod</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $advancingPlayers = $competition->players_advancing_per_group ?? 2;
                                @endphp
                                @foreach($competition->standings as $index => $standing)
                                <tr style="border-bottom: 1px solid var(--border-secondary); transition: background-color 0.2s;" class="hover:bg-[var(--bg-secondary)] {{ $index < $advancingPlayers ? 'bg-green-900/20' : '' }}">
                                    <td class="py-2 pr-2 font-mono" style="color: var(--text-tertiary);">{{ $standing->position }}</td>
                                    <td class="py-2 font-medium" style="color: var(--text-primary);">
                                        {{ $standing->participant->name }}
                                        @if($standing->participant->position)
                                        <span class="text-xs" style="color: var(--text-tertiary);">({{ $standing->participant->position }})</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-1 text-center" style="color: var(--text-secondary);">{{ ($standing->won ?? 0) + ($standing->drawn ?? 0) + ($standing->lost ?? 0) }}</td>
                                    <td class="py-2 px-1 text-center" style="color: var(--accent-green-solid);">{{ $standing->won ?? 0 }}</td>
                                    <td class="py-2 px-1 text-center" style="color: var(--accent-red);">{{ $standing->lost ?? 0 }}</td>
                                    <td class="py-2 px-1 text-center" style="color: var(--text-secondary);">{{ ($standing->sets_won ?? 0) }}-{{ ($standing->sets_lost ?? 0) }}</td>
                                    <td class="py-2 px-1 text-center font-bold" style="color: var(--accent-green-solid);">{{ $standing->points ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="backdrop-blur-xl rounded-2xl p-8 md:p-12 shadow-xl border text-center" style="background: var(--bg-card); border-color: var(--border-primary); box-shadow: 0 10px 25px var(--shadow-primary);">
                    <div class="text-4xl md:text-6xl mb-4">🏆</div>
                    <h3 class="text-lg md:text-xl font-semibold mb-2" style="color: var(--text-primary);">Tabela još nije dostupna</h3>
                    <p class="text-sm md:text-base" style="color: var(--text-tertiary);">Tabela će se pojaviti kada liga počne.</p>
                </div>
                @endif
            </div>

            <!-- Matches Tab Content -->
            <div id="matches-content" class="tab-content mt-4 md:mt-6 hidden">
                @php
                    $matches = $competition->type === 'league' ? $competition->leagueMatches : $competition->matches;
                    $matchesByRound = $matches->sortBy('round')->groupBy('round');
                @endphp
                @if($matchesByRound->count() > 0)
                <div class="space-y-3 md:space-y-5">
                    @foreach($matchesByRound as $round => $roundMatches)
                    <div>
                        <h4 class="text-xs md:text-base font-semibold text-center mb-3 md:mb-4 uppercase tracking-wider" style="color: var(--text-tertiary);">
                            Kolo {{ $round }}
                        </h4>
                        <div class="space-y-1 md:space-y-3">
                            @foreach($roundMatches->sortByDesc('scheduled_at') as $match)
                            <div class="block hover:scale-[1.01] rounded-md p-3 transition-all duration-200" style="background: var(--bg-tertiary);">
                                <div class="grid grid-cols-[3fr_120px] gap-0 items-center p-3">
                                    <!-- Players Column -->
                                    <div class="space-y-4">
                                                <!-- Home Player -->
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                                        <!-- Sets won indicator -->
                                                        <div class="w-8 h-8 rounded flex items-center justify-center text-xs font-bold flex-shrink-0" style="background: var(--bg-secondary); color: var(--text-primary);">
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
                                                                <span style="color: var(--accent-green-solid);">{{ $homeSetsWon }}</span>
                                                            @else
                                                                <span style="color: var(--text-muted);">0</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-xs md:text-sm font-semibold truncate" style="color: var(--text-primary);">
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
                                                        <div class="w-6 text-center {{ $i < 5 ? 'border-r' : '' }}" style="border-color: var(--border-secondary);">
                                                            @if(isset($displaySets[$i-1]))
                                                                <span class="text-xs px-1 py-0.5 rounded {{ $displaySets[$i-1]['home_score'] > $displaySets[$i-1]['away_score'] ? 'font-bold' : '' }}" style="background: {{ $displaySets[$i-1]['home_score'] > $displaySets[$i-1]['away_score'] ? 'var(--accent-green)' : 'transparent' }}; color: {{ $displaySets[$i-1]['home_score'] > $displaySets[$i-1]['away_score'] ? 'var(--accent-green-solid)' : 'var(--text-tertiary)' }};">
                                                                    {{ $displaySets[$i-1]['home_score'] ?? 0 }}
                                                                </span>
                                                            @else
                                                                <span class="text-xs px-1 py-0.5 rounded" style="color: var(--text-muted);">-</span>
                                                            @endif
                                                        </div>
                                                        @endfor
                                                    </div>
                                                </div>

                                                <!-- Away Player -->
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                                        <!-- Sets won indicator -->
                                                        <div class="w-8 h-8 rounded flex items-center justify-center text-xs font-bold flex-shrink-0" style="background: var(--bg-secondary); color: var(--text-primary);">
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
                                                                <span style="color: var(--accent-green-solid);">{{ $awaySetsWon }}</span>
                                                            @else
                                                                <span style="color: var(--text-muted);">0</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-xs md:text-sm font-semibold truncate" style="color: var(--text-primary);">
                                                            @if($competition->is_team_based)
                                                                {{ $match->awayTeam?->name ?? 'Away Team' }}
                                                            @else
                                                                {{ $match->awayPlayer?->name ?? 'Away Player' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-1 ml-4">
                                                        @for($i = 1; $i <= 5; $i++)
                                                        <div class="w-6 text-center {{ $i < 5 ? 'border-r' : '' }}" style="border-color: var(--border-secondary);">
                                                            @if(isset($displaySets[$i-1]))
                                                                <span class="text-xs px-1 py-0.5 rounded {{ $displaySets[$i-1]['away_score'] > $displaySets[$i-1]['home_score'] ? 'font-bold' : '' }}" style="background: {{ $displaySets[$i-1]['away_score'] > $displaySets[$i-1]['home_score'] ? 'var(--accent-green)' : 'transparent' }}; color: {{ $displaySets[$i-1]['away_score'] > $displaySets[$i-1]['home_score'] ? 'var(--accent-green-solid)' : 'var(--text-tertiary)' }};">
                                                                    {{ $displaySets[$i-1]['away_score'] ?? 0 }}
                                                                </span>
                                                            @else
                                                                <span class="text-xs px-1 py-0.5 rounded" style="color: var(--text-muted);">-</span>
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
                                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent-green);">
                                                            <div class="text-sm font-bold" style="color: var(--accent-green-solid);">
                                                                {{ $match->home_score ?? 0 }}
                                                            </div>
                                                        </div>
                                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent-green);">
                                                            <div class="text-sm font-bold" style="color: var(--accent-green-solid);">
                                                                {{ $match->away_score ?? 0 }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($match->status === 'completed')
                                                    <div class="flex flex-col items-center space-y-1">
                                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent-green);">
                                                            <div class="text-sm font-bold" style="color: var(--accent-green-solid);">
                                                                {{ $homeSetsWon }}
                                                            </div>
                                                        </div>
                                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent-green);">
                                                            <div class="text-sm font-bold" style="color: var(--accent-green-solid);">
                                                                {{ $awaySetsWon }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="flex flex-col items-center space-y-1">
                                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--bg-tertiary);">
                                                            <div class="text-sm font-bold" style="color: var(--text-muted);">-</div>
                                                        </div>
                                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--bg-tertiary);">
                                                            <div class="text-sm font-bold" style="color: var(--text-muted);">-</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        @if($match->scheduled_at)
                                        <div class="text-center text-xs md:text-sm mt-1 md:mt-2" style="color: var(--text-muted);">
                                            {{ $match->scheduled_at->format('d.m. H:i') }}
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="backdrop-blur-xl rounded-2xl p-8 md:p-12 shadow-xl border text-center" style="background: var(--bg-card); border-color: var(--border-primary); box-shadow: 0 10px 25px var(--shadow-primary);">
                            <div class="text-4xl md:text-6xl mb-4">🎯</div>
                            <h3 class="text-lg md:text-xl font-semibold mb-2" style="color: var(--text-primary);">Nema mečeva još</h3>
                            <p class="text-sm md:text-base" style="color: var(--text-tertiary);">Mečevi će se pojaviti kada liga počne.</p>
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
                            button.classList.add('border-transparent');
                            button.style.color = 'var(--text-tertiary)';
                        });

                        // Show selected tab content
                        document.getElementById(tabName + '-content').classList.remove('hidden');

                        // Set active state for selected tab
                        document.getElementById(tabName + '-tab').style.borderColor = 'var(--accent-blue)';
                        document.getElementById(tabName + '-tab').style.color = 'var(--accent-blue)';
                    }
                </script>
            @elseif($competition->type === 'tournament')
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    @include('public.leagues._tournament')
                </div>
            @endif

            <!-- Footer -->
            <div class="text-center mt-8 text-sm" style="color: var(--text-tertiary);">
                <p>Powered by TeamSphere • {{ $organization->name }}</p>
            </div>
@endsection

@push('scripts')
<script>
    function updateCompetitionMatches() {
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
                // Silent error handling
            });
    }

    function updateLiveMatchesSection(matchesData) {
        matchesData.forEach(match => {
            // Find match by ID in rounds section
            const roundMatchElements = document.querySelectorAll(`a[href*="/matches/${match.id}"]`);
            roundMatchElements.forEach(matchElement => {

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

                    // Find the players column div
                    const playersColumn = matchElement.querySelector('.space-y-4');
                    if (!playersColumn) {
                        return;
                    }

                    // Find all set score containers (both home and away rows)
                    const setContainers = playersColumn.querySelectorAll('.flex.gap-1.ml-4');

                    if (setContainers.length >= 2) {
                        // Home player sets (first container)
                        const homeSetDivs = setContainers[0].querySelectorAll('.w-6.text-center');
                        
                        homeSetDivs.forEach((div, index) => {
                            const span = div.querySelector('span');
                            if (!span) return;
                            
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

                        // Away player sets (second container)
                        const awaySetDivs = setContainers[1].querySelectorAll('.w-6.text-center');
                        
                        awaySetDivs.forEach((div, index) => {
                            const span = div.querySelector('span');
                            if (!span) return;
                            
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

                // Update current set scores in green boxes
                const currentScoreContainer = matchElement.querySelector('.flex.flex-col.items-start.justify-center.space-y-1.pl-4');
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
@endpush
