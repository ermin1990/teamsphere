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
    {{-- Live auto-refresh (fetch poll) disabled for now - page shows a static
         snapshot; reload manually to see updated scores. --}}
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