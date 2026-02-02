<div wire:poll.3s="pollMatchData">
@php
    $isIndividualMatch = (!$match->league || !$match->league->is_team_based) && (!$match->competition || !$match->competition->is_team_based);
@endphp

@if($isIndividualMatch)
    <!-- Individual Match Layout -->
    <div class="space-y-4">
        <!-- LIVE Status Above Scores -->
        <div id="match-status" class="text-center">
            @if($matchStatus === 'in_progress')
                <div class="text-green-400 font-semibold text-sm md:text-base">
                    🔴 LIVE
                </div>
            @elseif($matchStatus === 'completed')
                <div class="text-green-400 font-semibold text-sm md:text-base">
                    ✅ COMPLETED
                </div>
            @endif
        </div>

        <!-- Scores Row -->
        <div class="flex items-center justify-center gap-4 md:gap-8">
            <!-- Home Player -->
            <div class="flex-1 bg-[var(--bg-card)] backdrop-blur-xl rounded-2xl p-4 md:p-6 border border-[var(--border-primary)] shadow-xl text-center">
                <div class="mb-2 md:mb-4">
                    <h3 class="text-base md:text-xl font-bold transition-all duration-300">
                        <span class="text-blue-400">{{ $match->homePlayer->name ?? 'Home Player' }}</span>
                    </h3>
                </div>
                <div id="home-score" class="text-5xl md:text-7xl lg:text-8xl font-bold text-blue-400 mb-2">
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
                @if($matchStatus === 'completed' && $sets && count($sets) > 0)
                <div class="text-xs md:text-sm text-[var(--text-secondary)]">
                    Final Score
                </div>
                @elseif($sets && count($sets) > 0)
                <div class="text-xs md:text-sm text-[var(--text-secondary)]">
                    Sets: {{ count(array_filter($sets, function($set) {
                        $home = $set['home_score'] ?? $set['home'] ?? 0;
                        $away = $set['away_score'] ?? $set['away'] ?? 0;
                        return $home > $away;
                    })) }}
                </div>
                @endif
            </div>

            <!-- Away Player -->
            <div class="flex-1 bg-[var(--bg-card)] backdrop-blur-xl rounded-2xl p-4 md:p-6 border border-[var(--border-primary)] shadow-xl text-center">
                <div class="mb-2 md:mb-4">
                    <h3 class="text-base md:text-xl font-bold transition-all duration-300">
                        <span class="text-red-400">{{ $match->awayPlayer->name ?? 'Away Player' }}</span>
                    </h3>
                </div>
                <div id="away-score" class="text-5xl md:text-7xl lg:text-8xl font-bold text-red-400 mb-2">
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
                @if($matchStatus === 'completed' && $sets && count($sets) > 0)
                <div class="text-xs md:text-sm text-[var(--text-secondary)]">
                    Final Score
                </div>
                @elseif($sets && count($sets) > 0)
                <div class="text-xs md:text-sm text-[var(--text-secondary)]">
                    Sets: {{ count(array_filter($sets, function($set) {
                        $home = $set['home_score'] ?? $set['home'] ?? 0;
                        $away = $set['away_score'] ?? $set['away'] ?? 0;
                        return $away > $home;
                    })) }}
                </div>
                @endif
            </div>
        </div>

        <!-- Sets Breakdown (only for individual matches) -->
        @if($sets && count($sets) > 0)
        <div id="sets-breakdown" class="bg-[var(--bg-card)] backdrop-blur-xl rounded-2xl p-4 md:p-6 border border-[var(--border-primary)] shadow-xl">
            <h3 class="text-lg md:text-xl font-bold text-center mb-4 bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                Set Scores
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-center text-sm md:text-base">
                    <thead>
                        <tr class="border-b border-[var(--border-primary)]">
                            <th class="pb-2 md:pb-3 text-[var(--text-secondary)] font-medium text-xs md:text-sm">Set</th>
                            <th class="pb-2 md:pb-3 text-blue-400 font-medium text-xs md:text-sm">{{ $match->homePlayer->name ?? 'Home' }}</th>
                            <th class="pb-2 md:pb-3 text-[var(--text-muted)] font-medium text-xs md:text-sm">-</th>
                            <th class="pb-2 md:pb-3 text-red-400 font-medium text-xs md:text-sm">{{ $match->awayPlayer->name ?? 'Away' }}</th>
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

        <!-- Match Info at Bottom -->
        <div class="bg-[var(--bg-card)] backdrop-blur-xl rounded-2xl p-4 md:p-6 border border-[var(--border-primary)] shadow-xl text-center">
            @if($match->moderator)
                <div class="text-sm md:text-base text-[var(--text-secondary)] mb-3">
                    Referee: {{ $match->moderator->name }}
                </div>
            @endif

            @if($matchStatus !== 'completed')
            <div class="text-xs md:text-sm text-[var(--text-secondary)] flex items-center justify-center gap-2 mb-2">
                Real-time Updates
                @if($isUpdating)
                    <div class="animate-spin rounded-full h-2 w-2 md:h-3 md:w-3 border border-[var(--text-secondary)] border-t-transparent"></div>
                @endif
            </div>
            <div class="text-[var(--text-primary)] text-xs">Auto-refreshing every 3 seconds</div>
            <div id="last-updated-time" class="text-xs text-[var(--text-muted)] mt-1">
                Last updated: {{ $lastUpdated ? $lastUpdated->format('H:i:s') : 'Never' }}
            </div>
            @endif
        </div>
    </div>
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