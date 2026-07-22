<div>
@php
    $isIndividualMatch = (!$match->league || !$match->league->is_team_based) && (!$match->competition || !$match->competition->is_team_based);
@endphp

@if($isIndividualMatch)
    @php
        $homeSetsWon = $sets ? count(array_filter($sets, function($set) {
            $home = $set['home_score'] ?? $set['home'] ?? 0;
            $away = $set['away_score'] ?? $set['away'] ?? 0;
            return $home > $away;
        })) : 0;
        $awaySetsWon = $sets ? count(array_filter($sets, function($set) {
            $home = $set['home_score'] ?? $set['home'] ?? 0;
            $away = $set['away_score'] ?? $set['away'] ?? 0;
            return $away > $home;
        })) : 0;
        // Big numbers: final sets tally once completed, otherwise the live
        // point/game score of whichever set is currently being played (not
        // the sets tally - that's shown as the small caption underneath).
        $showFinalSets = $matchStatus === 'completed' && $sets && count($sets) > 0;
        $homeDisplayScore = $showFinalSets ? $homeSetsWon : $currentSetHomeScore;
        $awayDisplayScore = $showFinalSets ? $awaySetsWon : $currentSetAwayScore;
        $homeIsWinner = $matchStatus === 'completed' && $homeDisplayScore > $awayDisplayScore;
        $awayIsWinner = $matchStatus === 'completed' && $awayDisplayScore > $homeDisplayScore;
        $initials = fn ($name) => collect(explode(' ', trim($name ?? '?')))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
    @endphp
    <!-- Individual Match Layout -->
    <div id="live-score-app" class="space-y-4 lg:space-y-6" data-poll-url="{{ route('api.match', $match) }}" data-home-name="{{ $match->homePlayer->name ?? 'Domaći' }}" data-away-name="{{ $match->awayPlayer->name ?? 'Gost' }}">
        <!-- Status Above Scores -->
        <div class="flex justify-center">
            <span id="match-status-badge" class="{{ $matchStatus === 'in_progress' ? 'text-secondary font-bold text-sm uppercase tracking-[0.2em] animate-pulse' : ($matchStatus === 'completed' ? 'bg-primary text-on-primary px-6 py-1.5 rounded-full font-bold text-xs uppercase tracking-wider' : 'text-on-surface-variant font-bold text-sm uppercase tracking-[0.2em]') }}">
                {{ $matchStatus === 'in_progress' ? 'Uživo' : ($matchStatus === 'completed' ? 'Završeno' : 'Zakazano') }}
            </span>
        </div>

        <!-- Scores Row -->
        <div class="grid grid-cols-2 gap-3 md:gap-6">
            <!-- Home Player -->
            <div id="home-score-card" class="score-card bg-surface-container-low border {{ $homeIsWinner ? 'border-primary/40' : 'border-outline-variant' }} rounded-xl p-4 md:p-8 flex flex-col items-center text-center">
                <div id="home-avatar" class="w-14 h-14 md:w-20 md:h-20 rounded-full flex items-center justify-center font-display text-lg md:text-2xl mb-2 md:mb-4 {{ $homeIsWinner ? 'bg-primary/20 text-primary border-2 border-primary' : 'bg-surface-container-highest text-on-surface-variant border-2 border-outline-variant' }}">
                    {{ $initials($match->homePlayer->name ?? 'D') }}
                </div>
                <h3 class="text-sm md:text-lg font-bold text-on-surface truncate w-full">{{ $match->homePlayer->name ?? 'Domaći' }}</h3>
                <div id="home-score-value" class="text-4xl md:text-display font-display {{ $homeIsWinner ? 'text-primary' : 'text-on-surface' }} leading-none my-2 md:my-4">
                    {{ $matchStatus === 'scheduled' ? '-' : $homeDisplayScore }}
                </div>
                <p id="home-caption" class="font-label-bold text-[10px] md:text-label-bold uppercase tracking-wider {{ $homeIsWinner ? 'text-primary' : 'text-on-surface-variant' }}">
                    @if($matchStatus === 'in_progress') Setovi {{ $homeSetsWon }} @elseif($homeIsWinner) Pobjednik @endif
                </p>
            </div>

            <!-- Away Player -->
            <div id="away-score-card" class="score-card bg-surface-container-low border {{ $awayIsWinner ? 'border-primary/40' : 'border-outline-variant' }} rounded-xl p-4 md:p-8 flex flex-col items-center text-center">
                <div id="away-avatar" class="w-14 h-14 md:w-20 md:h-20 rounded-full flex items-center justify-center font-display text-lg md:text-2xl mb-2 md:mb-4 {{ $awayIsWinner ? 'bg-primary/20 text-primary border-2 border-primary' : 'bg-surface-container-highest text-on-surface-variant border-2 border-outline-variant' }}">
                    {{ $initials($match->awayPlayer->name ?? 'G') }}
                </div>
                <h3 class="text-sm md:text-lg font-bold text-on-surface truncate w-full">{{ $match->awayPlayer->name ?? 'Gost' }}</h3>
                <div id="away-score-value" class="text-4xl md:text-display font-display {{ $awayIsWinner ? 'text-primary' : 'text-on-surface' }} leading-none my-2 md:my-4">
                    {{ $matchStatus === 'scheduled' ? '-' : $awayDisplayScore }}
                </div>
                <p id="away-caption" class="font-label-bold text-[10px] md:text-label-bold uppercase tracking-wider {{ $awayIsWinner ? 'text-primary' : 'text-on-surface-variant' }}">
                    @if($matchStatus === 'in_progress') Setovi {{ $awaySetsWon }} @elseif($awayIsWinner) Pobjednik @endif
                </p>
            </div>
        </div>

        <!-- Sets Breakdown (only for individual matches) -->
        <div id="sets-breakdown-wrapper" class="bg-surface-container-low border border-outline-variant rounded-xl overflow-hidden {{ ($sets && count($sets) > 0) ? '' : 'hidden' }}">
            <div class="px-4 md:px-6 py-3 md:py-4 bg-surface-container border-b border-outline-variant text-center">
                <h3 class="font-headline-md text-on-surface">Pregled setova</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-center border-collapse">
                    <thead>
                        <tr class="bg-surface-container-highest/50">
                            <th class="py-2 md:py-3 px-2 md:px-4 text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">Set</th>
                            <th class="py-2 md:py-3 px-2 md:px-4 text-[10px] font-bold uppercase tracking-wide text-primary truncate max-w-[8rem]">{{ $match->homePlayer->name ?? 'Domaći' }}</th>
                            <th class="py-2 md:py-3 px-2 text-[10px] text-on-surface-variant/50">-</th>
                            <th class="py-2 md:py-3 px-2 md:px-4 text-[10px] font-bold uppercase tracking-wide text-on-surface-variant truncate max-w-[8rem]">{{ $match->awayPlayer->name ?? 'Gost' }}</th>
                        </tr>
                    </thead>
                    <tbody id="sets-table-body" class="divide-y divide-outline-variant/30">
                        @foreach($sets as $index => $set)
                        <tr>
                            <td class="py-2 md:py-3 px-2 md:px-4 font-bold text-on-surface-variant">{{ $index + 1 }}</td>
                            <td class="py-2 md:py-3 px-2 md:px-4 font-bold tabular-nums {{ ($set['home_score'] ?? $set['home'] ?? 0) > ($set['away_score'] ?? $set['away'] ?? 0) ? 'text-primary' : 'text-on-surface-variant' }}">{{ $set['home_score'] ?? $set['home'] ?? 0 }}</td>
                            <td class="py-2 md:py-3 px-2 text-xs text-on-surface-variant/50">-</td>
                            <td class="py-2 md:py-3 px-2 md:px-4 font-bold tabular-nums {{ ($set['away_score'] ?? $set['away'] ?? 0) > ($set['home_score'] ?? $set['home'] ?? 0) ? 'text-primary' : 'text-on-surface-variant' }}">{{ $set['away_score'] ?? $set['away'] ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($match->moderator)
        <div class="bg-surface-container-low border border-outline-variant rounded-xl p-4 md:p-6 text-center">
            <div class="text-sm text-on-surface-variant">Sudija: {{ $match->moderator->name }}</div>
        </div>
        @endif
    </div>

    <script>
    (function () {
        var root = document.getElementById('live-score-app');
        if (!root) return;

        var pollUrl = root.dataset.pollUrl;
        var statusBadge = document.getElementById('match-status-badge');
        var homeCard = document.getElementById('home-score-card');
        var awayCard = document.getElementById('away-score-card');
        var homeAvatar = document.getElementById('home-avatar');
        var awayAvatar = document.getElementById('away-avatar');
        var homeScoreEl = document.getElementById('home-score-value');
        var awayScoreEl = document.getElementById('away-score-value');
        var homeCaption = document.getElementById('home-caption');
        var awayCaption = document.getElementById('away-caption');
        var setsWrapper = document.getElementById('sets-breakdown-wrapper');
        var setsBody = document.getElementById('sets-table-body');

        function countSetsWon(sets, side) {
            var other = side === 'home' ? 'away' : 'home';
            var won = 0;
            (sets || []).forEach(function (s) {
                var a = s.home_score ?? s.home ?? 0;
                var b = s.away_score ?? s.away ?? 0;
                var mine = side === 'home' ? a : b;
                var theirs = side === 'home' ? b : a;
                if (mine > theirs) won++;
            });
            return won;
        }

        function blink(el) {
            el.classList.remove('score-pop');
            void el.offsetWidth; // force reflow so the animation restarts
            el.classList.add('score-pop');
        }

        function render(data) {
            var status = data.status;
            var sets = data.sets || [];
            var homeSetsWon = countSetsWon(sets, 'home');
            var awaySetsWon = countSetsWon(sets, 'away');
            var showFinalSets = status === 'completed' && sets.length > 0;
            var homeDisplay = showFinalSets ? homeSetsWon : (data.current_set_home_score ?? 0);
            var awayDisplay = showFinalSets ? awaySetsWon : (data.current_set_away_score ?? 0);
            var homeWin = status === 'completed' && homeDisplay > awayDisplay;
            var awayWin = status === 'completed' && awayDisplay > homeDisplay;

            // Status badge
            if (status === 'in_progress') {
                statusBadge.className = 'text-secondary font-bold text-sm uppercase tracking-[0.2em] animate-pulse';
                statusBadge.textContent = 'Uživo';
            } else if (status === 'completed') {
                statusBadge.className = 'bg-primary text-on-primary px-6 py-1.5 rounded-full font-bold text-xs uppercase tracking-wider';
                statusBadge.textContent = 'Završeno';
            } else {
                statusBadge.className = 'text-on-surface-variant font-bold text-sm uppercase tracking-[0.2em]';
                statusBadge.textContent = 'Zakazano';
            }

            // Score numbers (blink only if the value actually changed)
            var homeText = status === 'scheduled' ? '-' : String(homeDisplay);
            var awayText = status === 'scheduled' ? '-' : String(awayDisplay);
            if (homeScoreEl.textContent.trim() !== homeText) {
                homeScoreEl.textContent = homeText;
                blink(homeScoreEl);
            }
            if (awayScoreEl.textContent.trim() !== awayText) {
                awayScoreEl.textContent = awayText;
                blink(awayScoreEl);
            }
            homeScoreEl.className = 'text-4xl md:text-display font-display leading-none my-2 md:my-4 ' + (homeWin ? 'text-primary' : 'text-on-surface');
            awayScoreEl.className = 'text-4xl md:text-display font-display leading-none my-2 md:my-4 ' + (awayWin ? 'text-primary' : 'text-on-surface');

            // Winner styling
            homeCard.className = 'score-card bg-surface-container-low border rounded-xl p-4 md:p-8 flex flex-col items-center text-center ' + (homeWin ? 'border-primary/40' : 'border-outline-variant');
            awayCard.className = 'score-card bg-surface-container-low border rounded-xl p-4 md:p-8 flex flex-col items-center text-center ' + (awayWin ? 'border-primary/40' : 'border-outline-variant');
            homeAvatar.className = 'w-14 h-14 md:w-20 md:h-20 rounded-full flex items-center justify-center font-display text-lg md:text-2xl mb-2 md:mb-4 ' + (homeWin ? 'bg-primary/20 text-primary border-2 border-primary' : 'bg-surface-container-highest text-on-surface-variant border-2 border-outline-variant');
            awayAvatar.className = 'w-14 h-14 md:w-20 md:h-20 rounded-full flex items-center justify-center font-display text-lg md:text-2xl mb-2 md:mb-4 ' + (awayWin ? 'bg-primary/20 text-primary border-2 border-primary' : 'bg-surface-container-highest text-on-surface-variant border-2 border-outline-variant');

            // Captions
            homeCaption.className = 'font-label-bold text-[10px] md:text-label-bold uppercase tracking-wider ' + (homeWin ? 'text-primary' : 'text-on-surface-variant');
            awayCaption.className = 'font-label-bold text-[10px] md:text-label-bold uppercase tracking-wider ' + (awayWin ? 'text-primary' : 'text-on-surface-variant');
            homeCaption.textContent = status === 'in_progress' ? ('Setovi ' + homeSetsWon) : (homeWin ? 'Pobjednik' : '');
            awayCaption.textContent = status === 'in_progress' ? ('Setovi ' + awaySetsWon) : (awayWin ? 'Pobjednik' : '');

            // Sets breakdown table
            if (sets.length > 0) {
                setsWrapper.classList.remove('hidden');
                setsBody.innerHTML = sets.map(function (s, i) {
                    var h = s.home_score ?? s.home ?? 0;
                    var a = s.away_score ?? s.away ?? 0;
                    return '<tr>' +
                        '<td class="py-2 md:py-3 px-2 md:px-4 font-bold text-on-surface-variant">' + (i + 1) + '</td>' +
                        '<td class="py-2 md:py-3 px-2 md:px-4 font-bold tabular-nums ' + (h > a ? 'text-primary' : 'text-on-surface-variant') + '">' + h + '</td>' +
                        '<td class="py-2 md:py-3 px-2 text-xs text-on-surface-variant/50">-</td>' +
                        '<td class="py-2 md:py-3 px-2 md:px-4 font-bold tabular-nums ' + (a > h ? 'text-primary' : 'text-on-surface-variant') + '">' + a + '</td>' +
                        '</tr>';
                }).join('');
            } else {
                setsWrapper.classList.add('hidden');
            }
        }

        function poll() {
            fetch(pollUrl, { headers: { 'Accept': 'application/json' } })
                .then(function (res) { return res.json(); })
                .then(function (json) { if (json.success) render(json.data); })
                .catch(function () { /* silent - try again next tick */ });
        }

        setInterval(poll, 5000);
    })();
    </script>
@else
    <!-- Team Match Layout -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-8">
    <!-- Home Team/Player -->
    <div class="bg-[var(--bg-card)] backdrop-blur-xl rounded-2xl p-4 md:p-8 border border-[var(--border-primary)] shadow-xl text-center">
        <div class="mb-3 md:mb-6">
            <h3 class="text-lg md:text-2xl font-bold transition-all duration-300 mb-2">
                <span class="text-blue-400">
                    @if($parent && $parent->is_team_based)
                        {{ $match->homeTeam->name ?? 'Home Team' }}
                    @else
                        {{ $match->homePlayer->name ?? 'Home Player' }}
                    @endif
                </span>
            </h3>
            @if($parent && $parent->is_team_based && $match->homeTeam)
                <div class="text-xs md:text-sm text-[var(--text-secondary)] mb-2 md:mb-4">
                    @foreach($match->homeTeam->players as $player)
                        <div>{{ $player->name }}</div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="text-6xl md:text-8xl lg:text-9xl font-bold text-blue-400 mb-3 md:mb-6">
            @if($matchStatus === 'completed' && $sets && count($sets) > 0)
                {{ count(array_filter($sets, function($set) {
                    $home = $set['home_score'] ?? $set['home'] ?? 0;
                    $away = $set['away_score'] ?? $set['away'] ?? 0;
                    return $home > $away;
                })) }}
            @else
                {{ $homeScore }}
            @endif
        </div>

        <!-- Sets display for home -->
        @if($matchStatus === 'completed' && $sets && count($sets) > 0)
        <div class="text-xs md:text-sm text-[var(--text-secondary)]">
            <div class="mb-1 md:mb-2">Final Score</div>
        </div>
        @elseif($sets && count($sets) > 0)
        <div class="text-xs md:text-sm text-[var(--text-secondary)]">
            <div class="mb-1 md:mb-2">Sets: {{ count(array_filter($sets, function($set) {
                $home = $set['home_score'] ?? $set['home'] ?? 0;
                $away = $set['away_score'] ?? $set['away'] ?? 0;
                return $home > $away;
            })) }}</div>
        </div>
        @endif
    </div>

    <!-- Center Info -->
    <div class="bg-[var(--bg-card)] backdrop-blur-xl rounded-2xl p-4 md:p-8 border border-[var(--border-primary)] shadow-xl text-center flex flex-col justify-center">
        <div class="text-4xl md:text-6xl font-bold text-[var(--text-secondary)] mb-2 md:mb-4">VS</div>

        @if($matchStatus === 'in_progress')
        <div class="text-green-400 font-semibold mb-2 md:mb-4 text-sm md:text-base">
            🔴 LIVE
        </div>
        @elseif($matchStatus === 'completed')
        <div class="text-green-400 font-semibold mb-2 md:mb-4 text-sm md:text-base">
            ✅ COMPLETED
        </div>
        @endif

        @if($match->moderator)
        <div class="text-xs md:text-sm text-[var(--text-secondary)] mb-2 md:mb-4">
            Referee: {{ $match->moderator->name }}
        </div>
        @endif

        <div class="mt-3 md:mt-6">
            @if($matchStatus !== 'completed')
            <div class="text-xs md:text-sm text-[var(--text-secondary)] mb-1 md:mb-2 flex items-center justify-center gap-2">
                Real-time Updates
                @if($isUpdating)
                    <div class="animate-spin rounded-full h-2 w-2 md:h-3 md:w-3 border border-[var(--text-secondary)] border-t-transparent"></div>
                @endif
            </div>
            <div class="text-[var(--text-primary)] text-xs">Auto-refreshing every 2 seconds</div>
            <div class="text-xs text-[var(--text-muted)] mt-1">
                Last updated: {{ $lastUpdated ? $lastUpdated->format('H:i:s') : 'Never' }}
            </div>
            @endif
        </div>
    </div>

    <!-- Away Team/Player -->
    <div class="bg-[var(--bg-card)] backdrop-blur-xl rounded-2xl p-4 md:p-8 border border-[var(--border-primary)] shadow-xl text-center">
        <div class="mb-3 md:mb-6">
            <h3 class="text-lg md:text-2xl font-bold transition-all duration-300 mb-2">
                <span class="text-red-400">
                    @if($parent && $parent->is_team_based)
                        {{ $match->awayTeam->name ?? 'Away Team' }}
                    @else
                        {{ $match->awayPlayer->name ?? 'Away Player' }}
                    @endif
                </span>
            </h3>
            @if($parent && $parent->is_team_based && $match->awayTeam)
                <div class="text-xs md:text-sm text-[var(--text-secondary)] mb-2 md:mb-4">
                    @foreach($match->awayTeam->players as $player)
                        <div>{{ $player->name }}</div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="text-6xl md:text-8xl lg:text-9xl font-bold text-red-400 mb-3 md:mb-6">
            @if($matchStatus === 'completed' && $sets && count($sets) > 0)
                {{ count(array_filter($sets, function($set) {
                    $home = $set['home_score'] ?? $set['home'] ?? 0;
                    $away = $set['away_score'] ?? $set['away'] ?? 0;
                    return $away > $home;
                })) }}
            @else
                {{ $awayScore }}
            @endif
        </div>

        <!-- Sets display for away -->
        @if($matchStatus === 'completed' && $sets && count($sets) > 0)
        <div class="text-xs md:text-sm text-[var(--text-secondary)]">
            <div class="mb-1 md:mb-2">Final Score</div>
        </div>
        @elseif($sets && count($sets) > 0)
        <div class="text-xs md:text-sm text-[var(--text-secondary)]">
            <div class="mb-1 md:mb-2">Sets: {{ count(array_filter($sets, function($set) {
                $home = $set['home_score'] ?? $set['home'] ?? 0;
                $away = $set['away_score'] ?? $set['away'] ?? 0;
                return $away > $home;
            })) }}</div>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Sets Breakdown (only for team matches) -->
@if(!$isIndividualMatch && $sets && count($sets) > 0)
<div class="mt-6 md:mt-8 bg-[var(--bg-card)] backdrop-blur-xl rounded-2xl p-4 md:p-6 border border-[var(--border-primary)] shadow-xl">
    <h3 class="text-lg md:text-xl font-bold text-center mb-4 md:mb-6 bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
        Set Scores
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-center text-sm md:text-base">
            <thead>
                <tr class="border-b border-[var(--border-primary)]">
                    <th class="pb-2 md:pb-3 text-[var(--text-secondary)] font-medium text-xs md:text-sm">Set</th>
                    <th class="pb-2 md:pb-3 text-blue-400 font-medium text-xs md:text-sm">
                        @if($parent && $parent->is_team_based)
                            {{ $match->homeTeam->name ?? 'Home' }}
                        @else
                            {{ $match->homePlayer->name ?? 'Home' }}
                        @endif
                    </th>
                    <th class="pb-2 md:pb-3 text-[var(--text-muted)] font-medium text-xs md:text-sm">-</th>
                    <th class="pb-2 md:pb-3 text-red-400 font-medium text-xs md:text-sm">
                        @if($parent && $parent->is_team_based)
                            {{ $match->awayTeam->name ?? 'Away' }}
                        @else
                            {{ $match->awayPlayer->name ?? 'Away' }}
                        @endif
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($sets as $index => $set)
                <tr class="border-b border-[var(--border-secondary)]">
                    <td class="py-2 md:py-3 text-[var(--text-secondary)] font-medium text-xs md:text-sm">{{ $index + 1 }}</td>
                    <td class="py-2 md:py-3 text-blue-400 font-bold text-sm md:text-lg">{{ $set['home_score'] ?? $set['home'] ?? 0 }}</td>
                    <td class="py-2 md:py-3 text-[var(--text-muted)] text-xs md:text-sm">-</td>
                    <td class="py-2 md:py-3 text-red-400 font-bold text-sm md:text-lg">{{ $set['away_score'] ?? $set['away'] ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
</div>