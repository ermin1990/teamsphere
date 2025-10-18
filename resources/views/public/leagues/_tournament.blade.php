<!-- Tournament Groups with Tables and Matches -->
@if(($competition->status === 'active' || $competition->status === 'completed') && $competition->type === 'tournament')
    @php
        $knockoutMatches = App\Models\CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->with(['homePlayer', 'awayPlayer'])
            ->orderBy('round_number')
            ->orderBy('id')
            ->get()
            ->groupBy('round_number');

        $groupMatches = App\Models\CompetitionMatch::where('competition_id', $competition->id)
            ->whereNotNull('tournament_group_id')
            ->with(['homePlayer', 'awayPlayer', 'tournamentGroup'])
            ->orderBy('tournament_group_id')
            ->orderBy('id')
            ->get()
            ->groupBy('tournament_group_id');

        // Determine active phase
        $hasActiveGroupMatches = $groupMatches->flatten()->where('status', '!=', 'completed')->count() > 0;
        $hasKnockoutMatches = $knockoutMatches->count() > 0;
        $activePhase = $hasActiveGroupMatches ? 'groups' : ($hasKnockoutMatches ? 'knockout' : 'groups');

        // Get all live matches
        $allMatches = collect();
        $allMatches = $allMatches->merge($groupMatches->flatten());
        $allMatches = $allMatches->merge($knockoutMatches->flatten());
        $liveMatches = $allMatches->where('status', 'in_progress');
    @endphp

    <!-- Tournament Tabs -->
    <div class="mb-6 md:mb-8">
        <div class="border-b border-gray-700">
            <nav class="-mb-px flex space-x-6 md:space-x-8">
                <button onclick="showTournamentTab('groups')" id="groups-tab"
                        class="tab-button border-b-2 py-2 px-1 text-sm md:text-base font-medium transition-colors
                        {{ $activePhase === 'groups' ? 'border-blue-500 text-blue-400' : 'border-transparent text-gray-400 hover:text-gray-300' }}">
                    🏆 Grupna faza
                </button>
                @if($hasKnockoutMatches)
                <button onclick="showTournamentTab('knockout')" id="knockout-tab"
                        class="tab-button border-b-2 py-2 px-1 text-sm md:text-base font-medium transition-colors
                        {{ $activePhase === 'knockout' ? 'border-blue-500 text-blue-400' : 'border-transparent text-gray-400 hover:text-gray-300' }}">
                    🏅 Eliminaciona faza
                </button>
                @endif
            </nav>
        </div>

        <!-- Groups Tab Content -->
        <div id="groups-content" class="tab-content mt-4 md:mt-6 {{ $activePhase !== 'groups' ? 'hidden' : '' }}">
            @if($competition->tournamentGroups->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                @foreach($competition->tournamentGroups as $group)
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
                                    <span class="text-white font-medium truncate">{{ $standing->player->name }}</span>
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
                    @php
                        $currentGroupMatches = $groupMatches->get($group->id) ?? collect();
                    @endphp
                    @if($currentGroupMatches->count() > 0)
                    <div>
                        <h5 class="text-sm md:text-base font-semibold text-gray-300 mb-2 uppercase tracking-wide">Mečevi</h5>
                        <div class="space-y-1 md:space-y-3">
                            @foreach($currentGroupMatches as $match)
                            <a href="{{ route('public.matches.show', [$competition, $match]) }}"
                               class="block bg-gray-700/20 hover:bg-gray-700/40 rounded-md p-4 transition-all duration-200 hover:scale-[1.01]">
                                @if($match->status === 'in_progress')
                                <div class="text-center mb-2">
                                    <span class="text-red-400 font-semibold text-xs uppercase tracking-wider">Live</span>
                                </div>
                                @endif
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
                                                    {{ $match->homePlayer->name ?? 'Home Player' }}
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
                                                    {{ $match->awayPlayer->name ?? 'Away Player' }}
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

                                @if($match->played_at)
                                <div class="text-center text-xs md:text-sm text-gray-500 mt-1 md:mt-2">
                                    {{ $match->played_at->format('d.m. H:i') }}
                                </div>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 md:p-12 border border-gray-700/50 shadow-xl text-center">
                <div class="text-4xl md:text-6xl mb-4">🏆</div>
                <h3 class="text-lg md:text-xl font-semibold text-white mb-2">Nema grupa još</h3>
                <p class="text-gray-400 text-sm md:text-base">Grupe će se pojaviti kada turnir počne.</p>
            </div>
            @endif
        </div>

        <!-- Knockout Tab Content -->
        @if($hasKnockoutMatches)
        <div id="knockout-content" class="tab-content mt-4 md:mt-6 {{ $activePhase !== 'knockout' ? 'hidden' : '' }}">
            @php
                $totalRounds = $knockoutMatches->count();
                $firstRoundMatches = $knockoutMatches->get(1) ?? collect();
                $numPlayers = $firstRoundMatches->count() * 2;
                $expectedRounds = $numPlayers > 1 ? ceil(log($numPlayers, 2)) : 1;

                $roundNames = [
                    1 => $expectedRounds == 4 ? 'Šesnaestina finala' : ($expectedRounds == 3 ? 'Četvrtfinale' : ($expectedRounds == 2 ? 'Polufinale' : 'Runda 1')),
                    2 => $expectedRounds == 4 ? 'Četvrtfinale' : ($expectedRounds == 3 ? 'Polufinale' : ($expectedRounds == 2 ? 'Finale' : 'Runda 2')),
                    3 => $expectedRounds == 4 ? 'Polufinale' : 'Finale',
                    4 => 'Finale',
                ];

                // Check if tournament is completed and get winner
                $finalMatch = $knockoutMatches->get($totalRounds)?->first();
                $winner = null;
                if ($finalMatch && $finalMatch->status === 'completed' && $totalRounds == $expectedRounds) {
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

            <div class="space-y-4 md:space-y-6">
                @foreach($knockoutMatches->sortKeysDesc() as $roundNumber => $roundMatches)
                <div class="bg-gray-800/30 backdrop-blur-xl rounded-xl p-4 md:p-6 border border-gray-700/30 shadow-xl">
                    <h4 class="text-sm md:text-lg font-bold text-center mb-3 md:mb-4 text-white uppercase tracking-wider">
                        {{ $roundNames[$roundNumber] ?? 'Runda ' . $roundNumber }}
                    </h4>
                    <div class="space-y-2 md:space-y-3">
                        @foreach($roundMatches as $match)
                        <a href="{{ route('public.matches.show', [$competition, $match]) }}"
                           class="block bg-gray-700/20 hover:bg-gray-700/40 rounded-md p-4 transition-all duration-200 hover:scale-[1.01]">
                            @if($match->status === 'in_progress' && !$match->is_bye)
                            <div class="text-center mb-2">
                                <span class="text-red-400 font-semibold text-xs uppercase tracking-wider">Live</span>
                            </div>
                            @endif
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
                                                {{ $match->homePlayer->name ?? 'NEMA PROTIVNIKA' }}
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
                                                {{ $match->awayPlayer->name ?? 'NEMA PROTIVNIKA' }}
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
                                    @if($match->status === 'in_progress' && !$match->is_bye)
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
                                    @elseif($match->is_bye)
                                        <div class="flex flex-col items-center space-y-1">
                                            <div class="w-8 h-8 bg-gray-700/50 rounded-lg flex items-center justify-center">
                                                <div class="text-sm font-bold text-gray-500">bye</div>
                                            </div>
                                            <div class="w-8 h-8 bg-gray-700/50 rounded-lg flex items-center justify-center">
                                                <div class="text-sm font-bold text-gray-500">-</div>
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

                            @if($match->played_at)
                            <div class="text-center text-xs md:text-sm text-gray-500 mt-1 md:mt-2">
                                {{ $match->played_at->format('d.m. H:i') }}
                            </div>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
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
    </script>
@endif