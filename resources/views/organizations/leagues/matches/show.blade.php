<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Match Details
                </h2>
                <p class="text-gray-400 mt-1">{{ $league->name }} • Round {{ $match->round }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-2 py-1 text-xs sm:text-sm rounded-full whitespace-nowrap overflow-hidden text-ellipsis max-w-32 sm:max-w-none
                    @if($match->status === 'completed') bg-green-500/20 text-green-400
                    @elseif($match->status === 'in_progress') bg-yellow-500/20 text-yellow-400
                    @elseif($match->status === 'forfeited') bg-red-500/20 text-red-400
                    @elseif($match->status === 'cancelled') bg-orange-500/20 text-orange-400
                    @else bg-gray-500/20 text-gray-400 @endif"
                >
                    {{ ucfirst(str_replace('_', ' ', $match->status)) }}
                    @if($match->status === 'forfeited' && $match->forfeited_by)
                        - {{ $match->forfeited_by === 'home' ? ($league->is_team_based ? $match->homeTeam->name : $match->homePlayer->name) : ($league->is_team_based ? $match->awayTeam->name : $match->awayPlayer->name) }} Forfeited
                    @endif
                </span>
                <div class="flex space-x-2">
                    @if((isset($isOwner) && $isOwner) || (isset($isReferee) && $isReferee))
                    <a href="{{ request()->routeIs('referee.*') ? route('referee.match.edit', [$league, $match]) : route('leagues.matches.edit', [$league, $match]) }}"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                        ✏️ Edit Results
                    </a>
                    @if($match->status !== 'completed')
                    <a href="{{ request()->routeIs('referee.*') ? route('referee.match.live', [$league, $match]) : route('leagues.matches.live', [$league, $match]) }}"
                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors">
                        🎯 Live Score
                    </a>
                    @endif
                    @endif
                    <button onclick="shareMatch()"
                       class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition-colors">
                        📤 Share
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">

                <!-- Match Header -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                    <div class="text-center">
                        <div class="text-sm text-gray-400 mb-4">{{ $league->sport->name }} • Round {{ $match->round }}</div>

                        <div class="flex items-center justify-center space-x-8">
                            <!-- Home Participant -->
                            <div class="text-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-2xl font-bold text-white">
                                        @if($league->is_team_based)
                                            {{ substr($match->homeTeam->name ?? 'TBD', 0, 2) }}
                                        @else
                                            {{ substr($match->homePlayer->name ?? 'TBD', 0, 2) }}
                                        @endif
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-white">
                                    @if($league->is_team_based)
                                        {{ $match->homeTeam->name ?? 'TBD' }}
                                    @else
                                        {{ $match->homePlayer->name ?? 'TBD' }}
                                    @endif
                                </h3>
                                @if($league->is_team_based && $match->homeTeam)
                                    <div class="mt-2 text-sm text-gray-400">
                                        @foreach($match->homeTeam->players as $player)
                                            <div>{{ $player->name }}</div>
                                        @endforeach
                                    </div>
                                @endif
                                @if($match->status === 'forfeited')
                                    @if($match->forfeited_by === 'home')
                                        <div class="text-red-400 mt-2 text-center">
                                            <div class="text-sm">Forfeited</div>
                                            <div class="text-lg font-bold">Lost by Forfeit</div>
                                        </div>
                                    @else
                                        <div class="text-green-400 mt-2 text-center">
                                            <div class="text-sm">Won by</div>
                                            <div class="text-lg font-bold">Forfeit</div>
                                        </div>
                                    @endif
                                @elseif($match->status === 'cancelled')
                                @if($match->home_score || $match->away_score)
                                <div class="text-4xl font-bold text-blue-400 mt-2">{{ $match->home_score }}</div>
                                @else
                                <div class="text-orange-400 mt-2 text-center">
                                    <div class="text-sm">Match</div>
                                    <div class="text-lg font-bold">Cancelled</div>
                                </div>
                                @endif
                                @elseif(in_array($match->status, ['in_progress', 'completed']))
                                <div class="text-4xl font-bold text-blue-400 mt-2">{{ $match->home_score }}</div>
                                @endif
                            </div>

                            <!-- VS -->
                            <div class="text-center">
                                <div class="text-gray-400 text-lg font-medium mb-2">VS</div>
                                @if($match->status === 'completed')
                                <div class="text-green-400 text-sm">Completed</div>
                                @elseif($match->status === 'in_progress')
                                <div class="text-yellow-400 text-sm">In Progress</div>
                                @elseif($match->status === 'forfeited')
                                <div class="text-red-400 text-sm">Forfeited</div>
                                @elseif($match->status === 'cancelled')
                                <div class="text-orange-400 text-sm">Cancelled</div>
                                @else
                                <div class="text-gray-400 text-sm">Scheduled</div>
                                @endif
                            </div>

                            <!-- Away Participant -->
                            <div class="text-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-2xl font-bold text-white">
                                        @if($league->is_team_based)
                                            {{ substr($match->awayTeam->name ?? 'TBD', 0, 2) }}
                                        @else
                                            {{ substr($match->awayPlayer->name ?? 'TBD', 0, 2) }}
                                        @endif
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-white">
                                    @if($league->is_team_based)
                                        {{ $match->awayTeam->name ?? 'TBD' }}
                                    @else
                                        {{ $match->awayPlayer->name ?? 'TBD' }}
                                    @endif
                                </h3>
                                @if($league->is_team_based && $match->awayTeam)
                                    <div class="mt-2 text-sm text-gray-400">
                                        @foreach($match->awayTeam->players as $player)
                                            <div>{{ $player->name }}</div>
                                        @endforeach
                                    </div>
                                @endif
                                @if($match->status === 'forfeited')
                                    @if($match->forfeited_by === 'away')
                                        <div class="text-red-400 mt-2 text-center">
                                            <div class="text-sm">Forfeited</div>
                                            <div class="text-lg font-bold">Lost by Forfeit</div>
                                        </div>
                                    @else
                                        <div class="text-green-400 mt-2 text-center">
                                            <div class="text-sm">Won by</div>
                                            <div class="text-lg font-bold">Forfeit</div>
                                        </div>
                                    @endif
                                @elseif($match->status === 'cancelled')
                                @if($match->home_score || $match->away_score)
                                <div class="text-4xl font-bold text-red-400 mt-2">{{ $match->away_score }}</div>
                                @else
                                <div class="text-orange-400 mt-2 text-center">
                                    <div class="text-sm">Match</div>
                                    <div class="text-lg font-bold">Cancelled</div>
                                </div>
                                @endif
                                @elseif(in_array($match->status, ['in_progress', 'completed']))
                                <div class="text-4xl font-bold text-red-400 mt-2">{{ $match->away_score }}</div>
                                @endif
                            </div>
                        </div>

                        @if($match->scheduled_at)
                        <div class="mt-6 text-center">
                            <div class="text-gray-400 text-sm">Scheduled for</div>
                            <div class="text-white font-medium">{{ $match->scheduled_at->format('M d, Y \a\t H:i') }}</div>
                        </div>
                        @endif

                        @if($match->played_at)
                        <div class="mt-4 text-center">
                            <div class="text-gray-400 text-sm">Played on</div>
                            <div class="text-white font-medium">{{ $match->played_at->format('M d, Y \a\t H:i') }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Match Sets (if applicable) -->
                @if($league->sport->slug === 'stoni-tenis' && $match->sets)
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-semibold text-white mb-4">Set Scores</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="text-left py-3 px-2 text-gray-400 font-medium">Set</th>
                                    <th class="text-center py-3 px-2 text-gray-400 font-medium">
                                        @if($league->is_team_based)
                                            {{ $match->homeTeam->name ?? 'Home' }}
                                        @else
                                            {{ $match->homePlayer->name ?? 'Home' }}
                                        @endif
                                    </th>
                                    <th class="text-center py-3 px-2 text-gray-400 font-medium">
                                        @if($league->is_team_based)
                                            {{ $match->awayTeam->name ?? 'Away' }}
                                        @else
                                            {{ $match->awayPlayer->name ?? 'Away' }}
                                        @endif
                                    </th>
                                    <th class="text-center py-3 px-2 text-gray-400 font-medium">Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sets = $match->sets ?? [];
                                    $setDurations = $match->set_durations ?? [];
                                @endphp
                                @foreach($sets as $setNumber => $set)
                                <tr class="border-b border-gray-700/50">
                                    <td class="py-3 px-2 text-white font-medium">Set {{ $setNumber + 1 }}</td>
                                    <td class="py-3 px-2 text-center">
                                        @php
                                            $homeScore = $set['home_score'] ?? $set['home'] ?? 0;
                                            $awayScore = $set['away_score'] ?? $set['away'] ?? 0;
                                            $homeWon = $homeScore > $awayScore;
                                        @endphp
                                        <span class="{{ $homeWon ? 'text-green-400 font-bold text-lg' : 'text-white' }}">
                                            {{ $homeScore }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        @php
                                            $awayWon = $awayScore > $homeScore;
                                        @endphp
                                        <span class="{{ $awayWon ? 'text-green-400 font-bold text-lg' : 'text-white' }}">
                                            {{ $awayScore }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-center text-gray-300">
                                        @if(is_numeric($setDurations[$setNumber] ?? null))
                                            @php
                                                $duration = $setDurations[$setNumber];
                                                $minutes = floor($duration / 60);
                                                $seconds = $duration % 60;
                                                echo sprintf('%02d:%02d', $minutes, $seconds);
                                            @endphp
                                        @else
                                            {{ $setDurations[$setNumber] ?? '00:00' }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Match Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Match Info -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-xl font-semibold text-white mb-4">Match Information</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-400">League</span>
                                <span class="text-white">{{ $league->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Round</span>
                                <span class="text-white">{{ $match->round }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Sport</span>
                                <span class="text-white">{{ $league->sport->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Competition Type</span>
                                <span class="text-white">{{ $league->is_team_based ? 'Team-based' : 'Individual' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Status</span>
                                <span class="text-white">{{ ucfirst(str_replace('_', ' ', $match->status)) }}</span>
                            </div>
                            @if($match->scheduled_at)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Scheduled</span>
                                <span class="text-white">{{ $match->scheduled_at->format('M d, Y H:i') }}</span>
                            </div>
                            @endif
                            @if($match->played_at)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Played</span>
                                <span class="text-white">{{ $match->played_at->format('M d, Y H:i') }}</span>
                            </div>
                            @endif
                            @if($match->set_durations && count($match->set_durations) > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Total Match Time</span>
                                <span class="text-white font-medium">
                                    @php
                                        $totalSeconds = 0;
                                        foreach($match->set_durations as $duration) {
                                            if (is_numeric($duration)) {
                                                $totalSeconds += $duration;
                                            } elseif (preg_match('/^(\d{2}):(\d{2})$/', $duration, $matches)) {
                                                $totalSeconds += ($matches[1] * 60) + $matches[2];
                                            }
                                        }
                                        $totalMinutes = floor($totalSeconds / 60);
                                        $totalRemainingSeconds = $totalSeconds % 60;
                                        echo sprintf('%02d:%02d', $totalMinutes, $totalRemainingSeconds);
                                    @endphp
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Participants Info -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-xl font-semibold text-white mb-4">Participants</h3>
                        <div class="space-y-4">

                            <!-- Home Participant -->
                            <div class="p-4 bg-gray-700/30 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center">
                                        <span class="text-blue-400 font-bold">
                                            @if($league->is_team_based)
                                                {{ substr($match->homeTeam->name ?? 'TBD', 0, 1) }}
                                            @else
                                                {{ substr($match->homePlayer->name ?? 'TBD', 0, 1) }}
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-medium">
                                            @if($league->is_team_based)
                                                {{ $match->homeTeam->name ?? 'TBD' }}
                                            @else
                                                {{ $match->homePlayer->name ?? 'TBD' }}
                                            @endif
                                        </h4>
                                        <p class="text-gray-400 text-sm">Home</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Away Participant -->
                            <div class="p-4 bg-gray-700/30 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-red-500/20 rounded-full flex items-center justify-center">
                                        <span class="text-red-400 font-bold">
                                            @if($league->is_team_based)
                                                {{ substr($match->awayTeam->name ?? 'TBD', 0, 1) }}
                                            @else
                                                {{ substr($match->awayPlayer->name ?? 'TBD', 0, 1) }}
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-medium">
                                            @if($league->is_team_based)
                                                {{ $match->awayTeam->name ?? 'TBD' }}
                                            @else
                                                {{ $match->awayPlayer->name ?? 'TBD' }}
                                            @endif
                                        </h4>
                                        <p class="text-gray-400 text-sm">Away</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Match Officials -->
                @if($match->table || $match->referee || $match->moderator)
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-lg font-semibold text-white mb-4">🏓 Match Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($match->table)
                        <div class="bg-gray-700/30 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <div class="text-sm text-gray-400">Sto</div>
                            </div>
                            <div class="text-white font-medium">{{ $match->table->name }}</div>
                            @if($match->table->description)
                                <div class="text-xs text-gray-400 mt-1">{{ $match->table->description }}</div>
                            @endif
                        </div>
                        @endif
                        @if($match->referee)
                        <div class="bg-gray-700/30 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <div class="text-sm text-gray-400">Sudija</div>
                            </div>
                            <div class="text-white font-medium">{{ $match->referee->name }}</div>
                        </div>
                        @endif
                        @if($match->moderator)
                        <div class="bg-gray-700/30 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-gray-400">Moderator</div>
                            </div>
                            <div class="text-white font-medium">{{ $match->moderator->name }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Audit Information -->
                @if($match->edited_by || $match->completed_by)
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-lg font-semibold text-white mb-4">📋 Audit Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($match->edited_by)
                        <div class="bg-gray-700/30 rounded-lg p-4">
                            <div class="text-sm text-gray-400 mb-1">Last Edited By</div>
                            <div class="text-white font-medium">{{ $match->editedBy->name }}</div>
                            @if($match->edited_at)
                            <div class="text-xs text-gray-400 mt-1">{{ $match->edited_at->format('M j, Y g:i A') }}</div>
                            @endif
                        </div>
                        @endif
                        @if($match->completed_by)
                        <div class="bg-gray-700/30 rounded-lg p-4">
                            <div class="text-sm text-gray-400 mb-1">Completed By</div>
                            <div class="text-white font-medium">{{ $match->completedBy->name }}</div>
                            @if($match->completed_at)
                            <div class="text-xs text-gray-400 mt-1">{{ $match->completed_at->format('M j, Y g:i A') }}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Navigation -->
                <div class="flex justify-center space-x-4">
                    @if($organization->user_id === auth()->id())
                    <form method="POST" action="{{ request()->routeIs('referee.*') ? route('referee.match.reset', [$league, $match]) : route('leagues.matches.reset', [$league, $match]) }}"
                          onsubmit="return confirm('Are you sure you want to reset this match? All current data will be lost.')"
                          class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200">
                            🔄 Reset Match
                        </button>
                    </form>
                    @endif
                    <a href="{{ request()->routeIs('referee.*') ? route('referee.league.matches', $league) : route('leagues.show', $league) }}"
                       class="bg-gray-700/50 hover:bg-gray-600/50 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200">
                        ← Back to League
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script>
        function shareMatch() {
            const publicUrl = '{{ route("public.matches.show", [$league, $match]) }}';
            const liveUrl = '{{ route("public.matches.show", [$league, $match]) }}';

            const shareText = `Check out this match: {{ $match->homeTeam?->name ?? $match->homePlayer?->name ?? "Home" }} vs {{ $match->awayTeam?->name ?? $match->awayPlayer?->name ?? "Away" }}\n\n${publicUrl}`;

            if (navigator.share) {
                navigator.share({
                    title: 'Match Results',
                    text: shareText,
                    url: publicUrl
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(shareText).then(() => {
                    alert('Match link copied to clipboard!');
                }).catch(() => {
                    // Final fallback: show URLs
                    const message = `Share this match:\n\n${shareText}`;
                    if (liveUrl) {
                        message += `\n\nLive score: ${liveUrl}`;
                    }
                    alert(message);
                });
            }
        }
    </script>
</x-app-layout>