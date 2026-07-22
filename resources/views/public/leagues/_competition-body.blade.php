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

    // Join/apply state - self-contained so this partial works whether it's
    // included from the anonymous public page or the player's own view of a
    // competition they already belong to (where $canApply is naturally false).
    $viewerId = auth()->id();
    $isMember = $viewerId && $competition->players()->where('players.user_id', $viewerId)->exists();
    $isPending = $viewerId && \App\Models\CompetitionJoinRequest::where('competition_id', $competition->id)
        ->where('user_id', $viewerId)->where('status', 'pending')->exists();
    $canApply = $competition->registration_open
        && !$competition->is_team_based
        && !$isMember
        && (!$competition->registration_deadline || $competition->registration_deadline->isFuture());
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

        $sortedStandings = $competition->standings->sortBy('position')->values();
        $totalMatches = $leagueMatches->count();
        $completedMatches = $leagueMatches->whereIn('status', ['completed', 'forfeited'])->count();
        $leader = $sortedStandings->first();
        $bestDiffPlayerId = collect($diffByPlayer)->sortByDesc('s')->keys()->first();
        $bestDiffStanding = $bestDiffPlayerId ? $sortedStandings->firstWhere('player_id', $bestDiffPlayerId) : null;
    }
@endphp

@include('public.leagues._hero')

@if($showTabs ?? false)
    @include('public.leagues._tabs', ['activeTab' => 'overview'])
@endif

@php
    $featuredAnnouncement = $competition->featuredAnnouncement();
@endphp
@if($featuredAnnouncement)
<section class="-mx-margin-mobile lg:mx-0 mb-6 lg:mb-8 bg-primary/10 border-y lg:border border-primary/30 lg:rounded-xl px-margin-mobile py-4 lg:p-5">
    <div class="flex flex-wrap items-center gap-2 mb-2">
        <span class="material-symbols-outlined text-primary text-[18px]">push_pin</span>
        @if($featuredAnnouncement->isOrganizationWide())
            <span class="bg-secondary/20 text-secondary px-2.5 py-0.5 rounded-full text-label-bold uppercase">Organizacija</span>
        @endif
        <span class="text-xs text-on-surface-variant">{{ $featuredAnnouncement->created_at->format('d.m.Y.') }}</span>
    </div>
    <h3 class="font-headline-md">{{ $featuredAnnouncement->title }}</h3>
    <p class="text-sm text-on-surface-variant mt-1 whitespace-pre-line">{{ $featuredAnnouncement->body }}</p>
</section>
@endif

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

@if($canApply)
<section class="-mx-margin-mobile lg:mx-0 mb-6 lg:mb-8 bg-primary/10 border-y lg:border border-primary/30 lg:rounded-xl px-margin-mobile py-4 lg:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <p class="font-headline-md text-on-surface">Prijave su otvorene</p>
        <p class="text-sm text-on-surface-variant">Prijavi se da učestvuješ u ovom {{ $isTournament ? 'turniru' : 'takmičenju' }}.</p>
    </div>
    @if($isPending)
        <span class="shrink-0 px-4 py-2 rounded-full text-sm font-label-bold bg-secondary/15 text-secondary text-center">Zahtjev poslan</span>
    @else
        <form method="POST" action="{{ route('player.leagues.apply', $competition) }}" class="competition-apply-form shrink-0">
            @csrf
            <button type="submit" class="apply-button w-full sm:w-auto px-5 py-2.5 text-sm font-semibold rounded-full transition-all active:scale-95 whitespace-nowrap bg-primary text-on-primary inline-flex items-center justify-center gap-2 disabled:opacity-70">
                <span class="apply-button-text">Prijavi se</span>
                <svg class="apply-button-spinner hidden animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        </form>
    @endif
</section>
<script>
    document.querySelectorAll('.competition-apply-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const button = form.querySelector('.apply-button');
            button.disabled = true;
            button.querySelector('.apply-button-text').textContent = 'Šaljem zahtjev...';
            button.querySelector('.apply-button-spinner').classList.remove('hidden');
        });
    });
