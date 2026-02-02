<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Match Results - {{ $match->homeTeam?->name ?? $match->homePlayer?->name ?? 'Home' }} vs {{ $match->awayTeam?->name ?? $match->awayPlayer?->name ?? 'Away' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Auto refresh every 30 seconds -->
    <meta http-equiv="refresh" content="30">

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
        .embed-container {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-gray-900 text-white p-4">
    <div class="embed-container">
        <!-- Match Header -->
        <div class="text-center mb-4">
            <div class="text-xs text-gray-400 mb-1">
                @if($match->competition)
                    {{ $match->competition->name }}
                @elseif($match->competition)
                    {{ $match->competition->name }}
                @endif
                • Round {{ $match->round ?? 'N/A' }}
            </div>
            <div class="flex items-center justify-center space-x-2 text-xs text-gray-400">
                <span>{{ ucfirst(str_replace('_', ' ', $match->status)) }}</span>
                @if($match->status === 'in_progress')
                <span class="text-green-400">🔴 LIVE</span>
                @endif
            </div>
        </div>

        <!-- Score Display -->
        <div class="grid grid-cols-3 gap-4 items-center">
            <!-- Home -->
            <div class="text-center">
                <div class="text-sm font-semibold text-blue-400 mb-2 truncate">
                    @if($match->competition && $match->competition->is_team_based)
                        {{ $match->homeTeam?->name ?? 'Home' }}
                    @elseif($match->competition && $match->competition->is_team_based)
                        {{ $match->homeTeam?->name ?? 'Home' }}
                    @else
                        {{ $match->homePlayer?->name ?? 'Home' }}
                    @endif
                </div>
                <div class="text-3xl md:text-4xl font-bold text-blue-400">
                    {{ $match->home_score ?? 0 }}
                </div>
            </div>

            <!-- Center -->
            <div class="text-center">
                <div class="text-xl font-bold text-gray-400 mb-2">VS</div>
                @if($match->sets && count($match->sets) > 0)
                <div class="text-xs text-gray-500">
                    Sets: {{ implode(' | ', array_map(function($set) {
                        return ($set['home_score'] ?? $set['home'] ?? 0) . '-' . ($set['away_score'] ?? $set['away'] ?? 0);
                    }, $match->sets)) }}
                </div>
                @endif
            </div>

            <!-- Away -->
            <div class="text-center">
                <div class="text-sm font-semibold text-red-400 mb-2 truncate">
                    @if($match->competition && $match->competition->is_team_based)
                        {{ $match->awayTeam?->name ?? 'Away' }}
                    @elseif($match->competition && $match->competition->is_team_based)
                        {{ $match->awayTeam?->name ?? 'Away' }}
                    @else
                        {{ $match->awayPlayer?->name ?? 'Away' }}
                    @endif
                </div>
                <div class="text-3xl md:text-4xl font-bold text-red-400">
                    {{ $match->away_score ?? 0 }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 pt-4 border-t border-gray-700">
            <div class="text-xs text-gray-500">
                Powered by TeamSphere
                @if($match->moderator)
                • Referee: {{ $match->moderator->name }}
                @endif
            </div>
            <div class="text-xs text-gray-600 mt-1">
                Last updated: {{ now()->format('g:i A') }}
            </div>
        </div>
    </div>

    <!-- Embed script for dynamic sizing -->
    <script>
        // Auto-resize for embed
        function resizeEmbed() {
            if (window.parent !== window) {
                const height = document.body.scrollHeight;
                window.parent.postMessage({ type: 'resize', height: height }, '*');
            }
        }

        // Resize on load and content changes
        window.addEventListener('load', resizeEmbed);
        window.addEventListener('resize', resizeEmbed);

        // Resize every 5 seconds as backup
        setInterval(resizeEmbed, 5000);
    </script>
</body>
</html>
