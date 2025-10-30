<!-- Tournament Groups with Tables and Matches -->
@if($competition->type === 'tournament')
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
    @endphp

    <!-- Tournament Tabs -->
    <div class="mb-6 md:mb-8">
        <div class="border-b border-gray-700">
            <nav class="-mb-px flex space-x-6 md:space-x-8">
                @if($showGroupsTab)
                <button onclick="showTournamentTab('groups')" id="groups-tab"
                        class="tab-button border-b-2 py-2 px-1 text-sm md:text-base font-medium transition-colors
                        {{ $showGroupsTab ? 'border-blue-500 text-blue-400' : 'border-transparent text-gray-400 hover:text-gray-300' }}">
                    🏆 Grupna faza
                </button>
                @endif
                @if($showKnockoutTab)
                <button onclick="showTournamentTab('knockout')" id="knockout-tab"
                        class="tab-button border-b-2 py-2 px-1 text-sm md:text-base font-medium transition-colors
                        {{ !$showGroupsTab && $showKnockoutTab ? 'border-blue-500 text-blue-400' : 'border-transparent text-gray-400 hover:text-gray-300' }}">
                    🏅 Eliminaciona faza
                </button>
                @endif
            </nav>
        </div>

        <!-- Groups Tab Content -->
        <div id="groups-content" class="tab-content mt-4 md:mt-6 {{ !$showGroupsTab ? 'hidden' : '' }}">
            @if($hasGroupMatches)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                @foreach($competition->tournamentGroups as $group)
                @php
                    $currentGroupMatches = $groupMatches->get($group->id) ?? collect();
                @endphp
                @if($currentGroupMatches->count() > 0)
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-3 md:p-5 border border-gray-700/50 shadow-xl">
                    <h4 class="text-base md:text-lg font-bold text-white mb-3 md:mb-4">{{ $group->name }}</h4>

                    <!-- Group Standings -->
                    @php
                        $groupStandings = $group->standings()->with('player')->orderBy('position')->get();
                    @endphp
                    @if($groupStandings->count() > 0)
                    <div class="mb-4">
                        <h5 class="text-sm md:text-base font-semibold text-gray-300 mb-2 uppercase tracking-wide">Tabela</h5>

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
                            @foreach($groupStandings as $standing)
                            <div class="grid grid-cols-12 gap-2 items-center py-2 px-2 bg-gray-700/20 rounded text-xs md:text-sm">
                                <div class="col-span-6 flex items-center space-x-2">
                                    <span class="font-bold text-gray-400 w-6 text-center">{{ $standing->position }}</span>
                                    <span class="text-white font-medium text-xs truncate">{{ $standing->player->name }}</span>
                                </div>
                                <div class="col-span-1 text-center">
                                    <span class="text-green-400 font-bold">{{ $standing->won ?? 0 }}</span>
                                </div>
                                <div class="col-span-1 text-center">
                                    <span class="text-yellow-400 font-bold">{{ $standing->drawn ?? 0 }}</span>
                                </div>
                                <div class="col-span-1 text-center">
                                    <span class="text-red-400 font-bold">{{ $standing->lost ?? 0 }}</span>
                                </div>
                                <div class="col-span-1 text-center">
                                    <span class="text-cyan-400 font-bold">{{ ($standing->sets_won ?? 0) - ($standing->sets_lost ?? 0) }}</span>
                                </div>
                                <div class="col-span-2 text-center">
                                    <span class="text-blue-400 font-bold">{{ $standing->points ?? 0 }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Group Matches -->
                    @if($currentGroupMatches->count() > 0)
                    <div>
                        <h5 class="text-sm md:text-base font-semibold text-gray-300 mb-2 uppercase tracking-wide">Mečevi</h5>
                        <div class="space-y-1 md:space-y-3">
                            @foreach($currentGroupMatches as $match)
                            <a href="{{ route('public.matches.show', [$competition, $match]) }}"
                               class="block bg-gray-700/20 hover:bg-gray-700/40 rounded-md transition-all duration-200 hover:scale-[1.01]">
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
                                            <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                                @if($match->status === 'completed' || $match->status === 'in_progress')
                                                    {{ $homeSetsWon }}
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </div>
                                            <div class="text-sm font-semibold text-white truncate">
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
                                                <div class="w-8 h-8 bg-gray-700/50 rounded-lg flex items-center justify-center">
                                                    <div class="text-sm font-bold text-gray-500">-</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <!-- Sets won indicator -->
                                            <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                                @if($match->status === 'completed' || $match->status === 'in_progress')
                                                    {{ $awaySetsWon }}
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </div>
                                            <div class="text-sm font-semibold text-white truncate">
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
                                                <div class="w-8 h-8 bg-gray-700/50 rounded-lg flex items-center justify-center">
                                                    <div class="text-sm font-bold text-gray-500">-</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Mobile Set Display -->
                                    @if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0)
                                    <div class="mt-3 pt-3 border-t border-gray-600/30">
                                        <div class="flex justify-center gap-1">
                                            @php
                                                $displaySets = isset($match->sets) && is_array($match->sets) && count($match->sets) > 0 ? $match->sets : [];
                                            @endphp
                                            @for($i = 1; $i <= 5; $i++)
                                            <div class="flex flex-col items-center">
                                                <div class="text-xs text-gray-400 mb-1">{{ $i }}</div>
                                                <div class="flex gap-1">
                                                    @if(isset($displaySets[$i-1]))
                                                        @php
                                                            $homeScore = $displaySets[$i-1]['home_score'] ?? $displaySets[$i-1]['home'] ?? 0;
                                                            $awayScore = $displaySets[$i-1]['away_score'] ?? $displaySets[$i-1]['away'] ?? 0;
                                                        @endphp
                                                        <span class="text-xs px-1 py-0.5 rounded {{ $homeScore > $awayScore ? 'bg-green-900/60 text-green-300 font-bold' : 'text-gray-400' }}">
                                                            {{ $homeScore }}
                                                        </span>
                                                        <span class="text-xs px-1 py-0.5 rounded {{ $awayScore > $homeScore ? 'bg-green-900/60 text-green-300 font-bold' : 'text-gray-400' }}">
                                                            {{ $awayScore }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs px-1 py-0.5 rounded text-gray-600">-</span>
                                                        <span class="text-xs px-1 py-0.5 rounded text-gray-600">-</span>
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
                                                <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                                    @if($match->status === 'completed' || $match->status === 'in_progress')
                                                        {{ $homeSetsWon }}
                                                    @else
                                                        <span class="text-gray-500">-</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs md:text-sm font-semibold text-white truncate flex-1 min-w-0">
                                                    {{ $match->homePlayer->name ?? 'Home Player' }}
                                                </div>
                                                <!-- Sets -->
                                                <div class="flex gap-1 ml-4">
                                                    @php
                                                        $displaySets = isset($match->sets) && is_array($match->sets) && count($match->sets) > 0 ? $match->sets : [];
                                                    @endphp
                                                    @for($i = 1; $i <= 5; $i++)
                                                    <div class="w-6 text-center {{ $i < 5 ? 'border-r border-gray-600/30' : '' }}">
                                                        @if(isset($displaySets[$i-1]))
                                                            @php
                                                                $homeScore = $displaySets[$i-1]['home_score'] ?? $displaySets[$i-1]['home'] ?? 0;
                                                                $awayScore = $displaySets[$i-1]['away_score'] ?? $displaySets[$i-1]['away'] ?? 0;
                                                            @endphp
                                                            <span class="text-xs px-1 py-0.5 rounded {{ $homeScore > $awayScore ? 'bg-green-900/60 text-green-300 font-bold' : 'text-gray-400' }}">
                                                                {{ $homeScore }}
                                                            </span>
                                                        @else
                                                            <span class="text-xs px-1 py-0.5 rounded text-gray-600">-</span>
                                                        @endif
                                                    </div>
                                                    @endfor
                                                </div>
                                            </div>

                                            <!-- Away Player -->
                                            <div class="flex items-center gap-3">
                                                <!-- Sets won indicator -->
                                                <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                                    @if($match->status === 'completed' || $match->status === 'in_progress')
                                                        {{ $awaySetsWon }}
                                                    @else
                                                        <span class="text-gray-500">-</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs md:text-sm font-semibold text-white truncate flex-1 min-w-0">
                                                    {{ $match->awayPlayer->name ?? 'Away Player' }}
                                                </div>
                                                <!-- Sets -->
                                                <div class="flex gap-1 ml-4">
                                                    @for($i = 1; $i <= 5; $i++)
                                                    <div class="w-6 text-center {{ $i < 5 ? 'border-r border-gray-600/30' : '' }}">
                                                        @if(isset($displaySets[$i-1]))
                                                            @php
                                                                $homeScore = $displaySets[$i-1]['home_score'] ?? $displaySets[$i-1]['home'] ?? 0;
                                                                $awayScore = $displaySets[$i-1]['away_score'] ?? $displaySets[$i-1]['away'] ?? 0;
                                                            @endphp
                                                            <span class="text-xs px-1 py-0.5 rounded {{ $awayScore > $homeScore ? 'bg-green-900/60 text-green-300 font-bold' : 'text-gray-400' }}">
                                                                {{ $awayScore }}
                                                            </span>
                                                        @else
                                                            <span class="text-xs px-1 py-0.5 rounded text-gray-600">-</span>
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
                                                    <div class="w-10 h-10 bg-gray-700/50 rounded-lg flex items-center justify-center">
                                                        <div class="text-sm font-bold text-gray-500">-</div>
                                                    </div>
                                                    <div class="w-10 h-10 bg-gray-700/50 rounded-lg flex items-center justify-center">
                                                        <div class="text-sm font-bold text-gray-500">-</div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                @endforeach
            </div>
            @else
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 md:p-12 border border-gray-700/50 shadow-xl text-center">
                <div class="text-4xl md:text-6xl mb-4">🏆</div>
                <h3 class="text-lg md:text-xl font-semibold text-white mb-2">Nema grupa još</h3>
                <p class="text-gray-400 text-sm md:text-base">Grupe će se pojaviti kada turnir počne.</p>
            </div>
            @endif
        </div>        <!-- Knockout Tab Content -->
        @if($hasKnockoutMatches)
        <div id="knockout-content" class="tab-content mt-4 md:mt-6 {{ !$showKnockoutTab ? 'hidden' : '' }}">
            @php
                $totalRounds = $knockoutMatches->count();
                $firstRoundMatches = $knockoutMatches->get(1) ?? collect();
                $numPlayers = $firstRoundMatches->count() * 2;

                // Determine round names based on total rounds and position
                $roundNames = [];
                for ($round = 1; $round <= $totalRounds; $round++) {
                    if ($round === $totalRounds) {
                        // Last round is always Finale
                        $roundNames[$round] = 'Finale';
                    } else {
                        // Regular round names based on total rounds
                        if ($totalRounds >= 5) {
                            // 16+ players tournament
                            $regularNames = [
                                1 => '16/1 Finala',
                                2 => '1/8 Finala',
                                3 => '1/4 Finala',
                                4 => 'Polufinale',
                            ];
                        } elseif ($totalRounds >= 4) {
                            // 8+ players tournament
                            $regularNames = [
                                1 => '1/8 Finala',
                                2 => '1/4 Finala',
                                3 => 'Polufinale',
                            ];
                        } elseif ($totalRounds >= 3) {
                            // 4+ players tournament
                            $regularNames = [
                                1 => '1/4 Finala',
                                2 => 'Polufinale',
                            ];
                        } else {
                            // 2+ players tournament
                            $regularNames = [
                                1 => 'Polufinale',
                            ];
                        }
                        $roundNames[$round] = $regularNames[$round] ?? 'Runda ' . $round;
                    }
                }

                // Check if tournament is completed and get winner
                $finalMatch = $knockoutMatches->get($totalRounds)?->first();
                $winner = null;
                if ($finalMatch && $finalMatch->status === 'completed') {
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
                    <p class="text-xl md:text-2xl font-black text-white mb-2" style="font-family: 'Inter', sans-serif; letter-spacing: -0.02em;">
                        {{ $winner->name }}
                    </p>
                    <p class="text-sm md:text-base text-gray-400 font-medium">{{ $competition->name }}</p>
                </div>
            </div>
            @endif

            <!-- Tournament Bracket -->
            <div class="bg-gray-800/30 backdrop-blur-xl rounded-xl p-4 md:p-6 border border-gray-700/30 shadow-xl">
                <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
                    <div class="min-w-max">
                        <!-- Bracket Container -->
                        <div class="flex gap-4 md:gap-8 lg:gap-12 justify-center">
                            @for($round = 1; $round <= $totalRounds; $round++)
                            @php
                                $roundMatches = $knockoutMatches->get($round) ?? collect();
                                // Calculate spacing for bracket alignment
                                $matchesInRound = $roundMatches->count();
                                $spacingMultiplier = pow(2, $round - 1);
                            @endphp
                            @if($matchesInRound > 0)
                            <div class="flex flex-col justify-center gap-2" style="gap: {{ $spacingMultiplier * 1 }}rem;">
                                <!-- Round Header -->
                                <div class="text-center mb-4">
                                    <h4 class="text-sm md:text-base font-bold text-white uppercase tracking-wider">
                                        {{ $roundNames[$round] ?? 'Runda ' . $round }}
                                    </h4>
                                </div>

                                <!-- Round Matches -->
                                <div class="flex flex-col gap-2">
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

                                    <div class="block bg-gray-700/20 hover:bg-gray-700/40 rounded-lg transition-all duration-200 hover:scale-[1.02] border border-gray-600/30"
                                         data-match-id="{{ $match->id }}">
                                        @if($match->status === 'in_progress' && !$match->is_bye)
                                        <div class="text-center mb-2">
                                            <span class="text-red-400 font-semibold text-xs uppercase tracking-wider">Live</span>
                                        </div>
                                        @endif

                                        <!-- Match Header with Toggle -->
                                        <div class="flex items-center justify-between p-3 md:p-4">
                                            <a href="{{ route('public.matches.show', [$competition, $match]) }}"
                                               class="flex-1 text-xs text-gray-400 hover:text-gray-300">
                                                Detalji meča
                                            </a>
                                        </div>

                                        <!-- Match Players -->
                                        <div class="px-3 md:px-4 pb-3 md:pb-4">
                                            <!-- Home Player -->
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                                    <div class="w-6 h-6 rounded bg-white/20 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                                        @if($match->status === 'completed' || ($homeSetsWon > 0 || $awaySetsWon > 0))
                                                            {{ $homeSetsWon }}
                                                        @elseif($match->status === 'in_progress')
                                                            <span class="text-green-400">{{ $homeSetsWon }}</span>
                                                        @else
                                                            <span class="text-gray-500">0</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs md:text-sm font-semibold text-white truncate">
                                                        {{ $match->homePlayer->name ?? 'NEMA PROTIVNIKA' }}
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ml-2">
                                                    @if($match->status === 'in_progress' && !$match->is_bye)
                                                        <div class="w-6 h-6 bg-green-900/80 rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-green-300">
                                                                {{ $homeFinalScore }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->status === 'completed' || ($homeSetsWon > 0 || $awaySetsWon > 0))
                                                        <div class="w-6 h-6 bg-green-900/80 rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-green-300">
                                                                {{ $homeFinalScore ?: $homeSetsWon }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->is_bye)
                                                        <div class="w-6 h-6 bg-gray-700/50 rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-gray-500">bye</div>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-gray-700/50 rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-gray-500">-</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Away Player -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                                    <div class="w-6 h-6 rounded bg-white/20 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                                        @if($match->status === 'completed' || ($homeSetsWon > 0 || $awaySetsWon > 0))
                                                            {{ $awaySetsWon }}
                                                        @elseif($match->status === 'in_progress')
                                                            <span class="text-green-400">{{ $awaySetsWon }}</span>
                                                        @else
                                                            <span class="text-gray-500">0</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs md:text-sm font-semibold text-white truncate">
                                                        {{ $match->awayPlayer->name ?? 'NEMA PROTIVNIKA' }}
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ml-2">
                                                    @if($match->status === 'in_progress' && !$match->is_bye)
                                                        <div class="w-6 h-6 bg-green-900/80 rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-green-300">
                                                                {{ $awayFinalScore }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->status === 'completed' || ($homeSetsWon > 0 || $awaySetsWon > 0))
                                                        <div class="w-6 h-6 bg-green-900/80 rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-green-300">
                                                                {{ $awayFinalScore ?: $awaySetsWon }}
                                                            </div>
                                                        </div>
                                                    @elseif($match->is_bye)
                                                        <div class="w-6 h-6 bg-gray-700/50 rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-gray-500">bye</div>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-gray-700/50 rounded flex items-center justify-center">
                                                            <div class="text-xs font-bold text-gray-500">-</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Toggle Sets Button -->
                                            @if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0)
                                            <div class="mt-3 text-center">
                                                <button onclick="toggleMatchSets({{ $match->id }})"
                                                        class="text-xs text-gray-400 hover:text-white transition-colors flex items-center justify-center gap-1 mx-auto">
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
                                        <div id="sets-{{ $match->id }}" class="hidden px-3 md:px-4 pb-3 md:pb-4 border-t border-gray-600/30">
                                            <div class="mt-3">
                                                @if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0)
                                                <div class="flex justify-center gap-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                    <div class="flex flex-col items-center">
                                                        <div class="text-xs text-gray-400 mb-1">{{ $i }}</div>
                                                        <div class="flex flex-col gap-0.5">
                                                            @if(isset($match->sets[$i-1]))
                                                                @php
                                                                    $homeScore = $match->sets[$i-1]['home_score'] ?? $match->sets[$i-1]['home'] ?? 0;
                                                                    $awayScore = $match->sets[$i-1]['away_score'] ?? $match->sets[$i-1]['away'] ?? 0;
                                                                @endphp
                                                                <span class="text-xs px-1 py-0.5 rounded text-center {{ $homeScore > $awayScore ? 'bg-green-900/60 text-green-300 font-bold' : 'text-gray-400' }}">
                                                                    {{ $homeScore }}
                                                                </span>
                                                                <span class="text-xs px-1 py-0.5 rounded text-center {{ $awayScore > $homeScore ? 'bg-green-900/60 text-green-300 font-bold' : 'text-gray-400' }}">
                                                                    {{ $awayScore }}
                                                                </span>
                                                            @else
                                                                <span class="text-xs px-1 py-0.5 rounded text-gray-600 text-center">-</span>
                                                                <span class="text-xs px-1 py-0.5 rounded text-gray-600 text-center">-</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endfor
                                                </div>
                                                @else
                                                <div class="text-center text-xs text-gray-400">
                                                    Detalji o setovima nisu dostupni
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Bracket Lines (except for the last round) -->
                            @php
                                // Check if there's a next round with matches
                                $hasNextRound = false;
                                for($nextRound = $round + 1; $nextRound <= $totalRounds; $nextRound++) {
                                    $nextRoundMatches = $knockoutMatches->get($nextRound) ?? collect();
                                    if($nextRoundMatches->count() > 0) {
                                        $hasNextRound = true;
                                        break;
                                    }
                                }
                            @endphp
                            @if($hasNextRound)
                            <div class="flex items-center justify-center" style="margin-top: {{ $spacingMultiplier * 0.5 }}rem; margin-bottom: {{ $spacingMultiplier * 0.5 }}rem;">
                                <div class="w-8 h-px bg-gray-600/50"></div>
                            </div>
                            @endif
                            @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script>
        function showTournamentTab(tabName) {
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
    </script>
@endif