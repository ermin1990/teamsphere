<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Semafor - Team Sphere</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .tournament-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 20px;
            color: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }

        .tournament-card:hover {
            transform: translateY(-5px);
        }

        .match-card {
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 16px;
            margin: 8px 0;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .live-indicator {
            background: #ff4757;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .grid-display {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .selection-panel {
            background: #1a1a1a;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .checkbox-custom {
            appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #667eea;
            border-radius: 4px;
            position: relative;
            cursor: pointer;
        }

        .checkbox-custom:checked {
            background: #667eea;
        }

        .checkbox-custom:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .grid-display {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }

        @media (max-width: 480px) {
            .grid-display {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }
    </style>
</head>
<body class="antialiased bg-gray-900 text-white min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
                Semafor - Live Turniri
            </h1>
            <p class="text-gray-400">Odaberite turnire koje želite prikazati na semaforu</p>
        </div>

        <!-- Selection Panel -->
        @if($liveCompetitions->count() > 0)
        <div class="selection-panel">
            <h2 class="text-2xl font-bold mb-6 text-center">Odaberite Turnire za Prikaz</h2>

            <form id="selectionForm" method="GET" action="{{ route('semafor') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($liveCompetitions as $competition)
                    <div class="tournament-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <input type="checkbox"
                                       name="leagues[]"
                                       value="{{ $competition->id }}"
                                       id="league-{{ $competition->id }}"
                                       class="checkbox-custom"
                                       {{ in_array($competition->id, $selectedCompetitionIds) ? 'checked' : '' }}>
                                <label for="league-{{ $competition->id }}" class="cursor-pointer flex-1">
                                    <h3 class="text-xl font-bold">{{ $competition->name }}</h3>
                                    <p class="text-sm opacity-80">{{ $competition->organization->name }}</p>
                                </label>
                            </div>
                            <span class="live-indicator">LIVE</span>
                        </div>

                        <div class="space-y-2">
                            @foreach($competition->leagueMatches->take(3) as $match)
                            <div class="match-card">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-semibold">
                                        @if($competition->is_team_based)
                                            {{ $match->homeTeam?->name ?? 'TBD' }} vs {{ $match->awayTeam?->name ?? 'TBD' }}
                                        @else
                                            {{ $match->homePlayer?->name ?? 'TBD' }} vs {{ $match->awayPlayer?->name ?? 'TBD' }}
                                        @endif
                                    </span>
                                    <span class="text-xs opacity-70">{{ $match->scheduled_at ? $match->scheduled_at->format('H:i') : 'N/A' }}</span>
                                </div>
                            </div>
                            @endforeach
                            @if($competition->leagueMatches->count() > 3)
                            <p class="text-xs opacity-70 text-center">+{{ $competition->leagueMatches->count() - 3 }} više mečeva</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="text-center mt-8">
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                        Prikaži Odabrane Turnire
                    </button>
                </div>
            </form>
        </div>
        @endif

        <!-- Display Grid -->
        @if($selectedCompetitions->count() > 0)
        <div class="grid-display">
            @foreach($selectedCompetitions as $competition)
            <div class="tournament-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold">{{ $competition->name }}</h3>
                    <span class="live-indicator">LIVE</span>
                </div>

                <div class="space-y-3">
                    @foreach($competition->leagueMatches as $match)
                    <div class="match-card">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                @if($competition->is_team_based)
                                    <div class="font-semibold text-sm">
                                        {{ $match->homeTeam?->name ?? 'TBD' }}
                                    </div>
                                    <div class="text-xs opacity-70">vs</div>
                                    <div class="font-semibold text-sm">
                                        {{ $match->awayTeam?->name ?? 'TBD' }}
                                    </div>
                                @else
                                    <div class="font-semibold text-sm">
                                        {{ $match->homePlayer?->name ?? 'TBD' }}
                                    </div>
                                    <div class="text-xs opacity-70">vs</div>
                                    <div class="font-semibold text-sm">
                                        {{ $match->awayPlayer?->name ?? 'TBD' }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold">
                                    @if($match->home_score !== null && $match->away_score !== null)
                                        {{ $match->home_score }} - {{ $match->away_score }}
                                    @else
                                        VS
                                    @endif
                                </div>
                                <div class="text-xs opacity-70">
                                    {{ $match->scheduled_at ? $match->scheduled_at->format('H:i') : 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">Nema Odabranih Turnira</h3>
            <p class="text-gray-400 max-w-md mx-auto">
                Odaberite turnire iznad da ih prikažete na semaforu. Samo turniri sa live mečevima su dostupni za odabir.
            </p>
        </div>
        @endif

        @if($liveCompetitions->count() == 0)
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">Nema Live Turnira</h3>
            <p class="text-gray-400 max-w-md mx-auto">
                Trenutno nema turnira sa live mečevima. Provjerite kasnije ili kreirajte nove mečeve.
            </p>
        </div>
        @endif
    </div>

    <script>
        // Auto-refresh functionality (optional)
        setInterval(function() {
            // Only refresh if no form is being submitted
            if (!document.querySelector('form:invalid') && !document.activeElement.closest('form')) {
                location.reload();
            }
        }, 30000); // Refresh every 30 seconds
    </script>
</body>
</html>