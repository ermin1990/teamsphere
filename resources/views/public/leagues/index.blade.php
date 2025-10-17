<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Table Tennis Leagues - TeamSphere</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <a href="{{ route('public.leagues.index') }}" class="text-blue-400 font-semibold text-sm md:text-base">
                        🏆 Leagues
                    </a>
                </div>
            </nav>

            <!-- Header -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-8">
                <h1 class="text-3xl font-bold text-center bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                    🏓 Table Tennis Leagues
                </h1>
                <p class="text-gray-400 text-center mt-2">Choose a league to view standings and matches</p>
            </div>

            <!-- Leagues List -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($competitions as $competition)
                <a href="{{ route('public.leagues.show', $competition) }}"
                   class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:bg-gray-800/70 hover:border-gray-600/50 transition-all duration-200 group">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <span class="text-2xl">🏓</span>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">{{ $competition->name }}</h3>
                        <p class="text-gray-400 text-sm mb-2">{{ $competition->organization->name }}</p>
                        <p class="text-gray-500 text-xs">{{ $competition->sport->name }}</p>
                        <div class="mt-4 text-blue-400 font-medium">
                            View League →
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            @if($competitions->isEmpty())
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🏓</div>
                <h2 class="text-2xl font-bold text-gray-400 mb-2">No Leagues Available</h2>
                <p class="text-gray-500">Check back later for upcoming table tennis leagues.</p>
            </div>
            @endif

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-400 text-sm">
                <p>Powered by TeamSphere</p>
            </div>

            @if($competitions->isEmpty())
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🏓</div>
                <h2 class="text-2xl font-bold text-gray-400 mb-2">No Leagues Available</h2>
                <p class="text-gray-500">Check back later for upcoming table tennis leagues.</p>
            </div>
            @endif

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-400 text-sm">
                <p>Powered by TeamSphere</p>
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
