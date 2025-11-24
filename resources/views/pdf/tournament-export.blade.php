<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $competition->name }} - Izvještaj</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3b82f6;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            color: #1e40af;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
            color: #6b7280;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background: #3b82f6;
            color: white;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .group-container {
            width: 100%;
            margin-bottom: 0;
            padding: 20px 0;
            page-break-after: always;
            page-break-inside: avoid;
        }
        .group-container:last-child {
            page-break-after: auto;
        }
        .group-title {
            background: #dbeafe;
            color: #1e40af;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: bold;
            border-left: 3px solid #3b82f6;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            background: #f3f4f6;
            color: #374151;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border-bottom: 2px solid #d1d5db;
        }
        td {
            padding: 5px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }
        tr:hover {
            background: #f9fafb;
        }
        .position-1 {
            background: #d1fae5;
            font-weight: bold;
        }
        .position-2 {
            background: #fef3c7;
        }
        .match-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 8px;
            margin-bottom: 8px;
        }
        .match-header {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .match-player {
            padding: 4px 6px;
            margin: 2px 0;
            border-radius: 3px;
            font-size: 10px;
        }
        .match-home {
            background: #dbeafe;
            border-left: 3px solid #3b82f6;
        }
        .match-away {
            background: #fce7f3;
            border-left: 3px solid #ec4899;
        }
        .match-score {
            font-weight: bold;
            color: #1f2937;
            float: right;
        }
        .match-winner {
            background: #d1fae5;
            border-left-color: #10b981;
        }
        .knockout-round {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .knockout-title {
            background: #7c3aed;
            color: white;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 12px;
            border-radius: 6px;
            border-left: 4px solid #5b21b6;
        }
        .knockout-match {
            background: #f9fafb;
            border: 2px solid #dbeafe;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            page-break-inside: avoid;
            border-left: 4px solid #3b82f6;
        }
        .knockout-match-title {
            font-size: 10px;
            color: #374151;
            font-weight: bold;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }
        .knockout-player {
            padding: 8px 10px;
            margin: 4px 0;
            border-radius: 4px;
            font-size: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border: 1px solid #e5e7eb;
        }
        .winner-gold {
            background: #dcfce7;
            border-left: 4px solid #16a34a;
            font-weight: bold;
        }
        .winner-silver {
            background: #fef08a;
            border-left: 4px solid #d97706;
        }
        .match-home {
            background: #dbeafe;
            border-left-color: #0284c7;
        }
        .match-away {
            background: #fce7f3;
            border-left-color: #be185d;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        .signature-section {
            margin-top: 40px;
            padding: 20px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: #f9fafb;
        }
        .signature-line {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #374151;
            width: 300px;
            text-align: center;
            font-size: 9px;
        }
        .page-break {
            page-break-after: always;
        }
        .set-scores {
            font-size: 8px;
            color: #6b7280;
            margin-top: 2px;
        }
        /* EXCEL-STYLE BRACKET TABLE */
        .bracket-container-wrapper {
            width: 100%;
            background: #f9fafb;
            padding: 8px;
            border-radius: 4px;
            page-break-inside: avoid;
            margin-bottom: 8px;
        }
        .bracket-tournament {
            display: flex;
            flex-direction: row;
            gap: 8px;
            justify-content: center;
            align-items: flex-start;
            font-family: 'DejaVu Sans', sans-serif;
        }
        .bracket-round {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 85px;
            max-width: 105px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            background: #fff;
        }
        .bracket-round-title {
            font-size: 6px;
            font-weight: 700;
            color: #1e293b;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            background: #f3f4f6;
            padding: 2px 4px;
            border-bottom: 1px solid #d1d5db;
            border-radius: 3px 3px 0 0;
            position: relative;
        }
        .bracket-round-title .excel-column {
            font-size: 8px;
            font-weight: 900;
            color: #dc2626;
            position: absolute;
            top: -2px;
            left: 2px;
        }
        .bracket-round-title .round-subtitle {
            font-size: 4.5px;
            font-weight: 600;
            color: #6b7280;
            display: block;
            margin-top: 1px;
        }
        .bracket-match-simple {
            background: #fff;
            border: none;
            border-radius: 0;
            padding: 2px;
            font-size: 5.5px;
            line-height: 1.1;
            border-bottom: 1px solid #f1f5f9;
        }
        .bracket-match-simple:last-child {
            border-bottom: none;
        }
        .bracket-match-simple .player {
            margin-bottom: 1px;
            padding: 1px 2px;
            background: #f8fafc;
            border-radius: 1px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .bracket-match-simple .player.winner {
            background: #dcfce7;
            font-weight: 700;
            color: #166534;
        }
        .bracket-match-simple .player-name {
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 40px;
        }
        .bracket-match-simple .player-score {
            font-weight: 700;
            color: #374151;
            background: #f1f5f9;
            padding: 0px 2px;
            border-radius: 1px;
            font-size: 4.5px;
            min-width: 7px;
            text-align: center;
        }
        .bracket-arrow {
            font-size: 10px;
            color: #6b7280;
            align-self: center;
            margin: 0 4px;
        }
        .winner-column {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 85px;
            max-width: 105px;
            border: 2px solid #f59e0b;
            border-radius: 3px;
            background: #fef3c7;
        }
        .winner-title {
            font-size: 6px;
            font-weight: 700;
            color: #92400e;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            background: #f59e0b;
            color: #fff;
            padding: 2px 4px;
            border-radius: 3px 3px 0 0;
        }
        .winner-name {
            font-size: 7px;
            font-weight: 700;
            color: #92400e;
            text-align: center;
            padding: 8px 4px;
            background: #fef3c7;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $competition->name }}</h1>
        <p><strong>{{ $organization->name }}</strong></p>
        <p>Sport: {{ $competition->sport->name ?? 'N/A' }}</p>
        <p>Datum: {{ $competition->start_date ? $competition->start_date->format('d.m.Y') : 'N/A' }}</p>
        @if($competition->status === 'completed')
            <p style="color: #10b981; font-weight: bold;">✓ Završen turnir</p>
        @endif
    </div>

    {{-- Group Phase --}}
    @if($competition->tournamentGroups->count() > 0)
        <div class="section">
            <div class="section-title">📋 GRUPNA FAZA</div>
            
            @foreach($competition->tournamentGroups as $group)
                <div class="group-container">
                    <div class="group-title">Grupa {{ $group->name }}</div>
                    
                    {{-- Standings Table --}}
                    @php
                        $standings = App\Models\Standing::where('competition_id', $competition->id)
                            ->where('tournament_group_id', $group->id)
                            ->with('player')
                            ->orderByDesc('points')
                            ->orderByRaw('(sets_won - sets_lost) DESC')
                            ->orderByRaw('(points_won - points_lost) DESC')
                            ->orderByDesc('sets_won')
                            ->orderByDesc('won')
                            ->orderBy('id')
                            ->get();
                    @endphp
                    
                    @if($standings->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Igrač</th>
                                    <th>M</th>
                                    <th>P</th>
                                    <th>G</th>
                                    <th>I</th>
                                    <th>Bod</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($standings as $index => $standing)
                                    @php
                                        $actualPosition = $index + 1;
                                    @endphp
                                    <tr class="@if($actualPosition <= ($competition->players_advancing_per_group ?? 2)) position-{{ $actualPosition }} @endif">
                                        <td>{{ $actualPosition }}</td>
                                        <td>{{ $standing->player->name ?? 'N/A' }}</td>
                                        <td>{{ $standing->played }}</td>
                                        <td>{{ $standing->won }}</td>
                                        <td>{{ $standing->lost }}</td>
                                        <td>{{ $standing->sets_won }}-{{ $standing->sets_lost }}</td>
                                        <td><strong>{{ $standing->points }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    
                    {{-- Group Matches --}}
                    @php
                        $groupMatches = App\Models\CompetitionMatch::where('competition_id', $competition->id)
                            ->where('tournament_group_id', $group->id)
                            ->with(['homePlayer', 'awayPlayer'])
                            ->orderBy('round_number')
                            ->orderBy('match_order')
                            ->get();
                        
                        $matchesByRound = $groupMatches->groupBy('round_number');
                    @endphp
                    
                    @if($groupMatches->count() > 0)
                        <div style="margin-top: 10px;">
                            <strong style="font-size: 9px; color: #6b7280;">Raspored mečeva:</strong>
                            @foreach($matchesByRound as $roundNumber => $roundMatches)
                                <div style="margin-top: 8px; margin-bottom: 8px;">
                                    <div style="background: #e5e7eb; color: #374151; padding: 4px 8px; font-size: 9px; font-weight: bold; border-radius: 3px; margin-bottom: 4px;">
                                        Kolo {{ $roundNumber }}.
                                    </div>
                                    @foreach($roundMatches as $match)
                                        @php
                                            // Get positions for players in this match
                                            $homePosition = null;
                                            $awayPosition = null;
                                            
                                            if($match->home_player_id) {
                                                $homeStanding = $standings->firstWhere('player_id', $match->home_player_id);
                                                if($homeStanding) {
                                                    $homePosition = $standings->search(function($s) use ($match) {
                                                        return $s->player_id == $match->home_player_id;
                                                    }) + 1;
                                                }
                                            }
                                            
                                            if($match->away_player_id) {
                                                $awayStanding = $standings->firstWhere('player_id', $match->away_player_id);
                                                if($awayStanding) {
                                                    $awayPosition = $standings->search(function($s) use ($match) {
                                                        return $s->player_id == $match->away_player_id;
                                                    }) + 1;
                                                }
                                            }
                                        @endphp
                                        <div class="match-box">
                                            <div class="match-header">
                                                Meč {{ $match->match_order ?? $loop->iteration }}
                                                @if($match->status === 'completed')
                                                    <span style="color: #10b981;">✓</span>
                                                @endif
                                            </div>
                                            <div class="match-player match-home @if($match->winner_id == $match->home_player_id) match-winner @endif">
                                                {{ $match->homePlayer->name ?? 'TBD' }}
                                                @if($homePosition)
                                                    ({{ $homePosition }})
                                                @endif
                                                @if($match->home_score !== null)
                                                    <span class="match-score">{{ $match->home_score }}</span>
                                                @endif
                                            </div>
                                            @if($match->sets && is_array($match->sets))
                                                <div class="set-scores">
                                                    Setovi: 
                                                    @foreach($match->sets as $set)
                                                        {{ $set['home'] ?? 0 }}-{{ $set['away'] ?? 0 }}@if(!$loop->last), @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="match-player match-away @if($match->winner_id == $match->away_player_id) match-winner @endif">
                                                {{ $match->awayPlayer->name ?? 'TBD' }}
                                                @if($awayPosition)
                                                    ({{ $awayPosition }})
                                                @endif
                                                @if($match->away_score !== null)
                                                    <span class="match-score">{{ $match->away_score }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Page break before knockout phase --}}
    @if($knockoutMatches->count() > 0)
        <div style="page-break-before: always;"></div>
    @endif

    {{-- Knockout Phase - Bracket Tree --}}
    @if ($knockoutMatches->count() > 0)
        @php
            if (!function_exists('getRoundNamePDF')) {
                function getRoundNamePDF($roundNum, $totalRounds) {
                    $roundNum = (int) $roundNum;
                    $totalRounds = (int) $totalRounds;
                    $roundsFromFinal = $totalRounds - $roundNum;
                    
                    if ($roundNum == 0) return 'Playoff';
                    if ($roundsFromFinal == 0) return 'FINALE';
                    if ($roundsFromFinal == 1) return 'Polufinale';
                    if ($roundsFromFinal == 2) return 'Četvrtfinale';
                    if ($roundsFromFinal == 3) return 'Osmina';
                    if ($roundsFromFinal == 4) return 'Šesnaest.';
                    
                    return "Runda $roundNum";
                }
            }
        @endphp
        <div class="section">
            <div class="section-title">🏆 ELIMINACIONA FAZA</div>
            <div class="bracket-container-wrapper">
                <div class="bracket-tournament">
                    @php
                        $excelColumns = ['A', 'B', 'C', 'D', 'E', 'F'];
                        $columnIndex = 0;
                        $roundNames = [
                            0 => 'Playoff',
                            1 => '16-finala',
                            2 => '8-finala', 
                            3 => 'Četvrtfinale',
                            4 => 'Polufinale',
                            5 => 'Finale'
                        ];
                        // Determine winner from final match for trophy display
                        $finalMatch = $knockoutMatches->last()->first();
                        $winner = $finalMatch ? $finalMatch->winner : null;
                    @endphp

                    {{-- Dynamic rounds based on available data --}}
                    @foreach ($knockoutMatches as $round => $matches)
                        @if($matches->count() > 0)
                            @php
                                $roundInt = (int) $round;
                                $excelCol = $excelColumns[$columnIndex] ?? chr(65 + $columnIndex);
                                $roundName = $roundNames[$roundInt] ?? "Runda " . ($roundInt + 1);
                                $matchCount = $matches->count();
                                $columnIndex++;
                            @endphp

                            <div class="bracket-round">
                                <h3 class="bracket-round-title">
                                    <span class="excel-column">{{ $excelCol }}</span>
                                    {{ $roundName }}
                                    <span class="round-subtitle">{{ $matchCount }} meč{{ $matchCount > 1 ? 'eva' : '' }}</span>
                                </h3>
                                @foreach ($matches as $match)
                                    @php
                                        $homeName = optional($match->homePlayer)->name
                                            ?? optional($match->homeTeam)->name
                                            ?? data_get($match, 'home_placeholder_name')
                                            ?? data_get($match, 'home_placeholder')
                                            ?? 'TBD';

                                        $awayName = optional($match->awayPlayer)->name
                                            ?? optional($match->awayTeam)->name
                                            ?? data_get($match, 'away_placeholder_name')
                                            ?? data_get($match, 'away_placeholder')
                                            ?? ($match->is_bye ? 'BYE' : 'TBD');

                                        if (strlen($homeName) > 9) {
                                            $homeName = substr($homeName, 0, 7) . '..';
                                        }
                                        if (strlen($awayName) > 9) {
                                            $awayName = substr($awayName, 0, 7) . '..';
                                        }

                                        $homeScore = $match->home_score ?? '-';
                                        $awayScore = $match->away_score ?? '-';

                                        $homeIsWinner = $match->winner && (
                                            ($match->home_player_id && optional($match->winner)->id === $match->home_player_id) ||
                                            ($match->home_team_id && optional($match->winner)->id === $match->home_team_id)
                                        );
                                        $awayIsWinner = $match->winner && (
                                            ($match->away_player_id && optional($match->winner)->id === $match->away_player_id) ||
                                            ($match->away_team_id && optional($match->winner)->id === $match->away_team_id)
                                        );
                                    @endphp

                                    <div class="bracket-match-simple">
                                        <div class="player {{ $homeIsWinner ? 'winner' : '' }}">
                                            <span class="player-name">{{ $homeName }}</span>
                                            <span class="player-score">{{ $homeScore }}</span>
                                        </div>
                                        <div class="player {{ $awayIsWinner ? 'winner' : '' }}">
                                            <span class="player-name">{{ $awayName }}</span>
                                            <span class="player-score">{{ $awayScore }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if (!$loop->last || $winner)
                                <div class="bracket-arrow">{{ $loop->last && $winner ? '🏆' : '→' }}</div>
                            @endif
                        @endif
                    @endforeach

                    {{-- Winner Display --}}
                    @if ($winner)
                        <div class="winner-column">
                            <div class="winner-title">
                                <span class="excel-column">{{ $excelColumns[$columnIndex] ?? chr(65 + $columnIndex) }}</span>
                                POBJEDNIK
                            </div>
                            <div class="winner-name">
                                {{ $winner->getDisplayName() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Page break before knockout phase --}}
    @if($knockoutMatches->count() > 0)
        <div style="page-break-before: always;"></div>
    @endif

    {{-- Knockout Phase - Full Page Visualization (Identical to Website) --}}
    @if ($knockoutMatches->count() > 0)
        @php
            // Function to get round name based on number of participants
            if (!function_exists('getRoundNamePDF')) {
                function getRoundNamePDF($roundNum, $totalRounds) {
                    $roundNum = (int) $roundNum;
                    $totalRounds = (int) $totalRounds;
                    $roundsFromFinal = $totalRounds - $roundNum;
                    
                    if ($roundNum == 0) return 'Playoff';
                    if ($roundsFromFinal == 0) return 'Finale';
                    if ($roundsFromFinal == 1) return 'Polufinale';
                    if ($roundsFromFinal == 2) return 'Četvrtfinale';
                    if ($roundsFromFinal == 3) return 'Osmina finala';
                    if ($roundsFromFinal == 4) return 'Šesnaestina finala';
                    if ($roundsFromFinal == 5) return 'Tridesetdvojka';
                    
                    return "Runda $roundNum";
                }
            }
            
            // Matches are already grouped by round_number
            $matchesByRound = $knockoutMatches;
            $totalRounds = (int) $matchesByRound->keys()->max();
        @endphp

        <div style="background: rgba(31, 41, 55, 0.5); backdrop-filter: blur(24px); border-radius: 16px; padding: 24px; border: 1px solid rgba(55, 65, 81, 0.5); margin-bottom: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <h3 style="font-size: 24px; font-weight: 700; color: white; margin: 0;">🏆 Eliminaciona Faza</h3>
            </div>
            
            {{-- Bracket Tree Visualization --}}
            <div style="overflow-x: auto; padding-bottom: 16px;">
                <div style="display: flex; justify-content: center; align-items: center; min-height: 500px; padding: 32px 0;">
                    <div style="display: inline-flex; gap: 64px; min-width: max-content; align-items: center;">
                    
                    @foreach($matchesByRound as $roundNum => $matches)
                        <div style="display: flex; flex-direction: column; justify-content: space-around; min-height: 400px;">
                            {{-- Round Title --}}
                            <div style="text-align: center; margin-bottom: 16px;">
                                <div style="background: rgba(37, 99, 235, 0.2); border: 1px solid #3b82f6; border-radius: 8px; padding: 8px 16px;">
                                    <div style="color: #93c5fd; font-weight: 600; font-size: 14px;">{{ getRoundNamePDF($roundNum, $totalRounds) }}</div>
                                    <div style="color: #60a5fa; font-size: 12px; margin-top: 4px;">{{ $matches->count() }} {{ $matches->count() == 1 ? 'meč' : 'mečeva' }}</div>
                                </div>
                            </div>
                            
                            {{-- Matches in this round --}}
                            <div style="display: flex; flex-direction: column; gap: 16px; justify-content: space-around;">
                                @foreach($matches as $match)
                                    <div style="position: relative; background: rgba(55, 65, 81, 0.3); border-radius: 8px; border: 1px solid rgba(75, 85, 99, 0.5); min-width: 240px;">
                                        {{-- Match Header --}}
                                        <div style="background: rgba(31, 41, 55, 0.5); padding: 4px 8px; border-bottom: 1px solid rgba(75, 85, 99, 0.3); display: flex; align-items: center; justify-content: space-between;">
                                            <span style="font-size: 10px; font-weight: 600; color: #d1d5db;">Meč {{ $loop->parent->iteration }}.{{ $loop->iteration }}</span>
                                            <span style="font-size: 10px; padding: 2px 6px; border-radius: 9999px;
                                                @if($match->status === 'completed') background: rgba(22, 163, 74, 0.2); color: #86efac;
                                                @elseif($match->status === 'live') background: rgba(220, 38, 38, 0.2); color: #fca5a5;
                                                @else background: rgba(75, 85, 99, 0.2); color: #9ca3af;
                                                @endif">
                                                @if($match->status === 'completed') ✓
                                                @elseif($match->status === 'live') 🔴
                                                @else ⏳
                                                @endif
                                            </span>
                                        </div>
                                        
                                        {{-- Players --}}
                                        <div style="padding: 8px;">
                                            @php
                                                // Get player's group and position info from match data (saved when knockout was created)
                                                $homePlayerInfo = null;
                                                $awayPlayerInfo = null;
                                                
                                                if($match->homePlayer && $match->home_player_group && $match->home_player_position) {
                                                    $homePlayerInfo = [
                                                        'group' => $match->home_player_group,
                                                        'position' => $match->home_player_position
                                                    ];
                                                } elseif($match->homePlayer) {
                                                    // Fallback: calculate position from current standings
                                                    foreach($competition->tournamentGroups as $group) {
                                                        $allStandings = App\Models\Standing::where('competition_id', $competition->id)
                                                            ->where('tournament_group_id', $group->id)
                                                            ->orderByDesc('points')
                                                            ->orderByRaw('(sets_won - sets_lost) DESC')
                                                            ->orderByRaw('(points_won - points_lost) DESC')
                                                            ->orderByDesc('sets_won')
                                                            ->orderByDesc('won')
                                                            ->orderBy('id')
                                                            ->get();
                                                        
                                                        // Find position in sorted list
                                                        $position = $allStandings->search(function($s) use ($match) {
                                                            return $s->player_id == $match->home_player_id;
                                                        });
                                                        
                                                        if($position !== false) {
                                                            $homePlayerInfo = [
                                                                'group' => $group->name,
                                                                'position' => $position + 1
                                                            ];
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                if($match->awayPlayer && $match->away_player_group && $match->away_player_position) {
                                                    $awayPlayerInfo = [
                                                        'group' => $match->away_player_group,
                                                        'position' => $match->away_player_position
                                                    ];
                                                } elseif($match->awayPlayer) {
                                                    // Fallback: calculate position from current standings
                                                    foreach($competition->tournamentGroups as $group) {
                        $allStandings = App\Models\Standing::where('competition_id', $competition->id)
                            ->where('tournament_group_id', $group->id)
                            ->orderByDesc('points')
                            ->orderByRaw('(sets_won - sets_lost) DESC')
                            ->orderByRaw('(points_won - points_lost) DESC')
                            ->orderByDesc('sets_won')
                            ->orderByDesc('won')
                            ->orderBy('id')
                            ->get();
                        
                                                        $allStandings = App\Models\Standing::where('competition_id', $competition->id)
                                                            ->where('tournament_group_id', $group->id)
                                                            ->orderByDesc('points')
                                                            ->orderByRaw('(sets_won - sets_lost) DESC')
                                                            ->orderByRaw('(points_won - points_lost) DESC')
                                                            ->orderByDesc('sets_won')
                                                            ->orderByDesc('won')
                                                            ->orderBy('id')
                                                            ->get();
                                                        
                                                        $position = $allStandings->search(function($s) use ($match) {
                                                            return $s->player_id == $match->away_player_id;
                                                        });
                                                        
                                                        if($position !== false) {
                                                            $awayPlayerInfo = [
                                                                'group' => $group->name,
                                                                'position' => $position + 1
                                                            ];
                                                            break;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            
                                            {{-- Home Player --}}
                                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 6px; border-radius: 8px; margin-bottom: 4px;
                                                @if($homePlayerInfo && $homePlayerInfo['position'] == 1)
                                                    background: rgba(34, 197, 94, 0.1); border-left: 4px solid #22c55e;
                                                @elseif($homePlayerInfo && $homePlayerInfo['position'] == 2)
                                                    background: rgba(234, 179, 8, 0.1); border-left: 4px solid #eab308;
                                                @else
                                                    background: rgba(75, 85, 99, 0.2);
                                                @endif
                                                @if($match->status === 'completed' && $match->winner_id === $match->home_player_id)
                                                    outline: 2px solid #22c55e; outline-offset: 0;
                                                @endif">
                                                <div style="flex: 1; min-width: 0;">
                                                    @if($match->homePlayer)
                                                        <div style="display: flex; align-items: center; gap: 4px;">
                                                            @if($homePlayerInfo)
                                                                <span style="font-size: 10px;">{{ $homePlayerInfo['position'] == 1 ? '🥇' : '🥈' }}</span>
                                                            @endif
                                                            <span style="color: white; font-size: 12px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;">{{ $match->homePlayer->name }}</span>
                                                        </div>
                                                    @else
                                                        <span style="color: #6b7280; font-style: italic; font-size: 10px;">TBD</span>
                                                    @endif
                                                </div>
                                                @if($match->status === 'completed' && $match->home_score !== null)
                                                    <span style="font-size: 16px; font-weight: 700; margin-left: 8px;
                                                        @if($match->winner_id === $match->home_player_id) color: #86efac;
                                                        @else color: #9ca3af;
                                                        @endif">
                                                        {{ $match->home_score }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            {{-- Away Player --}}
                                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 6px; border-radius: 8px;
                                                @if($awayPlayerInfo && $awayPlayerInfo['position'] == 1)
                                                    background: rgba(34, 197, 94, 0.1); border-left: 4px solid #22c55e;
                                                @elseif($awayPlayerInfo && $awayPlayerInfo['position'] == 2)
                                                    background: rgba(234, 179, 8, 0.1); border-left: 4px solid #eab308;
                                                @else
                                                    background: rgba(75, 85, 99, 0.2);
                                                @endif
                                                @if($match->status === 'completed' && $match->winner_id === $match->away_player_id)
                                                    outline: 2px solid #22c55e; outline-offset: 0;
                                                @endif">
                                                <div style="flex: 1; min-width: 0;">
                                                    @if($match->awayPlayer)
                                                        <div style="display: flex; align-items: center; gap: 4px;">
                                                            @if($awayPlayerInfo)
                                                                <span style="font-size: 10px;">{{ $awayPlayerInfo['position'] == 1 ? '🥇' : '🥈' }}</span>
                                                            @endif
                                                            <span style="color: white; font-size: 12px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;">{{ $match->awayPlayer->name }}</span>
                                                        </div>
                                                    @else
                                                        <span style="color: #6b7280; font-style: italic; font-size: 10px;">TBD</span>
                                                    @endif
                                                </div>
                                                @if($match->status === 'completed' && $match->away_score !== null)
                                                    <span style="font-size: 16px; font-weight: 700; margin-left: 8px;
                                                        @if($match->winner_id === $match->away_player_id) color: #86efac;
                                                        @else color: #9ca3af;
                                                        @endif">
                                                        {{ $match->away_score }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            {{-- Match Status --}}
                                            <div style="margin-top: 8px; display: flex; gap: 4px;">
                                                @if($match->status === 'scheduled' || $match->status === 'pending')
                                                    <span style="flex: 1; font-size: 10px; background: rgba(234, 179, 8, 0.2); color: #fde047; padding: 4px 8px; border-radius: 4px; text-align: center;">
                                                        ⏳ Zakazano
                                                    </span>
                                                @elseif($match->status === 'in_progress')
                                                    <span style="flex: 1; font-size: 10px; background: rgba(22, 163, 74, 0.2); color: #86efac; padding: 4px 8px; border-radius: 4px; text-align: center;">
                                                        🔴 Live
                                                    </span>
                                                @elseif($match->status === 'completed')
                                                    <span style="flex: 1; font-size: 10px; background: rgba(75, 85, 99, 0.2); color: #9ca3af; padding: 4px 8px; border-radius: 4px; text-align: center;">
                                                        ✓ Završeno
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Connector line to next round --}}
                                        @if(!$loop->parent->last)
                                            <div style="position: absolute; top: 50%; right: -64px; width: 64px; height: 2px; background: linear-gradient(to right, rgba(59, 130, 246, 0.5), transparent); transform: translateY(-50%);"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Signature Section --}}
    <div class="signature-section">
        <p style="margin: 0 0 10px 0; font-size: 10px; font-weight: bold;">Potvrda ispravnosti rezultata:</p>
        <p style="margin: 5px 0; font-size: 9px; color: #6b7280;">
            Potvrđujem da su svi rezultati tačno uneseni i da je takmičenje sprovedeno u skladu sa pravilima.
        </p>
        <div style="margin-top: 50px; display: flex; justify-content: space-between;">
            <div style="width: 45%;">
                <div class="signature-line">
                    Potpis odgovornog lica
                </div>
            </div>
            <div style="width: 45%;">
                <div class="signature-line">
                    Datum: _________________
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p style="margin: 0; font-size: 8px; color: #9ca3af;">Generisano: {{ now()->format('d.m.Y H:i') }} | {{ config('app.name') }}</p>
    </div>
</body>
</html>
