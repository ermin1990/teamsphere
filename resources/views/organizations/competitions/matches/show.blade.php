<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Detalji Meča
                </h2>
                <p class="text-gray-400 mt-1">{{ $competition->name }}
                    @if($match->phase === 'group')
                        • Group {{ $match->tournamentGroup->group_number }} • Round {{ $match->round }}
                    @elseif($match->phase === 'knockout')
                        • Knockout Round {{ $match->round_number }}
                    @endif
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-2 py-1 text-xs sm:text-sm rounded-full whitespace-nowrap overflow-hidden text-ellipsis max-w-32 sm:max-w-none
                    @if($match->status === 'completed') bg-green-500/20 text-green-400
                    @elseif($match->status === 'in_progress') bg-yellow-500/20 text-yellow-400
                    @elseif($match->status === 'forfeited') bg-red-500/20 text-red-400
                    @elseif($match->status === 'cancelled') bg-orange-500/20 text-orange-400
                    @else bg-gray-500/20 text-gray-400 @endif"
                >
                    {{ ucfirst(str_replace('_', ' ', $match->status)) }}
                    @if($match->status === 'forfeited' && $match->forfeited_by)
                        - {{ $match->forfeited_by === 'home' ? $match->homePlayer->name : $match->awayPlayer->name }} Forfeited
                    @endif
                </span>
                <div class="flex space-x-2">
                    @if(isset($isOwner) && $isOwner)
                    <a href="{{ route('organizations.competitions.matches.edit', [$organization, $competition, $match]) }}"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                        ✏️ Edit Results
                    </a>
                    @if($match->status !== 'completed')
                    <a href="{{ route('competitions.live-score', ['match' => $match->id]) }}"
                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors">
                        🎯 Live Score
                    </a>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">

                <!-- Match Header -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                    <div class="text-center">
                        <div class="text-sm text-gray-400 mb-4">
                            {{ $competition->sport->name }}
                            @if($match->phase === 'group')
                                • Group {{ $match->tournamentGroup->group_number }} • Round {{ $match->round }}
                            @elseif($match->phase === 'knockout')
                                • Knockout Round {{ $match->round_number }}
                            @endif
                        </div>

                        <div class="flex flex-col md:flex-row items-center justify-center space-y-8 md:space-y-0 md:space-x-12 lg:space-x-20">
                            <!-- Home Player -->
                            <div class="text-center w-full md:w-auto">
                                @if($match->position_code === 'Dubl')
                                    <div class="flex -space-x-4 justify-center mb-3">
                                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center border-4 border-gray-800 z-10">
                                            <span class="text-xl font-bold text-white">
                                                {{ substr($doublesPlayers['home_1']->name ?? '?', 0, 2) }}
                                            </span>
                                        </div>
                                        <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center border-4 border-gray-800">
                                            <span class="text-xl font-bold text-white">
                                                {{ substr($doublesPlayers['home_2']->name ?? '?', 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <h3 class="text-xl font-bold text-white">
                                            {{ $doublesPlayers['home_1']->name ?? '?' }}
                                        </h3>
                                        <h3 class="text-xl font-bold text-white">
                                            {{ $doublesPlayers['home_2']->name ?? '?' }}
                                        </h3>
                                    </div>
                                @else
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <span class="text-2xl font-bold text-white">
                                            {{ substr($match->homePlayer->name ?? 'TBD', 0, 2) }}
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-bold text-white">
                                        {{ $match->homePlayer->name ?? 'TBD' }}
                                    </h3>
                                @endif
                                @if($match->homePlayer && $match->homePlayer->position && $match->position_code !== 'Dubl')
                                    <p class="text-sm text-gray-400 mt-1">({{ $match->homePlayer->position }})</p>
                                @endif
                                @if($match->status === 'forfeited')
                                    @if($match->forfeited_by === 'home')
                                        <div class="text-red-400 mt-2 text-center">
                                            <div class="text-sm">Forfeited</div>
                                            <div class="text-lg font-bold">Lost by Forfeit</div>
                                        </div>
                                    @else
                                        <div class="text-green-400 mt-2 text-center">
                                            <div class="text-sm">Won by</div>
                                            <div class="text-lg font-bold">Forfeit</div>
                                        </div>
                                    @endif
                                @elseif($match->status === 'cancelled')
                                @if($match->home_score || $match->away_score)
                                <div class="text-5xl font-black text-blue-400 mt-2">{{ $match->home_score }}</div>
                                @else
                                <div class="text-orange-400 mt-2 text-center">
                                    <div class="text-sm">Match</div>
                                    <div class="text-lg font-bold">Cancelled</div>
                                </div>
                                @endif
                                @elseif(in_array($match->status, ['in_progress', 'completed']))
                                <div class="text-5xl font-black text-blue-400 mt-2">{{ $match->home_score }}</div>
                                @endif
                            </div>

                            <!-- VS -->
                            <div class="text-center px-4">
                                <div class="text-gray-500 text-sm font-bold uppercase tracking-widest mb-2">VS</div>
                                @if($match->status === 'completed')
                                <div class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Završeno</div>
                                @elseif($match->status === 'in_progress')
                                <div class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider animate-pulse">U toku</div>
                                @elseif($match->status === 'forfeited')
                                <div class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Predaja</div>
                                @elseif($match->status === 'cancelled')
                                <div class="bg-orange-500/20 text-orange-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Otkazano</div>
                                @else
                                <div class="bg-gray-700 text-gray-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Zakazano</div>
                                @endif
                            </div>

                            <!-- Away Player -->
                            <div class="text-center w-full md:w-auto">
                                @if($match->position_code === 'Dubl')
                                    <div class="flex -space-x-4 justify-center mb-3">
                                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center border-4 border-gray-800 z-10">
                                            <span class="text-xl font-bold text-white">
                                                {{ substr($doublesPlayers['away_1']->name ?? '?', 0, 2) }}
                                            </span>
                                        </div>
                                        <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-500 rounded-full flex items-center justify-center border-4 border-gray-800">
                                            <span class="text-xl font-bold text-white">
                                                {{ substr($doublesPlayers['away_2']->name ?? '?', 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <h3 class="text-xl font-bold text-white">
                                            {{ $doublesPlayers['away_1']->name ?? '?' }}
                                        </h3>
                                        <h3 class="text-xl font-bold text-white">
                                            {{ $doublesPlayers['away_2']->name ?? '?' }}
                                        </h3>
                                    </div>
                                @else
                                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <span class="text-2xl font-bold text-white">
                                            {{ substr($match->awayPlayer->name ?? 'TBD', 0, 2) }}
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-bold text-white">
                                        {{ $match->awayPlayer->name ?? 'TBD' }}
                                    </h3>
                                @endif
                                @if($match->awayPlayer && $match->awayPlayer->position && $match->position_code !== 'Dubl')
                                    <p class="text-sm text-gray-400 mt-1">({{ $match->awayPlayer->position }})</p>
                                @endif
                                @if($match->status === 'forfeited')
                                    @if($match->forfeited_by === 'away')
                                        <div class="text-red-400 mt-2 text-center">
                                            <div class="text-sm">Forfeited</div>
                                            <div class="text-lg font-bold">Lost by Forfeit</div>
                                        </div>
                                    @else
                                        <div class="text-green-400 mt-2 text-center">
                                            <div class="text-sm">Won by</div>
                                            <div class="text-lg font-bold">Forfeit</div>
                                        </div>
                                    @endif
                                @elseif($match->status === 'cancelled')
                                @if($match->home_score || $match->away_score)
                                <div class="text-5xl font-black text-red-400 mt-2">{{ $match->away_score }}</div>
                                @else
                                <div class="text-orange-400 mt-2 text-center">
                                    <div class="text-sm">Match</div>
                                    <div class="text-lg font-bold">Cancelled</div>
                                </div>
                                @endif
                                @elseif(in_array($match->status, ['in_progress', 'completed']))
                                <div class="text-5xl font-black text-red-400 mt-2">{{ $match->away_score }}</div>
                                @endif
                            </div>
                        </div>

                        @if($match->scheduled_at)
                        <div class="mt-6 text-center">
                            <div class="text-gray-400 text-sm">Zakazano za</div>
                            <div class="text-white font-medium">{{ $match->scheduled_at->format('d.m.Y \u H:i') }}</div>
                        </div>
                        @endif

                        @if($match->played_at)
                        <div class="mt-4 text-center">
                            <div class="text-gray-400 text-sm">Odigrano</div>
                            <div class="text-white font-medium">{{ $match->played_at->format('d.m.Y \u H:i') }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Match Officials & Captains -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($competition->is_team_based && ($match->home_captain_id || $match->away_captain_id))
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Kapiteni</h3>
                        <div class="space-y-4">
                            @if($match->home_captain_id)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400 text-sm">{{ $match->homeTeam->name ?? 'Domaći' }}</span>
                                <span class="text-white font-bold">{{ $match->homeCaptain->name }}</span>
                            </div>
                            @endif
                            @if($match->away_captain_id)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400 text-sm">{{ $match->awayTeam->name ?? 'Gosti' }}</span>
                                <span class="text-white font-bold">{{ $match->awayCaptain->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($match->referee_name || $match->referee_user_id)
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Službena lica</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center text-xl">
                                ⚖️
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-purple-400 uppercase tracking-widest">Sudija</div>
                                <div class="font-bold text-white">
                                    {{ $match->referee_name ?: ($match->referee ? $match->referee->name : 'Nije dodijeljen') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Match Sets (if applicable) -->
                @if($competition->sport->slug === 'stoni-tenis' && $match->sets)
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-semibold text-white mb-4">Rezultati po setovima</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="text-left py-3 px-2 text-gray-400 font-medium">Set</th>
                                    <th class="text-center py-3 px-2 text-gray-400 font-medium">
                                        {{ $match->homePlayer->name ?? 'Domaćin' }}
                                    </th>
                                    <th class="text-center py-3 px-2 text-gray-400 font-medium">
                                        {{ $match->awayPlayer->name ?? 'Gost' }}
                                    </th>
                                    <th class="text-center py-3 px-2 text-gray-400 font-medium">Trajanje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sets = $match->sets ?? [];
                                    $setDurations = $match->set_durations ?? [];
                                @endphp
                                @foreach($sets as $setNumber => $set)
                                <tr class="border-b border-gray-700/50">
                                    <td class="py-3 px-2 text-white font-medium">Set {{ $setNumber + 1 }}</td>
                                    <td class="py-3 px-2 text-center">
                                        @php
                                            $homeScore = $set['home_score'] ?? $set['home'] ?? 0;
                                            $awayScore = $set['away_score'] ?? $set['away'] ?? 0;
                                            $homeWon = $homeScore > $awayScore;
                                        @endphp
                                        <span class="{{ $homeWon ? 'text-green-400 font-bold text-lg' : 'text-white' }}">
                                            {{ $homeScore }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        @php
                                            $awayWon = $awayScore > $homeScore;
                                        @endphp
                                        <span class="{{ $awayWon ? 'text-green-400 font-bold text-lg' : 'text-white' }}">
                                            {{ $awayScore }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-center text-gray-300">
                                        @if(is_numeric($setDurations[$setNumber] ?? null))
                                            @php
                                                $duration = $setDurations[$setNumber];
                                                $minutes = floor($duration / 60);
                                                $seconds = $duration % 60;
                                                echo sprintf('%02d:%02d', $minutes, $seconds);
                                            @endphp
                                        @else
                                            {{ $setDurations[$setNumber] ?? '00:00' }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Match Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Match Info -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-xl font-semibold text-white mb-4">Informacije o meču</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Takmičenje</span>
                                <span class="text-white">{{ $competition->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Runda</span>
                                <span class="text-white">{{ $match->round }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Sport</span>
                                <span class="text-white">{{ $competition->sport->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Tip takmičenja</span>
                                <span class="text-white">{{ $competition->is_team_based ? 'Ekipno' : 'Individualno' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Status</span>
                                <span class="text-white">
                                    @if($match->status === 'completed') Završeno
                                    @elseif($match->status === 'in_progress') U toku
                                    @elseif($match->status === 'forfeited') Predaja
                                    @elseif($match->status === 'cancelled') Otkazano
                                    @else Zakazano @endif
                                </span>
                            </div>
                            @if($match->scheduled_at)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Zakazano</span>
                                <span class="text-white">{{ $match->scheduled_at->format('d.m.Y H:i') }}</span>
                            </div>
                            @endif
                            @if($match->played_at)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Odigrano</span>
                                <span class="text-white">{{ $match->played_at->format('d.m.Y H:i') }}</span>
                            </div>
                            @endif
                            @if($match->set_durations && count($match->set_durations) > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Ukupno trajanje</span>
                                <span class="text-white font-medium">
                                    @php
                                        $totalSeconds = 0;
                                        foreach($match->set_durations as $duration) {
                                            if (is_numeric($duration)) {
                                                $totalSeconds += $duration;
                                            } elseif (preg_match('/^(\d{2}):(\d{2})$/', $duration, $matches)) {
                                                $totalSeconds += ($matches[1] * 60) + $matches[2];
                                            }
                                        }
                                        $totalMinutes = floor($totalSeconds / 60);
                                        $totalRemainingSeconds = $totalSeconds % 60;
                                        echo sprintf('%02d:%02d', $totalMinutes, $totalRemainingSeconds);
                                    @endphp
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Participants Info -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-xl font-semibold text-white mb-4">Učesnici</h3>
                        <div class="space-y-4">

                            <!-- Home Participant -->
                            <div class="p-4 bg-gray-700/30 rounded-xl border border-gray-600/30">
                                <div class="flex items-center space-x-4">
                                    @if($match->position_code === 'Dubl')
                                        <div class="flex -space-x-3">
                                            <div class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center border-2 border-gray-800">
                                                <span class="text-blue-400 font-bold text-xs">
                                                    {{ substr($doublesPlayers['home_1']->name ?? '?', 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="w-10 h-10 bg-blue-400/20 rounded-full flex items-center justify-center border-2 border-gray-800">
                                                <span class="text-blue-300 font-bold text-xs">
                                                    {{ substr($doublesPlayers['home_2']->name ?? '?', 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex flex-col">
                                                <h4 class="text-white font-bold">
                                                    {{ $doublesPlayers['home_1']->name ?? '?' }}
                                                </h4>
                                                <h4 class="text-white font-bold">
                                                    {{ $doublesPlayers['home_2']->name ?? '?' }}
                                                </h4>
                                            </div>
                                            <p class="text-blue-400 text-xs font-bold uppercase tracking-wider">Domaćin (Dubl)</p>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center">
                                            <span class="text-blue-400 font-bold">
                                                {{ substr($match->homePlayer->name ?? 'TBD', 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="text-white font-bold">
                                                {{ $match->homePlayer->name ?? 'TBD' }}
                                            </h4>
                                            <p class="text-blue-400 text-xs font-bold uppercase tracking-wider">Domaćin</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Away Participant -->
                            <div class="p-4 bg-gray-700/30 rounded-xl border border-gray-600/30">
                                <div class="flex items-center space-x-4">
                                    @if($match->position_code === 'Dubl')
                                        <div class="flex -space-x-3">
                                            <div class="w-10 h-10 bg-purple-500/20 rounded-full flex items-center justify-center border-2 border-gray-800">
                                                <span class="text-purple-400 font-bold text-xs">
                                                    {{ substr($doublesPlayers['away_1']->name ?? '?', 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="w-10 h-10 bg-purple-400/20 rounded-full flex items-center justify-center border-2 border-gray-800">
                                                <span class="text-purple-300 font-bold text-xs">
                                                    {{ substr($doublesPlayers['away_2']->name ?? '?', 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex flex-col">
                                                <h4 class="text-white font-bold">
                                                    {{ $doublesPlayers['away_1']->name ?? '?' }}
                                                </h4>
                                                <h4 class="text-white font-bold">
                                                    {{ $doublesPlayers['away_2']->name ?? '?' }}
                                                </h4>
                                            </div>
                                            <p class="text-purple-400 text-xs font-bold uppercase tracking-wider">Gost (Dubl)</p>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center">
                                            <span class="text-purple-400 font-bold">
                                                {{ substr($match->awayPlayer->name ?? 'TBD', 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="text-white font-bold">
                                                {{ $match->awayPlayer->name ?? 'TBD' }}
                                            </h4>
                                            <p class="text-purple-400 text-xs font-bold uppercase tracking-wider">Gost</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Match Officials -->
                @if($match->table || $match->referee || $match->moderator)
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-lg font-semibold text-white mb-4">🏓 Match Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($match->table)
                        <div class="bg-gray-700/30 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <div class="text-sm text-gray-400">Sto</div>
                            </div>
                            <div class="text-white font-medium">{{ $match->table->name }}</div>
                            @if($match->table->description)
                                <div class="text-xs text-gray-400 mt-1">{{ $match->table->description }}</div>
                            @endif
                        </div>
                        @endif
                        @if($match->referee)
                        <div class="bg-gray-700/30 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <div class="text-sm text-gray-400">Sudija</div>
                            </div>
                            <div class="text-white font-medium">{{ $match->referee->name }}</div>
                        </div>
                        @endif
                        @if($match->moderator)
                        <div class="bg-gray-700/30 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-gray-400">Moderator</div>
                            </div>
                            <div class="text-white font-medium">{{ $match->moderator->name }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Audit Information -->
                @if($match->edited_by || $match->completed_by)
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-lg font-semibold text-white mb-4">📋 Audit Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($match->edited_by)
                        <div class="bg-gray-700/30 rounded-lg p-4">
                            <div class="text-sm text-gray-400 mb-1">Last Edited By</div>
                            <div class="text-white font-medium">{{ $match->editedBy->name }}</div>
                            @if($match->edited_at)
                            <div class="text-xs text-gray-400 mt-1">{{ $match->edited_at->format('M j, Y g:i A') }}</div>
                            @endif
                        </div>
                        @endif
                        @if($match->completed_by)
                        <div class="bg-gray-700/30 rounded-lg p-4">
                            <div class="text-sm text-gray-400 mb-1">Completed By</div>
                            <div class="text-white font-medium">{{ $match->completedBy->name }}</div>
                            @if($match->completed_at)
                            <div class="text-xs text-gray-400 mt-1">{{ $match->completed_at->format('M j, Y g:i A') }}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Navigation -->
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}"
                       class="bg-gray-700/50 hover:bg-gray-600/50 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200">
                        ← Povratak na takmičenje
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script>
        function shareMatch() {
            const publicUrl = window.location.href;
            const liveUrl = window.location.href;

            const shareText = `Check out this match: {{ $match->homePlayer?->name ?? "Home" }} vs {{ $match->awayPlayer?->name ?? "Away" }}\n\n${publicUrl}`;

            if (navigator.share) {
                navigator.share({
                    title: 'Match Results',
                    text: shareText,
                    url: publicUrl
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(shareText).then(() => {
                    alert('Match link copied to clipboard!');
                }).catch(() => {
                    // Final fallback: show URLs
                    const message = `Share this match:\n\n${shareText}`;
                    if (liveUrl) {
                        message += `\n\nLive score: ${liveUrl}`;
                    }
                    alert(message);
                });
            }
        }
    </script>
</x-app-layout>