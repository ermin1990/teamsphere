<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $match->homeTeam?->name ?? $match->homePlayer?->name ?? 'Home' }} vs {{ $match->awayTeam?->name ?? $match->awayPlayer?->name ?? 'Away' }} - {{ $organization->name }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-900 text-white min-h-screen pb-32 md:pb-8">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Menu (Desktop only) -->
            <nav class="hidden md:block bg-gray-800/50 backdrop-blur-xl rounded-2xl p-4 border border-gray-700/50 shadow-xl mb-6">
                <div class="flex items-center justify-center space-x-6 md:space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors text-sm md:text-base font-medium">
                        🏠 Home
                    </a>
                    <a href="{{ route('public.live-matches') }}" class="text-gray-300 hover:text-white transition-colors text-sm md:text-base font-medium">
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
                    <div class="text-sm text-gray-400 mb-4">{{ $competition->sport->name }} • {{ $competition->name }}</div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
                        Match Details
                    </h1>
                    <p class="text-gray-400">Round {{ $match->round }}</p>
                </div>
            </div>

            <!-- Match Info -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
                <div class="flex items-center justify-center space-x-8 mb-8">
                    <!-- Home Participant -->
                    <div class="text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-white">
                                @if($competition->is_team_based)
                                    {{ substr($match->homeTeam->name ?? 'TBD', 0, 2) }}
                                @else
                                    {{ substr($match->homePlayer->name ?? 'TBD', 0, 2) }}
                                @endif
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">
                            @if($competition->is_team_based)
                                {{ $match->homeTeam->name ?? 'TBD' }}
                            @else
                                {{ $match->homePlayer->name ?? 'TBD' }}
                            @endif
                        </h3>
                        @if($competition->is_team_based && $match->homeTeam)
                            <div class="text-sm text-gray-400">
                                @foreach($match->homeTeam->players as $player)
                                    <div>{{ $player->name }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="text-center">
                        <div class="text-4xl font-bold text-gray-400 mb-2">VS</div>
                        <div class="px-4 py-2 bg-gray-700 rounded-lg">
                            <div class="text-sm text-gray-400">{{ ucfirst(str_replace('_', ' ', $match->status)) }}</div>
                        </div>
                    </div>

                    <!-- Away Participant -->
                    <div class="text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-white">
                                @if($competition->is_team_based)
                                    {{ substr($match->awayTeam->name ?? 'TBD', 0, 2) }}
                                @else
                                    {{ substr($match->awayPlayer->name ?? 'TBD', 0, 2) }}
                                @endif
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">
                            @if($competition->is_team_based)
                                {{ $match->awayTeam->name ?? 'TBD' }}
                            @else
                                {{ $match->awayPlayer->name ?? 'TBD' }}
                            @endif
                        </h3>
                        @if($competition->is_team_based && $match->awayTeam)
                            <div class="text-sm text-gray-400">
                                @foreach($match->awayTeam->players as $player)
                                    <div>{{ $player->name }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Score Display -->
                @if($match->status === 'completed' || $match->status === 'in_progress')
                <div class="text-center mb-8">
                    <div class="text-6xl font-bold bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent mb-4">
                        {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                    </div>
                    @if($match->sets && count($match->sets) > 0)
                        <div class="text-sm text-gray-400">
                            Sets: {{ implode(' | ', array_map(function($set) {
                                return ($set['home_score'] ?? $set['home'] ?? 0) . '-' . ($set['away_score'] ?? $set['away'] ?? 0);
                            }, $match->sets)) }}
                        </div>
                    @endif
                </div>
                @endif

                <!-- Live Score Button -->
                @if($match->status === 'in_progress')
                <div class="text-center">
                    <a href="{{ route('public.matches.live', [$competition, $match]) }}"
                       class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold">
                        🎯 Watch Live Score
                    </a>
                </div>
                @endif
            </div>

            <!-- Match Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Match Info -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Match Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Status:</span>
                            <span class="text-white">{{ ucfirst(str_replace('_', ' ', $match->status)) }}</span>
                        </div>
                        @if($match->scheduled_at)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Scheduled:</span>
                            <span class="text-white">{{ $match->scheduled_at->format('M j, Y g:i A') }}</span>
                        </div>
                        @endif
                        @if($match->played_at)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Played:</span>
                            <span class="text-white">{{ $match->played_at->format('M j, Y g:i A') }}</span>
                        </div>
                        @endif
                        @if($match->moderator)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Referee:</span>
                            <span class="text-white">{{ $match->moderator->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Organization Info -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Organization</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Name:</span>
                            <span class="text-white">{{ $organization->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Sport:</span>
                            <span class="text-white">{{ $competition->sport->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">League:</span>
                            <span class="text-white">{{ $competition->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back to League -->
            <div class="text-center mt-8 mb-20 md:mb-8">
                <a href="{{ route('public.leagues.show', $competition) }}"
                   class="inline-flex items-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    ← Back to League
                </a>
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
