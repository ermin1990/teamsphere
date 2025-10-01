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
                <span class="px-3 py-1 text-sm rounded-full
                    @if($match->status === 'completed') bg-green-500/20 text-green-400
                    @elseif($match->status === 'in_progress') bg-yellow-500/20 text-yellow-400
                    @elseif($match->status === 'forfeited') bg-red-500/20 text-red-400
                    @else bg-gray-500/20 text-gray-400 @endif"
                >
                    {{ ucfirst(str_replace('_', ' ', $match->status)) }}
                    @if($match->status === 'forfeited' && $match->forfeited_by)
                        - {{ $match->forfeited_by === 'home' ? ($league->is_team_based ? $match->homeTeam->name : $match->homePlayer->name) : ($league->is_team_based ? $match->awayTeam->name : $match->awayPlayer->name) }} Forfeited
                    @endif
                </span>
                <div class="flex space-x-2">
                    <a href="{{ route('organizations.leagues.matches.edit', [$organization, $league, $match]) }}"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                        ✏️ Edit Results
                    </a>
                    @if($match->status !== 'completed')
                    <a href="{{ route('organizations.leagues.matches.live', [$organization, $league, $match]) }}"
                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors">
                        🎯 Live Score
                    </a>
                    @endif
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
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sets = $match->sets ?? [];
                                @endphp
                                @foreach($sets as $setNumber => $set)
                                <tr class="border-b border-gray-700/50">
                                    <td class="py-3 px-2 text-white font-medium">Set {{ $setNumber + 1 }}</td>
                                    <td class="py-3 px-2 text-center text-white">{{ $set['home_score'] ?? '-' }}</td>
                                    <td class="py-3 px-2 text-center text-white">{{ $set['away_score'] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Navigation -->
                <div class="flex justify-center space-x-4">
                    <form method="POST" action="{{ route('organizations.leagues.matches.reset', [$organization, $league, $match]) }}"
                          onsubmit="return confirm('Are you sure you want to reset this match? All current data will be lost.')"
                          class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200">
                            🔄 Reset Match
                        </button>
                    </form>
                    <a href="{{ route('organizations.leagues.show', [$organization, $league]) }}"
                       class="bg-gray-700/50 hover:bg-gray-600/50 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200">
                        ← Back to League
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>