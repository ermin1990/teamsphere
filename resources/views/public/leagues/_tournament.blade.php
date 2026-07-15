<!-- Tournament Groups with Tables and Matches -->
@if($competition->type === 'tournament')
    <style>
        .tab-button.active {
            border-color: var(--accent-blue) !important;
            color: var(--accent-blue) !important;
            font-weight: 600 !important;
        }

        /* Light theme table colors */
        [data-theme="light"] .table-header-text {
            color: #111827 !important; /* gray-900 */
        }

        [data-theme="light"] .table-number-text {
            color: #111827 !important; /* gray-900 */
        }

        [data-theme="light"] .table-player-name {
            color: #3f4041ff !important; /* gray-900 */
        }

        [data-theme="light"] .table-player-position {
            color: #4b5563 !important; /* gray-600 */
        }

        [data-theme="light"] .table-advancing-bg {
            background-color: #f0fdf4 !important; /* green-50 */
        }

        /* Dark theme table colors */
        [data-theme="dark"] .table-header-text {
            color: var(--text-tertiary) !important;
        }

        [data-theme="dark"] .table-number-text {
            color: #d1d5db !important; /* gray-300 */
        }

        [data-theme="dark"] .table-number-text-pints {
            color: #2be013ff !important; /* gray-300 */
        }

         [data-theme="dark"] .table-number-text-pints {
            color: #226818ff !important; /* gray-300 */
        }

        [data-theme="dark"] .table-player-name {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .table-player-position {
            color: var(--text-tertiary) !important;
        }

        [data-theme="dark"] .table-advancing-bg {
            background-color: rgba(6, 78, 59, 0.3) !important; /* green-900/30 */
        }

        [data-theme="dark"] .table-loss-text {
            color: #f87171 !important; /* red-400 */
        }

        [data-theme="dark"] .table-points-text {
            color: #4ade80 !important; /* green-400 */
        }

        /* PDF Print Styles */
        @media print {
            .tab-button, nav, .border-b, .mb-6 {
                display: none !important;
            }

            #pdf-content {
                display: block !important;
            }

            body {
                background: white !important;
                color: black !important;
            }

            .break-inside-avoid {
                page-break-inside: avoid;
            }

            h1, h2, h3, h4 {
                color: black !important;
            }

            table {
                border-collapse: collapse;
                width: 100%;
            }

            th, td {
                border: 1px solid #ccc;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f5f5f5 !important;
                -webkit-print-color-adjust: exact;
            }
        }

        /* Knockout bracket hover effects */
        .knockout-match {
            transition: all 0.2s ease;
        }

        /* Player container hover effects */
        .player-container {
            transition: all 0.2s ease;
            border-radius: 4px;
            padding: 2px 4px;
            margin: -2px -4px;
        }

        /* Dark theme highlight */
        .player-container.player-highlight {
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.6);
            background-color: rgba(59, 130, 246, 0.1) !important;
        }

        /* Light theme highlight */
        [data-theme="light"] .player-container.player-highlight {
            box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.7);
            background-color: rgba(217, 119, 6, 0.15) !important;
        }
    </style>
    @php
        // Use already loaded matches from controller instead of re-querying
        $allMatches = $competition->matches ?? collect();

        // Group matches by phase more intelligently
        // Knockout: Ascending rounds (1, 2, 3...) to display left-to-right
        $knockoutMatches = $allMatches->where('phase', 'knockout')
            ->sortBy('match_order')
            ->groupBy(function($match) {
                return (int)$match->round_number;
            })
            ->sortKeys(); // Sort round keys in ascending order (1, 2, 3...)

        $maxKnockoutRound = $knockoutMatches->keys()->last();

        $groupMatches = $allMatches->whereNotNull('tournament_group_id')
            ->sortBy('match_order')
            ->groupBy('tournament_group_id');

        // Determine active phase - show both phases if they exist
        $hasActiveGroupMatches = $groupMatches->flatten()->where('status', '!=', 'completed')->count() > 0;
        $hasActiveKnockoutMatches = $knockoutMatches->flatten()->where('status', '!=', 'completed')->count() > 0;
        $hasKnockoutMatches = $knockoutMatches->count() > 0;
        $hasGroupMatches = $groupMatches->count() > 0;

        // Always show both phases if they exist - no need to determine "active" phase
        $showGroupsTab = $hasGroupMatches;
        $showKnockoutTab = $hasKnockoutMatches;
        $showPdfTab = $hasGroupMatches || $hasKnockoutMatches;
    @endphp

    <!-- Tournament Tabs -->
    <div class="mb-6 md:mb-8">
        <div class="border-b" style="border-color: var(--border-primary);">
            <nav class="-mb-px flex space-x-6 md:space-x-8">
                @if($showGroupsTab)
                <button onclick="showTournamentTab('groups')" id="groups-tab"
                        class="tab-button border-b-2 py-2 px-1 text-sm md:text-base font-medium transition-colors" style="border-color: transparent; color: var(--text-tertiary);">
                    🏆 Grupna faza
                </button>
                @endif
                @if($showKnockoutTab)
                <button onclick="showTournamentTab('knockout')" id="knockout-tab"
                        class="tab-button border-b-2 py-2 px-1 text-sm md:text-base font-medium transition-colors" style="border-color: transparent; color: var(--text-tertiary);">
                    🏅 Eliminaciona faza
                </button>
                @endif
            </nav>
        </div>

        <!-- Groups Tab Content -->
        <div id="groups-content" class="tab-content mt-4 md:mt-6 {{ !$showGroupsTab ? 'hidden' : '' }}">
            @if($hasGroupMatches)
            @php
                // Jedna grupa zauzima cijelu sirinu umjesto da bude stisnuta u
                // pola kolone kao da postoji jos jedna pored nje.
                $groupCount = $competition->tournamentGroups->count();
            @endphp
            <div class="grid grid-cols-1 {{ $groupCount > 1 ? 'md:grid-cols-2' : '' }} gap-4 md:gap-6">
                @foreach($competition->tournamentGroups as $group)
                @php
                    $currentGroupMatches = $groupMatches->get($group->id) ?? collect();
                    $groupRoundOf = fn($m) => $m->round_number ?? $m->round;
                    $groupMatchesByRound = $currentGroupMatches->sortBy($groupRoundOf)->groupBy($groupRoundOf);
                @endphp
                @if($currentGroupMatches->count() > 0)
                                            <div class="[var(--bg-card)] backdrop-blur-xl rounded-xl p-4 md:p-6 border border-[var(--border-primary)] shadow-xl">
                    <h4 class="text-base md:text-lg font-bold text-[var(--text-primary)] mb-3 md:mb-4">{{ $group->name }}</h4>

                    <!-- Tabela / Mečevi sub-tabs -->
                    <div class="flex gap-4 mb-4 border-b" style="border-color: var(--border-primary);">
                        <button type="button" onclick="showGroupSubTab({{ $group->id }}, 'standings')" id="group-{{ $group->id }}-standings-tab"
                                class="pb-2 text-sm font-semibold border-b-2 transition-colors" style="color: var(--accent-blue); border-color: var(--accent-blue);">
                            Tabela
                        </button>
                        <button type="button" onclick="showGroupSubTab({{ $group->id }}, 'matches')" id="group-{{ $group->id }}-matches-tab"
                                class="pb-2 text-sm font-semibold border-b-2 transition-colors" style="color: var(--text-tertiary); border-color: transparent;">
                            Mečevi
                        </button>
                    </div>

                    <!-- Group Standings -->
                    @php
                        $groupStandings = $group->standings()->with('player')
                            ->orderByRaw('CASE WHEN manual_order IS NULL THEN 1 ELSE 0 END ASC, manual_order ASC')
                            ->orderBy('points', 'desc')
                            ->orderByRaw('(sets_won - sets_lost) desc')
                            ->orderByRaw('(points_won - points_lost) desc')
                            ->orderByDesc('points_won')
                            ->orderByDesc('sets_won')
                            ->orderByDesc('won')
                            ->orderBy('id')
                            ->get();
                    @endphp
                    <div id="group-{{ $group->id }}-standings-content" class="group-subtab-content">
                    @if($groupStandings->count() > 0)
                    <div class="bg-[#0d1527] rounded-xl p-3 border border-slate-800">
                        <div class="grid grid-cols-[auto_1fr_auto_auto_auto_auto_auto] md:grid-cols-[auto_1fr_auto_auto_auto_auto_auto_auto] gap-x-1.5 mb-1.5 text-[9px] text-slate-500 font-bold uppercase tracking-tighter px-2.5">
                            <div class="flex items-center w-5 justify-center">#</div>
                            <div>Igrač</div>
                            <div class="hidden md:block text-center w-6">M</div>
                            <div class="text-center w-5">P</div>
                            <div class="text-center w-5">I</div>
                            <div class="text-center w-7">S</div>
                            <div class="text-center w-7">G</div>
                            <div class="text-center w-8 text-blue-400">Bod</div>
                        </div>
                        <div class="space-y-1.5">
                            @php
                                $advancingPlayers = $competition->players_advancing_per_group ?? 2;
                            @endphp
                            @foreach($groupStandings as $index => $standing)
                                @php
                                    $played = ($standing->won ?? 0) + ($standing->lost ?? 0);
                                    $setDiff = ($standing->sets_won ?? 0) - ($standing->sets_lost ?? 0);
                                    $gemDiff = ($standing->points_won ?? 0) - ($standing->points_lost ?? 0);
                                    $advancing = $index < $advancingPlayers;
                                @endphp
                                <div class="grid grid-cols-[auto_1fr_auto_auto_auto_auto_auto] md:grid-cols-[auto_1fr_auto_auto_auto_auto_auto_auto] gap-x-1.5 items-center py-2 px-2.5 rounded-lg transition-all duration-300 border {{ $advancing ? 'bg-emerald-500/5 border-emerald-500/30' : 'bg-slate-900/40 border-slate-800/80 hover:bg-slate-800/60' }}">
                                    <span class="w-5 text-center text-[10px] font-black text-slate-500">{{ $index + 1 }}</span>
                                    <div class="flex flex-col min-w-0 overflow-hidden">
                                        <span class="text-white font-bold text-[12px] truncate leading-tight">{{ $standing->player->name }}@if($standing->player->position) <span class="text-slate-500 text-[10px]">({{ $standing->player->position }})</span>@endif</span>
                                    </div>
                                    <div class="hidden md:flex w-6 justify-center text-slate-300 font-bold text-[11px]">{{ $played }}</div>
                                    <div class="w-5 text-center text-emerald-300 font-bold text-[11px]">{{ $standing->won ?? 0 }}</div>
                                    <div class="w-5 text-center text-rose-300 font-bold text-[11px]">{{ $standing->lost ?? 0 }}</div>
                                    <div class="w-7 text-center font-black text-[11px] {{ $setDiff > 0 ? 'text-emerald-400' : ($setDiff < 0 ? 'text-rose-400' : 'text-slate-400') }}">{{ $setDiff > 0 ? '+' : '' }}{{ $setDiff }}</div>
                                    <div class="w-7 text-center text-[10px] font-bold {{ $gemDiff > 0 ? 'text-emerald-500/80' : ($gemDiff < 0 ? 'text-rose-500/80' : 'text-slate-400') }}">{{ $gemDiff > 0 ? '+' : '' }}{{ $gemDiff }}</div>
                                    <div class="w-8 flex justify-center"><span class="bg-blue-500/15 text-blue-300 px-1.5 py-0.5 rounded-md text-[11px] font-black ring-1 ring-inset ring-blue-500/20">{{ $standing->points ?? 0 }}</span></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    </div>

                    <!-- Group Matches -->
                    @if($currentGroupMatches->count() > 0)
                    <div id="group-{{ $group->id }}-matches-content" class="group-subtab-content hidden">
                        @foreach($groupMatchesByRound as $round => $roundMatches)
                        <div class="flex items-center gap-3 mb-3 mt-4 first:mt-0">
                            <div class="h-px flex-1" style="background: var(--border-primary);"></div>
                            <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--text-tertiary);">Kolo {{ $round }}</span>
                            <div class="h-px flex-1" style="background: var(--border-primary);"></div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
                            @foreach($roundMatches as $match)
                            @php
                                $homeSetsWon = 0; $awaySetsWon = 0;
                                $displaySets = collect($match->sets ?? [])->map(fn ($s) => [
                                    'h' => $s['home_score'] ?? $s['home'] ?? null,
                                    'a' => $s['away_score'] ?? $s['away'] ?? null,
                                ])->filter(fn ($s) => !is_null($s['h']) || !is_null($s['a']));
                                foreach ($displaySets as $s) {
                                    if ((int) $s['h'] > (int) $s['a']) $homeSetsWon++;
                                    if ((int) $s['a'] > (int) $s['h']) $awaySetsWon++;
                                }
                                if ($displaySets->isEmpty() && $match->status === 'completed') {
                                    $homeSetsWon = $match->home_score ?? 0;
                                    $awaySetsWon = $match->away_score ?? 0;
                                }
                                $displaySets = $displaySets->values();
                                // Cap placeholders at the competition's actual max sets
                                // (best-of-(2*sets_to_win - 1)) instead of a hardcoded 5.
                                $maxPossibleSets = max(1, (2 * ($competition->sets_to_win ?: 1)) - 1);
                                $cellCount = max($maxPossibleSets, $displaySets->count());
                                $completed = $match->status === 'completed';
                                $live = $match->status === 'in_progress';
                                $homeWin = $completed && $homeSetsWon > $awaySetsWon;
                                $awayWin = $completed && $awaySetsWon > $homeSetsWon;
                                $homeName = $match->homePlayer->name ?? 'TBD';
                                $awayName = $match->awayPlayer->name ?? 'TBD';
                                $homeSeed = $playerPositionSeeding[$match->home_player_id] ?? null;
                                $awaySeed = $playerPositionSeeding[$match->away_player_id] ?? null;
                            @endphp
                            <div class="relative overflow-hidden bg-[#10192d] border border-slate-800 rounded-lg">
                                @if($completed)
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500/60"></div>
                                @elseif($live)
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500/70 animate-pulse"></div>
                                @endif
                                <div class="p-2.5 md:p-3">
                                    <div class="flex items-stretch gap-3">
                                        <div class="flex-1 flex flex-col justify-between gap-2 min-w-0">
                                            <div class="flex items-center justify-between gap-2 min-w-0">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full {{ $homeWin ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-transparent' }}"></div>
                                                    <div class="text-[12px] md:text-[13px] font-bold truncate {{ $homeWin ? 'text-white' : ($awayWin ? 'text-slate-500' : 'text-slate-300') }}">{{ $homeName }}@if($homeSeed) <span class="text-slate-500 text-[10px]">({{ $homeSeed }})</span>@endif</div>
                                                </div>
                                                <div class="flex items-center gap-1 ml-auto">
                                                    @for($i = 0; $i < $cellCount; $i++)
                                                        @php $c = $displaySets[$i] ?? null; @endphp
                                                        <div class="w-[18px] text-center">
                                                            @if($c && !is_null($c['h']))
                                                                <span class="text-[9px] font-black {{ (int) $c['h'] >= (int) $c['a'] ? 'text-emerald-400' : 'text-slate-600' }}">{{ $c['h'] }}</span>
                                                            @else
                                                                <span class="text-[9px] text-slate-700">-</span>
                                                            @endif
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between gap-2 min-w-0">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full {{ $awayWin ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-transparent' }}"></div>
                                                    <div class="text-[12px] md:text-[13px] font-bold truncate {{ $awayWin ? 'text-white' : ($homeWin ? 'text-slate-500' : 'text-slate-300') }}">{{ $awayName }}@if($awaySeed) <span class="text-slate-500 text-[10px]">({{ $awaySeed }})</span>@endif</div>
                                                </div>
                                                <div class="flex items-center gap-1 ml-auto">
                                                    @for($i = 0; $i < $cellCount; $i++)
                                                        @php $c = $displaySets[$i] ?? null; @endphp
                                                        <div class="w-[18px] text-center">
                                                            @if($c && !is_null($c['a']))
                                                                <span class="text-[9px] font-black {{ (int) $c['a'] >= (int) $c['h'] ? 'text-emerald-400' : 'text-slate-600' }}">{{ $c['a'] }}</span>
                                                            @else
                                                                <span class="text-[9px] text-slate-700">-</span>
                                                            @endif
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-center justify-center gap-1.5 pl-2.5 border-l border-slate-800 min-w-[36px]">
                                            <div class="w-7 h-7 rounded-md flex items-center justify-center transition-all duration-300 {{ $homeWin ? 'bg-emerald-500 text-white shadow-[0_0_15px_rgba(16,185,129,0.25)]' : 'bg-slate-800/50 text-slate-500' }}">
                                                <span class="text-[13px] font-black italic">{{ $match->status === 'scheduled' ? 0 : ($live ? ($match->home_score ?? 0) : $homeSetsWon) }}</span>
                                            </div>
                                            <div class="w-7 h-7 rounded-md flex items-center justify-center transition-all duration-300 {{ $awayWin ? 'bg-emerald-500 text-white shadow-[0_0_15px_rgba(16,185,129,0.25)]' : 'bg-slate-800/50 text-slate-500' }}">
                                                <span class="text-[13px] font-black italic">{{ $match->status === 'scheduled' ? 0 : ($live ? ($match->away_score ?? 0) : $awaySetsWon) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif
                @endforeach
            </div>
            @else
                        <div class="bg-[var(--bg-card)] backdrop-blur-xl rounded-2xl p-8 md:p-12 border border-[var(--border-primary)] shadow-xl text-center">
                <div class="text-4xl md:text-6xl mb-4">🏆</div>
                <h3 class="text-lg md:text-xl font-semibold text-[var(--text-primary)] mb-2">Nema grupa još</h3>
                <p class="text-[var(--text-tertiary)] text-sm md:text-base">Grupe će se pojaviti kada turnir počne.</p>
            </div>
            @endif
        </div>        <!-- Knockout Tab Content -->
        @if($hasKnockoutMatches)
                <!-- Knockout Tab Content -->
        <div id="knockout-content" class="tab-content mt-4 md:mt-6 pb-16 md:pb-24 hidden">
            @php
                $totalRounds = $knockoutMatches->count();
                $firstRoundMatches = $knockoutMatches->get(1) ?? collect();
                $numPlayers = $firstRoundMatches->count() * 2;

                // Round names will be calculated per round based on number of matches
                $roundNames = [];

                // Check if tournament is completed and get winner
                $finalMatch = $knockoutMatches->get($totalRounds)?->first();
                $winner = null;
                $allMatchesCompleted = true;

                // Check if all knockout matches are completed
                foreach($knockoutMatches as $roundMatches) {
                    foreach($roundMatches as $match) {
                        if($match->status !== 'completed' && !$match->is_bye) {
                            $allMatchesCompleted = false;
                            break 2;
                        }
                    }
                }

                if ($finalMatch && $finalMatch->status === 'completed' && $allMatchesCompleted) {
                    $winner = $finalMatch->home_score > $finalMatch->away_score
                        ? $finalMatch->homePlayer
                        : $finalMatch->awayPlayer;
                }
            @endphp

            <!-- Winner Display (if tournament is completed) -->
            @if($winner)
            <div class="mb-6 md:mb-8 text-center">
                <div class="text-center">
                    <h2 class="text-base md:text-lg font-semibold text-amber-400 mb-2 md:mb-3 tracking-wide">
                        🏆 ŠAMPION TURNIRA 🏆
                    </h2>
                    <p class="text-xl md:text-2xl font-black text-[var(--text-primary)] mb-2" style="font-family: 'Inter', sans-serif; letter-spacing: -0.02em;">
                        {{ $winner->name }}
                    </p>
                    <p class="text-sm md:text-base text-[var(--text-tertiary)] font-medium">{{ $competition->name }}</p>
                </div>
            </div>
            @endif

            <!-- Tournament Bracket -->
            <div class="bg-[var(--bg-card)] backdrop-blur-xl rounded-xl p-4 md:p-6 border border-[var(--border-primary)] shadow-xl">
                <div class="flex justify-end mb-2">
                    <div class="flex items-center gap-2">
                                                <button id="knockout-zoom-out" type="button" class="px-2 py-1 rounded bg-[var(--bg-tertiary)] hover:bg-[var(--bg-tertiary)] text-[var(--text-primary)] text-lg font-bold" title="Smanji">&minus; </button>
                        <button id="knockout-zoom-in" type="button" class="px-2 py-1 rounded bg-[var(--bg-tertiary)] hover:bg-[var(--bg-tertiary)] text-[var(--text-primary)] text-lg font-bold" title="Povećaj">
                            &plus;
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800 pb-6">
                    <div class="min-w-max">
                        <!-- Bracket Container -->
                        <div id="knockout-bracket-scale" class="flex justify-center transition-transform duration-200" style="transform: scale(1); transform-origin: top left; gap: 3px;">
                            @foreach($knockoutMatches as $round => $roundMatches)
                            @php
                                // Calculate spacing for bracket alignment
                                $matchesInRound = $roundMatches->count();
                                
                                // Calculate round name based on distance to max round
                                $distanceToFinal = $maxKnockoutRound - (int)$round;

                                $roundName = match($distanceToFinal) {
                                    0 => 'Finale',
                                    1 => 'Polufinale',
                                    2 => 'Četvrtfinale',
                                    3 => 'Osmina finala',
                                    4 => 'Šesnaestina finala',
                                    default => 'Runda ' . $round
                                };
                            @endphp
                            @if($matchesInRound > 0)
                            <div class="flex flex-col justify-center gap-2" style="gap: 3px;">
                                {{-- Round Header (only if roundName exists) --}}
                                @if($roundName)
                                <div class="text-center" style="margin-bottom: 3px;">
                                    <h4 class="text-sm md:text-base font-bold text-[var(--text-primary)] uppercase tracking-wider">
                                        {{ $roundName }}
                                    </h4>
                                </div>
                                @endif

                                @if($matchesInRound > 0)
                                {{-- Round Matches Container --}}
                                <div class="flex flex-col" style="gap: 3px;">
                                    @foreach($roundMatches as $index => $match)
                                    @php
                                        $homeSetsWon = 0;
                                        $awaySetsWon = 0;
                                        $homeFinalScore = $match->home_score ?? 0;
                                        $awayFinalScore = $match->away_score ?? 0;

                                        if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
                                            foreach($match->sets as $set) {
                                                $homeSetScore = $set['home_score'] ?? $set['home'] ?? 0;
                                                $awaySetScore = $set['away_score'] ?? $set['away'] ?? 0;
                                                if($homeSetScore > $awaySetScore) {
                                                    $homeSetsWon++;
                                                } elseif($awaySetScore > $homeSetScore) {
                                                    $awaySetsWon++;
                                                }
                                            }
                                        }

                                        // If match is completed and we don't have sets data, use final scores
                                        if($match->status === 'completed' && $homeSetsWon === 0 && $awaySetsWon === 0) {
                                            $homeSetsWon = $homeFinalScore;
                                            $awaySetsWon = $awayFinalScore;
                                        }
                                    @endphp

                                    <div class="block bg-[var(--bg-tertiary)] hover:bg-[var(--bg-tertiary)] rounded-lg transition-all duration-200 hover:scale-[1.02] border border-[var(--border-primary)] knockout-match"
                                         data-match-id="{{ $match->id }}" 
                                         data-home-player="{{ $match->homePlayer->id ?? '' }}" 
                                         data-away-player="{{ $match->awayPlayer->id ?? '' }}"
                                         style="padding-top: 3px; margin-top: 3px; margin-bottom: 3px;">
                                        @if($match->status === 'in_progress' && !$match->is_bye)
                                        <div class="text-center mb-2">
                                            <span class="text-red-400 font-semibold text-xs uppercase tracking-wider">Live</span>
                                        </div>
                                        @endif

                                        <!-- Match Players -->
                                        <div class="px-3 md:px-4" style="padding-bottom: 3px;">
                                            <!-- Home Player -->
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-2 flex-1 min-w-0 player-container" data-player-id="{{ $match->homePlayer->id ?? '' }}">
                                                    <div class="text-xs md:text-sm font-semibold {{ ($homeSetsWon > $awaySetsWon) && ($homeSetsWon > 0 || $awaySetsWon > 0) || ($match->is_bye && $match->homePlayer) ? 'text-green-600' : 'text-[var(--text-tertiary)]' }} truncate">
                                                        {{ $match->homePlayer->name ?? 'NEMA PROTIVNIKA' }}
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ml-2">
                                                    @if($match->status === 'in_progress' && !$match->is_bye)
                                                        <div class="w-6 h-6 bg-green-900/80 rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-white badge-number">
                                                                {{ $homeFinalScore }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->status === 'completed' || ($homeSetsWon > 0 || $awaySetsWon > 0))
                                                        <div class="w-6 h-6 bg-green-900/80 rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-white badge-number">
                                                                {{ $homeFinalScore ?: $homeSetsWon }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->is_bye)
                                                        <div class="w-6 h-6 bg-[var(--bg-tertiary)] rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-[var(--text-muted)] badge-number">bye</div>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-[var(--bg-tertiary)] rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-[var(--text-muted)] badge-number">-</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Away Player -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2 flex-1 min-w-0 player-container" data-player-id="{{ $match->awayPlayer->id ?? '' }}">
                                                    <div class="text-xs md:text-sm font-semibold {{ ($awaySetsWon > $homeSetsWon) && ($homeSetsWon > 0 || $awaySetsWon > 0) || ($match->is_bye && $match->awayPlayer) ? 'text-green-600' : 'text-[var(--text-tertiary)]' }} truncate">
                                                        {{ $match->awayPlayer->name ?? 'NEMA PROTIVNIKA' }}
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ml-2">
                                                    @if($match->status === 'in_progress' && !$match->is_bye)
                                                        <div class="w-6 h-6 bg-green-900/80 rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-white badge-number">
                                                                {{ $awayFinalScore }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->status === 'completed' || ($homeSetsWon > 0 || $awaySetsWon > 0))
                                                        <div class="w-6 h-6 bg-green-900/80 rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-white badge-number">
                                                                {{ $awayFinalScore ?: $awaySetsWon }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->is_bye)
                                                        <div class="w-6 h-6 bg-[var(--bg-tertiary)] rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-[var(--text-muted)] badge-number">bye</div>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-[var(--bg-tertiary)] rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-[var(--text-muted)] badge-number">-</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Toggle Sets Button -->
                                            @if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0)
                                            <div class="mt-3 text-center">
                                                <button onclick="toggleMatchSets({{ $match->id }})"
                                                        class="text-xs text-[var(--text-tertiary)] hover:text-[var(--text-primary)] transition-colors flex items-center justify-center gap-1 mx-auto">
                                                    <span id="toggle-text-{{ $match->id }}">Prikaži po setovima</span>
                                                    <svg id="arrow-{{ $match->id }}" class="w-3 h-3 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Set Details Display (Hidden by default) -->
                                        @if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0)
                                        <div id="sets-{{ $match->id }}" class="hidden px-3 md:px-4 pb-3 md:pb-4 border-t border-[var(--border-secondary)]">
                                            <div class="mt-3">
                                                @if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0)
                                                <div class="flex justify-center gap-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                    <div class="flex flex-col items-center">
                                                        <div class="text-xs text-[var(--text-tertiary)] mb-1">{{ $i }}</div>
                                                        <div class="flex flex-col gap-0.5">
                                                            @if(isset($match->sets[$i-1]))
                                                                @php
                                                                    $homeScore = $match->sets[$i-1]['home_score'] ?? $match->sets[$i-1]['home'] ?? 0;
                                                                    $awayScore = $match->sets[$i-1]['away_score'] ?? $match->sets[$i-1]['away'] ?? 0;
                                                                @endphp
                                                                <span class="text-xs px-1 py-0.5 rounded text-center {{ $homeScore > $awayScore ? 'bg-green-900/60 text-white font-bold' : 'text-[var(--text-tertiary)]' }}">
                                                                    {{ $homeScore }}
                                                                </span>
                                                                <span class="text-xs px-1 py-0.5 rounded text-center {{ $awayScore > $homeScore ? 'bg-green-900/60 text-white font-bold' : 'text-[var(--text-tertiary)]' }}">
                                                                    {{ $awayScore }}
                                                                </span>
                                                            @else
                                                                <span class="text-xs px-1 py-0.5 rounded text-[var(--text-muted)] text-center">-</span>
                                                                <span class="text-xs px-1 py-0.5 rounded text-[var(--text-muted)] text-center">-</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endfor
                                                </div>
                                                @else
                                                <div class="text-center text-xs text-[var(--text-tertiary)]">
                                                    Detalji o setovima nisu dostupni
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    <script>
    // Knockout bracket zoom logic
    (function() {
        const scaleContainer = document.getElementById('knockout-bracket-scale');
        const zoomInBtn = document.getElementById('knockout-zoom-in');
        const zoomOutBtn = document.getElementById('knockout-zoom-out');
        let scale = 1;
        const minScale = 0.4;
        const maxScale = 2.2;
        const step = 0.15;
        function updateScale() {
            if (scaleContainer) {
                scaleContainer.style.transform = `scale(${scale})`;
            }
        }
        if (zoomInBtn && zoomOutBtn && scaleContainer) {
            zoomInBtn.addEventListener('click', function() {
                scale = Math.min(maxScale, scale + step);
                updateScale();
            });
            zoomOutBtn.addEventListener('click', function() {
                scale = Math.max(minScale, scale - step);
                updateScale();
            });
        }
    })();
        function showGroupSubTab(groupId, tab) {
            const standingsContent = document.getElementById('group-' + groupId + '-standings-content');
            const matchesContent = document.getElementById('group-' + groupId + '-matches-content');
            const standingsTab = document.getElementById('group-' + groupId + '-standings-tab');
            const matchesTab = document.getElementById('group-' + groupId + '-matches-tab');

            standingsContent.classList.toggle('hidden', tab !== 'standings');
            matchesContent.classList.toggle('hidden', tab !== 'matches');

            standingsTab.style.color = tab === 'standings' ? 'var(--accent-blue)' : 'var(--text-tertiary)';
            standingsTab.style.borderColor = tab === 'standings' ? 'var(--accent-blue)' : 'transparent';
            matchesTab.style.color = tab === 'matches' ? 'var(--accent-blue)' : 'var(--text-tertiary)';
            matchesTab.style.borderColor = tab === 'matches' ? 'var(--accent-blue)' : 'transparent';
        }

        function showTournamentTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.style.borderColor = 'transparent';
                button.style.color = 'var(--text-tertiary)';
                button.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById(tabName + '-content').classList.remove('hidden');

            // Set active state for selected tab
            const activeTab = document.getElementById(tabName + '-tab');
            activeTab.style.borderColor = 'var(--accent-blue)';
            activeTab.style.color = 'var(--accent-blue)';
            activeTab.classList.add('active');
        }

        function toggleMatchSets(matchId) {
            const setsElement = document.getElementById('sets-' + matchId);
            const arrowElement = document.getElementById('arrow-' + matchId);
            const textElement = document.getElementById('toggle-text-' + matchId);

            if (setsElement && arrowElement && textElement) {
                const isHidden = setsElement.classList.contains('hidden');
                setsElement.classList.toggle('hidden');
                arrowElement.classList.toggle('rotate-180');

                if (isHidden) {
                    textElement.textContent = 'Sakrij po setovima';
                } else {
                    textElement.textContent = 'Prikaži po setovima';
                }
            }
        }
        // Initialize first available tab as active
        document.addEventListener('DOMContentLoaded', function() {
            @if($showGroupsTab)
                showTournamentTab('groups');
            @elseif($showKnockoutTab)
                showTournamentTab('knockout');
            @elseif($showPdfTab)
                showTournamentTab('pdf');
            @endif

            // Initialize knockout bracket hover effects
            initializeKnockoutHover();
        });

        function initializeKnockoutHover() {
            const playerContainers = document.querySelectorAll('.player-container');

            playerContainers.forEach(container => {
                container.addEventListener('mouseenter', function() {
                    const playerId = this.getAttribute('data-player-id');
                    if (playerId) {
                        highlightPlayerPath(playerId);
                    }
                });

                container.addEventListener('mouseleave', function() {
                    clearAllHighlights();
                });
            });
        }

        function highlightPlayerPath(playerId) {
            const allPlayerContainers = document.querySelectorAll('.player-container');

            allPlayerContainers.forEach(container => {
                const containerPlayerId = container.getAttribute('data-player-id');
                if (containerPlayerId === playerId) {
                    container.classList.add('player-highlight');
                }
            });
        }

        function clearAllHighlights() {
            const highlightedContainers = document.querySelectorAll('.player-container.player-highlight');
            highlightedContainers.forEach(container => {
                container.classList.remove('player-highlight');
            });
        }
    </script>
@endif