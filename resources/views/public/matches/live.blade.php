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
<body class="antialiased bg-gray-900 text-white min-h-screen pb-16 md:pb-8">
    <div class="py-8">
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
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-4 border border-gray-700/50 shadow-xl mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                            🏓 Live Table Tennis Score
                        </h1>
                        <p class="text-gray-400 text-sm md:text-base mt-1">{{ $competition->name }} • Round {{ $match->round }}</p>
                    </div>
                    <div class="flex items-center space-x-2 md:space-x-4">
                        <a href="{{ route('public.matches.show', [$competition, $match]) }}"
                           class="px-3 py-1 md:px-4 md:py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors text-sm">
                            ← Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Live Score Display -->
            @livewire('public-live-score', ['match' => $match])

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
