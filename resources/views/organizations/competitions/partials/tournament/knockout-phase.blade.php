{{-- Knockout Phase Bracket --}}
@php
    $knockoutMatches = isset($knockoutMatches) ? $knockoutMatches : collect();
    $isOwner = isset($isOwner) ? $isOwner : false;
    $organization = isset($organization) ? $organization : null;
    $competition = isset($competition) ? $competition : null;
@endphp

@if($knockoutMatches && $knockoutMatches->count() > 0)
<div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 mb-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-white">🏆 Knockout Faza</h3>
        
        @if($isOwner && $knockoutMatches->count() > 0)
            <div class="flex gap-2">
                {{-- Check if current round is complete and show advance button --}}
                @php
                    $groupedByRound = $knockoutMatches->groupBy('round_number');
                    $currentRound = $groupedByRound->keys()->max();
                    $currentRoundMatches = $groupedByRound->get($currentRound);
                    $allMatchesComplete = $currentRoundMatches->every(function($match) {
                        // Bye matches are automatically completed when created
                        return in_array($match->status, ['completed', 'forfeited']);
                    });
                    $isFinale = $currentRoundMatches->count() == 1;
                @endphp
                
                @if($allMatchesComplete && !$isFinale)
                    <button type="button" onclick="confirmAdvanceRound({{ $currentRound }})"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <span>⏭️</span>
                        <span>Generiši narednu rundu</span>
                    </button>
                @endif
                
                <button type="button" onclick="confirmResetKnockout()"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                    🔄 Resetuj
                </button>
            </div>
        @endif
    </div>

    {{-- Bracket visualization --}}
    <div class="overflow-x-auto pb-4">
        <div class="inline-flex gap-12 min-w-max items-center p-4">
            @php
                $groupedByRound = $knockoutMatches->groupBy('round_number');
                $totalRounds = $groupedByRound->keys()->max();
            @endphp

            @foreach($groupedByRound as $round => $roundMatches)
                <div class="flex flex-col justify-around min-h-[400px] gap-4">
                    {{-- Round title --}}
                    <div class="text-center mb-4">
                        <div class="bg-blue-600/20 border border-blue-500 rounded-lg px-3 py-2">
                            <div class="text-blue-300 font-semibold text-sm">
                                @php
                                    $matchCount = $roundMatches->count();
                                    if ($matchCount == 1) echo 'Finale';
                                    elseif ($matchCount == 2) echo 'Polufinale';
                                    elseif ($matchCount == 4) echo 'Četvrtfinale';
                                    elseif ($matchCount == 8) echo 'Osmina finala';
                                    else echo 'Runda ' . $round;
                                @endphp
                            </div>
                        </div>
                    </div>

                    {{-- Matches in round --}}
                    <div class="space-y-3">
                        @foreach($roundMatches as $match)
                            <div class="w-56 bg-gray-700/30 rounded-lg border border-gray-600/50 overflow-hidden hover:border-blue-500/50 transition-all">
                                {{-- Match header --}}
                                <div class="bg-gray-800/50 px-3 py-2 border-b border-gray-600/30 flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-400">Meč {{ $match->match_order }}</span>
                                    <div class="flex items-center gap-2">
                                        @if($match->is_bye)
                                            <span class="text-xs px-2 py-1 rounded-full bg-blue-600/20 text-blue-400">
                                                BYE
                                            </span>
                                        @endif
                                        <span class="text-xs px-2 py-1 rounded-full
                                            @if($match->status === 'completed') bg-green-600/20 text-green-400
                                            @elseif($match->status === 'live') bg-red-600/20 text-red-400 animate-pulse
                                            @else bg-gray-600/20 text-gray-400
                                            @endif">
                                            @if($match->status === 'completed') ✓
                                            @elseif($match->status === 'live') 🔴
                                            @else ⏳
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                {{-- Players and score --}}
                                <div class="p-3">
                                    {{-- Home player --}}
                                    <div class="flex justify-between items-center mb-2 p-2 rounded-lg
                                        @if($match->homePlayer && (!$match->awayPlayer || ($match->status === 'completed' && $match->home_score > $match->away_score))) bg-green-500/10 border-l-4 border-l-green-500
                                        @else bg-gray-600/20
                                        @endif">
                                        <span class="text-white text-sm font-medium">
                                            @if($match->homePlayer)
                                                {{ $match->homePlayer->name }}
                                            @else
                                                TBD
                                            @endif
                                        </span>
                                        @if($match->status === 'completed' && $match->homePlayer && $match->awayPlayer)
                                            <span class="text-base font-bold {{ $match->home_score > $match->away_score ? 'text-green-400' : 'text-gray-400' }}">
                                                {{ $match->home_score ?? '-' }}
                                            </span>
                                        @elseif($match->homePlayer && !$match->awayPlayer)
                                            <span class="text-base font-bold text-green-400">1</span>
                                        @endif
                                    </div>

                                    {{-- Away player --}}
                                    <div class="flex justify-between items-center p-2 rounded-lg
                                        @if($match->awayPlayer && (!$match->homePlayer || ($match->status === 'completed' && $match->away_score > $match->home_score))) bg-green-500/10 border-l-4 border-l-green-500
                                        @else bg-gray-600/20
                                        @endif">
                                        <span class="text-white text-sm font-medium">
                                            @if($match->awayPlayer)
                                                {{ $match->awayPlayer->name }}
                                            @else
                                                TBD
                                            @endif
                                        </span>
                                        @if($match->status === 'completed' && $match->homePlayer && $match->awayPlayer)
                                            <span class="text-base font-bold {{ $match->away_score > $match->home_score ? 'text-green-400' : 'text-gray-400' }}">
                                                {{ $match->away_score ?? '-' }}
                                            </span>
                                        @elseif($match->awayPlayer && !$match->homePlayer)
                                            <span class="text-base font-bold text-green-400">1</span>
                                        @endif
                                    </div>

                                    {{-- Actions --}}
                                    @if(!$match->is_bye)
                                        <div class="mt-3 flex gap-1 text-xs">
                                            @if($match->status === 'scheduled' || $match->status === 'pending')
                                                <button type="button"
                                                    onclick="openQuickResultModal({{ json_encode((string)($match->id ?? '')) }}, {{ json_encode($match->homePlayer->name ?? 'TBD') }}, {{ json_encode($match->awayPlayer->name ?? 'TBD') }})"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-1.5 py-0.5 rounded text-xs transition-colors">
                                                    ⚡
                                                </button>
                                                <a href="{{ route('organizations.competitions.matches.edit', [$organization, $competition, $match]) }}"
                                                   class="bg-purple-600 hover:bg-purple-700 text-white px-1.5 py-0.5 rounded text-xs transition-colors text-center inline-block">
                                                    ✏️
                                                </a>
                                                <a href="{{ route('competitions.live-score', ['match' => $match->id]) }}"
                                                   class="bg-red-600 hover:bg-red-700 text-white px-1.5 py-0.5 rounded text-xs transition-colors text-center inline-block">
                                                    🔴 Live
                                                </a>
                                            @elseif($match->status === 'completed')
                                                <button type="button"
                                                    onclick="openQuickEditModal({{ json_encode((string)($match->id ?? '')) }}, {{ json_encode($match->homePlayer->name ?? 'TBD') }}, {{ json_encode($match->awayPlayer->name ?? 'TBD') }}, {{ json_encode($match->home_score ?? 0) }}, {{ json_encode($match->away_score ?? 0) }}, {{ json_encode($match->sets ?? []) }})"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-1.5 py-0.5 rounded text-xs transition-colors">
                                                    ⚡
                                                </button>
                                                <a href="{{ route('organizations.competitions.matches.edit', [$organization, $competition, $match]) }}"
                                                   class="bg-purple-600 hover:bg-purple-700 text-white px-1.5 py-0.5 rounded text-xs transition-colors text-center inline-block">
                                                    ✏️
                                                </a>
                                                <a href="{{ route('organizations.competitions.matches.show', [$organization, $competition, $match]) }}"
                                                   class="bg-gray-600 hover:bg-gray-700 text-white px-1.5 py-0.5 rounded text-xs transition-colors text-center inline-block">
                                                    👁️
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@else
<div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 mb-6 text-center">
    <p class="text-gray-300">Knockout faza još nije kreirana</p>
    @if($isOwner)
        <div class="mt-4 flex gap-3 justify-center">
            <a href="{{ route('organizations.competitions.knockout-setup', [$organization, $competition]) }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                🎯 Ručno Postavi
            </a>
            <form method="POST" action="{{ route('organizations.competitions.auto-generate-knockout', [$organization, $competition]) }}" style="display: inline;">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    ⚡ Automatski Generiši
                </button>
            </form>
        </div>
    @endif
