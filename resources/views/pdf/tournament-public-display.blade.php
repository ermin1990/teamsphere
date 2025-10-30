<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $competition->name }} - Turnirski Prikaz</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #000000;
            margin: 0;
            padding: 20px;
            background: #ffffff;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border: 2px solid #000000;
            border-radius: 0;
        }

        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            color: #000000;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #333333;
            font-weight: normal;
        }

        .content-section {
            background: #ffffff;
            border: 2px solid #000000;
            padding: 24px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid #000000;
            text-transform: uppercase;
        }

        .group-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .group-card {
            background: #ffffff;
            border: 2px solid #000000;
            page-break-inside: avoid;
        }

        .group-header {
            background: #000000;
            color: #ffffff;
            padding: 16px 20px;
            border-bottom: 2px solid #000000;
        }

        .group-title {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .group-subtitle {
            font-size: 12px;
            opacity: 0.9;
            margin: 4px 0 0 0;
        }

        .standings-section {
            margin-bottom: 20px;
        }

        .standings-title {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #000000;
            text-transform: uppercase;
        }

        .standings-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000000;
        }

        .standings-table th {
            background: #f0f0f0;
            color: #000000;
            padding: 8px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #000000;
        }

        .standings-table td {
            padding: 6px 12px;
            border: 1px solid #cccccc;
            font-size: 11px;
            color: #000000;
        }

        .standings-table tr:nth-child(1) {
            background: #e0e0e0;
            font-weight: bold;
        }

        .standings-table tr:nth-child(2) {
            background: #f0f0f0;
        }

        .matches-section {
            margin-top: 20px;
        }

        .matches-title {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #000000;
            text-transform: uppercase;
        }

        .match-card {
            background: #ffffff;
            border: 1px solid #000000;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .match-header {
            background: #f0f0f0;
            padding: 8px 12px;
            font-size: 11px;
            color: #000000;
            border-bottom: 1px solid #cccccc;
            font-weight: bold;
        }

        .match-content {
            padding: 12px;
        }

        .match-players {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .player-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .player-sets-won {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            background: #ffffff;
            border: 2px solid #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            color: #000000;
            flex-shrink: 0;
        }

        .player-name {
            font-size: 12px;
            font-weight: 600;
            color: #000000;
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sets-display {
            display: flex;
            gap: 2px;
            margin-left: 12px;
        }

        .set-score {
            width: 18px;
            text-align: center;
            font-size: 10px;
            padding: 2px 4px;
            border-radius: 3px;
            border: 1px solid #000000;
        }

        .set-score.winner {
            background: #000000;
            color: #ffffff;
            font-weight: bold;
        }

        .set-score.loser {
            background: #ffffff;
            color: #000000;
        }

        .final-score {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
            margin-left: 8px;
            flex-shrink: 0;
        }

        .knockout-section {
            background: #ffffff;
            border: 2px solid #000000;
            padding: 20px;
            margin-bottom: 20px;
            page-break-before: always;
            page-break-inside: avoid;
        }

        .knockout-title {
            font-size: 20px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid #000000;
            text-transform: uppercase;
        }

        .bracket-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        .bracket-rounds {
            display: flex;
            gap: 24px;
            justify-content: center;
            align-items: flex-start;
        }

        .bracket-round {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 200px;
        }

        .round-title {
            background: #000000;
            color: #ffffff;
            padding: 8px 16px;
            margin-bottom: 16px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid #000000;
        }

        .round-matches {
            display: flex;
            flex-direction: column;
            gap: 12px;
            justify-content: center;
            min-height: 300px;
        }

        .bracket-match {
            background: #ffffff;
            border: 1px solid #000000;
            border-radius: 8px;
            padding: 12px;
            min-width: 180px;
            position: relative;
            page-break-inside: avoid;
        }

        .bracket-match-header {
            font-size: 10px;
            color: #666666;
            margin-bottom: 8px;
            text-align: center;
            padding-bottom: 4px;
            border-bottom: 1px solid #cccccc;
        }

        .bracket-player {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 11px;
        }

        .bracket-player-name {
            color: #000000;
            font-weight: 500;
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bracket-player-score {
            font-size: 12px;
            font-weight: 700;
            color: #000000;
            margin-left: 8px;
            flex-shrink: 0;
        }

        .bracket-connector {
            position: absolute;
            top: 50%;
            right: -24px;
            width: 24px;
            height: 1px;
            background: #000000;
            transform: translateY(-50%);
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #000000;
            font-size: 10px;
            color: #666666;
        }

        @page {
            margin: 20mm;
            size: A4;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $competition->name }}</h1>
        <p><strong>Organizacija:</strong> {{ $organization->name }}</p>
        <p><strong>Sport:</strong> {{ $competition->sport->name ?? 'N/A' }}</p>
        <p><strong>Datum generisanja:</strong> {{ now()->format('d.m.Y H:i') }}</p>
    </div>

    <!-- Groups Phase -->
    @if($hasGroups)
    <div class="content-section">
        <div class="section-title">
            🏆 Grupna Faza
        </div>

        <div class="group-grid">
            @foreach($competition->tournamentGroups as $group)
            @php
                $currentGroupMatches = $group->matches ?? collect();
            @endphp
            @if($currentGroupMatches->count() > 0)
            <div class="group-card">
                <div class="group-header">
                    <h3 class="group-title">{{ $group->name }}</h3>
                    <p class="group-subtitle">{{ $group->standings->count() }} igrača • {{ $currentGroupMatches->count() }} mečeva</p>
                </div>

                <!-- Group Standings -->
                @php
                    $groupStandings = $group->standings()->with('player')->orderBy('position')->get();
                @endphp
                @if($groupStandings->count() > 0)
                <div class="standings-section">
                    <h4 class="standings-title">Tabela</h4>

                    <!-- Table Header -->
                    <div class="grid grid-cols-12 gap-2 mb-2 text-xs text-gray-400 font-medium px-2">
                        <div class="col-span-6"></div>
                        <div class="col-span-1 text-center">Pob</div>
                        <div class="col-span-1 text-center">Rem</div>
                        <div class="col-span-1 text-center">Por</div>
                        <div class="col-span-1 text-center">Set ±</div>
                        <div class="col-span-2 text-center">Bod</div>
                    </div>

                    <!-- Table Rows -->
                    <div class="space-y-1">
                        @foreach($groupStandings as $standing)
                        <div class="grid grid-cols-12 gap-2 items-center py-2 px-2 bg-gray-700/20 rounded text-xs md:text-sm">
                            <div class="col-span-6 flex items-center space-x-2">
                                <span class="font-bold text-gray-400 w-6 text-center">{{ $standing->position }}</span>
                                <span class="text-black font-medium text-xs truncate">{{ $standing->player->name }}</span>
                            </div>
                            <div class="col-span-1 text-center">
                                <span class="text-green-400 font-bold">{{ $standing->won ?? 0 }}</span>
                            </div>
                            <div class="col-span-1 text-center">
                                <span class="text-yellow-400 font-bold">{{ $standing->drawn ?? 0 }}</span>
                            </div>
                            <div class="col-span-1 text-center">
                                <span class="text-red-400 font-bold">{{ $standing->lost ?? 0 }}</span>
                            </div>
                            <div class="col-span-1 text-center">
                                <span class="text-cyan-400 font-bold">{{ ($standing->sets_won ?? 0) - ($standing->sets_lost ?? 0) }}</span>
                            </div>
                            <div class="col-span-2 text-center">
                                <span class="text-blue-400 font-bold">{{ $standing->points ?? 0 }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Group Matches -->
                @if($currentGroupMatches->count() > 0)
                <div class="matches-section">
                    <h4 class="matches-title">Mečevi</h4>
                    <div class="space-y-1 md:space-y-3">
                        @foreach($currentGroupMatches as $match)
                        @if($match->status === 'in_progress')
                        <div class="text-center mb-2">
                            <span class="text-red-400 font-semibold text-xs uppercase tracking-wider">Live</span>
                        </div>
                        @endif

                        @php
                            $homeSetsWon = 0;
                            $awaySetsWon = 0;
                            if($match->played_at) {
                                if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
                                    foreach($match->sets as $set) {
                                        if(($set['home_score'] ?? 0) > ($set['away_score'] ?? 0)) {
                                            $homeSetsWon++;
                                        }
                                        if(($set['away_score'] ?? 0) > ($set['home_score'] ?? 0)) {
                                            $awaySetsWon++;
                                        }
                                    }
                                } elseif ($match->status === 'completed') {
                                    $homeSetsWon = $match->home_score ?? 0;
                                    $awaySetsWon = $match->away_score ?? 0;
                                }
                            }
                            $displaySets = isset($match->sets) && is_array($match->sets) && count($match->sets) > 0 ? $match->sets : [];
                        @endphp

                        <div class="match-card">
                            <div class="match-header">
                                Meč {{ $loop->iteration }}
                                @if($match->status === 'completed')
                                    <span>✓ Završen</span>
                                @elseif($match->status === 'in_progress')
                                    <span>🔴 U tijeku</span>
                                @else
                                    <span>⏳ Zakazan</span>
                                @endif
                            </div>

                            <div class="match-content">
                                <div class="flex items-center justify-between">
                                    <!-- Left side: Players and sets -->
                                    <div class="flex-1 space-y-4">
                                        <!-- Home Player -->
                                        <div class="flex items-center gap-3">
                                            <!-- Sets won indicator -->
                                            <div class="player-sets-won">
                                                @if($match->status === 'completed' || $match->status === 'in_progress')
                                                    {{ $homeSetsWon }}
                                                @else
                                                    <span>0</span>
                                                @endif
                                            </div>
                                            <div class="player-name">
                                                {{ $match->homePlayer->name ?? 'Home Player' }}
                                            </div>
                                            <!-- Sets -->
                                            <div class="sets-display">
                                                @for($i = 1; $i <= 5; $i++)
                                                <div class="w-6 text-center {{ $i < 5 ? 'border-r border-gray-600/30' : '' }}">
                                                    @if(isset($displaySets[$i-1]))
                                                        @php
                                                            $homeScore = $displaySets[$i-1]['home_score'] ?? $displaySets[$i-1]['home'] ?? 0;
                                                            $awayScore = $displaySets[$i-1]['away_score'] ?? $displaySets[$i-1]['away'] ?? 0;
                                                        @endphp
                                                        <span class="set-score {{ $homeScore > $awayScore ? 'winner' : 'loser' }}">
                                                            {{ $homeScore }}
                                                        </span>
                                                    @else
                                                        <span class="set-score loser">-</span>
                                                    @endif
                                                </div>
                                                @endfor
                                            </div>
                                        </div>

                                        <!-- Away Player -->
                                        <div class="flex items-center gap-3">
                                            <!-- Sets won indicator -->
                                            <div class="player-sets-won">
                                                @if($match->status === 'completed' || $match->status === 'in_progress')
                                                    {{ $awaySetsWon }}
                                                @else
                                                    <span>0</span>
                                                @endif
                                            </div>
                                            <div class="player-name">
                                                {{ $match->awayPlayer->name ?? 'Away Player' }}
                                            </div>
                                            <!-- Sets -->
                                            <div class="sets-display">
                                                @for($i = 1; $i <= 5; $i++)
                                                <div class="w-6 text-center {{ $i < 5 ? 'border-r border-gray-600/30' : '' }}">
                                                    @if(isset($displaySets[$i-1]))
                                                        @php
                                                            $homeScore = $displaySets[$i-1]['home_score'] ?? $displaySets[$i-1]['home'] ?? 0;
                                                            $awayScore = $displaySets[$i-1]['away_score'] ?? $displaySets[$i-1]['away'] ?? 0;
                                                        @endphp
                                                        <span class="set-score {{ $awayScore > $homeScore ? 'winner' : 'loser' }}">
                                                            {{ $awayScore }}
                                                        </span>
                                                    @else
                                                        <span class="set-score loser">-</span>
                                                    @endif
                                                </div>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right side: Final scores -->
                                    <div class="flex flex-col items-center justify-center space-y-2 ml-6">
                                        @if($match->status === 'in_progress')
                                            <div class="flex flex-col items-center space-y-2">
                                                <div class="final-score">
                                                    {{ $match->home_score ?? 0 }}
                                                </div>
                                                <div class="final-score">
                                                    {{ $match->away_score ?? 0 }}
                                                </div>
                                            </div>
                                        @elseif($match->status === 'completed')
                                            <div class="flex flex-col items-center space-y-2">
                                                <div class="final-score">
                                                    {{ $homeSetsWon }}
                                                </div>
                                                <div class="final-score">
                                                    {{ $awaySetsWon }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center space-y-2">
                                                <div class="final-score">-</div>
                                                <div class="final-score">-</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Knockout Phase - New Page -->
    @if($hasKnockoutMatches)
    <div class="page-break"></div>

    <div class="knockout-section">
        <div class="knockout-title">
            🏅 Eliminaciona Faza
        </div>

        @php
            $totalRounds = $knockoutMatches->count();
            $firstRoundMatches = $knockoutMatches->get(1) ?? collect();
            $numPlayers = $firstRoundMatches->count() * 2;
            $expectedRounds = $numPlayers > 1 ? ceil(log($numPlayers, 2)) : 1;

            $roundNames = [
                1 => $expectedRounds == 4 ? 'Šesnaestina finala' : ($expectedRounds == 3 ? 'Četvrtfinale' : ($expectedRounds == 2 ? 'Polufinale' : 'Runda 1')),
                2 => $expectedRounds == 4 ? 'Četvrtfinale' : ($expectedRounds == 3 ? 'Polufinale' : ($expectedRounds == 2 ? 'Finale' : 'Runda 2')),
                3 => $expectedRounds == 4 ? 'Polufinale' : 'Finale',
                4 => 'Finale',
            ];

            // Check if tournament is completed and get winner
            $finalMatch = $knockoutMatches->get($totalRounds)?->first();
            $winner = null;
            if ($finalMatch && $finalMatch->status === 'completed' && $totalRounds == $expectedRounds) {
                $winner = $finalMatch->home_score > $finalMatch->away_score
                    ? $finalMatch->homePlayer
                    : $finalMatch->awayPlayer;
            }
        @endphp

        <!-- Winner Display (if tournament is completed) -->
        @if($winner)
        <div class="mb-6 md:mb-8 text-center">
            <div class="text-center">
                <h2 class="text-base md:text-lg font-semibold text-black mb-2 md:mb-3 tracking-wide">
                    🏆 ŠAMPION TURNIRA 🏆
                </h2>
                <p class="text-xl md:text-2xl font-black text-black mb-2" style="font-family: 'Inter', sans-serif; letter-spacing: -0.02em;">
                    {{ $winner->name }}
                </p>
                <p class="text-sm md:text-base text-gray-600 font-medium">{{ $competition->name }}</p>
            </div>
        </div>
        @endif

        <!-- Tournament Bracket -->
        <div class="bracket-container">
            <div class="min-w-max">
                <!-- Bracket Container -->
                <div class="bracket-rounds">
                    @for($round = 1; $round <= $totalRounds; $round++)
                    @php
                        $roundMatches = $knockoutMatches->get($round) ?? collect();
                        // Calculate spacing for bracket alignment
                        $matchesInRound = $roundMatches->count();
                        $spacingMultiplier = pow(2, $round - 1);
                    @endphp
                    <div class="flex flex-col justify-center gap-4" style="gap: {{ $spacingMultiplier * 1 }}rem;">
                        <!-- Round Header -->
                        <div class="text-center mb-4">
                            <h4 class="round-title">
                                {{ $roundNames[$round] ?? 'Runda ' . $round }}
                            </h4>
                        </div>

                        <!-- Round Matches -->
                        <div class="round-matches">
                            @foreach($roundMatches as $index => $match)
                            @php
                                $homeSetsWon = 0;
                                $awaySetsWon = 0;
                                if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
                                    foreach($match->sets as $set) {
                                        if(($set['home_score'] ?? $set['home'] ?? 0) > ($set['away_score'] ?? $set['away'] ?? 0)) {
                                            $homeSetsWon++;
                                        }
                                        if(($set['away_score'] ?? $set['away'] ?? 0) > ($set['home_score'] ?? $set['home'] ?? 0)) {
                                            $awaySetsWon++;
                                        }
                                    }
                                } elseif ($match->status === 'completed') {
                                    $homeSetsWon = $match->home_score ?? 0;
                                    $awaySetsWon = $match->away_score ?? 0;
                                }
                            @endphp

                            <div class="bracket-match">
                                @if($match->status === 'in_progress' && !$match->is_bye)
                                <div class="text-center mb-2">
                                    <span class="text-red-400 font-semibold text-xs uppercase tracking-wider">Live</span>
                                </div>
                                @endif

                                <!-- Match Players -->
                                <div class="p-3 md:p-4">
                                    <!-- Home Player -->
                                    <div class="bracket-player">
                                        <div class="flex items-center gap-2 flex-1 min-w-0">
                                            <div class="player-sets-won">
                                                @if($match->status === 'completed')
                                                    {{ $homeSetsWon }}
                                                @elseif($match->status === 'in_progress')
                                                    <span>{{ $homeSetsWon }}</span>
                                                @else
                                                    <span>0</span>
                                                @endif
                                            </div>
                                            <div class="bracket-player-name">
                                                {{ $match->homePlayer->name ?? 'NEMA PROTIVNIKA' }}
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ml-2">
                                            @if($match->status === 'in_progress' && !$match->is_bye)
                                                <div class="final-score">
                                                    {{ $match->home_score ?? 0 }}
                                                </div>
                                            @elseif($match->status === 'completed')
                                                <div class="final-score">
                                                    {{ $homeSetsWon }}
                                                </div>
                                            @elseif($match->is_bye)
                                                <div class="final-score">bye</div>
                                            @else
                                                <div class="final-score">-</div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Away Player -->
                                    <div class="bracket-player">
                                        <div class="flex items-center gap-2 flex-1 min-w-0">
                                            <div class="player-sets-won">
                                                @if($match->status === 'completed')
                                                    {{ $awaySetsWon }}
                                                @elseif($match->status === 'in_progress')
                                                    <span>{{ $awaySetsWon }}</span>
                                                @else
                                                    <span>0</span>
                                                @endif
                                            </div>
                                            <div class="bracket-player-name">
                                                {{ $match->awayPlayer->name ?? 'NEMA PROTIVNIKA' }}
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ml-2">
                                            @if($match->status === 'in_progress' && !$match->is_bye)
                                                <div class="final-score">
                                                    {{ $match->away_score ?? 0 }}
                                                </div>
                                            @elseif($match->status === 'completed')
                                                <div class="final-score">
                                                    {{ $awaySetsWon }}
                                                </div>
                                            @elseif($match->is_bye)
                                                <div class="final-score">bye</div>
                                            @else
                                                <div class="final-score">-</div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Set Details Display -->
                                    @if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0)
                                    <div class="mt-3 pt-3 border-t border-gray-600/30">
                                        <div class="flex justify-center gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                            <div class="flex flex-col items-center">
                                                <div class="text-xs text-gray-400 mb-1">{{ $i }}</div>
                                                <div class="flex flex-col gap-0.5">
                                                    @if(isset($match->sets[$i-1]))
                                                        @php
                                                            $homeScore = $match->sets[$i-1]['home_score'] ?? $match->sets[$i-1]['home'] ?? 0;
                                                            $awayScore = $match->sets[$i-1]['away_score'] ?? $match->sets[$i-1]['away'] ?? 0;
                                                        @endphp
                                                        <span class="set-score {{ $homeScore > $awayScore ? 'winner' : 'loser' }}">
                                                            {{ $homeScore }}
                                                        </span>
                                                        <span class="set-score {{ $awayScore > $homeScore ? 'winner' : 'loser' }}">
                                                            {{ $awayScore }}
                                                        </span>
                                                    @else
                                                        <span class="set-score loser">-</span>
                                                        <span class="set-score loser">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @endfor
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Bracket Lines (except for the last round) -->
                    @if($round < $totalRounds)
                    <div class="flex items-center justify-center" style="margin-top: {{ $spacingMultiplier * 0.5 }}rem; margin-bottom: {{ $spacingMultiplier * 0.5 }}rem;">
                        <div class="w-8 h-px bg-gray-600/50"></div>
                    </div>
                    @endif
                    @endfor
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Generisano: {{ now()->format('d.m.Y H:i') }} | {{ config('app.name') }}</p>
    </div>
</body>
</html>