@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-white">Detalji Meča</h1>
            <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" class="text-blue-400 hover:text-blue-300 transition-colors">
                &larr; Nazad na takmičenje
            </a>
        </div>

        <!-- Scoreboard -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 overflow-hidden mb-8">
            <div class="bg-gray-900/50 px-6 py-4 border-b border-gray-700/50 flex justify-between items-center">
                <span class="text-sm font-medium text-gray-400 uppercase tracking-wider">Ekipni Meč</span>
                <span class="px-3 py-1 text-xs font-bold uppercase tracking-widest rounded-full {{ $teamMatch->status === 'completed' ? 'bg-green-500/20 text-green-400' : 'bg-blue-500/20 text-blue-400' }}">
                    {{ $teamMatch->status === 'completed' ? 'Završeno' : 'U toku' }}
                </span>
            </div>
            <div class="p-8 flex justify-between items-center">
                <div class="text-center flex-1">
                    <h2 class="text-2xl font-bold text-white">{{ $teamMatch->homeTeam->name ?? 'Nepoznato' }}</h2>
                </div>
                <div class="flex flex-col items-center px-8">
                    <div class="text-5xl md:text-6xl font-black text-white tracking-tighter">
                        {{ $teamMatch->home_score }} : {{ $teamMatch->away_score }}
                    </div>
                    @if($teamMatch->scheduled_at)
                        <div class="text-sm text-gray-400 mt-2 font-medium">
                            {{ $teamMatch->scheduled_at->format('d.m.Y H:i') }}
                        </div>
                    @endif
                </div>
                <div class="text-center flex-1">
                    <h2 class="text-2xl font-bold text-white">{{ $teamMatch->awayTeam->name ?? 'Nepoznato' }}</h2>
                </div>
            </div>
        </div>

        @unless($teamMatch->usesSingleMatchTie())
        <!-- Captains and Referee -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-700/50 bg-gray-900/50">
                <h3 class="font-bold text-white">Kapetani i Sudija</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Home Captain -->
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-wider">Kapetan domaćina</label>
                        <select onchange="updateCaptainsAndReferee('home_captain_id', this.value)"
                            class="w-full bg-gray-900/50 border border-gray-700 text-white focus:border-blue-500 focus:ring-blue-500 rounded-xl p-3">
                            <option value="">Odaberi kapitena...</option>
                            @foreach($homePlayers as $player)
                                <option value="{{ $player->id }}" {{ $teamMatch->home_captain_id == $player->id ? 'selected' : '' }}>
                                    {{ $player->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Away Captain -->
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-wider">Kapetan gosta</label>
                        <select onchange="updateCaptainsAndReferee('away_captain_id', this.value)"
                            class="w-full bg-gray-900/50 border border-gray-700 text-white focus:border-blue-500 focus:ring-blue-500 rounded-xl p-3">
                            <option value="">Odaberi kapitena...</option>
                            @foreach($awayPlayers as $player)
                                <option value="{{ $player->id }}" {{ $teamMatch->away_captain_id == $player->id ? 'selected' : '' }}>
                                    {{ $player->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Referee -->
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-wider">Sudija</label>
                        <input type="text"
                            value="{{ $teamMatch->referee_name }}"
                            onchange="updateCaptainsAndReferee('referee_name', this.value)"
                            placeholder="Unesite ime sudije..."
                            class="w-full bg-gray-900/50 border border-gray-700 text-white focus:border-blue-500 focus:ring-blue-500 rounded-xl p-3">
                    </div>
                </div>
            </div>
        </div>
        @endunless

        @if($teamMatch->usesSingleMatchTie())
        <!-- Single Match (e.g. Padel doubles - the team already IS the pair, played as one match like Tennis) -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700/50 bg-gray-900/50 flex items-center justify-between">
                <h3 class="font-bold text-white">Rezultat Meča</h3>
                @if($singleMatch?->venue)
                    <a href="{{ route('venues.public.show', $singleMatch->venue) }}" target="_blank" class="text-xs font-bold text-gray-400 hover:text-blue-400 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ $singleMatch->venue->name }}
                    </a>
                @endif
            </div>
            <div class="bg-gray-900/50 rounded-xl p-3 sm:p-4 m-4 sm:m-6 border border-gray-700/30 flex flex-wrap sm:flex-nowrap sm:items-center">
                <div class="w-1/2 sm:w-auto sm:flex-1 text-left sm:text-right pr-2 sm:pr-4 min-w-0 order-1">
                    <span class="text-white font-medium text-sm sm:text-base break-words">{{ $teamMatch->homeTeam->name ?? 'TBD' }}</span>
                </div>
                <div class="w-full sm:w-auto flex flex-col items-center px-2 sm:px-4 sm:min-w-[110px] shrink-0 order-3 sm:order-2 mt-3 sm:mt-0">
                    <span class="text-lg sm:text-xl font-black text-white">
                        {{ !$singleMatch || $singleMatch->status !== 'completed' ? '-' : $singleMatch->home_score }} : {{ !$singleMatch || $singleMatch->status !== 'completed' ? '-' : $singleMatch->away_score }}
                    </span>
                    @if($singleMatch && $singleMatch->status === 'completed' && !empty($singleMatch->sets))
                        <div class="flex flex-wrap items-center justify-center gap-1 mt-1.5">
                            @foreach($singleMatch->sets as $set)
                                @php
                                    $sh = $set['home'] ?? $set['home_score'] ?? null;
                                    $sa = $set['away'] ?? $set['away_score'] ?? null;
                                @endphp
                                @if($sh !== null && $sa !== null)
                                    <span class="text-[10px] font-bold text-gray-400 bg-gray-800/80 border border-gray-700/50 rounded px-1.5 py-0.5 tabular-nums">{{ $sh }}:{{ $sa }}</span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="w-1/2 sm:w-auto sm:flex-1 text-right sm:text-left pl-2 sm:pl-4 min-w-0 order-2 sm:order-3">
                    <span class="text-white font-medium text-sm sm:text-base break-words">{{ $teamMatch->awayTeam->name ?? 'TBD' }}</span>
                </div>

                @if($singleMatch)
                    <div class="w-full sm:w-auto flex items-center justify-center gap-5 sm:gap-1 mt-3 sm:mt-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-700/30 sm:ml-2 order-4">
                        <button type="button"
                                onclick="openQuickResultModal({{ $singleMatch->id }}, '{{ addslashes($teamMatch->homeTeam->name ?? 'TBD') }}', '{{ addslashes($teamMatch->awayTeam->name ?? 'TBD') }}', {{ $singleMatch->status === 'completed' ? $singleMatch->home_score : 'null' }}, {{ $singleMatch->status === 'completed' ? $singleMatch->away_score : 'null' }}, {{ json_encode($singleMatch->sets ?? []) }}, '{{ $singleMatch->played_at ? $singleMatch->played_at->format('Y-m-d\TH:i') : '' }}', '{{ $singleMatch->venue_id }}')"
                                class="text-gray-500 hover:text-yellow-400 transition-colors p-1" title="{{ $singleMatch->status === 'completed' ? 'Izmijeni rezultat' : 'Brzi unos rezultata' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </button>
                        <a href="{{ route('organizations.competitions.matches.edit', [$organization, $competition, $singleMatch]) }}" class="text-gray-600 hover:text-blue-400 transition-colors p-1" title="Uredi meč (teren, datum, detalji)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
        @else
        <!-- Individual Matches -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700/50 bg-gray-900/50 flex justify-between items-center">
                <h3 class="font-bold text-white">Pojedinačni Mečevi (Corbillon sistem)</h3>
                <div class="flex gap-2">
                    <form action="{{ route('organizations.competitions.team-matches.initialize', [$organization, $competition, $teamMatch]) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                            Dodaj sve (7 mečeva)
                        </button>
                    </form>
                    <button onclick="document.getElementById('addSingleMatchModal').classList.remove('hidden')" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                        Dodaj jedan meč
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-900/30">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Domaćin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Gost</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Rezultat</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Akcija</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($teamMatch->individualMatches->sortBy('match_order') as $match)
                        <tr class="{{ $match->status === 'completed' ? 'bg-gray-900/20' : 'hover:bg-gray-700/30' }} transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-400">
                                {{ $match->match_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white {{ $match->status === 'completed' && $match->home_score > $match->away_score ? 'bg-green-500/10' : '' }}">
                                @if($match->position_code === 'Dubl')
                                    <div class="flex flex-col gap-1">
                                        <select onchange="updateLineup('home_dubl_1', this.value)" 
                                            class="bg-gray-900/50 border-gray-700 text-white text-[10px] rounded-lg p-1 w-full {{ $match->status === 'completed' && $match->home_score > $match->away_score ? 'border-green-500/50' : '' }}">
                                            <option value="">Igrač 1...</option>
                                            @foreach($homePlayers as $player)
                                                <option value="{{ $player->id }}" {{ ($teamMatch->lineup['home_dubl_1'] ?? null) == $player->id ? 'selected' : '' }}>
                                                    {{ $player->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <select onchange="updateLineup('home_dubl_2', this.value)" 
                                            class="bg-gray-900/50 border-gray-700 text-white text-[10px] rounded-lg p-1 w-full {{ $match->status === 'completed' && $match->home_score > $match->away_score ? 'border-green-500/50' : '' }}">
                                            <option value="">Igrač 2...</option>
                                            @foreach($homePlayers as $player)
                                                <option value="{{ $player->id }}" {{ ($teamMatch->lineup['home_dubl_2'] ?? null) == $player->id ? 'selected' : '' }}>
                                                    {{ $player->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <select onchange="updateMatchPlayer({{ $match->id }}, 'home', this.value)" 
                                        class="bg-gray-900/50 border-gray-700 text-white text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 {{ $match->status === 'completed' && $match->home_score > $match->away_score ? 'border-green-500/50' : '' }}">
                                        <option value="">Odaberi igrača...</option>
                                        @foreach($homePlayers as $player)
                                            <option value="{{ $player->id }}" {{ $match->home_player_id == $player->id ? 'selected' : '' }}>
                                                {{ $player->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white {{ $match->status === 'completed' && $match->away_score > $match->home_score ? 'bg-green-500/10' : '' }}">
                                @if($match->position_code === 'Dubl')
                                    <div class="flex flex-col gap-1">
                                        <select onchange="updateLineup('away_dubl_1', this.value)" 
                                            class="bg-gray-900/50 border-gray-700 text-white text-[10px] rounded-lg p-1 w-full {{ $match->status === 'completed' && $match->away_score > $match->home_score ? 'border-green-500/50' : '' }}">
                                            <option value="">Igrač 1...</option>
                                            @foreach($awayPlayers as $player)
                                                <option value="{{ $player->id }}" {{ ($teamMatch->lineup['away_dubl_1'] ?? null) == $player->id ? 'selected' : '' }}>
                                                    {{ $player->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <select onchange="updateLineup('away_dubl_2', this.value)" 
                                            class="bg-gray-900/50 border-gray-700 text-white text-[10px] rounded-lg p-1 w-full {{ $match->status === 'completed' && $match->away_score > $match->home_score ? 'border-green-500/50' : '' }}">
                                            <option value="">Igrač 2...</option>
                                            @foreach($awayPlayers as $player)
                                                <option value="{{ $player->id }}" {{ ($teamMatch->lineup['away_dubl_2'] ?? null) == $player->id ? 'selected' : '' }}>
                                                    {{ $player->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <select onchange="updateMatchPlayer({{ $match->id }}, 'away', this.value)" 
                                        class="bg-gray-900/50 border-gray-700 text-white text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 {{ $match->status === 'completed' && $match->away_score > $match->home_score ? 'border-green-500/50' : '' }}">
                                        <option value="">Odaberi igrača...</option>
                                        @foreach($awayPlayers as $player)
                                            <option value="{{ $player->id }}" {{ $match->away_player_id == $player->id ? 'selected' : '' }}>
                                                {{ $player->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold">
                                @if($match->status === 'completed' || ($match->home_score > 0 || $match->away_score > 0))
                                    <span class="{{ $match->status === 'completed' ? 'text-white' : 'text-blue-400' }}">
                                        {{ $match->home_score }} : {{ $match->away_score }}
                                    </span>
                                @else
                                    <span class="text-gray-600">- : -</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @if($match->status !== 'completed' && ($teamMatch->home_score < 4 && $teamMatch->away_score < 4))
                                        <button onclick="openQuickResultModal({{ $match->id }}, '{{ $match->position_code === 'Dubl' ? ($doublesPlayers['home_1']->name ?? '?') . ' / ' . ($doublesPlayers['home_2']->name ?? '?') : ($match->homePlayer->name ?? 'Nepoznat') }}', '{{ $match->position_code === 'Dubl' ? ($doublesPlayers['away_1']->name ?? '?') . ' / ' . ($doublesPlayers['away_2']->name ?? '?') : ($match->awayPlayer->name ?? 'Nepoznat') }}')"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-lg text-white bg-yellow-600/80 hover:bg-yellow-600 shadow-sm transition-colors duration-150">
                                            ⚡ Brzi unos
                                        </button>
                                        <a href="{{ route('organizations.competitions.matches.show', [$organization, $competition, $match]) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-lg text-white bg-blue-600/80 hover:bg-blue-600 shadow-sm transition-colors duration-150">
                                            Unesi rezultat
                                        </a>
                                    @elseif($match->status === 'completed')
                                        <a href="{{ route('organizations.competitions.matches.show', [$organization, $competition, $match]) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-gray-700 text-xs font-bold rounded-lg text-gray-300 bg-gray-800 hover:bg-gray-700 shadow-sm transition-colors duration-150">
                                            Pregled
                                        </a>
                                    @endif
                                    
                                    <form action="{{ route('organizations.competitions.team-matches.individual.destroy', [$organization, $competition, $teamMatch, $match]) }}" method="POST" onsubmit="return confirm('Obrisati ovaj pojedinačni meč?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400 p-1.5 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

    <!-- Add Single Match Modal -->
    <div id="addSingleMatchModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-2xl max-w-md w-full">
            <div class="p-6 border-b border-gray-700 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white">Dodaj pojedinačni meč</h3>
                <button onclick="document.getElementById('addSingleMatchModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="{{ route('organizations.competitions.team-matches.add-single', [$organization, $competition, $teamMatch]) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-wider">Redni broj meča</label>
                    <input type="number" name="match_order" value="{{ $teamMatch->individualMatches->count() + 1 }}" 
                        class="w-full bg-gray-900/50 border-gray-700 text-white focus:border-blue-500 focus:ring-blue-500 rounded-xl" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-wider">Oznaka (npr. A-X, Dubl...)</label>
                    <input type="text" name="position_code" placeholder="A-X" 
                        class="w-full bg-gray-900/50 border-gray-700 text-white focus:border-blue-500 focus:ring-blue-500 rounded-xl" required>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('addSingleMatchModal').classList.add('hidden')" class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-bold transition">
                        Odustani
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-lg shadow-blue-500/20">
                        Dodaj meč
                    </button>
                </div>
            </form>
        </div>
    </div>

@include('organizations.competitions.partials.modals')
@include('organizations.competitions.partials.scripts')

<script>
function updateMatchPlayer(matchId, side, playerId) {
    const url = `{{ route('organizations.competitions.team-matches.individual.players', [$organization, $competition, $teamMatch, ':match']) }}`.replace(':match', matchId);
    
    const data = {};
    data[side + '_player_id'] = playerId;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Optional: Show a small toast or success indicator
            console.log('Igrač uspješno ažuriran');
        }
    })
    .catch(error => {
        console.error('Greška:', error);
        alert('Došlo je do greške prilikom ažuriranja igrača.');
    });
}

function updateLineup(field, playerId) {
    const url = `{{ route('organizations.competitions.team-matches.lineup.update', [$organization, $competition, $teamMatch]) }}`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ field, player_id: playerId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Postava uspješno ažurirana');
        }
    })
    .catch(error => {
        console.error('Greška:', error);
        alert('Došlo je do greške prilikom ažuriranja postave.');
    });
}

function updateCaptainsAndReferee(field, value) {
    const url = `{{ route('organizations.competitions.team-matches.captains-referee.update', [$organization, $competition, $teamMatch]) }}`;
    
    const data = {};
    data[field] = value;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log('Kapetani i sudija uspješno ažurirani');
        }
    })
    .catch(error => {
        console.error('Greška:', error);
        alert(error.error || 'Došlo je do greške prilikom ažuriranja kapetana ili sudije.');
    });
}
</script>
@endsection