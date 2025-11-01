<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $competition->name }} - PDF Export</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }

            /* Prevent page breaks inside important elements */
            table { page-break-inside: avoid; }
            .break-inside-avoid { page-break-inside: avoid; }

            /* Ensure group sections stay together */
            .group-section { page-break-inside: avoid; }

            /* Prevent breaking inside match cards */
            .match-card { page-break-inside: avoid; }

            /* Add some spacing between sections */
            .section-spacing { margin-bottom: 2rem; }

            /* Ensure tournament header stays together */
            .tournament-header { page-break-inside: avoid; }

            /* Winner section should not break */
            .winner-section { page-break-inside: avoid; page-break-before: avoid; }

            /* Knockout bracket container */
            .bracket-container { page-break-inside: avoid; }

            /* Force page break before knockout section */
            .knockout-section { page-break-before: always; page-break-inside: avoid; }

            /* Force page breaks for section headers */
            .section-header { page-break-before: always; }

            /* Group layout - 2 groups per page */
            .group-section:nth-child(odd) { page-break-after: always; }

            /* Smaller fonts for better fit */
            .tournament-header h1 { font-size: 2rem !important; }
            .tournament-header p { font-size: 0.875rem !important; }
            .group-section h3 { font-size: 1.125rem !important; }
            .match-card { font-size: 0.75rem !important; padding: 0.75rem !important; }
            .match-card .text-xs { font-size: 0.625rem !important; }
            .match-card .player-name { font-size: 0.75rem !important; font-weight: 600 !important; }
            table { font-size: 0.75rem !important; }
            table th, table td { padding: 0.25rem 0.5rem !important; }
            table .player-name-table { font-size: 0.625rem !important; font-weight: 500 !important; }

            /* Compact knockout bracket for PDF - optimized for single page */
            .knockout-bracket { font-size: 0.6rem !important; padding: 0.25rem !important; }
            .knockout-bracket .round-header { font-size: 0.55rem !important; margin-bottom: 0.125rem !important; }
            .knockout-bracket .match-card { padding: 0.25rem !important; margin: 0.125rem 0 !important; }
            .knockout-bracket .player-name-knockout { font-size: 0.5rem !important; font-weight: 600 !important; }
            .knockout-bracket .score-circle { width: 0.875rem !important; height: 0.875rem !important; font-size: 0.4rem !important; }
            .knockout-bracket .overflow-x-auto { overflow-x: visible !important; }
            .knockout-bracket .min-w-max { min-width: auto !important; }
            .knockout-bracket .gap-2 { gap: 0.25rem !important; }
            .knockout-bracket .px-3 { padding-left: 0.25rem !important; padding-right: 0.25rem !important; }
            .knockout-bracket .pb-3 { padding-bottom: 0.25rem !important; }
            .knockout-bracket .mb-1 { margin-bottom: 0.125rem !important; }
            .knockout-bracket .ml-1 { margin-left: 0.125rem !important; }

            /* Footer should not break to new page */
            .footer-section { page-break-inside: avoid; page-break-before: avoid; margin-top: 1rem !important; padding-top: 1rem !important; }

            /* Force 2 columns for groups in PDF */
            .groups-container { 
                display: grid !important; 
                grid-template-columns: repeat(2, 1fr) !important; 
                gap: 1rem !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-white">
    <!-- Print/Save as PDF Button -->
    <div class="no-print fixed top-4 right-4 z-50">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transition-colors duration-200">
            💾 Spremi kao PDF
        </button>
    </div>

    <div class="max-w-3xl mx-auto pt-16 p-2">
        <!-- Tournament Header -->
        <div class="text-center mb-6 tournament-header">
            <p class="text-lg text-gray-600">{{ $competition->organization->name ?? 'TeamSphere' }} - {{ $competition->name }}</p>
            <p class="text-sm text-gray-500 mt-2">
                @if($competition->start_date)
                    {{ \Carbon\Carbon::parse($competition->start_date)->format('d.m.Y') }}
                    @if($competition->end_date && $competition->end_date != $competition->start_date)
                        - {{ \Carbon\Carbon::parse($competition->end_date)->format('d.m.Y') }}
                    @endif
                @endif
            </p>
        </div>

    <!-- Groups Section -->
    @if($hasGroupMatches)
    <div class="mb-8">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 groups-container">
            @foreach($competition->tournamentGroups as $group)
            @php
                $currentGroupMatches = $groupMatches->get($group->id) ?? collect();
                $groupStandings = $group->standings()->with('player')->orderBy('position')->get();
                $advancingPlayers = $competition->players_advancing_per_group ?? 2;
            @endphp

            @if($currentGroupMatches->count() > 0)
            <div class="break-inside-avoid group-section">
                <!-- Group Standings Table -->
                @if($groupStandings->count() > 0)
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-700 mb-3">Grupa {{ $group->name }}</h4>
                    <div class="overflow-hidden border border-gray-300 rounded">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-700">#</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Igrač</th>
                                    <th class="px-4 py-2 text-center font-semibold text-gray-700">P</th>
                                    <th class="px-4 py-2 text-center font-semibold text-gray-700">I</th>
                                    <th class="px-4 py-2 text-center font-semibold text-gray-700">Set±</th>
                                    <th class="px-4 py-2 text-center font-semibold text-gray-700">B</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupStandings as $index => $standing)
                                <tr class="{{ $index < $advancingPlayers ? 'bg-green-50' : 'bg-white' }} border-t border-gray-200">
                                    <td class="px-4 py-2 text-center font-medium">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 player-name-table">{{ $standing->player->name }}@if($standing->player->position) <span class="text-xs text-gray-500">({{ $standing->player->position }})</span>@endif</td>
                                    <td class="px-4 py-2 text-center">{{ $standing->won ?? 0 }}</td>
                                    <td class="px-4 py-2 text-center">{{ $standing->lost ?? 0 }}</td>
                                    <td class="px-4 py-2 text-center">{{ ($standing->sets_won ?? 0) - ($standing->sets_lost ?? 0) }}</td>
                                    <td class="px-4 py-2 text-center font-semibold">{{ $standing->points ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Group Matches -->
                <div>
                    <h4 class="text-lg font-medium text-gray-700 mb-3">Grupa {{ $group->name }}</h4>
                    <div class="space-y-3">
                        @foreach($currentGroupMatches as $match)
                        @php
                            $homeSetsWon = 0;
                            $awaySetsWon = 0;
                            $homeFinalScore = $match->home_score ?? null;
                            $awayFinalScore = $match->away_score ?? null;

                            if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
                                foreach($match->sets as $set) {
                                    $h = $set['home_score'] ?? $set['home'] ?? 0;
                                    $a = $set['away_score'] ?? $set['away'] ?? 0;
                                    if($h > $a) { $homeSetsWon++; }
                                    if($a > $h) { $awaySetsWon++; }
                                }
                            }

                            // Prefer explicit final scores if provided, otherwise derive from sets
                            $homeDisplay = $homeFinalScore !== null ? $homeFinalScore : $homeSetsWon;
                            $awayDisplay = $awayFinalScore !== null ? $awayFinalScore : $awaySetsWon;

                            // Determine winner for display (only when a clear winner exists)
                            $matchWinner = null;
                            if($homeDisplay > $awayDisplay) {
                                $matchWinner = 'home';
                            } elseif($awayDisplay > $homeDisplay) {
                                $matchWinner = 'away';
                            }
                        @endphp

                        <div class="border border-gray-300 rounded p-4 bg-white match-card">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center space-x-3 flex-1">
                                    <span class="player-name {{ $matchWinner === 'home' ? 'text-gray-900 font-bold' : 'text-gray-600' }}">{{ $match->homePlayer->name ?? 'Home Player' }}@if(isset($playerPositionSeeding[$match->home_player_id])) <span class="text-xs text-gray-500">({{ $playerPositionSeeding[$match->home_player_id] }})</span>@endif</span>
                                    <span class="text-sm text-gray-600">vs</span>
                                    <span class="player-name {{ $matchWinner === 'away' ? 'text-gray-900 font-bold' : 'text-gray-600' }}">{{ $match->awayPlayer->name ?? 'Away Player' }}@if(isset($playerPositionSeeding[$match->away_player_id])) <span class="text-xs text-gray-500">({{ $playerPositionSeeding[$match->away_player_id] }})</span>@endif</span>
                                </div>
                                <div class="text-sm font-medium text-gray-700">
                                    <span class="font-medium">{{ $homeDisplay }}</span> - <span class="font-medium">{{ $awayDisplay }}</span>
                                </div>
                            </div>

                            @if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0)
                            <div class="text-xs text-gray-600">
                                Setovi: {{ collect($match->sets)->map(function($set) {
                                    $home = $set['home_score'] ?? $set['home'] ?? 0;
                                    $away = $set['away_score'] ?? $set['away'] ?? 0;
                                    return $home . '-' . $away;
                                })->join(', ') }}
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Knockout Section -->
    @if($hasKnockoutMatches)
    <div class="mb-8 knockout-section">

        @php
            $totalRounds = $knockoutMatches->count();
            $firstRoundMatches = $knockoutMatches->get(1) ?? collect();
            $numPlayers = $firstRoundMatches->count() * 2;

            // Check if tournament is completed and get winner
            $finalMatch = $knockoutMatches->get($totalRounds)?->first();
            $winner = null;
            $allMatchesCompleted = true;

            // Check if all knockout matches are completed
            foreach($knockoutMatches as $roundMatches) {
                foreach($roundMatches as $match) {
                    if($match->status !== 'completed' && !$match->is_bye) {
                        $allMatchesCompleted = false;
                        break 2;
                    }
                }
            }

            // Determine final match winner using sets when available, otherwise fall back to final scores
            if ($finalMatch) {
                $homeFinal = null;
                $awayFinal = null;

                if(isset($finalMatch->sets) && is_array($finalMatch->sets) && count($finalMatch->sets) > 0) {
                    $homeSets = 0;
                    $awaySets = 0;
                    foreach($finalMatch->sets as $set) {
                        $h = $set['home_score'] ?? $set['home'] ?? 0;
                        $a = $set['away_score'] ?? $set['away'] ?? 0;
                        if($h > $a) { $homeSets++; }
                        if($a > $h) { $awaySets++; }
                    }
                    $homeFinal = $homeSets;
                    $awayFinal = $awaySets;
                }

                if($homeFinal === null || $awayFinal === null) {
                    $homeFinal = $finalMatch->home_score ?? 0;
                    $awayFinal = $finalMatch->away_score ?? 0;
                }

                if (($finalMatch->status === 'completed' || $homeFinal > 0 || $awayFinal > 0) && $allMatchesCompleted) {
                    if ($homeFinal > $awayFinal) {
                        $winner = $finalMatch->homePlayer;
                    } elseif ($awayFinal > $homeFinal) {
                        $winner = $finalMatch->awayPlayer;
                    }
                }
            }
        @endphp

        @if($winner)
        <div class="winner-section text-center mb-4">
            <h3 class="text-lg font-semibold text-amber-400 mb-1">ŠAMPION TURNIRA</h3>
            <p class="text-xl font-black text-gray-900">{{ $winner->name }}</p>
        </div>
        @endif

        <!-- Tournament Bracket -->
        <div class="bg-gray-50 rounded-xl p-4 md:p-6 border border-gray-300 bracket-container knockout-bracket">
            <div class="overflow-x-auto pb-6">
                <div class="min-w-max">
                    <!-- Bracket Container -->
                    <div class="flex justify-center" style="gap: 3px;">
                        @for($round = 1; $round <= $totalRounds; $round++)
                        @php
                            $roundMatches = $knockoutMatches->get($round) ?? collect();
                            // Calculate spacing for bracket alignment
                            $matchesInRound = $roundMatches->count();
                            $spacingMultiplier = pow(2, $round - 1);

                            // Calculate round name based on number of matches in this round
                            if ($matchesInRound == 1 && $round === $totalRounds) {
                                // Last round with 1 match = Finale
                                $roundName = 'Finale';
                            } elseif ($matchesInRound == 1) {
                                // 1 match but not last round = Polufinale
                                $roundName = 'Polufinale';
                            } else {
                                // Multiple matches = 1/N Finala where N is half the number of players in this round
                                // Number of players = matchesInRound * 2
                                // So 1/(matchesInRound * 2 / 2) = 1/matchesInRound
                                $roundName = '1/' . $matchesInRound . ' Finala';
                            }
                        @endphp
                        @if($matchesInRound > 0 || ($round === $totalRounds && $totalRounds > 1))
                        <div class="flex flex-col justify-center gap-2" style="gap: 3px;">
                            {{-- Round Header --}}
                            <div class="text-center" style="margin-bottom: 3px;">
                                <h4 class="text-sm md:text-base font-bold text-gray-900 uppercase tracking-wider round-header">
                                    {{ $roundName }}
                                </h4>
                            </div>

                            @if($matchesInRound > 0)
                            {{-- Round Matches Container --}}
                            <div class="flex flex-col" style="gap: 3px;">
                                @foreach($roundMatches as $index => $match)
                                @php
                                    $homeSetsWon = 0;
                                    $awaySetsWon = 0;
                                    $homeFinalScore = $match->home_score ?? 0;
                                    $awayFinalScore = $match->away_score ?? 0;

                                    if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
                                        foreach($match->sets as $set) {
                                            $homeSetScore = $set['home_score'] ?? $set['home'] ?? 0;
                                            $awaySetScore = $set['away_score'] ?? $set['away'] ?? 0;
                                            if($homeSetScore > $awaySetScore) {
                                                $homeSetsWon++;
                                            } elseif($awaySetScore > $homeSetScore) {
                                                $awaySetsWon++;
                                            }
                                        }
                                    }

                                    // If match is completed and we don't have sets data, use final scores
                                    if($match->status === 'completed' && $homeSetsWon === 0 && $awaySetsWon === 0) {
                                        $homeSetsWon = $homeFinalScore;
                                        $awaySetsWon = $awayFinalScore;
                                    }
                                @endphp

                                <div class="block bg-white rounded-lg border border-gray-300 break-inside-avoid"
                                     data-match-id="{{ $match->id }}" style="padding-top: 3px; margin-top: 3px; margin-bottom: 3px;">

                                    @if($match->status === 'in_progress' && !$match->is_bye)
                                    <div class="text-center mb-1">
                                        <span class="text-red-600 font-semibold text-xs uppercase tracking-wider">Live</span>
                                    </div>
                                    @endif

                                    <!-- Match Players -->
                                    <div class="px-2 md:px-3" style="padding-bottom: 3px;">
                                        <!-- Home Player -->
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center gap-1 flex-1 min-w-0">
                                                <div class="text-xs md:text-sm font-semibold {{ ($homeSetsWon > $awaySetsWon) && ($homeSetsWon > 0 || $awaySetsWon > 0) || ($match->is_bye && $match->homePlayer) ? 'text-gray-900 font-bold' : 'text-gray-600' }} truncate player-name-knockout">
                                                    {{ $match->homePlayer->name ?? 'NEMA PROTIVNIKA' }}@if(isset($playerGroupSeeding[$match->home_player_id])) <span class="text-xs text-gray-500">({{ $playerGroupSeeding[$match->home_player_id] }})</span>@endif
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 ml-1">
                                                @if($match->status === 'in_progress' && !$match->is_bye)
                                                    <div class="w-5 h-5 bg-green-600 rounded flex items-center justify-center score-circle">
                                                        <div class="text-xs font-bold text-white">
                                                            {{ $homeFinalScore }}
                                                        </div>
                                                    </div>
                                                @elseif($match->status === 'completed' || ($homeSetsWon > 0 || $awaySetsWon > 0))
                                                    <div class="w-5 h-5 bg-green-600 rounded flex items-center justify-center score-circle">
                                                        <div class="text-xs font-bold text-white">
                                                            {{ $homeFinalScore ?: $homeSetsWon }}
                                                        </div>
                                                    </div>
                                                @elseif($match->is_bye)
                                                    <div class="w-5 h-5 bg-gray-200 rounded flex items-center justify-center score-circle">
                                                        <div class="text-xs font-bold text-gray-500">bye</div>
                                                    </div>
                                                @else
                                                    <div class="w-5 h-5 bg-gray-200 rounded flex items-center justify-center score-circle">
                                                        <div class="text-xs font-bold text-gray-400">-</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Away Player -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1 flex-1 min-w-0">
                                                <div class="text-xs md:text-sm font-semibold {{ ($awaySetsWon > $homeSetsWon) && ($homeSetsWon > 0 || $awaySetsWon > 0) || ($match->is_bye && $match->awayPlayer) ? 'text-gray-900 font-bold' : 'text-gray-600' }} truncate player-name-knockout">
                                                    {{ $match->awayPlayer->name ?? 'NEMA PROTIVNIKA' }}@if(isset($playerGroupSeeding[$match->away_player_id])) <span class="text-xs text-gray-500">({{ $playerGroupSeeding[$match->away_player_id] }})</span>@endif
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 ml-1">
                                                @if($match->status === 'in_progress' && !$match->is_bye)
                                                    <div class="w-5 h-5 bg-green-600 rounded flex items-center justify-center score-circle">
                                                        <div class="text-xs font-bold text-white">
                                                            {{ $awayFinalScore }}
                                                        </div>
                                                    </div>
                                                @elseif($match->status === 'completed' || ($homeSetsWon > 0 || $awaySetsWon > 0))
                                                    <div class="w-5 h-5 bg-green-600 rounded flex items-center justify-center score-circle">
                                                        <div class="text-xs font-bold text-white">
                                                            {{ $awayFinalScore ?: $awaySetsWon }}
                                                        </div>
                                                    </div>
                                                @elseif($match->is_bye)
                                                    <div class="w-5 h-5 bg-gray-200 rounded flex items-center justify-center score-circle">
                                                        <div class="text-xs font-bold text-gray-500">bye</div>
                                                    </div>
                                                @else
                                                    <div class="w-5 h-5 bg-gray-200 rounded flex items-center justify-center score-circle">
                                                        <div class="text-xs font-bold text-gray-400">-</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        @endif
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="text-center text-sm text-gray-500 mt-2 pt-2 border-t border-gray-300 footer-section">
        <p>Generisano od strane TeamSphere - {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</div>
</body>
</html>