</script>
@elseif($competition->registration_open && $competition->is_team_based && !$isMember)
<section class="-mx-margin-mobile lg:mx-0 mb-6 lg:mb-8 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl px-margin-mobile py-4 lg:p-5">
    <p class="text-sm text-on-surface-variant">📋 Prijave su otvorene, ali je ovo timsko takmičenje — kontaktiraj organizatora direktno da prijaviš svoj tim{{ $competition->organizer_contact ? ' (' . $competition->organizer_contact . ')' : '' }}.</p>
</section>
@endif

@unless($hideAnnouncementsSection ?? false)
@php
    $announcements = $competition->visibleAnnouncements();
@endphp
@if($announcements->isNotEmpty())
<section id="announcements-section" class="-mx-margin-mobile lg:mx-0 mb-6 lg:mb-8 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl px-margin-mobile py-5 lg:p-6">
    <h2 class="font-headline-md mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary">campaign</span> Obavijesti</h2>
    <div class="space-y-4">
        @foreach($announcements as $announcement)
            <div class="p-4 bg-surface-container-lowest border border-outline-variant rounded-lg">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    @if($announcement->isOrganizationWide())
                        <span class="bg-secondary/20 text-secondary px-2.5 py-0.5 rounded-full text-label-bold uppercase">Organizacija</span>
                    @endif
                    <span class="text-xs text-on-surface-variant">{{ $announcement->created_at->format('d.m.Y.') }}</span>
                </div>
                <h3 class="font-bold">{{ $announcement->title }}</h3>
                <p class="text-sm text-on-surface-variant mt-1 whitespace-pre-line">{{ $announcement->body }}</p>
            </div>
        @endforeach
    </div>
