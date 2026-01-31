<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $competition->name }} - Projektor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Optimize for 1024x768 projector resolution */
        body {
            margin: 0;
            padding: 0;
            width: 1024px;
            min-height: 768px;
            overflow-x: hidden;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #ffffff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .projector-container {
            width: 1024px;
            min-height: 768px;
            padding: 12px;
            box-sizing: border-box;
        }

        /* Compact header */
        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 8px 16px;
            margin-bottom: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header .org-name {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 2px;
        }

        /* Knockout bracket optimized for projector */
        .knockout-section {
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
            padding: 6px 12px;
            background: rgba(147, 51, 234, 0.2);
            border-left: 3px solid #9333ea;
            border-radius: 4px;
        }

        .knockout-rounds {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .round-column {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 6px;
            padding: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .round-title {
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            color: #a78bfa;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid rgba(167, 139, 250, 0.3);
        }

        .match-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            padding: 6px 8px;
            margin-bottom: 6px;
            border-left: 2px solid transparent;
            font-size: 11px;
        }

        .match-card.completed {
            border-left-color: #10b981;
        }

        .match-card.in_progress {
            border-left-color: #f59e0b;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .match-players {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 6px;
        }

        .player-name {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 10px;
        }

        .player-name.winner {
            font-weight: 700;
            color: #10b981;
        }

        .score {
            font-weight: 700;
            font-size: 12px;
            min-width: 20px;
            text-align: center;
        }

        .vs {
            color: rgba(255, 255, 255, 0.4);
            font-size: 9px;
        }

        /* Groups optimized for projector */
        .groups-section {
            margin-bottom: 16px;
        }

        .groups-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .group-card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 6px;
            padding: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .group-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
            padding: 4px;
            border-radius: 4px;
            margin-bottom: 6px;
        }

        .standings-table {
            width: 100%;
            font-size: 9px;
        }

        .standings-table th {
            background: rgba(255, 255, 255, 0.05);
            padding: 3px 4px;
            text-align: left;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .standings-table td {
            padding: 3px 4px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .standings-table tr.advancing {
            background: rgba(16, 185, 129, 0.1);
        }

        .matches-list {
            margin-top: 6px;
            max-height: 150px;
            overflow-y: auto;
        }

        .matches-list::-webkit-scrollbar {
            width: 4px;
        }

        .matches-list::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 2px;
        }

        .matches-list::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
        }

        /* Compact match in groups */
        .group-match {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 3px 6px;
            margin-bottom: 3px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 3px;
            font-size: 9px;
        }

        .group-match.completed {
            background: rgba(16, 185, 129, 0.1);
        }

        /* Live indicator */
        .live-indicator {
            display: inline-block;
            width: 6px;
            height: 6px;
            background: #ef4444;
            border-radius: 50%;
            margin-right: 4px;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Auto-refresh indicator */
        .refresh-indicator {
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(16, 185, 129, 0.9);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .refresh-indicator.active {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="projector-container">
        <!-- Header -->
        <div class="header">
            <h1>⚽ {{ $competition->name }}</h1>
            <div class="org-name">{{ $competition->organization->name }}</div>
        </div>

        @if($competition->type === 'tournament')
            <!-- Knockout Phase Section -->
            @if($knockoutMatches && $knockoutMatches->count() > 0)
            <div class="knockout-section">
                <div class="section-title">🏆 Eliminaciona Faza</div>
                
                @php
                    $rounds = $knockoutMatches->groupBy('round_number');
                    $maxRound = $rounds->keys()->max();
                @endphp

                <div class="knockout-rounds">
                    @foreach($rounds as $roundNumber => $roundMatches)
                        @php
                            $roundName = match($roundNumber) {
                                1 => $roundMatches->count() === 1 ? 'Finale' : ($roundMatches->count() === 2 ? 'Polufinale' : 'Kolo 1'),
                                2 => $roundMatches->count() === 1 ? 'Finale' : ($roundMatches->count() === 2 ? 'Polufinale' : 'Kolo 2'),
                                3 => 'Polufinale',
                                4 => 'Četvrtfinale',
                                default => "Kolo {$roundNumber}"
                            };
                        @endphp

                        <div class="round-column">
                            <div class="round-title">{{ $roundName }}</div>
                            
                            @foreach($roundMatches as $match)
                                <div class="match-card {{ $match->status }}">
                                    <div class="match-players">
                                        <div class="player-name {{ $match->status === 'completed' && $match->home_score > $match->away_score ? 'winner' : '' }}">
                                            {{ $match->homePlayer ? $match->homePlayer->name : 'TBD' }}
                                        </div>
                                        <div class="score">{{ $match->home_score ?? '-' }}</div>
                                        @if($match->status === 'in_progress')
                                            <span class="live-indicator"></span>
                                        @else
                                            <span class="vs">:</span>
                                        @endif
                                        <div class="score">{{ $match->away_score ?? '-' }}</div>
                                        <div class="player-name {{ $match->status === 'completed' && $match->away_score > $match->home_score ? 'winner' : '' }}">
                                            {{ $match->awayPlayer ? $match->awayPlayer->name : 'TBD' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Groups Section -->
            @if($competition->tournamentGroups->count() > 0)
            <div class="groups-section">
                <div class="section-title">📋 Grupna Faza</div>
                
                <div class="groups-grid">
                    @foreach($competition->tournamentGroups as $group)
                        @php
                            $matchesInGroup = $groupMatches->get($group->id, collect());
                            $standings = App\Models\Standing::where('competition_id', $competition->id)
                                ->where('tournament_group_id', $group->id)
                                ->with('player')
                                ->orderBy('points', 'desc')
                                ->orderByRaw('(sets_won - sets_lost) desc')
                                ->orderByRaw('(points_won - points_lost) desc')
                                ->orderBy('id')
                                ->get();
                        @endphp

                        <div class="group-card">
                            <div class="group-header">Grupa {{ $group->name }}</div>
                            
                            <!-- Standings -->
                            <table class="standings-table">
                                <thead>
                                    <tr>
                                        <th style="width: 30px;">#</th>
                                        <th>Igrač</th>
                                        <th style="width: 25px; text-align: center;">M</th>
                                        <th style="width: 25px; text-align: center;">Bod</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($standings as $index => $standing)
                                    <tr class="{{ $index < ($competition->players_advancing_per_group ?? 2) ? 'advancing' : '' }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td style="font-weight: 600;">{{ Str::limit($standing->player->name, 20) }}</td>
                                        <td style="text-align: center;">{{ $standing->played }}</td>
                                        <td style="text-align: center; font-weight: 700; color: #10b981;">{{ $standing->points }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Recent Matches -->
                            @if($matchesInGroup->count() > 0)
                            <div class="matches-list">
                                @foreach($matchesInGroup->sortByDesc('id')->take(5) as $match)
                                <div class="group-match {{ $match->status }}">
                                    <span style="flex: 1;">{{ Str::limit($match->homePlayer->name ?? 'TBD', 12) }}</span>
                                    <span style="font-weight: 700;">{{ $match->home_score ?? '-' }} : {{ $match->away_score ?? '-' }}</span>
                                    <span style="flex: 1; text-align: right;">{{ Str::limit($match->awayPlayer->name ?? 'TBD', 12) }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endif

        <!-- Auto-refresh indicator -->
        <div class="refresh-indicator" id="refreshIndicator">
            🔄 Ažuriranje...
        </div>
    </div>

    <script>
        // Auto-refresh every 10 seconds
        let refreshInterval = setInterval(function() {
            const indicator = document.getElementById('refreshIndicator');
            indicator.classList.add('active');
            
            setTimeout(function() {
                location.reload();
            }, 500);
        }, 10000);

        // Show timestamp on load
        console.log('Projector view loaded at: ' + new Date().toLocaleTimeString());
    </script>
</body>
</html>
