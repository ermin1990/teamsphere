<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Live Matches - TeamSphere</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Auto refresh every 60 seconds -->
    <meta http-equiv="refresh" content="60">
</head>
<body class="antialiased bg-gray-900 text-white min-h-screen pb-16 md:pb-8">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Menu (Desktop only) -->
            <nav class="hidden md:block bg-gray-800/50 backdrop-blur-xl rounded-2xl p-4 border border-gray-700/50 shadow-xl mb-6">
                <div class="flex items-center justify-center space-x-6 md:space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors text-sm md:text-base font-medium">
                        🏠 Home
                    </a>
                    <a href="{{ route('public.live-matches') }}" class="text-blue-400 font-semibold text-sm md:text-base">
                        📺 Live Matches
                    </a>
                    <a href="{{ route('public.leagues.index') }}" class="text-gray-300 hover:text-white transition-colors text-sm md:text-base font-medium">
                        🏆 Leagues
                    </a>
                </div>
            </nav>

            <!-- Header -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent mb-2">
                        🏓 Live Matches
                    </h1>
                    <p class="text-gray-400">Watch live table tennis matches from all leagues</p>
                    <div class="mt-4 text-sm text-gray-500">
                        Page auto-refreshes every 60 seconds • {{ $liveMatches->count() }} live matches
                    </div>
                </div>
            </div>

            @if($liveMatches->count() > 0)
                <!-- Live Matches Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($liveMatches as $match)
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:bg-gray-800/70 transition-colors">
                        <!-- Match Header -->
                        <div class="text-center mb-4">
                            <div class="text-xs text-gray-400 mb-1">
                                {{ $match->competition->organization->name }} • {{ $match->competition->name }}
                            </div>
                            <div class="text-xs text-green-400 font-semibold">
                                🔴 LIVE • Round {{ $match->round }}
                            </div>
                        </div>

                        <!-- Teams/Players -->
                        <div class="space-y-3 mb-4">
                            <!-- Home -->
                            <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-bold text-white">
                                            @if($match->competition->is_team_based)
                                                {{ substr($match->homeTeam?->name ?? 'H', 0, 1) }}
                                            @else
                                                {{ substr($match->homePlayer?->name ?? 'H', 0, 1) }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="text-sm font-medium text-white">
                                        @if($match->competition->is_team_based)
                                            {{ $match->homeTeam?->name ?? 'Home Team' }}
                                        @else
                                            {{ $match->homePlayer?->name ?? 'Home Player' }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-xl font-bold text-blue-400">
                                    {{ $match->home_score ?? 0 }}
                                </div>
                            </div>

                            <!-- Away -->
                            <div class="flex items-center justify-between p-3 bg-gray-700/50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-bold text-white">
                                            @if($match->competition->is_team_based)
                                                {{ substr($match->awayTeam?->name ?? 'A', 0, 1) }}
                                            @else
                                                {{ substr($match->awayPlayer?->name ?? 'A', 0, 1) }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="text-sm font-medium text-white">
                                        @if($match->competition->is_team_based)
                                            {{ $match->awayTeam?->name ?? 'Away Team' }}
                                        @else
                                            {{ $match->awayPlayer?->name ?? 'Away Player' }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-xl font-bold text-red-400">
                                    {{ $match->away_score ?? 0 }}
                                </div>
                            </div>
                        </div>

                        <!-- Sets Info -->
                        @if($match->sets && count($match->sets) > 0)
                        <div class="text-center mb-4">
                            <div class="text-xs text-gray-400 mb-1">Sets</div>
                            <div class="text-sm text-white">
                                {{ implode(' | ', array_map(function($set) {
                                    return ($set['home_score'] ?? $set['home'] ?? 0) . '-' . ($set['away_score'] ?? $set['away'] ?? 0);
                                }, $match->sets)) }}
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <a href="{{ route('public.matches.live', [$match->competition, $match]) }}"
                               class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                                🎯 Watch Live
                            </a>
                            <a href="{{ route('public.matches.show', [$match->competition, $match]) }}"
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                                📊 Details
                            </a>
                        </div>

                        <!-- Last Updated -->
                        <div class="text-center mt-3 text-xs text-gray-500">
                            Updated {{ $match->updated_at->diffForHumans() }}
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- No Live Matches -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-12 border border-gray-700/50 shadow-xl text-center">
                    <div class="text-6xl mb-4">🏓</div>
                    <h2 class="text-2xl font-bold text-white mb-4">No Live Matches</h2>
                    <p class="text-gray-400 mb-6">There are currently no live matches being played. Check back later!</p>
                    <div class="text-sm text-gray-500">
                        Live matches will appear here automatically when games start.
                    </div>
                </div>
            @endif

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-400 text-sm">
                <p>Powered by TeamSphere • Real-time match updates</p>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu (Fixed Bottom) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-gray-800/95 backdrop-blur-xl border-t border-gray-700/50 shadow-2xl z-50">
        <div class="flex items-center justify-between py-3 px-4 w-full">
            <a href="{{ route('home') }}" class="flex flex-col items-center text-gray-300 hover:text-white transition-colors text-xs flex-1">
                <span class="text-lg">🏠</span>
                <span class="mt-1">Home</span>
            </a>
            <a href="{{ route('public.live-matches') }}" class="flex flex-col items-center text-gray-300 hover:text-white transition-colors text-xs flex-1">
                <span class="text-lg">📺</span>
                <span class="mt-1">Live</span>
            </a>
            <a href="{{ route('public.leagues.index') }}" class="flex flex-col items-center text-gray-300 hover:text-white transition-colors text-xs flex-1">
                <span class="text-lg">🏆</span>
                <span class="mt-1">Leagues</span>
            </a>
        </div>
    </nav>
</body>
</html>
