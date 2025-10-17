<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $league->name }} - {{ $organization->name }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-900 text-white min-h-screen">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
                        {{ $league->name }}
                    </h1>
                    <p class="text-gray-400">{{ $organization->name }} • {{ $league->sport->name }}</p>
                </div>
            </div>

            <!-- League Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">League Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Sport:</span>
                            <span class="text-white">{{ $league->sport->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Type:</span>
                            <span class="text-white">{{ $league->is_team_based ? 'Team League' : 'Individual League' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Status:</span>
                            <span class="text-white">{{ ucfirst($league->status) }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Statistics</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total Matches:</span>
                            <span class="text-white">{{ $league->matches->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Completed:</span>
                            <span class="text-white">{{ $league->matches->where('status', 'completed')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">In Progress:</span>
                            <span class="text-white">{{ $league->matches->where('status', 'in_progress')->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Live Matches</h3>
                    @php
                        $liveMatches = $league->matches->where('status', 'in_progress');
                    @endphp
                    @if($liveMatches->count() > 0)
                        <div class="space-y-2">
                            @foreach($liveMatches as $match)
                            <a href="{{ route('public.matches.live', [$league, $match]) }}"
                               class="block p-3 bg-green-600/20 hover:bg-green-600/30 rounded-lg transition-colors">
                                <div class="text-sm font-semibold text-green-400">
                                    Round {{ $match->round }}
                                </div>
                                <div class="text-xs text-gray-300">
                                    @if($league->is_team_based)
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

            <!-- Matches List -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                <h2 class="text-2xl font-bold text-white mb-6">Matches</h2>

                @if($league->matches->count() > 0)
                <div class="space-y-4">
                    @foreach($league->matches->sortByDesc('scheduled_at') as $match)
                    <div class="bg-gray-700/50 rounded-lg p-4 hover:bg-gray-700/70 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-400 w-16">Round {{ $match->round }}</span>

                                    <!-- Home -->
                                    <div class="text-center">
                                        <div class="text-sm font-semibold text-white">
                                            @if($league->is_team_based)
                                                {{ $match->homeTeam?->name ?? 'TBD' }}
                                            @else
                                                {{ $match->homePlayer?->name ?? 'TBD' }}
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-center px-4">
                                        <div class="text-lg font-bold text-gray-400">VS</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ ucfirst(str_replace('_', ' ', $match->status)) }}
                                        </div>
                                    </div>

                                    <!-- Away -->
                                    <div class="text-center">
                                        <div class="text-sm font-semibold text-white">
                                            @if($league->is_team_based)
                                                {{ $match->awayTeam?->name ?? 'TBD' }}
                                            @else
                                                {{ $match->awayPlayer?->name ?? 'TBD' }}
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Score -->
                                    @if($match->status === 'completed' || $match->status === 'in_progress')
                                    <div class="text-center ml-4">
                                        <div class="text-xl font-bold text-green-400">
                                            {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                @if($match->status === 'in_progress')
                                <a href="{{ route('public.matches.live', [$league, $match]) }}"
                                   class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition-colors">
                                    🎯 Live
                                </a>
                                @endif
                                <a href="{{ route('public.matches.show', [$league, $match]) }}"
                                   class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition-colors">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
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
</body>
</html>