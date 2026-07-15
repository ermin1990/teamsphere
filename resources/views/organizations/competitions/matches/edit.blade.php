<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl sm:text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Edituj rezultate utakmice
                </h2>
                <p class="text-gray-400 mt-1 text-sm sm:text-base">{{ $organization->name }} • {{ $competition->name }}</p>
            </div>
            <div class="flex items-center">
                <a href="{{ request()->routeIs('referee.*') ? route('referee.competition.match.show', [$competition, $match]) : route('organizations.competitions.show', [$organization, $competition]) }}"
                   class="px-3 py-2 sm:px-4 sm:py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors text-sm sm:text-base w-full sm:w-auto text-center">
                    ← Nazad na takmičenje
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-4 sm:p-6 lg:p-8 border border-gray-700/50 shadow-xl">
                <form method="POST" action="{{ request()->routeIs('referee.*') ? route('referee.competition.match.update', [$competition, $match]) : route('organizations.competitions.matches.update', [$organization, $competition, $match]) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Match Info -->
                    <div class="text-center mb-6 sm:mb-8">
                        <div class="text-sm text-gray-400 mb-4">Stoni Tenis • Match #{{ $match->id }}</div>

                        <div class="flex items-center justify-center gap-2 sm:gap-8">
                            <!-- Home Participant -->
                            <div class="text-center">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <span class="text-sm sm:text-lg font-bold text-white">
                                        @if($competition->is_team_based)
                                            {{ substr($match->homeTeam->name ?? 'TBD', 0, 2) }}
                                        @else
                                            {{ substr($match->homePlayer->name ?? 'TBD', 0, 2) }}
                                        @endif
                                    </span>
                                </div>
                                <h3 class="text-sm sm:text-lg font-bold text-white">
                                    @if($competition->is_team_based)
                                        {{ $match->homeTeam->name ?? 'TBD' }}
                                    @else
                                        {{ $match->homePlayer->name ?? 'TBD' }}
                                    @endif
                                </h3>
                            </div>

                            <div class="text-lg sm:text-2xl font-bold text-gray-400 mx-2 sm:mx-4">VS</div>

                            <!-- Away Participant -->
                            <div class="text-center">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <span class="text-sm sm:text-lg font-bold text-white">
                                        @if($competition->is_team_based)
                                            {{ substr($match->awayTeam->name ?? 'TBD', 0, 2) }}
                                        @else
                                            {{ substr($match->awayPlayer->name ?? 'TBD', 0, 2) }}
                                        @endif
                                    </span>
                                </div>
                                <h3 class="text-sm sm:text-lg font-bold text-white">
                                    @if($competition->is_team_based)
                                        {{ $match->awayTeam->name ?? 'TBD' }}
                                    @else
                                        {{ $match->awayPlayer->name ?? 'TBD' }}
                                    @endif
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Match Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status utakmice</label>
                        <select name="status" id="status" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="scheduled" {{ $match->status === 'scheduled' ? 'selected' : '' }}>Zakazano</option>
                            <option value="in_progress" {{ $match->status === 'in_progress' ? 'selected' : '' }}>U toku</option>
                            <option value="completed" {{ $match->status === 'completed' ? 'selected' : '' }}>Završeno</option>
                            <option value="forfeited" {{ $match->status === 'forfeited' ? 'selected' : '' }}>Predato</option>
                            <option value="cancelled" {{ $match->status === 'cancelled' ? 'selected' : '' }}>Otkazano</option>
                        </select>
                    </div>

                    @if($competition->is_team_based)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Home Captain -->
                        <div>
                            <label for="home_captain_id" class="block text-sm font-medium text-gray-300 mb-2">Kapiten ({{ $match->homeTeam->name ?? 'Domaći' }})</label>
                            <select name="home_captain_id" id="home_captain_id" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Odaberite kapitena</option>
                                @foreach($homePlayers as $player)
                                    <option value="{{ $player->id }}" {{ $match->home_captain_id == $player->id ? 'selected' : '' }}>
                                        {{ $player->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Away Captain -->
                        <div>
                            <label for="away_captain_id" class="block text-sm font-medium text-gray-300 mb-2">Kapiten ({{ $match->awayTeam->name ?? 'Gosti' }})</label>
                            <select name="away_captain_id" id="away_captain_id" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Odaberite kapitena</option>
                                @foreach($awayPlayers as $player)
                                    <option value="{{ $player->id }}" {{ $match->away_captain_id == $player->id ? 'selected' : '' }}>
                                        {{ $player->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <!-- Referee -->
                    <div>
                        <label for="referee_name" class="block text-sm font-medium text-gray-300 mb-2">Sudija</label>
                        <input type="text" name="referee_name" id="referee_name" list="referees-list" value="{{ old('referee_name', $match->referee_name) }}" 
                               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Upišite ime sudije...">
                        <datalist id="referees-list">
                            @foreach($referees as $refName)
                                <option value="{{ $refName }}">
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Forfeited By (only shown when status is forfeited) -->
                    <div id="forfeited-section" class="hidden">
                        <label for="forfeited_by" class="block text-sm font-medium text-gray-300 mb-2">Predato od strane</label>
                        <select name="forfeited_by" id="forfeited_by" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Odaberite ko je predao</option>
                            <option value="home" {{ $match->forfeited_by === 'home' ? 'selected' : '' }}>
                                @if($competition->is_team_based)
                                    {{ $match->homeTeam->name ?? 'Domaći tim' }}
                                @else
                                    {{ $match->homePlayer->name ?? 'Domaći igrač' }}
                                @endif
                            </option>
                            <option value="away" {{ $match->forfeited_by === 'away' ? 'selected' : '' }}>
                                @if($competition->is_team_based)
                                    {{ $match->awayTeam->name ?? 'Gostujući tim' }}
                                @else
                                    {{ $match->awayPlayer->name ?? 'Gostujući igrač' }}
                                @endif
                            </option>
                        </select>
                    </div>

                    <!-- Final Scores -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="home_score" class="block text-sm font-medium text-gray-300 mb-2">
                                @if($competition->is_team_based)
                                    {{ $match->homeTeam->name ?? 'Domaći tim' }}
                                @else
                                    {{ $match->homePlayer->name ?? 'Domaći igrač' }}
                                @endif
                                Rezultat
                            </label>
                <input type="number" name="home_score" id="home_score" value="{{ old('home_score', $match->home_score) }}"
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    min="0">
                        </div>

                        <div>
                            <label for="away_score" name="away_score" id="away_score" class="block text-sm font-medium text-gray-300 mb-2">
                                @if($competition->is_team_based)
                                    {{ $match->awayTeam->name ?? 'Gostujući tim' }}
                                @else
                                    {{ $match->awayPlayer->name ?? 'Gostujući igrač' }}
                                @endif
                                Rezultat
                            </label>
                <input type="number" name="away_score" id="away_score" value="{{ old('away_score', $match->away_score) }}"
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    min="0">
                        </div>
                    </div>

                    <!-- Date/Time & Venue -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="played_at" class="block text-sm font-medium text-gray-300 mb-2">Datum i vrijeme</label>
                            <input type="datetime-local" name="played_at" id="played_at"
                                   value="{{ old('played_at', optional($match->played_at)->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="venue_id" class="block text-sm font-medium text-gray-300 mb-2">Teren</label>
                            <select name="venue_id" id="venue_id" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">— nije odabran —</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ (string) old('venue_id', $match->venue_id) === (string) $venue->id ? 'selected' : '' }}>
                                        {{ $venue->name }}{{ $venue->city ? ' ('.$venue->city->name.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Set Details (for table tennis) -->
                    <div id="sets-section" class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <label class="block text-sm font-medium text-gray-300">Detalji setova</label>
                            <button type="button" id="add-set" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors w-full sm:w-auto">
                                + Dodaj set
                            </button>
                        </div>

                        <div id="sets-container" class="space-y-3">
                            @php
                                $setsArray = is_string($match->sets) ? json_decode($match->sets, true) : ($match->sets ?? []);
                            @endphp
                            @if($setsArray && count($setsArray) > 0)
                                @foreach($setsArray as $index => $set)
                                    <div class="set-row flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 p-3 bg-gray-700/50 rounded-lg">
                                        <span class="text-sm font-medium text-gray-300 w-full sm:w-16">Set {{ $index + 1 }}:</span>
                                        <div class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto">
                                            <input type="number" name="sets[{{ $index }}][home_score]" value="{{ $set['home_score'] ?? $set['home'] ?? 0 }}"
                                                   class="w-16 sm:w-20 px-2 py-1 bg-gray-600 border border-gray-500 rounded text-white text-center" min="0">
                                            <span class="text-gray-400">-</span>
                                            <input type="number" name="sets[{{ $index }}][away_score]" value="{{ $set['away_score'] ?? $set['away'] ?? 0 }}"
                                                   class="w-16 sm:w-20 px-2 py-1 bg-gray-600 border border-gray-500 rounded text-white text-center" min="0">
                                            <button type="button" class="remove-set px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition-colors">
                                                Ukloni
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Referee Assignment -->
                    <div>
                        <label for="referee_user_id" class="block text-sm font-medium text-gray-300 mb-2">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Sudija Meča</span>
                            </span>
                        </label>
                        @if(isset($isReferee) && $isReferee)
                            <!-- Referee can only view, not edit -->
                            <div class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                                @php
                                    $referee = $match->referee;
                                @endphp
                                {{ $referee ? $referee->name : 'Bez sudije' }}
                            </div>
                            <input type="hidden" name="referee_user_id" value="{{ $match->referee_user_id }}">
                            <p class="text-xs text-gray-400 mt-1">Sudija može samo vidjeti dodijeljenog sudiju</p>
                        @else
                            <select name="referee_user_id" id="referee_user_id" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Bez sudije</option>
                                @php
                                    // Get all organization members who can referee
                                    $organizationMembers = \App\Models\User::whereHas('organizationUsers', function($q) use ($organization) {
                                        $q->where('organization_id', $organization->id);
                                    })->get();
                                @endphp
                                @foreach($organizationMembers as $member)
                                    <option value="{{ $member->id }}" {{ $match->referee_user_id == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Sudija će imati puna prava za ažuriranje rezultata ovog meča</p>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row sm:justify-end gap-3 sm:gap-4 pt-6 border-t border-gray-700">
                        <a href="{{ request()->routeIs('referee.*') ? route('referee.competition.match.show', [$competition, $match]) : route('organizations.competitions.show', [$organization, $competition]) }}"
                           class="px-4 py-2 sm:px-6 sm:py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors text-center">
                            Odustani
                        </a>
                        <button type="submit" class="px-4 py-2 sm:px-6 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            Ažuriraj rezultate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        @php
            $setsArray = is_string($match->sets) ? json_decode($match->sets, true) : ($match->sets ?? []);
        @endphp
        let setCount = {{ count($setsArray) }};

        // Save scroll position before form submission
        document.querySelector('form').addEventListener('submit', function() {
            const scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            sessionStorage.setItem('scrollPosition', scrollPosition);
        });

        document.getElementById('add-set').addEventListener('click', function() {
            const container = document.getElementById('sets-container');
            const setRow = document.createElement('div');
            setRow.className = 'set-row flex items-center space-x-4 p-3 bg-gray-700/50 rounded-lg';
            setRow.innerHTML = `
                <div class="set-row flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 p-3 bg-gray-700/50 rounded-lg">
                    <span class="text-sm font-medium text-gray-300 w-full sm:w-16">Set ${setCount + 1}:</span>
                    <div class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto">
                        <input type="number" name="sets[${setCount}][home_score]" value="0"
                               class="w-16 sm:w-20 px-2 py-1 bg-gray-600 border border-gray-500 rounded text-white text-center" min="0">
                        <span class="text-gray-400">-</span>
                        <input type="number" name="sets[${setCount}][away_score]" value="0"
                               class="w-16 sm:w-20 px-2 py-1 bg-gray-600 border border-gray-500 rounded text-white text-center" min="0">
                        <button type="button" class="remove-set px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition-colors">
                            Ukloni
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(setRow);
            setCount++;

            // Add event listener to the new remove button
            setRow.querySelector('.remove-set').addEventListener('click', function() {
                setRow.remove();
                setCount--;
                updateSetLabels();
            });
        });

        // Add event listeners to existing remove buttons
        document.querySelectorAll('.remove-set').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.set-row').remove();
                setCount--;
                updateSetLabels();
            });
        });

        function updateSetLabels() {
            document.querySelectorAll('.set-row').forEach((row, index) => {
                row.querySelector('span').textContent = `Set ${index + 1}:`;
                row.querySelector('input[name*="[home_score]"]').name = `sets[${index}][home_score]`;
                row.querySelector('input[name*="[away_score]"]').name = `sets[${index}][away_score]`;
            });
        }

        // Show/hide forfeited section based on status
        document.getElementById('status').addEventListener('change', function() {
            const forfeitedSection = document.getElementById('forfeited-section');
            if (this.value === 'forfeited') {
                forfeitedSection.classList.remove('hidden');
            } else {
                forfeitedSection.classList.add('hidden');
            }
        });

        // Initialize forfeited section visibility
        if (document.getElementById('status').value === 'forfeited') {
            document.getElementById('forfeited-section').classList.remove('hidden');
        }
    </script>
</x-app-layout>