</div>
@endif

@once
    {{-- Quick Result Modal --}}
    <div id="quickResultModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">⚡ Brzi unos rezultata</h3>
                    <button onclick="closeQuickResultModal()" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="quickResultForm">
                    @csrf
                    <input type="hidden" id="quickMatchId" name="match_id">

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-white font-medium mb-2" id="homePlayerName">Igrač 1</div>
                                <input type="number" id="homeScore" name="home_score" min="0" max="7"
                                       class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white text-center"
                                       placeholder="0">
                                <div class="text-xs text-gray-400 mt-1">Setovi osvojeni</div>
                            </div>
                            <div class="text-center">
                                <div class="text-white font-medium mb-2" id="awayPlayerName">Igrač 2</div>
                                <input type="number" id="awayScore" name="away_score" min="0" max="7"
                                       class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white text-center"
                                       placeholder="0">
                                <div class="text-xs text-gray-400 mt-1">Setovi osvojeni</div>
                            </div>
                        </div>

                        <div class="text-center text-gray-400 text-sm">
                            Broj setova koje je svaki igrač osvojio
                        </div>

                        {{-- Set details (optional) --}}
                        <div id="setsContainer" class="space-y-2">
                            <div class="text-center text-gray-300 text-sm mb-2">Detalji setova (opciono)</div>
                            <div id="setsList"></div>
                            <button type="button" onclick="addSet()" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm">
                                + Dodaj set
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="closeQuickResultModal()"
                                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors">
                            Odustani
                        </button>
                        <button type="button" onclick="saveQuickResult()"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition-colors">
                            💾 Sačuvaj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    // ...existing code...
    </script>
