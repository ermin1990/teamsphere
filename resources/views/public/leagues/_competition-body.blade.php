{{--
    Shared competition body (hero, standings table, schedule, stats/organization
    sidebar, or tournament groups/knockout) - used by both the public spectator
    page (public.leagues.show) and the player's own view of a competition
    (player.leagues.show). Expects $competition, $organization,
    $playerGroupSeeding, $playerPositionSeeding.
--}}
@php
    $sportIcon = function ($sport) {
        $n = mb_strtolower($sport?->name ?? '');
        if (str_contains($n, 'fudbal')) return 'sports_soccer';
        if (str_contains($n, 'košark') || str_contains($n, 'kosark')) return 'sports_basketball';
        if (str_contains($n, 'odbojk')) return 'sports_volleyball';
        return 'sports_tennis';
    };
    $isTournament = $competition->type === 'tournament';
    $advancingPlayers = $competition->players_advancing_per_group ?? 0;
    $fmtDiff = fn ($n) => ($n > 0 ? '+' : '') . $n;

    if (!$isTournament) {
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

        $sortedStandings = $competition->standings->sortByDesc('points')->values();
        $totalMatches = $leagueMatches->count();
        $completedMatches = $leagueMatches->whereIn('status', ['completed', 'forfeited'])->count();
        $leader = $sortedStandings->first();
        $bestDiffPlayerId = collect($diffByPlayer)->sortByDesc('s')->keys()->first();
        $bestDiffStanding = $bestDiffPlayerId ? $sortedStandings->firstWhere('player_id', $bestDiffPlayerId) : null;
    }
@endphp

<!-- Hero -->
<section class="-mx-margin-mobile lg:mx-0 mb-6 lg:mb-10 bg-surface-container-low lg:p-8 border-y lg:border border-outline-variant lg:rounded-xl relative overflow-hidden">
    <div class="absolute top-0 right-0 p-6 lg:p-8 opacity-10 hidden sm:block">
        <span class="material-symbols-outlined text-[80px] lg:text-[120px]">{{ $sportIcon($competition->sport) }}</span>
    </div>
    <div class="relative z-10 px-margin-mobile py-5 lg:p-0">
        <div class="flex flex-wrap items-center gap-2 mb-3 lg:mb-4">
            <span class="bg-primary/20 text-primary px-3 py-1 rounded-full text-label-bold uppercase">
                @if($competition->status === 'completed') Završeno
                @elseif($competition->status === 'in_progress') U toku
                @else Zakazano @endif
            </span>
            <span class="bg-secondary/20 text-secondary px-3 py-1 rounded-full text-label-bold uppercase">{{ $isTournament ? 'Turnir' : 'Liga' }}</span>
            @if($competition->sets_to_win)
                <span class="bg-surface-container-highest text-on-surface-variant px-3 py-1 rounded-full text-label-bold uppercase">Do {{ $competition->sets_to_win }} dobijena</span>
            @endif
        </div>
        <h1 class="font-display text-3xl lg:text-display mb-2 truncate">{{ $competition->name }}</h1>
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-on-surface-variant text-sm lg:text-body-md">
            <a href="{{ route('competitions.organization', $organization) }}" class="flex items-center gap-1 hover:text-primary transition-colors"><span class="material-symbols-outlined text-body-sm">group</span> {{ $organization->name }}</a>
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-body-sm">{{ $sportIcon($competition->sport) }}</span> {{ $competition->sport->name }}</span>
            @if($competition->city)
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-body-sm">location_on</span> {{ $competition->city->name }}</span>
            @endif
        </div>
    </div>
</section>

@if($competition->description || $competition->location || $competition->organizer_contact || $competition->entry_fee)
<section class="-mx-margin-mobile lg:mx-0 mb-6 lg:mb-8 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl px-margin-mobile py-4 lg:p-5 space-y-2">
    @if($competition->description)
        <p class="text-on-surface-variant text-sm">{{ $competition->description }}</p>
    @endif
    <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm text-on-surface-variant">
        @if($competition->location)<span>📍 {{ $competition->location }}</span>@endif
        @if($competition->entry_fee)<span>💳 {{ $competition->entry_fee }}</span>@endif
        @if($competition->organizer_contact)<span>☎️ {{ $competition->organizer_contact }}</span>@endif
    </div>
