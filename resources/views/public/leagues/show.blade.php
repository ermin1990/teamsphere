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
                <p class="text-sm mb-4" style="color: var(--text-tertiary);">
                    <a href="{{ route('public.leagues.organization', $organization) }}" class="hover:text-blue-400 transition-colors">
                        {{ $organization->name }}
                    </a>
                    • {{ $competition->sport->name }}
                </p>
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
                <p class="text-base" style="color: var(--text-tertiary);">
                    <a href="{{ route('public.leagues.organization', $organization) }}" class="hover:text-blue-400 transition-colors">
                        {{ $organization->name }}
                    </a>
                    • {{ $competition->sport->name }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                @if($competition->type === 'tournament')
                <a href="{{ route('public.leagues.tournament.pdf', $competition->slug) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors"
                   style="color: var(--accent-blue); background: var(--bg-tertiary); border: 1px solid var(--border-primary); display: none;">
                    📄 PDF Export
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
                <a href="{{ route('projector.display', ['ids' => $competition->id, 'resolution' => '1024x768', 'layout' => 'single']) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors hover:opacity-80"
                   style="color: white; background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%); border: 1px solid rgba(147, 51, 234, 0.3);">
                    📽️ Projektor (1024x768)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
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
                            Draft
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
            <div class="flex items-center justify-center md:justify-start border-b border-white/5">
                <nav class="-mb-px flex space-x-4 md:space-x-8">
                    <button onclick="showLeagueTab('standings')" id="standings-tab"
                            class="tab-button relative py-4 px-2 text-sm md:text-base font-bold transition-all duration-200 group" 
                            style="color: var(--accent-blue);">
                        <span class="flex items-center gap-2">
                            <span class="text-lg">🏆</span>
                            Tabela
                        </span>
                        <div class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                    </button>
                    <button onclick="showLeagueTab('matches')" id="matches-tab"
                            class="tab-button relative py-4 px-2 text-sm md:text-base font-bold transition-all duration-200 group text-gray-500 hover:text-gray-300">
                        <span class="flex items-center gap-2">
                            <span class="text-lg">🎯</span>
                            Mečevi
                        </span>
                        <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-500 transition-all duration-200 group-hover:w-full opacity-0 group-hover:opacity-50"></div>
                    </button>
                </nav>
            </div>

            <!-- Standings Tab Content -->
            <div id="standings-content" class="tab-content mt-4 md:mt-6">
                @if($competition->standings && $competition->standings->count() > 0)
                <div class="backdrop-blur-xl rounded-xl p-3 md:p-5 shadow-xl border" style="background: var(--bg-card); border-color: var(--border-primary); box-shadow: 0 10px 25px var(--shadow-primary);">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs md:text-sm">
                            <thead>
                                <tr class="text-gray-400 border-b border-gray-700/50">
                                    <th class="text-left py-2 pr-2 font-medium">#</th>
                                    <th class="text-left py-2 font-medium">{{ $competition->is_team_based ? 'Ekipa' : 'Igrač' }}</th>
                                    <th class="text-center py-2 px-1 font-medium" title="Odigrano mečeva">OM</th>
                                    <th class="text-center py-2 px-1 font-medium" title="{{ $competition->is_team_based ? 'Meč razlika' : 'Pobjede/Porazi' }}">{{ $competition->is_team_based ? 'MR' : 'P/I' }}</th>
                                    <th class="text-center py-2 px-1 font-medium" title="{{ $competition->is_team_based ? 'Partije' : 'Setovi' }}">{{ $competition->is_team_based ? 'PR' : 'SET' }}</th>
                                    <th class="text-center py-2 px-1 font-medium text-green-400">BOD</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $advancingPlayers = $competition->players_advancing_per_group ?? 2;
                                @endphp
                                @foreach($competition->standings as $index => $standing)
                                <tr class="border-b border-gray-700/30 hover:bg-gray-700/30 transition-colors {{ $index < $advancingPlayers ? 'bg-green-900/10' : '' }}">
                                    <td class="py-3 pr-2 text-gray-400 font-mono">{{ $standing->position }}</td>
                                    <td class="py-3 text-white font-medium">
                                        @if($competition->is_team_based && $standing->team_id)
                                            <a href="{{ route('public.teams.show', $standing->team_id) }}" class="hover:text-blue-400 transition-colors">
                                                {{ $standing->participant->name ?? 'Nepoznato' }}
                                            </a>
                                        @else
                                            {{ $standing->participant->name ?? 'Nepoznato' }}
                                        @endif
                                    </td>
                                    <td class="py-3 px-1 text-center text-gray-300">{{ ($standing->won ?? 0) + ($standing->drawn ?? 0) + ($standing->lost ?? 0) }}</td>
                                    <td class="py-3 px-1 text-center text-gray-300 font-medium">{{ $standing->won ?? 0 }}:{{ $standing->lost ?? 0 }}</td>
                                    <td class="py-3 px-1 text-center text-gray-300">{{ ($standing->sets_won ?? 0) }}:{{ ($standing->sets_lost ?? 0) }}</td>
                                    <td class="py-3 px-1 text-center text-green-400 font-bold text-base">{{ $standing->points ?? 0 }}</td>
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
                    if ($competition->type === 'league') {
                        $matches = $competition->is_team_based ? $competition->teamMatches : $competition->leagueMatches;
                    } else {
                        $matches = $competition->matches;
                    }
                    $matchesByRound = $matches->sortBy('round')->groupBy('round');
                @endphp
                @if($matchesByRound->count() > 0)
                <div class="space-y-6 md:space-y-10">
                    @foreach($matchesByRound as $round => $roundMatches)
                    <div class="relative">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="h-px flex-1 bg-gradient-to-r from-transparent to-gray-700/50"></div>
                            <h4 class="text-[10px] md:text-xs font-black uppercase tracking-[0.2em] text-gray-500 whitespace-nowrap">
                                Kolo {{ $round }}
                            </h4>
                            <div class="h-px flex-1 bg-gradient-to-l from-transparent to-gray-700/50"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 md:gap-3">
                            @foreach($roundMatches->sortBy('scheduled_at') as $match)
                            @php
                                $isTeamMatch = $competition->is_team_based && $competition->type === 'league';
                                $route = $isTeamMatch 
                                    ? route('public.team-matches.show', [$competition->slug, $match->id])
                                    : route('public.matches.show', [$competition->slug, $match->id]);
                            @endphp
                            <a href="{{ $route }}" 
                               class="group relative flex items-center justify-between bg-[#1a1a1a] hover:bg-[#222] border border-gray-800/50 hover:border-blue-500/30 rounded-lg p-2 md:p-3 transition-all duration-200">
                                
                                <!-- Home Team -->
                                <div class="flex-1 flex items-center gap-2 min-w-0">
                                    <div class="w-1 h-6 rounded-full {{ ($match->home_score > $match->away_score) ? 'bg-blue-500' : 'bg-transparent' }}"></div>
                                    <span class="text-xs md:text-sm font-medium truncate {{ ($match->home_score > $match->away_score) ? 'text-white' : 'text-gray-400' }}">
                                        @if($isTeamMatch)
                                            {{ $match->homeTeam?->name ?? 'Home Team' }}
                                        @elseif($match->position_code === 'Dubl')
                                            Dubl
                                        @elseif($match->homePlayer)
                                            {{ $match->homePlayer->name }}
                                        @else
                                            {{ $match->homeTeam?->name ?? 'Home Team' }}
                                        @endif
                                    </span>
                                </div>

                                <!-- Score -->
                                <div class="flex flex-col items-center px-3 shrink-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm md:text-base font-bold {{ ($match->home_score > $match->away_score) ? 'text-blue-400' : 'text-gray-300' }}">
                                            {{ $match->home_score ?? 0 }}
                                        </span>
                                        <span class="text-[10px] text-gray-600 font-bold">:</span>
                                        <span class="text-sm md:text-base font-bold {{ ($match->away_score > $match->home_score) ? 'text-blue-400' : 'text-gray-300' }}">
                                            {{ $match->away_score ?? 0 }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Away Team -->
                                <div class="flex-1 flex items-center justify-end gap-2 min-w-0 text-right">
                                    <span class="text-xs md:text-sm font-medium truncate {{ ($match->away_score > $match->home_score) ? 'text-white' : 'text-gray-400' }}">
                                        @if($isTeamMatch)
                                            {{ $match->awayTeam?->name ?? 'Away Team' }}
                                        @elseif($match->position_code === 'Dubl')
                                            Dubl
                                        @elseif($match->awayPlayer)
                                            {{ $match->awayPlayer->name }}
                                        @else
                                            {{ $match->awayTeam?->name ?? 'Away Team' }}
                                        @endif
                                    </span>
                                    <div class="w-1 h-6 rounded-full {{ ($match->away_score > $match->home_score) ? 'bg-blue-500' : 'bg-transparent' }}"></div>
                                </div>

                                <!-- Hover Arrow -->
                                <div class="absolute -right-1 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity pr-2">
                                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
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
            @elseif($competition->type === 'tournament')
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    @include('public.leagues._tournament')
                </div>
            @endif

            <!-- Footer -->
            <div class="text-center mt-8 text-sm" style="color: var(--text-tertiary);">
                <p>Powered by MojTurnir • {{ $organization->name }}</p>
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

    function showLeagueTab(tab) {
        // Hide all content
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        // Show selected content
        document.getElementById(tab + '-content').classList.remove('hidden');
        
        // Reset all buttons
        document.querySelectorAll('.tab-button').forEach(el => {
            el.classList.remove('text-white');
            el.classList.add('text-gray-500');
            el.style.color = 'var(--text-tertiary)';
            const indicator = el.querySelector('div');
            if (indicator) {
                indicator.classList.remove('w-full', 'opacity-100');
                indicator.classList.add('w-0', 'opacity-0');
            }
        });
        
        // Highlight active button
        const activeTab = document.getElementById(tab + '-tab');
        activeTab.classList.remove('text-gray-500');
        activeTab.classList.add('text-white');
        activeTab.style.color = 'var(--accent-blue)';
        const activeIndicator = activeTab.querySelector('div');
        if (activeIndicator) {
            activeIndicator.classList.remove('w-0', 'opacity-0');
            activeIndicator.classList.add('w-full', 'opacity-100');
        }
    }

    let updateInterval;

    function startUpdates() {
        if (updateInterval) clearInterval(updateInterval);
        updateInterval = setInterval(updateCompetitionMatches, 5000);
    }

    function stopUpdates() {
        if (updateInterval) {
            clearInterval(updateInterval);
            updateInterval = null;
        }
    }

    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopUpdates();
        } else {
            startUpdates();
        }
    });

    // Initial update
    document.addEventListener('DOMContentLoaded', function() {
        updateCompetitionMatches();
        if (!document.hidden) {
            startUpdates();
        }
    });
</script>
@endpush
