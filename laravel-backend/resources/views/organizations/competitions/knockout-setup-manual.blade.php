@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">🎯 Ručno Kreiraj Knockout Fazu</h1>
        <p class="text-gray-300">{{ $competition->name }} - Turnir</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
        <!-- Info Card -->
        <div class="lg:col-span-4 bg-blue-600/20 border border-blue-500 rounded-lg p-4">
            <h3 class="text-white font-semibold mb-2">📋 Upustva</h3>
            <ul class="text-sm text-gray-300 space-y-1">
                <li>✓ Odaberi broj mečeva koji želiš za primeiro kolo</li>
                <li>✓ Sistem će automatski rasporediti igrače prema Svjetskom prvenstvu pravilima</li>
                <li>✓ Pobjednici grupa će biti na suprotnim stranama bracket-a</li>
                <li>✓ Igrači iz iste grupe se neće susresti do finala</li>
            </ul>
        </div>

        <!-- Qualified Players -->
        <div class="lg:col-span-2 bg-gray-800/50 rounded-lg p-6 border border-gray-700">
            <h3 class="text-white font-bold mb-4">👥 Kvalificirani Igrači</h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @php
                    $qualifiedPlayers = collect();
                    foreach($competition->tournamentGroups as $group) {
                        $standings = App\Models\Standing::where('competition_id', $competition->id)
                            ->where('tournament_group_id', $group->id)
                            ->with('player')
                            ->orderBy('points', 'desc')
                            ->orderByRaw('(sets_won - sets_lost) desc')
                            ->orderByRaw('(points_won - points_lost) desc')
                            ->limit($competition->players_advancing_per_group ?? 2)
                            ->get();
                        foreach($standings as $standing) {
                            if($standing->player) {
                                $qualifiedPlayers->push([
                                    'player' => $standing->player,
                                    'group' => $group->name,
                                    'position' => $standing->position ?? $loop->iteration
                                ]);
                            }
                        }
                    }
                @endphp

                @foreach($qualifiedPlayers as $qualified)
                    <div class="flex items-center justify-between p-2 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 cursor-pointer transition-colors"
                         onclick="selectPlayer('{{ $qualified['player']->id }}', '{{ $qualified['player']->name }}')">
                        <div>
                            <div class="text-white font-medium">{{ $qualified['player']->name }}</div>
                            <div class="text-xs text-gray-400">Grupa {{ $qualified['group'] }} - {{ $qualified['position'] == 1 ? 'Pobjednika' : 'Drugoplasirani' }}</div>
                        </div>
                        <div class="text-lg">
                            {{ $qualified['position'] == 1 ? '🥇' : '🥈' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Match Setup -->
        <div class="lg:col-span-2 bg-gray-800/50 rounded-lg p-6 border border-gray-700">
            <h3 class="text-white font-bold mb-4">🎮 Konfiguraj Mečeve</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Broj mečeva u prvom kolu:</label>
                <select id="matchCount" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Odaberi broj...</option>
                    <option value="1">1 meč (Finale)</option>
                    <option value="2">2 meča (Polufinale)</option>
                    <option value="4">4 meča (Četvrtfinale)</option>
                    <option value="8">8 mečeva (Osmina finala)</option>
                    <option value="16">16 mečeva (Šesnaestina)</option>
                </select>
            </div>

            <div id="matchesContainer" class="space-y-3 max-h-96 overflow-y-auto hidden">
                <!-- Matches will be generated here -->
            </div>
        </div>
    </div>

    <!-- Selected Matches -->
    <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700 mb-8">
        <h3 class="text-white font-bold mb-4">✅ Odabrani Mečevi</h3>
        <form id="knockoutForm" method="POST" action="{{ route('organizations.competitions.save-manual-knockout', [$organization, $competition]) }}">
            @csrf
            <div id="selectedMatches" class="space-y-3 min-h-32 bg-gray-700/20 rounded-lg p-4">
                <p class="text-gray-400 text-center">Klikni na igrače da kreiraj mečeve...</p>
            </div>

            <div class="mt-6 flex gap-3">
                <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" 
                   class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Odustani
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold">
                    💾 Sačuvaj Knockout Fazu
                </button>
            </div>
        </form>
    </div>

    <!-- Or Auto-Generate -->
    <div class="bg-purple-600/20 border border-purple-500 rounded-lg p-6 text-center">
        <h3 class="text-white font-bold mb-2 text-lg">⚡ Ili Koristi Automatsku Generisanje</h3>
        <p class="text-gray-300 mb-4">Sistem će automatski kreiraj bracket prema Svjetskom prvenstvu pravilima</p>
        <form method="POST" action="{{ route('organizations.competitions.auto-generate-knockout', [$organization, $competition]) }}" style="display: inline;">
            @csrf
            <button type="submit" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors font-semibold">
                🔄 Automatski Generiši Bracket
            </button>
        </form>
    </div>
</div>

<script>
const selectedMatches = {};
let firstSelectedPlayer = null;
let currentMatchCount = 0;

document.getElementById('matchCount').addEventListener('change', function(e) {
    const count = parseInt(e.target.value);
    if (count) {
        currentMatchCount = count;
        generateMatchSlots(count);
    }
});

function generateMatchSlots(count) {
    const container = document.getElementById('matchesContainer');
    container.innerHTML = '';
    
    for (let i = 0; i < count; i++) {
        const slot = document.createElement('div');
        slot.className = 'flex items-center gap-2 p-2 bg-gray-600/30 rounded-lg border border-gray-500/30';
        slot.innerHTML = `
            <div class="flex-1 text-sm text-gray-400">Meč ${i + 1}</div>
            <div id="match-${i}-player1" class="flex-1 px-2 py-1 bg-gray-700 rounded text-xs text-gray-400">Odaberi 1.</div>
            <span class="text-gray-400">vs</span>
            <div id="match-${i}-player2" class="flex-1 px-2 py-1 bg-gray-700 rounded text-xs text-gray-400">Odaberi 2.</div>
        `;
        container.appendChild(slot);
    }
    
    container.classList.remove('hidden');
    selectedMatches = {};
    firstSelectedPlayer = null;
}

function selectPlayer(playerId, playerName) {
    if (!firstSelectedPlayer) {
        firstSelectedPlayer = { id: playerId, name: playerName };
    } else {
        // Find first empty match slot
        for (let i = 0; i < currentMatchCount; i++) {
            const player1 = document.getElementById(`match-${i}-player1`);
            const player2 = document.getElementById(`match-${i}-player2`);
            
            if (player1.textContent === 'Odaberi 1.' || player2.textContent === 'Odaberi 2.') {
                if (player1.textContent === 'Odaberi 1.') {
                    player1.innerHTML = `<span class="text-green-400">${firstSelectedPlayer.name}</span>`;
                    if (!selectedMatches[i]) selectedMatches[i] = {};
                    selectedMatches[i].home_player_id = firstSelectedPlayer.id;
                } else {
                    player2.innerHTML = `<span class="text-green-400">${playerName}</span>`;
                    if (!selectedMatches[i]) selectedMatches[i] = {};
                    selectedMatches[i].away_player_id = playerId;
                    
                    // If both are filled, move to next match
                    if (selectedMatches[i].home_player_id && selectedMatches[i].away_player_id) {
                        firstSelectedPlayer = null;
                    }
                }
                break;
            }
        }
        
        if (!firstSelectedPlayer) firstSelectedPlayer = null;
    }
}

document.getElementById('knockoutForm').addEventListener('submit', function(e) {
    if (Object.keys(selectedMatches).length === 0) {
        e.preventDefault();
        alert('Molim odaberi igrače za mečeve');
        return;
    }
    
    // Create hidden inputs for each match
    for (const [index, match] of Object.entries(selectedMatches)) {
        const homeInput = document.createElement('input');
        homeInput.type = 'hidden';
        homeInput.name = `matches[${index}][home_player_id]`;
        homeInput.value = match.home_player_id;
        this.appendChild(homeInput);
        
        const awayInput = document.createElement('input');
        awayInput.type = 'hidden';
        awayInput.name = `matches[${index}][away_player_id]`;
        awayInput.value = match.away_player_id;
        this.appendChild(awayInput);
    }
});
</script>
@endsection