</section>
@endif

@if($isTournament)
    <!-- Tournament: reuse the existing group/knockout renderer, wrapped in the new shell -->
    <div id="standings-section">
        @include('public.leagues._tournament')
    </div>
@else
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
        <!-- Left: Standings + Schedule -->
        <div class="xl:col-span-8 space-y-6 lg:space-y-8">
            <section id="standings-section" class="-mx-margin-mobile lg:mx-0 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl overflow-hidden lg:shadow-2xl">
                <div class="px-margin-mobile py-4 lg:p-6 border-b border-outline-variant">
                    <h2 class="font-headline-md">Tabela</h2>
                </div>
                @if($sortedStandings->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead class="bg-surface-container-lowest text-on-surface-variant text-label-bold uppercase">
                                <tr>
                                    <th class="px-3 lg:px-4 py-2.5 lg:py-3">#</th>
                                    <th class="px-2 lg:px-4 py-2.5 lg:py-3">{{ $competition->is_team_based ? 'Ekipa' : 'Igrač' }}</th>
                                    <th class="hidden md:table-cell px-2 py-2.5 lg:py-3 text-center">M</th>
                                    <th class="px-2 py-2.5 lg:py-3 text-center">P</th>
                                    <th class="px-2 py-2.5 lg:py-3 text-center">I</th>
                                    <th class="px-2 py-2.5 lg:py-3 text-center">S</th>
                                    <th class="hidden sm:table-cell px-2 py-2.5 lg:py-3 text-center">G</th>
                                    <th class="px-3 lg:px-4 py-2.5 lg:py-3 text-center text-primary">Bod</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant">
                                @foreach($sortedStandings as $index => $standing)
                                    @php
                                        $pid = $standing->player_id;
                                        $d = ($pid && isset($diffByPlayer[$pid])) ? $diffByPlayer[$pid] : null;
                                        $sd = $d['s'] ?? null; $gd = $d['g'] ?? null;
                                        $played = ($standing->won ?? 0) + ($standing->drawn ?? 0) + ($standing->lost ?? 0);
                                        $advancing = $advancingPlayers > 0 && $index < $advancingPlayers;
                                        $clubName = !$competition->is_team_based ? ($standing->player->organization->name ?? null) : null;
                                    @endphp
                                    <tr class="transition-colors group {{ $advancing ? 'bg-primary/5' : 'hover:bg-surface-variant/30' }}">
                                        <td class="px-3 lg:px-4 py-2 lg:py-2.5 font-bold {{ $index < 3 ? 'text-primary' : '' }}">{{ $index + 1 }}</td>
                                        <td class="px-2 lg:px-4 py-2 lg:py-2.5">
                                            <span class="font-semibold group-hover:text-primary transition-colors truncate block">{{ $standing->participant_name }}</span>
                                            @if($clubName)
                                                <span class="text-xs text-on-surface-variant truncate block">{{ $clubName }}</span>
                                            @endif
                                        </td>
                                        <td class="hidden md:table-cell px-2 py-2 lg:py-2.5 text-center">{{ $played }}</td>
                                        <td class="px-2 py-2 lg:py-2.5 text-center text-primary font-bold">{{ $standing->won ?? 0 }}</td>
                                        <td class="px-2 py-2 lg:py-2.5 text-center text-error">{{ $standing->lost ?? 0 }}</td>
                                        <td class="px-2 py-2 lg:py-2.5 text-center {{ is_null($sd) ? '' : ($sd > 0 ? 'text-primary' : ($sd < 0 ? 'text-error' : '')) }} font-bold">{{ is_null($sd) ? '–' : $fmtDiff($sd) }}</td>
                                        <td class="hidden sm:table-cell px-2 py-2 lg:py-2.5 text-center {{ is_null($gd) ? '' : ($gd > 0 ? 'text-primary' : ($gd < 0 ? 'text-error' : '')) }}">{{ is_null($gd) ? '–' : $fmtDiff($gd) }}</td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-2.5 text-center"><span class="{{ $index < 3 ? 'bg-primary text-on-primary' : 'bg-surface-container-high text-on-surface-variant' }} px-2.5 py-1 rounded font-bold text-xs">{{ $standing->points ?? 0 }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10 text-on-surface-variant text-sm">Tabela će se pojaviti kada liga počne.</div>
                @endif
            </section>

            <section id="schedule-section">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-headline-md flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">event_available</span> Raspored i Rezultati
                    </h2>
                </div>
                @if($matchesByRound->count() > 0)
                    <div class="space-y-4">
                        @foreach($matchesByRound as $round => $roundMatches)
                            @php $roundFinished = $roundMatches->every(fn ($m) => in_array($m->status, ['completed', 'forfeited'])); @endphp
                            <details class="round" open>
                                <summary class="flex items-center gap-4 mb-4 cursor-pointer select-none">
                                    <span class="bg-surface-container-highest px-4 py-1.5 rounded-lg font-bold border border-outline-variant">Kolo {{ $round }}</span>
                                    @if($roundFinished)<span class="text-[10px] px-2 py-1 rounded-full font-bold uppercase bg-primary/15 text-primary">Završeno</span>@endif
                                    <div class="flex-1 h-px bg-outline-variant"></div>
                                    <span class="material-symbols-outlined round-chevron text-on-surface-variant transition-transform">expand_more</span>
                                </summary>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    @foreach($roundMatches->sortBy('scheduled_at') as $match)
                                        @php
                                            $isTeamMatch = $competition->is_team_based;
                                            $mHomeName = $isTeamMatch ? ($match->homeTeam->name ?? 'Domaći') : ($match->homePlayer->name ?? 'Domaći');
                                            $mAwayName = $isTeamMatch ? ($match->awayTeam->name ?? 'Gost') : ($match->awayPlayer->name ?? 'Gost');
                                            $mCompleted = in_array($match->status, ['completed', 'forfeited']);
                                            $mLive = $match->status === 'in_progress';
                                            $mHomeWin = $mCompleted && $match->home_score > $match->away_score;
                                            $mAwayWin = $mCompleted && $match->away_score > $match->home_score;
                                            $mSets = collect($match->sets ?? []);
                                            $mHomeSets = $mSets->map(fn ($s) => $s['home'] ?? $s['home_score'] ?? $s['p1'] ?? null)->filter(fn ($v) => $v !== null)->implode(', ');
                                            $mAwaySets = $mSets->map(fn ($s) => $s['away'] ?? $s['away_score'] ?? $s['p2'] ?? null)->filter(fn ($v) => $v !== null)->implode(', ');
                                            $mVenue = !$isTeamMatch ? $match->venue : null;
                                        @endphp
                                        <div class="bg-surface-container-low p-4 lg:p-5 rounded-xl transition-all-200 {{ $mCompleted ? 'border-l-4 border-primary rounded-r-xl' : ($mLive ? 'border-l-4 border-secondary rounded-r-xl glow-teal' : 'border border-outline-variant hover:border-primary/50') }}">
                                            <div class="flex justify-between items-center mb-4 text-label-bold text-on-surface-variant uppercase">
                                                @if($mCompleted)
                                                    <span>Završeno</span>
                                                @elseif($mLive)
                                                    <span class="text-secondary animate-pulse">Uživo</span>
                                                @else
                                                    <span class="text-secondary">Zakazano</span>
                                                @endif
                                                <span>
                                                    @if($match->played_at ?? $match->scheduled_at)
                                                        {{ optional($match->played_at ?? $match->scheduled_at)->format('d.m.Y. H:i') }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="space-y-3">
                                                <div class="flex justify-between items-center {{ $mAwayWin ? 'opacity-60' : '' }}">
                                                    <span class="font-medium truncate">{{ $mHomeName }}</span>
                                                    <span class="font-bold {{ $mHomeWin ? 'text-primary' : 'text-on-surface-variant' }} text-body-lg shrink-0">
                                                        {{ $mCompleted || $mLive ? $match->home_score : '-' }}
                                                        @if($mHomeSets)<span class="text-on-surface-variant text-body-sm font-normal ml-2">({{ $mHomeSets }})</span>@endif
                                                    </span>
                                                </div>
                                                <div class="flex justify-between items-center {{ $mHomeWin ? 'opacity-60' : '' }}">
                                                    <span class="font-medium truncate">{{ $mAwayName }}</span>
                                                    <span class="font-bold {{ $mAwayWin ? 'text-primary' : 'text-on-surface-variant' }} text-body-lg shrink-0">
                                                        {{ $mCompleted || $mLive ? $match->away_score : '-' }}
                                                        @if($mAwaySets)<span class="text-on-surface-variant text-body-sm font-normal ml-2">({{ $mAwaySets }})</span>@endif
                                                    </span>
                                                </div>
                                            </div>
                                            @if($mVenue)
                                                <div class="mt-4 pt-4 border-t border-outline-variant">
                                                    <span class="text-label-bold text-on-surface-variant flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">location_on</span> {{ $mVenue->name }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-on-surface-variant text-sm">Mečevi će se pojaviti kada liga počne.</div>
                @endif
            </section>
        </div>

        <!-- Right: Stats + Organization -->
        <aside class="xl:col-span-4 space-y-6 lg:space-y-8">
            <section id="stats-section" class="-mx-margin-mobile lg:mx-0 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl px-margin-mobile py-5 lg:p-6 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/10 rounded-full blur-2xl"></div>
                <h3 class="font-headline-md mb-6 flex items-center gap-2"><span class="material-symbols-outlined text-primary">insights</span> Statistika</h3>
                <div class="space-y-5">
                    @if($leader)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg bg-surface-container-highest flex items-center justify-center text-primary shrink-0"><span class="material-symbols-outlined">trending_up</span></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-label-bold text-on-surface-variant uppercase">Lider tabele</p>
                                <p class="font-bold truncate">{{ $leader->participant_name }} ({{ $leader->points ?? 0 }} bod.)</p>
                            </div>
                        </div>
                    @endif
                    @if($bestDiffStanding)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg bg-surface-container-highest flex items-center justify-center text-secondary shrink-0"><span class="material-symbols-outlined">star</span></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-label-bold text-on-surface-variant uppercase">Najbolja set-razlika</p>
                                <p class="font-bold truncate">{{ $bestDiffStanding->participant_name }} ({{ $fmtDiff($diffByPlayer[$bestDiffPlayerId]['s']) }})</p>
                            </div>
                        </div>
                    @endif
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-surface-container-highest flex items-center justify-center text-on-surface-variant shrink-0"><span class="material-symbols-outlined">history</span></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-label-bold text-on-surface-variant uppercase">Odigrano mečeva</p>
                            <p class="font-bold">{{ $completedMatches }} / {{ $totalMatches }}</p>
                        </div>
                        <div class="w-16 bg-surface-container-highest h-2 rounded-full overflow-hidden shrink-0">
                            <div class="bg-primary h-full" style="width: {{ $totalMatches > 0 ? round($completedMatches / $totalMatches * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="-mx-margin-mobile lg:mx-0 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl px-margin-mobile py-5 lg:p-6">
                <h3 class="font-headline-md mb-4">Organizacija</h3>
                <div class="p-4 bg-surface-container-lowest border border-outline-variant rounded-lg mb-4 flex items-center gap-4">
                    @if($organization->logo)
                        <img alt="{{ $organization->name }}" class="w-12 h-12 rounded object-cover shrink-0" src="{{ asset('storage/' . $organization->logo) }}">
                    @else
                        <div class="w-12 h-12 rounded bg-surface-container-high flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-on-surface-variant">corporate_fare</span></div>
                    @endif
                    <div class="min-w-0">
                        <p class="font-bold truncate">{{ $organization->name }}</p>
                        <p class="text-body-sm text-on-surface-variant truncate">{{ $competition->sport->name }}</p>
                    </div>
                </div>
                <a href="{{ route('competitions.organization', $organization) }}" class="w-full py-2.5 bg-surface-container-high hover:bg-surface-container-highest border border-outline-variant rounded-lg font-medium transition-all text-body-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">emoji_events</span> Sva takmičenja organizacije
                </a>
            </section>
        </aside>
    </div>
@endif