@endonce

<script>
function confirmResetKnockout() {
    if (confirm('Da li si siguran? Ovo će obrisati svu knockout fazu.')) {
        // Create and submit a form to reset knockout
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("organizations.competitions.reset-knockout", [$organization, $competition]) }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmAdvanceRound(currentRound) {
    if (confirm(`Da li želiš generirati sledeću rundu? Svi mečevi runde ${currentRound} su završeni.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("organizations.competitions.advance-knockout-round", [$organization, $competition]) }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add current round
        const roundInput = document.createElement('input');
        roundInput.type = 'hidden';
        roundInput.name = 'current_round';
        roundInput.value = currentRound;
        form.appendChild(roundInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

let currentSets = [];

function openQuickResultModal(matchId, homePlayer, awayPlayer) {
    // Fallback for null/undefined matchId
    if (matchId === null || matchId === undefined) matchId = '';
    matchId = String(matchId);
    console.log('Opening modal with:', { matchId, homePlayer, awayPlayer });
    document.getElementById('quickMatchId').value = matchId;
    document.getElementById('homePlayerName').textContent = homePlayer;
    document.getElementById('awayPlayerName').textContent = awayPlayer;
    document.getElementById('homeScore').value = '';
    document.getElementById('awayScore').value = '';
    currentSets = [];
    document.getElementById('setsList').innerHTML = '';
    document.getElementById('setsContainer').classList.remove('hidden'); // Show sets container immediately
    document.getElementById('quickResultModal').classList.remove('hidden');
}

function openQuickEditModal(matchId, homePlayer, awayPlayer, homeScore, awayScore, sets) {
    // Fallback for null/undefined matchId
    if (matchId === null || matchId === undefined) matchId = '';
    matchId = String(matchId);
    console.log('Opening edit modal with:', { matchId, homePlayer, awayPlayer, homeScore, awayScore, sets });
    
    // Set basic match info
    document.getElementById('quickMatchId').value = matchId;
    document.getElementById('homePlayerName').textContent = homePlayer;
    document.getElementById('awayPlayerName').textContent = awayPlayer;
    document.getElementById('homeScore').value = homeScore || '';
    document.getElementById('awayScore').value = awayScore || '';
    
    // Clear existing sets
    currentSets = [];
    document.getElementById('setsList').innerHTML = '';
    
    // Load existing sets
    if (sets && Array.isArray(sets) && sets.length > 0) {
        sets.forEach((set, index) => {
            const setNumber = index + 1;
            const setDiv = document.createElement('div');
            setDiv.className = 'flex gap-2 items-center';
            setDiv.innerHTML = `
                <span class="text-gray-400 text-sm w-12">Set ${setNumber}:</span>
                <input type="number" name="sets[${setNumber}][home]" min="0" max="21" value="${set.home || set.home_score || 0}"
                       class="flex-1 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white text-center text-sm">
                <span class="text-gray-400">-</span>
                <input type="number" name="sets[${setNumber}][away]" min="0" max="21" value="${set.away || set.away_score || 0}"
                       class="flex-1 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white text-center text-sm">
                <button type="button" onclick="removeSet(${index})" class="text-red-400 hover:text-red-300 text-sm px-2">×</button>
            `;
            document.getElementById('setsList').appendChild(setDiv);
            currentSets.push(setNumber);
        });
    }
    
    document.getElementById('setsContainer').classList.remove('hidden');
    document.getElementById('quickResultModal').classList.remove('hidden');
}

function closeQuickResultModal() {
    document.getElementById('quickResultModal').classList.add('hidden');
}

function addSet() {
    const setNumber = currentSets.length + 1;
    const setDiv = document.createElement('div');
    setDiv.className = 'flex gap-2 items-center';
    setDiv.innerHTML = `
        <span class="text-gray-400 text-sm w-12">Set ${setNumber}:</span>
        <input type="number" name="sets[${setNumber}][home]" min="0" max="21" 
               class="flex-1 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white text-center text-sm" placeholder="0">
        <span class="text-gray-400">-</span>
        <input type="number" name="sets[${setNumber}][away]" min="0" max="21" 
               class="flex-1 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white text-center text-sm" placeholder="0">
        <button type="button" onclick="removeSet(${setNumber - 1})" class="text-red-400 hover:text-red-300 text-sm px-2">×</button>
    `;
    document.getElementById('setsList').appendChild(setDiv);
    currentSets.push(setNumber);
    document.getElementById('setsContainer').classList.remove('hidden');
}

function removeSet(index) {
    currentSets.splice(index, 1);
    const setsList = document.getElementById('setsList');
    setsList.innerHTML = '';
    currentSets.forEach((setNum, i) => {
        const setDiv = document.createElement('div');
        setDiv.className = 'flex gap-2 items-center';
        setDiv.innerHTML = `
            <span class="text-gray-400 text-sm w-12">Set ${i + 1}:</span>
            <input type="number" name="sets[${i + 1}][home]" min="0" max="21" 
                   class="flex-1 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white text-center text-sm" placeholder="0">
            <span class="text-gray-400">-</span>
            <input type="number" name="sets[${i + 1}][away]" min="0" max="21" 
                   class="flex-1 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white text-center text-sm" placeholder="0">
            <button type="button" onclick="removeSet(${i})" class="text-red-400 hover:text-red-300 text-sm px-2">×</button>
        `;
        setsList.appendChild(setDiv);
    });
}

function saveQuickResult() {
    const matchId = document.getElementById('quickMatchId').value;
    const homeScore = document.getElementById('homeScore').value;
    const awayScore = document.getElementById('awayScore').value;

    console.log('Saving result:', { matchId, homeScore, awayScore });

    if (!matchId || matchId.trim() === '') {
        alert('Greška: ID meča nije pronađen');
        return;
    }

    if (!homeScore || !awayScore) {
        alert('Molimo unesite rezultate za oba igrača');
        return;
    }

    // Collect set data
    const sets = [];
    currentSets.forEach((setNum, index) => {
        const homeSetScore = document.querySelector(`input[name="sets[${setNum}][home]"]`)?.value;
        const awaySetScore = document.querySelector(`input[name="sets[${setNum}][away]"]`)?.value;
        if (homeSetScore && awaySetScore) {
            sets.push({
                home: parseInt(homeSetScore),
                away: parseInt(awaySetScore)
            });
        }
    });

    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/competitions/matches/' + matchId + '/quick-result';
    form.style.display = 'none';

    console.log('Form action:', form.action);

    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);

    // Add match data
    const matchIdField = document.createElement('input');
    matchIdField.type = 'hidden';
    matchIdField.name = 'match_id';
    matchIdField.value = matchId;
    form.appendChild(matchIdField);

    const homeScoreField = document.createElement('input');
    homeScoreField.type = 'hidden';
    homeScoreField.name = 'home_score';
    homeScoreField.value = homeScore;
    form.appendChild(homeScoreField);

    const awayScoreField = document.createElement('input');
    awayScoreField.type = 'hidden';
    awayScoreField.name = 'away_score';
    awayScoreField.value = awayScore;
    form.appendChild(awayScoreField);

    // Add sets data as individual fields (not JSON string)
    sets.forEach((set, index) => {
        const homeSetField = document.createElement('input');
        homeSetField.type = 'hidden';
        homeSetField.name = `sets[${index}][home]`;
        homeSetField.value = set.home;
        form.appendChild(homeSetField);

        const awaySetField = document.createElement('input');
        awaySetField.type = 'hidden';
        awaySetField.name = `sets[${index}][away]`;
        awaySetField.value = set.away;
        form.appendChild(awaySetField);
    });

    document.body.appendChild(form);
    form.submit();
}
</script>