</section>
@endif
@endunless

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
                                        // Only show the player's club if it's a different organization than
                                        // the one running this league - otherwise it's just their default
                                        // registration org, not a real "playing for another club" affiliation.
                                        $clubName = (!$competition->is_team_based && $standing->player && $standing->player->organization_id !== $competition->organization_id)
                                            ? ($standing->player->organization->name ?? null)
                                            : null;
                                        // Doubles pairs are stored as "PLAYER ONE/PLAYER TWO" -
                                        // split so each name gets its own line, same as match cards.
                                        $participantNameParts = explode('/', $standing->participant_name);
                                    @endphp
                                    <tr class="transition-colors group {{ $advancing ? 'bg-primary/5' : 'hover:bg-surface-variant/30' }}">
                                        <td class="px-3 lg:px-4 py-2 lg:py-2.5 font-bold {{ $index < 3 ? 'text-primary' : '' }}">{{ $index + 1 }}</td>
                                        <td class="px-2 lg:px-4 py-2 lg:py-2.5">
                                            @if($standing->player)
                                                <a href="{{ route('competitions.player.show', $standing->player) }}" class="font-semibold group-hover:text-primary transition-colors leading-tight">
                                                    @foreach($participantNameParts as $part)
                                                        <span class="truncate block">{{ trim($part) }}</span>
                                                    @endforeach
                                                </a>
                                            @else
                                                <span class="font-semibold group-hover:text-primary transition-colors leading-tight block">
                                                    @foreach($participantNameParts as $part)
                                                        <span class="truncate block">{{ trim($part) }}</span>
                                                    @endforeach
                                                </span>
                                            @endif
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
                    <div class="px-margin-mobile lg:px-6 py-4 border-t border-outline-variant flex items-start gap-2 text-label-bold text-on-surface-variant">
                        <span class="px-1.5 py-0.5 rounded bg-orange-500/20 text-orange-500 text-[10px] font-bold tracking-wide shrink-0 mt-0.5">WO</span>
                        <span class="normal-case font-normal">WalkOver — meč predat bez igre (protivnik se nije pojavio ili je diskvalifikovan; pobjeda se dodjeljuje prisutnom igraču).</span>
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
                                            // Doubles pairs are stored as a single Player whose name is
                                            // "PLAYER ONE/PLAYER TWO" - split so each name gets its own
                                            // (smaller) line instead of being squeezed/truncated onto one.
                                            $mHomeNameParts = explode('/', $mHomeName);
                                            $mAwayNameParts = explode('/', $mAwayName);
                                            $mCompleted = in_array($match->status, ['completed', 'forfeited']);
                                            $mLive = $match->status === 'in_progress';
                                            $mHomeWin = $mCompleted && $match->home_score > $match->away_score;
                                            $mAwayWin = $mCompleted && $match->away_score > $match->home_score;
                                            $mSetRows = collect($match->sets ?? [])
                                                ->map(fn ($s) => [
                                                    'home' => $s['home'] ?? $s['home_score'] ?? $s['p1'] ?? null,
                                                    'away' => $s['away'] ?? $s['away_score'] ?? $s['p2'] ?? null,
                                                ])
                                                ->filter(fn ($s) => $s['home'] !== null && $s['away'] !== null)
                                                ->values();
                                            $mVenue = !$isTeamMatch ? $match->venue : null;
                                            $mDate = $match->played_at ?? $match->scheduled_at;
                                            $mScheduled = !$mCompleted && !$mLive;
                                            $mShowHeader = $mLive || ($mDate && !$mScheduled);
                                            $mHomeUrl = !$isTeamMatch && $match->homePlayer ? route('competitions.player.show', $match->homePlayer) : null;
                                            $mAwayUrl = !$isTeamMatch && $match->awayPlayer ? route('competitions.player.show', $match->awayPlayer) : null;
                                            $mDetailsUrl = $isTeamMatch
                                                ? route('competitions.team-matches.show', [$competition, $match])
                                                : route('competitions.matches.show', [$competition, $match]);
                                        @endphp
                                        <div>
                                            @if($mShowHeader)
                                                <div class="flex justify-between items-center mb-2 text-label-bold text-on-surface-variant uppercase">
                                                    @if($mLive)
                                                        <span class="text-secondary animate-pulse">Uživo</span>
                                                    @else
                                                        <span></span>
                                                    @endif
                                                    <span>{{ $mDate?->format('d.m.Y. H:i') }}</span>
                                                </div>
                                            @endif
                                            @if($mSetRows->isNotEmpty())
                                                <div class="border border-outline-variant rounded-lg overflow-hidden">
                                                    <div class="overflow-x-auto">
                                                    <table class="w-full border-collapse" style="min-width: {{ 120 + $mSetRows->count() * 40 }}px">
                                                        <thead>
                                                            <tr class="bg-surface-container-highest">
                                                                <th class="text-left px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide text-on-surface-variant sticky left-0 bg-surface-container-highest">Igrač</th>
                                                                @foreach($mSetRows as $i => $set)
                                                                    <th class="text-center px-2 py-1.5 text-[10px] font-bold uppercase tracking-wide text-on-surface-variant w-10 whitespace-nowrap">{{ $i + 1 }}.</th>
                                                                @endforeach
                                                                <th class="text-center px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide text-primary border-l border-outline-variant whitespace-nowrap">Rezultat</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="{{ $mAwayWin ? 'opacity-60' : '' }}">
                                                                <td class="px-3 py-2 font-medium max-w-[8rem] sticky left-0 bg-surface-container-low leading-tight">@if($mHomeUrl)<a href="{{ $mHomeUrl }}" class="hover:text-primary transition-colors block">@foreach($mHomeNameParts as $part)<span class="truncate block {{ count($mHomeNameParts) > 1 ? 'text-xs' : 'text-sm' }}">{{ trim($part) }}</span>@endforeach</a>@else<span class="block">@foreach($mHomeNameParts as $part)<span class="truncate block {{ count($mHomeNameParts) > 1 ? 'text-xs' : 'text-sm' }}">{{ trim($part) }}</span>@endforeach</span>@endif</td>
                                                                @foreach($mSetRows as $set)
                                                                    <td class="text-center px-2 py-2 text-sm tabular-nums whitespace-nowrap {{ $set['home'] > $set['away'] ? 'font-bold text-primary' : 'text-on-surface-variant' }}">{{ $set['home'] }}</td>
                                                                @endforeach
                                                                <td class="text-center px-3 py-2 font-bold text-body-lg tabular-nums whitespace-nowrap border-l border-outline-variant {{ $mHomeWin ? 'text-primary' : 'text-on-surface-variant' }}">{{ $match->home_score }}</td>
                                                            </tr>
                                                            <tr class="border-t border-outline-variant {{ $mHomeWin ? 'opacity-60' : '' }}">
                                                                <td class="px-3 py-2 font-medium max-w-[8rem] sticky left-0 bg-surface-container-low leading-tight">@if($mAwayUrl)<a href="{{ $mAwayUrl }}" class="hover:text-primary transition-colors block">@foreach($mAwayNameParts as $part)<span class="truncate block {{ count($mAwayNameParts) > 1 ? 'text-xs' : 'text-sm' }}">{{ trim($part) }}</span>@endforeach</a>@else<span class="block">@foreach($mAwayNameParts as $part)<span class="truncate block {{ count($mAwayNameParts) > 1 ? 'text-xs' : 'text-sm' }}">{{ trim($part) }}</span>@endforeach</span>@endif</td>
                                                                @foreach($mSetRows as $set)
                                                                    <td class="text-center px-2 py-2 text-sm tabular-nums whitespace-nowrap {{ $set['away'] > $set['home'] ? 'font-bold text-primary' : 'text-on-surface-variant' }}">{{ $set['away'] }}</td>
                                                                @endforeach
                                                                <td class="text-center px-3 py-2 font-bold text-body-lg tabular-nums whitespace-nowrap border-l border-outline-variant {{ $mAwayWin ? 'text-primary' : 'text-on-surface-variant' }}">{{ $match->away_score }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    </div>
                                                </div>
                                            @elseif($mScheduled)
                                                <div class="border border-outline-variant rounded-lg overflow-hidden">
                                                    <div class="overflow-x-auto">
                                                    <table class="w-full border-collapse" style="min-width: 200px">
                                                        <thead>
                                                            <tr class="bg-surface-container-highest">
                                                                <th class="text-left px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide text-on-surface-variant sticky left-0 bg-surface-container-highest">Igrač</th>
                                                                <th class="text-center px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide text-secondary border-l border-outline-variant whitespace-nowrap">Zakazano</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="px-3 py-2 font-medium max-w-[8rem] sticky left-0 bg-surface-container-low leading-tight">@if($mHomeUrl)<a href="{{ $mHomeUrl }}" class="hover:text-primary transition-colors block">@foreach($mHomeNameParts as $part)<span class="truncate block {{ count($mHomeNameParts) > 1 ? 'text-xs' : 'text-sm' }}">{{ trim($part) }}</span>@endforeach</a>@else<span class="block">@foreach($mHomeNameParts as $part)<span class="truncate block {{ count($mHomeNameParts) > 1 ? 'text-xs' : 'text-sm' }}">{{ trim($part) }}</span>@endforeach</span>@endif</td>
                                                                <td class="text-center px-3 py-2 font-bold text-body-lg tabular-nums whitespace-nowrap border-l border-outline-variant text-on-surface-variant">–</td>
                                                            </tr>
                                                            <tr class="border-t border-outline-variant">
                                                                <td class="px-3 py-2 font-medium max-w-[8rem] sticky left-0 bg-surface-container-low leading-tight">@if($mAwayUrl)<a href="{{ $mAwayUrl }}" class="hover:text-primary transition-colors block">@foreach($mAwayNameParts as $part)<span class="truncate block {{ count($mAwayNameParts) > 1 ? 'text-xs' : 'text-sm' }}">{{ trim($part) }}</span>@endforeach</a>@else<span class="block">@foreach($mAwayNameParts as $part)<span class="truncate block {{ count($mAwayNameParts) > 1 ? 'text-xs' : 'text-sm' }}">{{ trim($part) }}</span>@endforeach</span>@endif</td>
                                                                <td class="text-center px-3 py-2 font-bold text-body-lg tabular-nums whitespace-nowrap border-l border-outline-variant text-on-surface-variant">–</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="border border-outline-variant rounded-lg overflow-hidden">
                                                    <div class="overflow-x-auto">
                                                    <table class="w-full border-collapse" style="min-width: 200px">
                                                        <thead>
                                                            <tr class="bg-surface-container-highest">
                                                                <th class="text-left px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide text-on-surface-variant sticky left-0 bg-surface-container-highest">Igrač</th>
                                                                <th class="text-center px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide {{ $match->forfeited_by ? 'text-orange-500' : 'text-primary' }} border-l border-outline-variant whitespace-nowrap">{{ $match->forfeited_by ? 'WO' : 'Rezultat' }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="{{ $mAwayWin ? 'opacity-60' : '' }}">
                                                                <td class="px-3 py-2 font-medium max-w-[8rem] sticky left-0 bg-surface-container-low leading-tight">@if($mHomeUrl)<a href="{{ $mHomeUrl }}" class="hover:text-primary transition-colors block">@foreach($mHomeNameParts as $part)<span class="truncate block {{ count($mHomeNameParts) > 1 ? 'text-xs' : 'text-sm' }}">{{ trim($part) }}</span>@endforeach</a>@else<span class="block">@foreach($mHomeNameParts as $part)<span class="truncate block {{ count($mHomeNameParts) > 1 ? 'text-xs' : 'text-sm' }}">{{ trim($part) }}</span>@endforeach</span>@endif</td>
                                                                <td class="text-center px-3 py-2 font-bold text-body-lg tabular-nums whitespace-nowrap border-l border-outline-variant {{ $mHomeWin ? 'text-primary' : 'text-on-surface-variant' }}">{{ $match->home_score }}</td>
                                                            </tr>
                                                            <tr class="border-t border-outline-variant {{ $mHomeWin ? 'opacity-60' : '' }}">
                                                                <td class="px-3 py-2 font-medium max-w-[8rem] sticky left-0 bg-surface-container-low leading-tight">@if($mAwayUrl)<a href="{{ $mAwayUrl }}" class="hover:text-primary transition-colors block">@foreach($mAwayNameParts as $part)<span class="truncate block {{ count($mAwayNameParts) > 1 ? 'text-xs' : 'text-sm' }}">{{ trim($part) }}</span>@endforeach</a>@else<span class="block">@foreach($mAwayNameParts as $part)<span class="truncate block {{ count($mAwayNameParts) > 1 ? 'text-xs' : 'text-sm' }}">{{ trim($part) }}</span>@endforeach</span>@endif</td>
                                                                <td class="text-center px-3 py-2 font-bold text-body-lg tabular-nums whitespace-nowrap border-l border-outline-variant {{ $mAwayWin ? 'text-primary' : 'text-on-surface-variant' }}">{{ $match->away_score }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="mt-3 pt-3 border-t border-outline-variant flex items-center justify-between gap-2">
                                                @if($mVenue)
                                                    <span class="text-label-bold text-on-surface-variant flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-[14px]">location_on</span> {{ $mVenue->name }}
                                                    </span>
                                                @else
                                                    <span></span>
                                                @endif
                                                <a href="{{ $mDetailsUrl }}" class="inline-flex items-center gap-1 text-xs font-label-bold text-primary hover:underline shrink-0">
                                                    Detalji meča <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                                </a>
                                            </div>
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

            @include('public.leagues._banners', ['placement' => \App\Models\Banner::PLACEMENT_LEAGUE])
        </aside>
    </div>
@endif

@unless($hideRulesSection ?? false)
@php $effectiveRulesText = $competition->effectiveRulesText(); @endphp
@if($effectiveRulesText || $competition->sets_to_win)
<section id="rules-section" class="-mx-margin-mobile lg:mx-0 mt-6 lg:mt-8 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl px-margin-mobile py-5 lg:p-6">
    <h2 class="font-headline-md mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary">gavel</span> Pravila</h2>

    @include('public.leagues._rules-summary')

    @if($effectiveRulesText)
        <p class="text-sm text-on-surface-variant whitespace-pre-line">{{ $effectiveRulesText }}</p>
    @endif
</section>
@endif
@endunless
