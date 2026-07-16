    @if($competition->description || $competition->location || $competition->organizer_contact || $competition->entry_fee)
    <div class="backdrop-blur-xl rounded-2xl p-4 md:p-6 shadow-xl mb-8 border space-y-3" style="background: var(--bg-card); border-color: var(--border-primary);">
        @if($competition->description)
            <p style="color: var(--text-secondary);">{{ $competition->description }}</p>
        @endif
        @if($competition->location || $competition->organizer_contact || $competition->entry_fee)
            <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm" style="color: var(--text-tertiary);">
                @if($competition->location)
                    <span>📍 {{ $competition->location }}</span>
                @endif
                @if($competition->entry_fee)
                    <span>💳 {{ $competition->entry_fee }}</span>
                @endif
                @if($competition->organizer_contact)
                    <span>☎️ {{ $competition->organizer_contact }}</span>
                @endif
            </div>
        @endif
    </div>
    @endif

    <!-- Competition Content -->
    @if($competition->type === 'league')
        <!-- League content -->
        <div class="mt-surface rounded-2xl p-4 md:p-6 backdrop-blur-xl">
            @php
                $advancingPlayers = $competition->players_advancing_per_group ?? 0;
                $fmtDiff = fn ($n) => ($n > 0 ? '+' : '') . $n;
                $roundOf = fn ($m) => $m->round_number ?? $m->round;
                $leagueMatches = $competition->is_team_based ? $competition->teamMatches : $competition->leagueMatches;
                // TeamMatch has no venue relation/column - only LeagueMatch does.
                if (!$competition->is_team_based) {
                    $leagueMatches->loadMissing('venue');
                }
                $matchesByRound = $leagueMatches->sortBy($roundOf)->groupBy($roundOf);

                // S (set diff) and G (game diff) per player, computed from completed
                // individual matches (league standings don't store sets/games).
                $diffByPlayer = [];
                if (!$competition->is_team_based) {
                    foreach ($competition->leagueMatches as $lm) {
                        if ($lm->status !== 'completed') continue;
                        $hp = $lm->home_player_id; $ap = $lm->away_player_id;
                        if (!$hp || !$ap) continue;
                        $diffByPlayer[$hp] ??= ['s' => 0, 'g' => 0];
                        $diffByPlayer[$ap] ??= ['s' => 0, 'g' => 0];
                        $diffByPlayer[$hp]['s'] += ($lm->home_score ?? 0) - ($lm->away_score ?? 0);
                        $diffByPlayer[$ap]['s'] += ($lm->away_score ?? 0) - ($lm->home_score ?? 0);
                        $gh = 0; $ga = 0;
                        foreach (($lm->sets ?? []) as $set) {
                            $gh += (int) ($set['home'] ?? $set['home_score'] ?? $set['p1'] ?? 0);
                            $ga += (int) ($set['away'] ?? $set['away_score'] ?? $set['p2'] ?? 0);
                        }
                        $diffByPlayer[$hp]['g'] += $gh - $ga;
                        $diffByPlayer[$ap]['g'] += $ga - $gh;
                    }
                }
            @endphp

            @once
            <style>
                details.round > summary { list-style: none; }
                details.round > summary::-webkit-details-marker { display: none; }
                details.round[open] .round-chevron { transform: rotate(180deg); }
                .mt-surface { background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary); }
                .mt-eyebrow { color: var(--text-muted); letter-spacing: 0.2em; font-family: 'Unbounded', ui-sans-serif, sans-serif; }
                .mt-stand-row { transition: background .2s ease; }
                .mt-stand-row:hover { background: var(--bg-hover); }
                .mt-round-sum { background: var(--bg-hover); transition: background .2s ease; }
                .mt-round-sum:hover { background: var(--bg-tertiary); }
            </style>
            @endonce

            {{-- Standings --}}
            <div class="mb-7">
                <h5 class="text-[10px] font-bold uppercase mt-eyebrow mb-3.5 px-1">Tabela</h5>
                @if($competition->standings && $competition->standings->count() > 0)
                    <div class="grid grid-cols-[auto_1fr_auto_auto_auto_auto_auto] md:grid-cols-[auto_1fr_auto_auto_auto_auto_auto_auto] gap-x-1.5 pb-2.5 mb-1 text-[9px] font-bold uppercase tracking-tighter px-3" style="color: var(--text-muted); border-bottom: 1px solid var(--border-secondary);">
                        <div class="flex items-center w-5 justify-center">#</div>
                        <div>{{ $competition->is_team_based ? 'Ekipa' : 'Igrač' }}</div>
                        <div class="hidden md:block text-center w-6">M</div>
                        <div class="text-center w-5">P</div>
                        <div class="text-center w-5">I</div>
                        <div class="text-center w-7">S</div>
                        <div class="text-center w-7">G</div>
                        <div class="text-center w-8" style="color: var(--accent-blue);">Bod</div>
                    </div>
                    <div class="mt-1 space-y-0.5">
                        @foreach($competition->standings->sortByDesc('points')->values() as $index => $standing)
                            @php
                                $pid = $standing->player_id;
                                $d = ($pid && isset($diffByPlayer[$pid])) ? $diffByPlayer[$pid] : null;
                                $sd = $d['s'] ?? null; $gd = $d['g'] ?? null;
                                $played = ($standing->won ?? 0) + ($standing->drawn ?? 0) + ($standing->lost ?? 0);
                                $advancing = $advancingPlayers > 0 && $index < $advancingPlayers;
                            @endphp
                            <div class="mt-stand-row relative grid grid-cols-[auto_1fr_auto_auto_auto_auto_auto] md:grid-cols-[auto_1fr_auto_auto_auto_auto_auto_auto] gap-x-1.5 items-center py-2.5 px-3 rounded-xl" @if($advancing) style="background: rgba(52, 211, 153, 0.09);" @endif>
                                @if($advancing)<div class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-full" style="background: var(--accent-green-solid);"></div>@endif
                                <span class="w-5 text-center text-[11px] font-black" style="color: {{ $index < 3 ? 'var(--accent-blue)' : 'var(--text-muted)' }};">{{ $index + 1 }}</span>
                                <div class="flex flex-col min-w-0 overflow-hidden">
                                    <span class="font-bold text-[12.5px] truncate leading-tight" style="color: var(--text-primary);">{{ $standing->participant->name ?? 'Nepoznato' }}</span>
                                </div>
                                <div class="hidden md:flex w-6 justify-center font-bold text-[11px]" style="color: var(--text-secondary);">{{ $played }}</div>
                                <div class="w-5 text-center font-bold text-[11px]" style="color: var(--accent-green-solid);">{{ $standing->won ?? 0 }}</div>
                                <div class="w-5 text-center font-bold text-[11px]" style="color: var(--accent-red);">{{ $standing->lost ?? 0 }}</div>
                                <div class="w-7 text-center font-black text-[11px]" style="color: {{ is_null($sd) ? 'var(--text-muted)' : ($sd > 0 ? 'var(--accent-green-solid)' : ($sd < 0 ? 'var(--accent-red)' : 'var(--text-tertiary)')) }};">{{ is_null($sd) ? '–' : $fmtDiff($sd) }}</div>
                                <div class="w-7 text-center text-[10px] font-bold" style="color: {{ is_null($gd) ? 'var(--text-muted)' : ($gd > 0 ? 'var(--accent-green-solid)' : ($gd < 0 ? 'var(--accent-red)' : 'var(--text-tertiary)')) }};">{{ is_null($gd) ? '–' : $fmtDiff($gd) }}</div>
                                <div class="w-8 flex justify-center"><span class="px-1.5 py-0.5 rounded-md text-[11px] font-black" style="background: rgba(180, 192, 255, 0.15); color: var(--accent-blue);">{{ $standing->points ?? 0 }}</span></div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-sm" style="color: var(--text-muted);">Tabela će se pojaviti kada liga počne.</div>
                @endif
            </div>

            {{-- Schedule & results --}}
            <div class="pt-6" style="border-top: 1px solid var(--border-primary);">
                <h5 class="text-[10px] font-bold uppercase mt-eyebrow mb-3.5 px-1">Raspored i Rezultati</h5>
                @if($matchesByRound->count() > 0)
                    <div class="space-y-3">
                        @foreach($matchesByRound as $round => $roundMatches)
                            @php $roundFinished = $roundMatches->every(fn ($m) => in_array($m->status, ['completed', 'forfeited'])); @endphp
                            <details class="round" open>
                                <summary class="mt-round-sum flex items-center justify-between gap-2 px-3 py-2 rounded-xl cursor-pointer">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-[10px] font-black uppercase tracking-[0.14em]" style="color: var(--text-secondary);">Kolo {{ $round }}</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full" style="background: var(--bg-tertiary); color: var(--text-muted);">{{ $roundMatches->count() }}</span>
                                        @if($roundFinished)<span class="text-[8px] px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wider" style="background: rgba(52, 211, 153, 0.15); color: var(--accent-green-solid);">Završeno</span>@endif
                                    </div>
                                    <svg class="round-chevron w-3.5 h-3.5" style="transition: transform .2s ease; color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </summary>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-2.5 mt-2.5">
                                    @foreach($roundMatches->sortBy('scheduled_at') as $match)
                                        @include('public.leagues.partials.match-card', ['match' => $match, 'competition' => $competition])
                                    @endforeach
                                </div>
                            </details>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-sm" style="color: var(--text-muted);">Mečevi će se pojaviti kada liga počne.</div>
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

@push('scripts')
<script>
    function updateCompetitionMatches() {
        fetch('{{ route("api.live-matches") }}')
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
