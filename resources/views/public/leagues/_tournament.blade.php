<!-- Tournament Groups with Tables and Matches -->
@if($competition->type === 'tournament')
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }

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

        .player-container.player-highlight {
            box-shadow: 0 0 0 2px rgba(87, 241, 219, 0.6);
            background-color: rgba(87, 241, 219, 0.1) !important;
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
        <div class="flex bg-surface-container-low p-1 rounded-lg border border-outline-variant w-fit">
            @if($showGroupsTab)
            <button onclick="showTournamentTab('groups')" id="groups-tab"
                    class="tab-button px-4 py-1.5 rounded-md text-sm font-label-bold transition-all bg-primary text-on-primary">
                🏆 Grupna faza
            </button>
            @endif
            @if($showKnockoutTab)
            <button onclick="showTournamentTab('knockout')" id="knockout-tab"
                    class="tab-button px-4 py-1.5 rounded-md text-sm font-label-bold transition-all text-on-surface-variant hover:text-on-surface">
                🏅 Eliminaciona faza
            </button>
            @endif
        </div>

        <!-- Groups Tab Content -->
        <div id="groups-content" class="tab-content mt-4 md:mt-6 {{ !$showGroupsTab ? 'hidden' : '' }}">
            @if($hasGroupMatches)
            @php
                // Jedna grupa zauzima cijelu sirinu umjesto da bude stisnuta u
                // pola kolone kao da postoji jos jedna pored nje.
                $groupCount = $competition->tournamentGroups->count();
            @endphp
            <div class="grid grid-cols-1 {{ $groupCount > 1 ? 'xl:grid-cols-2' : '' }} gap-6 lg:gap-8">
                @foreach($competition->tournamentGroups as $group)
                @php
                    $currentGroupMatches = $groupMatches->get($group->id) ?? collect();
                    $groupRoundOf = fn($m) => $m->round_number ?? $m->round;
                    $groupMatchesByRound = $currentGroupMatches->sortBy($groupRoundOf)->groupBy($groupRoundOf);
                @endphp
                @if($currentGroupMatches->count() > 0)
                <section class="-mx-margin-mobile lg:mx-0 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl overflow-hidden lg:shadow-2xl">
                    <div class="px-margin-mobile py-4 lg:p-6 border-b border-outline-variant flex items-center justify-between gap-3 flex-wrap">
                        <h2 class="font-headline-md text-on-surface">{{ $group->name }}</h2>
                        <!-- Tabela / Mečevi sub-tabs -->
                        <div class="flex bg-surface-container-lowest p-1 rounded-lg border border-outline-variant">
                            <button type="button" onclick="showGroupSubTab({{ $group->id }}, 'standings')" id="group-{{ $group->id }}-standings-tab"
                                    class="group-subtab px-4 py-1.5 rounded-md text-sm font-label-bold transition-all bg-primary text-on-primary">
                                Tabela
                            </button>
                            <button type="button" onclick="showGroupSubTab({{ $group->id }}, 'matches')" id="group-{{ $group->id }}-matches-tab"
                                    class="group-subtab px-4 py-1.5 rounded-md text-sm font-label-bold transition-all text-on-surface-variant hover:text-on-surface">
                                Mečevi
                            </button>
                        </div>
                    </div>

                    <!-- Group Standings -->
                    @php
                        $groupStandings = $group->standings()->with('player.organization')
                            ->orderByRaw('CASE WHEN manual_order IS NULL THEN 1 ELSE 0 END ASC, manual_order ASC')
                            ->orderBy('points', 'desc')
                            ->orderByRaw('(sets_won - sets_lost) desc')
                            ->orderByRaw('(points_won - points_lost) desc')
                            ->orderByDesc('points_won')
                            ->orderByDesc('sets_won')
                            ->orderByDesc('won')
                            ->orderBy('id')
                            ->get();
                        $advancingPlayers = $competition->players_advancing_per_group ?? 2;
                    @endphp
                    <div id="group-{{ $group->id }}-standings-content" class="group-subtab-content">
                    @if($groupStandings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-sm">
                                <thead class="bg-surface-container-lowest text-on-surface-variant text-label-bold uppercase">
                                    <tr>
                                        <th class="px-3 lg:px-4 py-2.5 lg:py-3">#</th>
                                        <th class="px-2 lg:px-4 py-2.5 lg:py-3">Igrač</th>
                                        <th class="hidden md:table-cell px-2 py-2.5 lg:py-3 text-center">M</th>
                                        <th class="px-2 py-2.5 lg:py-3 text-center">P</th>
                                        <th class="px-2 py-2.5 lg:py-3 text-center">I</th>
                                        <th class="px-2 py-2.5 lg:py-3 text-center">S</th>
                                        <th class="hidden sm:table-cell px-2 py-2.5 lg:py-3 text-center">G</th>
                                        <th class="px-3 lg:px-4 py-2.5 lg:py-3 text-center text-primary">Bod</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant">
                                    @foreach($groupStandings as $index => $standing)
                                        @php
                                            $played = ($standing->won ?? 0) + ($standing->lost ?? 0);
                                            $setDiff = ($standing->sets_won ?? 0) - ($standing->sets_lost ?? 0);
                                            $gemDiff = ($standing->points_won ?? 0) - ($standing->points_lost ?? 0);
                                            $advancing = $index < $advancingPlayers;
                                            $clubName = $standing->player->organization->name ?? null;
                                        @endphp
                                        <tr class="transition-colors group {{ $advancing ? 'bg-primary/5' : 'hover:bg-surface-variant/30' }}">
                                            <td class="px-3 lg:px-4 py-2 lg:py-2.5 font-bold {{ $advancing ? 'text-primary' : '' }}">{{ $index + 1 }}</td>
                                            <td class="px-2 lg:px-4 py-2 lg:py-2.5">
                                                <span class="font-semibold group-hover:text-primary transition-colors truncate block">{{ $standing->player->name }}</span>
                                                @if($clubName)
                                                    <span class="text-xs text-on-surface-variant truncate block">{{ $clubName }}</span>
                                                @endif
                                            </td>
                                            <td class="hidden md:table-cell px-2 py-2 lg:py-2.5 text-center">{{ $played }}</td>
                                            <td class="px-2 py-2 lg:py-2.5 text-center text-primary font-bold">{{ $standing->won ?? 0 }}</td>
                                            <td class="px-2 py-2 lg:py-2.5 text-center text-error">{{ $standing->lost ?? 0 }}</td>
                                            <td class="px-2 py-2 lg:py-2.5 text-center {{ $setDiff > 0 ? 'text-primary' : ($setDiff < 0 ? 'text-error' : '') }} font-bold">{{ $setDiff > 0 ? '+' : '' }}{{ $setDiff }}</td>
                                            <td class="hidden sm:table-cell px-2 py-2 lg:py-2.5 text-center {{ $gemDiff > 0 ? 'text-primary' : ($gemDiff < 0 ? 'text-error' : '') }}">{{ $gemDiff > 0 ? '+' : '' }}{{ $gemDiff }}</td>
                                            <td class="px-3 lg:px-4 py-2 lg:py-2.5 text-center"><span class="{{ $advancing ? 'bg-primary text-on-primary' : 'bg-surface-container-high text-on-surface-variant' }} px-2.5 py-1 rounded font-bold text-xs">{{ $standing->points ?? 0 }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-10 text-on-surface-variant text-sm">Tabela će se pojaviti kada grupa počne.</div>
                    @endif
                    </div>

                    <!-- Group Matches -->
                    @if($currentGroupMatches->count() > 0)
                    <div id="group-{{ $group->id }}-matches-content" class="group-subtab-content hidden px-margin-mobile py-4 lg:p-6">
                        @foreach($groupMatchesByRound as $round => $roundMatches)
                        <div class="flex items-center gap-3 mb-3 mt-4 first:mt-0">
                            <span class="bg-surface-container-highest px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider border border-outline-variant">Kolo {{ $round }}</span>
                            <div class="h-px flex-1 bg-outline-variant"></div>
                        </div>
                        <div class="grid grid-cols-1 {{ $groupCount === 1 ? 'lg:grid-cols-2' : '' }} gap-2">
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
                            <div class="bg-surface-container-low p-4 lg:p-5 rounded-xl transition-all-200 {{ $completed ? 'border-l-4 border-primary rounded-r-xl' : ($live ? 'border-l-4 border-secondary rounded-r-xl' : 'border border-outline-variant hover:border-primary/50') }}">
                                <div class="flex justify-between items-center mb-3 text-label-bold text-on-surface-variant uppercase text-xs">
                                    @if($completed)
                                        <span>Završeno</span>
                                    @elseif($live)
                                        <span class="text-secondary animate-pulse">Uživo</span>
                                    @else
                                        <span class="text-secondary">Zakazano</span>
                                    @endif
                                    @if($homeSeed || $awaySeed)
                                        <span>{{ $homeSeed ? '#' . $homeSeed : '' }}@if($homeSeed && $awaySeed) vs @endif{{ $awaySeed ? '#' . $awaySeed : '' }}</span>
                                    @endif
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center gap-3 {{ $awayWin ? 'opacity-60' : '' }}">
                                        <span class="font-medium truncate">{{ $homeName }}</span>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <div class="flex items-center gap-1">
                                                @for($i = 0; $i < $cellCount; $i++)
                                                    @php $c = $displaySets[$i] ?? null; @endphp
                                                    <span class="w-[18px] text-center text-[11px] font-bold {{ $c && !is_null($c['h']) ? ((int) $c['h'] >= (int) $c['a'] ? 'text-primary' : 'text-on-surface-variant/50') : 'text-on-surface-variant/30' }}">{{ $c && !is_null($c['h']) ? $c['h'] : '-' }}</span>
                                                @endfor
                                            </div>
                                            <span class="font-bold {{ $homeWin ? 'text-primary' : 'text-on-surface-variant' }} text-body-lg">{{ $completed || $live ? $homeSetsWon : '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center gap-3 {{ $homeWin ? 'opacity-60' : '' }}">
                                        <span class="font-medium truncate">{{ $awayName }}</span>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <div class="flex items-center gap-1">
                                                @for($i = 0; $i < $cellCount; $i++)
                                                    @php $c = $displaySets[$i] ?? null; @endphp
                                                    <span class="w-[18px] text-center text-[11px] font-bold {{ $c && !is_null($c['a']) ? ((int) $c['a'] >= (int) $c['h'] ? 'text-primary' : 'text-on-surface-variant/50') : 'text-on-surface-variant/30' }}">{{ $c && !is_null($c['a']) ? $c['a'] : '-' }}</span>
                                                @endfor
                                            </div>
                                            <span class="font-bold {{ $awayWin ? 'text-primary' : 'text-on-surface-variant' }} text-body-lg">{{ $completed || $live ? $awaySetsWon : '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                    @endif
                </section>
                @endif
                @endforeach
            </div>
            @else
            <div class="-mx-margin-mobile lg:mx-0 text-center py-16 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4 block">emoji_events</span>
                <h3 class="font-headline-md text-on-surface-variant mb-2">Nema grupa još</h3>
                <p class="text-on-surface-variant text-sm">Grupe će se pojaviti kada turnir počne.</p>
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
                    <h2 class="text-base md:text-lg font-semibold text-secondary mb-2 md:mb-3 tracking-wide">
                        🏆 ŠAMPION TURNIRA 🏆
                    </h2>
                    <p class="text-xl md:text-2xl font-display text-on-surface mb-2">
                        {{ $winner->name }}
                    </p>
                    <p class="text-sm md:text-base text-on-surface-variant font-medium">{{ $competition->name }}</p>
                </div>
            </div>
            @endif

            <!-- Tournament Bracket -->
            <div class="-mx-margin-mobile lg:mx-0 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl px-margin-mobile py-4 lg:p-6">
                <div class="flex justify-end mb-2">
                    <div class="flex items-center gap-2">
                        <button id="knockout-zoom-out" type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-surface-container-high hover:bg-surface-container-highest text-on-surface text-lg font-bold transition-colors" title="Smanji">&minus;</button>
                        <button id="knockout-zoom-in" type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-surface-container-high hover:bg-surface-container-highest text-on-surface text-lg font-bold transition-colors" title="Povećaj">&plus;</button>
                    </div>
                </div>
                <div class="overflow-x-auto custom-scrollbar pb-6">
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
                                    <h4 class="text-sm md:text-base font-bold text-on-surface uppercase tracking-wider">
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

                                    <div class="block bg-surface-container-low hover:bg-surface-variant/30 rounded-lg transition-all duration-200 hover:scale-[1.02] border border-outline-variant knockout-match"
                                         data-match-id="{{ $match->id }}"
                                         data-home-player="{{ $match->homePlayer->id ?? '' }}"
                                         data-away-player="{{ $match->awayPlayer->id ?? '' }}"
                                         style="padding-top: 3px; margin-top: 3px; margin-bottom: 3px;">
                                        @if($match->status === 'in_progress' && !$match->is_bye)
                                        <div class="text-center mb-2">
                                            <span class="text-secondary font-semibold text-xs uppercase tracking-wider animate-pulse">Uživo</span>
                                        </div>
                                        @endif

                                        <!-- Match Players -->
                                        <div class="px-3 md:px-4" style="padding-bottom: 3px;">
                                            <!-- Home Player -->
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-2 flex-1 min-w-0 player-container" data-player-id="{{ $match->homePlayer->id ?? '' }}">
                                                    <div class="text-xs md:text-sm font-semibold {{ ($homeSetsWon > $awaySetsWon) && ($homeSetsWon > 0 || $awaySetsWon > 0) || ($match->is_bye && $match->homePlayer) ? 'text-primary' : 'text-on-surface-variant' }} truncate">
                                                        {{ $match->homePlayer->name ?? 'NEMA PROTIVNIKA' }}
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ml-2">
                                                    @if($match->status === 'in_progress' && !$match->is_bye)
                                                        <div class="w-6 h-6 bg-primary/80 rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-on-primary badge-number">
                                                                {{ $homeFinalScore }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->status === 'completed' || ($homeSetsWon > 0 || $awaySetsWon > 0))
                                                        <div class="w-6 h-6 bg-primary/80 rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-on-primary badge-number">
                                                                {{ $homeFinalScore ?: $homeSetsWon }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->is_bye)
                                                        <div class="w-6 h-6 bg-surface-container-highest rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-on-surface-variant badge-number">bye</div>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-surface-container-highest rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-on-surface-variant badge-number">-</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Away Player -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2 flex-1 min-w-0 player-container" data-player-id="{{ $match->awayPlayer->id ?? '' }}">
                                                    <div class="text-xs md:text-sm font-semibold {{ ($awaySetsWon > $homeSetsWon) && ($homeSetsWon > 0 || $awaySetsWon > 0) || ($match->is_bye && $match->awayPlayer) ? 'text-primary' : 'text-on-surface-variant' }} truncate">
                                                        {{ $match->awayPlayer->name ?? 'NEMA PROTIVNIKA' }}
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ml-2">
                                                    @if($match->status === 'in_progress' && !$match->is_bye)
                                                        <div class="w-6 h-6 bg-primary/80 rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-on-primary badge-number">
                                                                {{ $awayFinalScore }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->status === 'completed' || ($homeSetsWon > 0 || $awaySetsWon > 0))
                                                        <div class="w-6 h-6 bg-primary/80 rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-on-primary badge-number">
                                                                {{ $awayFinalScore ?: $awaySetsWon }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->is_bye)
                                                        <div class="w-6 h-6 bg-surface-container-highest rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-on-surface-variant badge-number">bye</div>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-surface-container-highest rounded flex items-center justify-center badge-box">
                                                            <div class="text-xs font-bold text-on-surface-variant badge-number">-</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Toggle Sets Button -->
                                            @if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0)
                                            <div class="mt-3 text-center">
                                                <button onclick="toggleMatchSets({{ $match->id }})"
                                                        class="text-xs text-on-surface-variant hover:text-primary transition-colors flex items-center justify-center gap-1 mx-auto">
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
                                        <div id="sets-{{ $match->id }}" class="hidden px-3 md:px-4 pb-3 md:pb-4 border-t border-outline-variant">
                                            <div class="mt-3">
                                                @if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0)
                                                <div class="flex justify-center gap-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                    <div class="flex flex-col items-center">
                                                        <div class="text-xs text-on-surface-variant mb-1">{{ $i }}</div>
                                                        <div class="flex flex-col gap-0.5">
                                                            @if(isset($match->sets[$i-1]))
                                                                @php
                                                                    $homeScore = $match->sets[$i-1]['home_score'] ?? $match->sets[$i-1]['home'] ?? 0;
                                                                    $awayScore = $match->sets[$i-1]['away_score'] ?? $match->sets[$i-1]['away'] ?? 0;
                                                                @endphp
                                                                <span class="text-xs px-1 py-0.5 rounded text-center {{ $homeScore > $awayScore ? 'bg-primary/20 text-primary font-bold' : 'text-on-surface-variant' }}">
                                                                    {{ $homeScore }}
                                                                </span>
                                                                <span class="text-xs px-1 py-0.5 rounded text-center {{ $awayScore > $homeScore ? 'bg-primary/20 text-primary font-bold' : 'text-on-surface-variant' }}">
                                                                    {{ $awayScore }}
                                                                </span>
                                                            @else
                                                                <span class="text-xs px-1 py-0.5 rounded text-on-surface-variant/50 text-center">-</span>
                                                                <span class="text-xs px-1 py-0.5 rounded text-on-surface-variant/50 text-center">-</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endfor
                                                </div>
                                                @else
                                                <div class="text-center text-xs text-on-surface-variant">
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

            const activeClasses = ['bg-primary', 'text-on-primary'];
            const inactiveClasses = ['text-on-surface-variant'];

            standingsTab.classList.remove(...activeClasses, ...inactiveClasses);
            standingsTab.classList.add(...(tab === 'standings' ? activeClasses : inactiveClasses));
            matchesTab.classList.remove(...activeClasses, ...inactiveClasses);
            matchesTab.classList.add(...(tab === 'matches' ? activeClasses : inactiveClasses));
        }

        function showTournamentTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Reset all tab buttons to inactive state
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('bg-primary', 'text-on-primary');
                button.classList.add('text-on-surface-variant');
            });

            // Show selected tab content
            document.getElementById(tabName + '-content').classList.remove('hidden');

            // Set active state for selected tab
            const activeTab = document.getElementById(tabName + '-tab');
            activeTab.classList.remove('text-on-surface-variant');
            activeTab.classList.add('bg-primary', 'text-on-primary');
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
