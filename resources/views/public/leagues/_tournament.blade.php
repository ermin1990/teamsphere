<!-- Tournament Groups with Tables and Matches -->
@if($competition->type === 'tournament')
    <style>
        .tab-button.active {
            border-color: var(--accent-blue) !important;
            color: var(--accent-blue) !important;
            font-weight: 600 !important;
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
        $knockoutMatches = $allMatches->where('phase', 'knockout')
            ->sortBy(['round_number', 'match_order'])
            ->groupBy('round_number');

        $groupMatches = $allMatches->whereNotNull('tournament_group_id')
            ->sortBy(['round_number', 'match_order'])
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
            <div class="grid grid-cols-2 gap-4 md:gap-6">
                @foreach($competition->tournamentGroups as $group)
                @php
                    $currentGroupMatches = $groupMatches->get($group->id) ?? collect();
                @endphp
                @if($currentGroupMatches->count() > 0)
                                            <div class="[var(--bg-card)] backdrop-blur-xl rounded-xl p-4 md:p-6 border border-[var(--border-primary)] shadow-xl">
                    <h4 class="text-base md:text-lg font-bold text-[var(--text-primary)] mb-3 md:mb-4">{{ $group->name }}</h4>

                    <!-- Group Standings -->
                    @php
                        $groupStandings = $group->standings()->with('player')->orderBy('position')->get();
                    @endphp
                    @if($groupStandings->count() > 0)
                    <div class="mb-4">
                                                <h5 class="text-sm md:text-base font-semibold text-[var(--text-secondary)] mb-2 uppercase tracking-wide">Tabela</h5>

                        <!-- Table Header -->
                                                <div class="grid grid-cols-12 gap-2 mb-2 text-xs text-[var(--text-tertiary)] font-medium px-2">
                            <div class="col-span-6"></div>
                            <div class="col-span-1 text-center">P</div>
                            <div class="col-span-1 text-center">I</div>
                            <div class="col-span-1 text-center">Set±</div>
                            <div class="col-span-2 text-center">B</div>
                        </div>

                        <!-- Table Rows -->
                        <div class="space-y-1">
                            @php
                                $advancingPlayers = $competition->players_advancing_per_group ?? 2;
                            @endphp
                            @foreach($groupStandings as $index => $standing)
                            <div class="grid grid-cols-12 gap-2 items-center py-2 px-2 {{ $index < $advancingPlayers ? 'bg-green-900/20 border border-green-600/30' : 'bg-[var(--bg-tertiary)]' }} hover:bg-[var(--bg-secondary)] rounded text-xs md:text-sm transition-all duration-200">
                                <div class="col-span-6 flex items-center space-x-2">
                                    <span class="font-bold text-[var(--text-tertiary)] w-6 text-center">{{ $index + 1 }}</span>
                                    <span class="text-[var(--text-primary)] font-medium text-xs truncate">{{ $standing->player->name }}</span>
                                </div>
                                <div class="col-span-1 text-center">
                                    <span class="text-green-600 font-bold">{{ $standing->won ?? 0 }}</span>
                                </div>
                               
                                <div class="col-span-1 text-center">
                                    <span class="text-red-600 font-bold">{{ $standing->lost ?? 0 }}</span>
                                </div>
                                <div class="col-span-1 text-center">
                                    <span class="text-cyan-600 font-bold">{{ ($standing->sets_won ?? 0) - ($standing->sets_lost ?? 0) }}</span>
                                </div>
                                <div class="col-span-2 text-center">
                                    <span class="text-blue-600 font-bold">{{ $standing->points ?? 0 }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Group Matches -->
                    @if($currentGroupMatches->count() > 0)
                    <div>
                        <h5 class="text-sm md:text-base font-semibold text-[var(--text-secondary)] mb-2 uppercase tracking-wide">Mečevi</h5>
                        <div class="space-y-1 md:space-y-3">
                            @foreach($currentGroupMatches as $match)
                            <div class="block bg-[var(--bg-tertiary)] hover:bg-[var(--bg-tertiary)] rounded-md transition-all duration-200 hover:scale-[1.01]">
                                @if($match->status === 'in_progress')
                                <div class="text-center mb-2">
                                    <span class="text-red-400 font-semibold text-xs uppercase tracking-wider">Live</span>
                                </div>
                                @endif

                                @php
                                    $homeSetsWon = 0;
                                    $awaySetsWon = 0;
                                    if($match->played_at) {
                                        if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
                                            foreach($match->sets as $set) {
                                                if(($set['home_score'] ?? 0) > ($set['away_score'] ?? 0)) {
                                                    $homeSetsWon++;
                                                }
                                                if(($set['away_score'] ?? 0) > ($set['home_score'] ?? 0)) {
                                                    $awaySetsWon++;
                                                }
                                            }
                                        } elseif ($match->status === 'completed') {
                                            $homeSetsWon = $match->home_score ?? 0;
                                            $awaySetsWon = $match->away_score ?? 0;
                                        }
                                    }
                                    $displaySets = isset($match->sets) && is_array($match->sets) && count($match->sets) > 0 ? $match->sets : [];
                                @endphp

                                <!-- Mobile Layout -->
                                <div class="block md:hidden p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <!-- Sets won indicator -->
                                            <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center text-xs font-bold text-[var(--text-primary)] flex-shrink-0">
                                                @if($match->status === 'completed' || $match->status === 'in_progress')
                                                    {{ $homeSetsWon }}
                                                @else
                                                    <span class="text-[var(--text-muted)]">-</span>
                                                @endif
                                            </div>
                                            <div class="text-sm font-semibold {{ $homeSetsWon > $awaySetsWon ? 'font-bold' : '' }} text-[var(--text-primary)] truncate">
                                                {{ $match->homePlayer->name ?? 'Home Player' }}
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ml-2">
                                            @if($match->status === 'in_progress')
                                                <div class="w-8 h-8 bg-green-900/80 rounded-lg flex items-center justify-center">
                                                    <div class="text-sm font-bold text-green-300">
                                                        {{ $match->home_score ?? 0 }}
                                                    </div>
                                                </div>
                                            @elseif($match->status === 'completed')
                                                <div class="w-8 h-8 bg-green-900/80 rounded-lg flex items-center justify-center">
                                                    <div class="text-sm font-bold text-green-300">
                                                        {{ $homeSetsWon }}
                                                    </div>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 bg-[var(--bg-tertiary)] rounded-lg flex items-center justify-center">
                                                    <div class="text-sm font-bold text-[var(--text-muted)]">-</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <!-- Sets won indicator -->
                                            <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center text-xs font-bold text-[var(--text-primary)] flex-shrink-0">
                                                @if($match->status === 'completed' || $match->status === 'in_progress')
                                                    {{ $awaySetsWon }}
                                                @else
                                                    <span class="text-[var(--text-muted)]">-</span>
                                                @endif
                                            </div>
                                            <div class="text-sm font-semibold {{ $awaySetsWon > $homeSetsWon ? 'font-bold' : '' }} text-[var(--text-primary)] truncate">
                                                {{ $match->awayPlayer->name ?? 'Away Player' }}
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ml-2">
                                            @if($match->status === 'in_progress')
                                                <div class="w-8 h-8 bg-green-900/80 rounded-lg flex items-center justify-center">
                                                    <div class="text-sm font-bold text-green-300">
                                                        {{ $match->away_score ?? 0 }}
                                                    </div>
                                                </div>
                                            @elseif($match->status === 'completed')
                                                <div class="w-8 h-8 bg-green-900/80 rounded-lg flex items-center justify-center">
                                                    <div class="text-sm font-bold text-green-300">
                                                        {{ $awaySetsWon }}
                                                    </div>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 bg-[var(--bg-tertiary)] rounded-lg flex items-center justify-center">
                                                    <div class="text-sm font-bold text-[var(--text-muted)]">-</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Mobile Set Display -->
                                    @if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0)
                                    <div class="mt-3 pt-3 border-t border-[var(--border-secondary)]">
                                        <div class="flex justify-center gap-1">
                                            @php
                                                $displaySets = isset($match->sets) && is_array($match->sets) && count($match->sets) > 0 ? $match->sets : [];
                                            @endphp
                                            @for($i = 1; $i <= 5; $i++)
                                            <div class="flex flex-col items-center">
                                                <div class="text-xs text-[var(--text-tertiary)] mb-1">{{ $i }}</div>
                                                <div class="flex gap-1">
                                                    @if(isset($displaySets[$i-1]))
                                                        @php
                                                            $homeScore = $displaySets[$i-1]['home_score'] ?? $displaySets[$i-1]['home'] ?? 0;
                                                            $awayScore = $displaySets[$i-1]['away_score'] ?? $displaySets[$i-1]['away'] ?? 0;
                                                        @endphp
                                                        <span class="text-xs px-1 py-0.5 rounded {{ $homeScore > $awayScore ? 'bg-green-900/60 text-green-300 font-bold' : 'text-[var(--text-tertiary)]' }}">
                                                            {{ $homeScore }}
                                                        </span>
                                                        <span class="text-xs px-1 py-0.5 rounded {{ $awayScore > $homeScore ? 'bg-green-900/60 text-green-300 font-bold' : 'text-[var(--text-tertiary)]' }}">
                                                            {{ $awayScore }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs px-1 py-0.5 rounded text-[var(--text-muted)]">-</span>
                                                        <span class="text-xs px-1 py-0.5 rounded text-[var(--text-muted)]">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @endfor
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Desktop Layout -->
                                <div class="hidden md:block p-4">
                                    @php
                                        $homeSetsWon = 0;
                                        $awaySetsWon = 0;
                                        if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
                                            foreach($match->sets as $set) {
                                                if(($set['home_score'] ?? $set['home'] ?? 0) > ($set['away_score'] ?? $set['away'] ?? 0)) {
                                                    $homeSetsWon++;
                                                }
                                                if(($set['away_score'] ?? $set['away'] ?? 0) > ($set['home_score'] ?? $set['home'] ?? 0)) {
                                                    $awaySetsWon++;
                                                }
                                            }
                                        } elseif ($match->status === 'completed') {
                                            $homeSetsWon = $match->home_score ?? 0;
                                            $awaySetsWon = $match->away_score ?? 0;
                                        }
                                    @endphp
                                    <div class="flex items-center justify-between">
                                        <!-- Left side: Players and sets -->
                                        <div class="flex-1 space-y-4">
                                            <!-- Home Player -->
                                            <div class="flex items-center gap-3">
                                                <!-- Sets won indicator -->
                                                <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center text-xs font-bold text-[var(--text-primary)] flex-shrink-0">
                                                    @if($match->status === 'completed' || $match->status === 'in_progress')
                                                        {{ $homeSetsWon }}
                                                    @else
                                                        <span class="text-[var(--text-muted)]">-</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs md:text-sm font-semibold {{ $homeSetsWon > $awaySetsWon ? 'font-bold' : '' }} text-[var(--text-primary)] truncate flex-1 min-w-0">
                                                    {{ $match->homePlayer->name ?? 'Home Player' }}
                                                </div>
                                                <!-- Sets -->
                                                <div class="flex gap-1 ml-4">
                                                    @php
                                                        $displaySets = isset($match->sets) && is_array($match->sets) && count($match->sets) > 0 ? $match->sets : [];
                                                    @endphp
                                                    @for($i = 1; $i <= 5; $i++)
                                                    <div class="w-6 text-center {{ $i < 5 ? 'border-r border-[var(--border-secondary)]' : '' }}">
                                                        @if(isset($displaySets[$i-1]))
                                                            @php
                                                                $homeScore = $displaySets[$i-1]['home_score'] ?? $displaySets[$i-1]['home'] ?? 0;
                                                                $awayScore = $displaySets[$i-1]['away_score'] ?? $displaySets[$i-1]['away'] ?? 0;
                                                            @endphp
                                                            <span class="text-xs px-1 py-0.5 rounded {{ $homeScore > $awayScore ? 'bg-green-900/60 text-green-300 font-bold' : 'text-[var(--text-tertiary)]' }}">
                                                                {{ $homeScore }}
                                                            </span>
                                                        @else
                                                            <span class="text-xs px-1 py-0.5 rounded text-[var(--text-muted)]">-</span>
                                                        @endif
                                                    </div>
                                                    @endfor
                                                </div>
                                            </div>

                                            <!-- Away Player -->
                                            <div class="flex items-center gap-3">
                                                <!-- Sets won indicator -->
                                                <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center text-xs font-bold text-[var(--text-primary)] flex-shrink-0">
                                                    @if($match->status === 'completed' || $match->status === 'in_progress')
                                                        {{ $awaySetsWon }}
                                                    @else
                                                        <span class="text-[var(--text-muted)]">-</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs md:text-sm font-semibold {{ $awaySetsWon > $homeSetsWon ? 'font-bold' : '' }} text-[var(--text-primary)] truncate flex-1 min-w-0">
                                                    {{ $match->awayPlayer->name ?? 'Away Player' }}
                                                </div>
                                                <!-- Sets -->
                                                <div class="flex gap-1 ml-4">
                                                    @for($i = 1; $i <= 5; $i++)
                                                    <div class="w-6 text-center {{ $i < 5 ? 'border-r border-[var(--border-secondary)]' : '' }}">
                                                        @if(isset($displaySets[$i-1]))
                                                            @php
                                                                $homeScore = $displaySets[$i-1]['home_score'] ?? $displaySets[$i-1]['home'] ?? 0;
                                                                $awayScore = $displaySets[$i-1]['away_score'] ?? $displaySets[$i-1]['away'] ?? 0;
                                                            @endphp
                                                            <span class="text-xs px-1 py-0.5 rounded {{ $awayScore > $homeScore ? 'bg-green-900/60 text-green-300 font-bold' : 'text-[var(--text-tertiary)]' }}">
                                                                {{ $awayScore }}
                                                            </span>
                                                        @else
                                                            <span class="text-xs px-1 py-0.5 rounded text-[var(--text-muted)]">-</span>
                                                        @endif
                                                    </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right side: Final scores -->
                                        <div class="flex flex-col items-center justify-center space-y-2 ml-6">
                                            @if($match->status === 'in_progress')
                                                <div class="flex flex-col items-center space-y-2">
                                                    <div class="w-10 h-10 bg-green-900/80 rounded-lg flex items-center justify-center">
                                                        <div class="text-sm font-bold text-green-300">
                                                            {{ $match->home_score ?? 0 }}
                                                        </div>
                                                    </div>
                                                    <div class="w-10 h-10 bg-green-900/80 rounded-lg flex items-center justify-center">
                                                        <div class="text-sm font-bold text-green-300">
                                                            {{ $match->away_score ?? 0 }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($match->status === 'completed')
                                                <div class="flex flex-col items-center space-y-2">
                                                    <div class="w-10 h-10 bg-green-900/80 rounded-lg flex items-center justify-center">
                                                        <div class="text-sm font-bold text-green-300">
                                                            {{ $homeSetsWon }}
                                                        </div>
                                                    </div>
                                                    <div class="w-10 h-10 bg-green-900/80 rounded-lg flex items-center justify-center">
                                                        <div class="text-sm font-bold text-green-300">
                                                            {{ $awaySetsWon }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex flex-col items-center space-y-2">
                                                    <div class="w-10 h-10 bg-[var(--bg-tertiary)] rounded-lg flex items-center justify-center">
                                                        <div class="text-sm font-bold text-[var(--text-muted)">-</div>
                                                    </div>
                                                    <div class="w-10 h-10 bg-[var(--bg-tertiary)] rounded-lg flex items-center justify-center">
                                                        <div class="text-sm font-bold text-[var(--text-muted)">-</div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>                            </div>
                            @endforeach
                        </div>
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
                            @for($round = 1; $round <= $totalRounds; $round++)
                            @php
                                $roundMatches = $knockoutMatches->get($round) ?? collect();
                                // Calculate spacing for bracket alignment
                                $matchesInRound = $roundMatches->count();
                                $spacingMultiplier = pow(2, $round - 1);
                                
                                // Calculate round name based on number of matches in this round
                                if ($matchesInRound == 1 && $round === $totalRounds) {
                                    // Last round with 1 match = Finale
                                    $roundName = 'Finale';
                                } elseif ($matchesInRound == 1) {
                                    // 1 match but not last round = Polufinale
                                    $roundName = 'Polufinale';
                                } else {
                                    // Multiple matches = 1/N Finala where N is half the number of players in this round
                                    // Number of players = matchesInRound * 2
                                    // So 1/(matchesInRound * 2 / 2) = 1/matchesInRound
                                    $roundName = '1/' . $matchesInRound . ' Finala';
                                }
                            @endphp
                            @if($matchesInRound > 0 || ($round === $totalRounds && $totalRounds > 1))
                            <div class="flex flex-col justify-center gap-2" style="gap: 3px;">
                                {{-- Round Header --}}
                                <div class="text-center" style="margin-bottom: 3px;">
                                    <h4 class="text-sm md:text-base font-bold text-[var(--text-primary)] uppercase tracking-wider">
                                        {{ $roundName }}
                                    </h4>
                                </div>

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
                                                        <div class="w-6 h-6 bg-green-900/80 rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-white">
                                                                {{ $homeFinalScore }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->status === 'completed' || ($homeSetsWon > 0 || $awaySetsWon > 0))
                                                        <div class="w-6 h-6 bg-green-900/80 rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-white">
                                                                {{ $homeFinalScore ?: $homeSetsWon }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->is_bye)
                                                        <div class="w-6 h-6 bg-[var(--bg-tertiary)] rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-[var(--text-muted)]">bye</div>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-[var(--bg-tertiary)] rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-[var(--text-muted)]">-</div>
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
                                                        <div class="w-6 h-6 bg-green-900/80 rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-white">
                                                                {{ $awayFinalScore }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->status === 'completed' || ($homeSetsWon > 0 || $awaySetsWon > 0))
                                                        <div class="w-6 h-6 bg-green-900/80 rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-white">
                                                                {{ $awayFinalScore ?: $awaySetsWon }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->is_bye)
                                                        <div class="w-6 h-6 bg-[var(--bg-tertiary)] rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-[var(--text-muted)]">bye</div>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-[var(--bg-tertiary)] rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-[var(--text-muted)]">-</div>
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
                            @endfor
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