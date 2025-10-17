<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Live: {{ $match->homeTeam?->name ?? $match->homePlayer?->name ?? 'Home' }} vs {{ $match->awayTeam?->name ?? $match->awayPlayer?->name ?? 'Away' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Auto refresh every 30 seconds -->
    <meta http-equiv="refresh" content="30">
</head>
<body class="antialiased bg-gray-900 text-white min-h-screen">
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                            🏓 Live Table Tennis Score
                        </h1>
                        <p class="text-gray-400 mt-1">{{ $league->name }} • Round {{ $match->round }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 text-sm rounded-full
                            @if($match->status === 'completed') bg-green-500/20 text-green-400
                            @elseif($match->status === 'in_progress') bg-yellow-500/20 text-yellow-400
                            @else bg-gray-500/20 text-gray-400 @endif"
                        >
                            {{ ucfirst(str_replace('_', ' ', $match->status)) }}
                        </span>
                        <a href="{{ route('public.matches.show', [$league, $match]) }}"
                           class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                            ← Match Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Live Score Display -->
            @if($match->status === 'in_progress' || $match->status === 'completed')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Home Team/Player -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl text-center">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold transition-all duration-300 mb-2">
                            <span class="text-blue-400">
                                @if($league->is_team_based)
                                    {{ $match->homeTeam->name ?? 'Home Team' }}
                                @else
                                    {{ $match->homePlayer->name ?? 'Home Player' }}
                                @endif
                            </span>
                        </h3>
                        @if($league->is_team_based && $match->homeTeam)
                            <div class="text-sm text-gray-400 mb-4">
                                @foreach($match->homeTeam->players as $player)
                                    <div>{{ $player->name }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="text-8xl md:text-9xl font-bold text-blue-400 mb-6">
                        {{ $match->home_score ?? 0 }}
                    </div>

                    <!-- Sets display for home -->
                    @if($match->sets && count($match->sets) > 0)
                    <div class="text-sm text-gray-400">
                        <div class="mb-2">Sets Won: {{ count(array_filter($match->sets, function($set) {
                            $home = $set['home_score'] ?? $set['home'] ?? 0;
                            $away = $set['away_score'] ?? $set['away'] ?? 0;
                            return $home > $away;
                        })) }}</div>
                        <div>Sets: {{ implode(' | ', array_map(function($set) {
                            return ($set['home_score'] ?? $set['home'] ?? 0) . '-' . ($set['away_score'] ?? $set['away'] ?? 0);
                        }, $match->sets)) }}</div>
                    </div>
                    @endif
                </div>

                <!-- Center Info -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl text-center flex flex-col justify-center">
                    <div class="text-6xl font-bold text-gray-400 mb-4">VS</div>

                    @if($match->status === 'in_progress')
                    <div class="text-green-400 font-semibold mb-4">
                        🔴 LIVE
                    </div>
                    @elseif($match->status === 'completed')
                    <div class="text-green-400 font-semibold mb-4">
                        ✅ COMPLETED
                    </div>
                    @endif

                    @if($match->moderator)
                    <div class="text-sm text-gray-400">
                        Referee: {{ $match->moderator->name }}
                    </div>
                    @endif

                    <div class="mt-6">
                        <div class="text-sm text-gray-400 mb-2">Last Updated</div>
                        <div class="text-white">{{ now()->format('g:i:s A') }}</div>
                        <div class="text-xs text-gray-500">Auto-refreshes every 30s</div>
                    </div>
                </div>

                <!-- Away Team/Player -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl text-center">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold transition-all duration-300 mb-2">
                            <span class="text-red-400">
                                @if($league->is_team_based)
                                    {{ $match->awayTeam->name ?? 'Away Team' }}
                                @else
                                    {{ $match->awayPlayer->name ?? 'Away Player' }}
                                @endif
                            </span>
                        </h3>
                        @if($league->is_team_based && $match->awayTeam)
                            <div class="text-sm text-gray-400 mb-4">
                                @foreach($match->awayTeam->players as $player)
                                    <div>{{ $player->name }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="text-8xl md:text-9xl font-bold text-red-400 mb-6">
                        {{ $match->away_score ?? 0 }}
                    </div>

                    <!-- Sets display for away -->
                    @if($match->sets && count($match->sets) > 0)
                    <div class="text-sm text-gray-400">
                        <div class="mb-2">Sets Won: {{ count(array_filter($match->sets, function($set) {
                            $home = $set['home_score'] ?? $set['home'] ?? 0;
                            $away = $set['away_score'] ?? $set['away'] ?? 0;
                            return $away > $home;
                        })) }}</div>
                        <div>Sets: {{ implode(' | ', array_map(function($set) {
                            return ($set['home_score'] ?? $set['home'] ?? 0) . '-' . ($set['away_score'] ?? $set['away'] ?? 0);
                        }, $match->sets)) }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <!-- Not Started -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-12 border border-gray-700/50 shadow-xl text-center">
                <div class="text-6xl mb-4">⏳</div>
                <h2 class="text-2xl font-bold text-white mb-4">Match Not Started</h2>
                <p class="text-gray-400 mb-6">This match hasn't started yet. Check back later for live updates.</p>
                @if($match->scheduled_at)
                <div class="text-sm text-gray-400">
                    Scheduled for: {{ $match->scheduled_at->format('M j, Y g:i A') }}
                </div>
                @endif
            </div>
            @endif

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-400 text-sm">
                <p>Powered by TeamSphere • {{ $organization->name }}</p>
            </div>
        </div>
    </div>
</body>
</html>