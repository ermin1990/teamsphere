<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $competition->name }} - {{ $organization->name }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
                        {{ $competition->name }}
                    </h1>
                    <p class="text-gray-400">{{ $organization->name }} • {{ $competition->sport->name }}</p>
                </div>
            </div>

            <!-- League Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">League Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Sport:</span>
                            <span class="text-white">{{ $competition->sport->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Type:</span>
                            <span class="text-white">{{ $competition->is_team_based ? 'Team League' : 'Individual League' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Status:</span>
                            <span class="text-white">{{ ucfirst($competition->status) }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Statistics</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total Matches:</span>
                            <span class="text-white">{{ $competition->matches->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Completed:</span>
                            <span class="text-white">{{ $competition->matches->where('status', 'completed')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">In Progress:</span>
                            <span class="text-white">{{ $competition->matches->where('status', 'in_progress')->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Live Matches</h3>
                    @php
                        $liveMatches = $competition->matches->where('status', 'in_progress');
                    @endphp
                    @if($liveMatches->count() > 0)
                        <div class="space-y-2">
                            @foreach($liveMatches as $match)
                            <a href="{{ route('public.matches.live', [$competition, $match]) }}"
                               class="block p-3 bg-green-600/20 hover:bg-green-600/30 rounded-lg transition-colors">
                                <div class="text-sm font-semibold text-green-400">
                                    Round {{ $match->round }}
                                </div>
                                <div class="text-xs text-gray-300">
                                    @if($competition->is_team_based)
                                        {{ $match->homeTeam?->name ?? 'TBD' }} vs {{ $match->awayTeam?->name ?? 'TBD' }}
                                    @else
                                        {{ $match->homePlayer?->name ?? 'TBD' }} vs {{ $match->awayPlayer?->name ?? 'TBD' }}
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-sm">No live matches at the moment</p>
                    @endif
                </div>
            </div>

            <!-- Standings Table -->
            @if($competition->standings->count() > 0)
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-8">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                        🏆 League Standings
                    </span>
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700/50">
                                <th class="text-left py-3 px-2 text-gray-400 font-semibold">#</th>
                                <th class="text-left py-3 px-2 text-gray-400 font-semibold">Team/Player</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-semibold">P</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-semibold">W</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-semibold">D</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-semibold">L</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-semibold">GF</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-semibold">GA</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-semibold">GD</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-semibold">Pts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($competition->standings as $standing)
                            <tr class="border-b border-gray-700/30 hover:bg-gray-700/20 transition-colors">
                                <td class="py-3 px-2 text-gray-300 font-medium">{{ $standing->position }}</td>
                                <td class="py-3 px-2 text-white font-semibold">{{ $standing->participant->name }}</td>
                                <td class="py-3 px-2 text-center text-gray-300">{{ $standing->played }}</td>
                                <td class="py-3 px-2 text-center text-green-400 font-semibold">{{ $standing->won }}</td>
                                <td class="py-3 px-2 text-center text-yellow-400 font-semibold">{{ $standing->drawn }}</td>
                                <td class="py-3 px-2 text-center text-red-400 font-semibold">{{ $standing->lost }}</td>
                                <td class="py-3 px-2 text-center text-blue-300">{{ $standing->goals_for }}</td>
                                <td class="py-3 px-2 text-center text-orange-300">{{ $standing->goals_against }}</td>
                                <td class="py-3 px-2 text-center {{ $standing->goal_difference >= 0 ? 'text-green-400' : 'text-red-400' }} font-semibold">
                                    {{ $standing->goal_difference >= 0 ? '+' : '' }}{{ $standing->goal_difference }}
                                </td>
                                <td class="py-3 px-2 text-center font-bold text-blue-400 text-lg">{{ $standing->points }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-xs text-gray-500 text-center">
                    P = Played, W = Won, D = Drawn, L = Lost, GF = Goals For, GA = Goals Against, GD = Goal Difference, Pts = Points
                </div>
            </div>
            @endif

            <!-- Matches by Rounds -->
            <div class="space-y-8">
                @php
                    $matchesByRound = $competition->matches->sortBy('round')->groupBy('round');
                @endphp

                @if($matchesByRound->count() > 0)
                    @foreach($matchesByRound as $round => $roundMatches)
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                            <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                                Round {{ $round }}
                            </span>
                            @if($roundMatches->where('status', 'in_progress')->count() > 0)
                            <span class="ml-2 text-red-500 animate-pulse">🔴</span>
                            @endif
                        </h3>

                        <div class="space-y-3">
                            @foreach($roundMatches->sortByDesc('scheduled_at') as $match)
                            <a href="{{ route('public.matches.show', [$competition, $match]) }}"
                               class="block bg-gray-700/50 hover:bg-gray-700/70 rounded-lg p-4 transition-all duration-200 hover:scale-[1.02] hover:shadow-lg">
                                <div class="flex items-center justify-between">
                                    <!-- Match Participants -->
                                    <div class="flex items-center space-x-4 flex-1">
                                        <!-- Home -->
                                        <div class="text-center min-w-0 flex-1">
                                            <div class="text-sm font-semibold text-white truncate">
                                                @if($competition->is_team_based)
                                                    {{ $match->homeTeam?->name ?? 'TBD' }}
                                                @else
                                                    {{ $match->homePlayer?->name ?? 'TBD' }}
                                                @endif
                                            </div>
                                        </div>

                                        <div class="text-center px-3">
                                            <div class="text-lg font-bold text-gray-400">VS</div>
                                            @if($match->status === 'completed' || $match->status === 'in_progress')
                                            <div class="text-sm font-bold text-green-400 mt-1">
                                                {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Away -->
                                        <div class="text-center min-w-0 flex-1">
                                            <div class="text-sm font-semibold text-white truncate">
                                                @if($competition->is_team_based)
                                                    {{ $match->awayTeam?->name ?? 'TBD' }}
                                                @else
                                                    {{ $match->awayPlayer?->name ?? 'TBD' }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Indicator -->
                                    <div class="flex items-center space-x-2 ml-4">
                                        @if($match->status === 'in_progress')
                                        <div class="flex items-center space-x-2">
                                            <span class="text-red-500 animate-pulse text-lg">🔴</span>
                                            <span class="text-xs text-green-400 font-semibold">LIVE</span>
                                        </div>
                                        @elseif($match->status === 'completed')
                                        <span class="text-xs text-gray-400 font-medium">FINISHED</span>
                                        @elseif($match->status === 'scheduled')
                                        <span class="text-xs text-blue-400 font-medium">SCHEDULED</span>
                                        @else
                                        <span class="text-xs text-gray-500 font-medium">{{ ucfirst(str_replace('_', ' ', $match->status)) }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Match Time/Date if scheduled -->
                                @if($match->scheduled_at)
                                <div class="mt-2 text-xs text-gray-500 text-center">
                                    {{ $match->scheduled_at->format('M j, Y g:i A') }}
                                </div>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-12 border border-gray-700/50 shadow-xl text-center">
                    <div class="text-6xl mb-4">🏓</div>
                    <h3 class="text-xl font-semibold text-white mb-2">No Matches Yet</h3>
                    <p class="text-gray-400">Matches will appear here once they are scheduled.</p>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-400 text-sm">
                <p>Powered by TeamSphere • {{ $organization->name }}</p>
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
