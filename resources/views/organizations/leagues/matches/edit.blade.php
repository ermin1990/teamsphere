<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Edit Match Results
                </h2>
                <p class="text-gray-400 mt-1">{{ $league->name }} • Round {{ $match->round }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ request()->routeIs('referee.*') ? route('referee.match.show', [$league, $match]) : route('leagues.matches.show', [$league, $match]) }}"
                   class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    ← Back to Match
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                <form method="POST" action="{{ request()->routeIs('referee.*') ? route('referee.match.update', [$league, $match]) : route('leagues.matches.update', [$league, $match]) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Match Info -->
                    <div class="text-center mb-8">
                        <div class="text-sm text-gray-400 mb-4">{{ $league->sport->name }} • Round {{ $match->round }}</div>

                        <div class="flex items-center justify-center space-x-8">
                            <!-- Home Participant -->
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <span class="text-lg font-bold text-white">
                                        @if($league->is_team_based)
                                            {{ substr($match->homeTeam->name ?? 'TBD', 0, 2) }}
                                        @else
                                            {{ substr($match->homePlayer->name ?? 'TBD', 0, 2) }}
                                        @endif
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-white">
                                    @if($league->is_team_based)
                                        {{ $match->homeTeam->name ?? 'TBD' }}
                                    @else
                                        {{ $match->homePlayer->name ?? 'TBD' }}
                                    @endif
                                </h3>
                            </div>

                            <div class="text-2xl font-bold text-gray-400">VS</div>

                            <!-- Away Participant -->
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <span class="text-lg font-bold text-white">
                                        @if($league->is_team_based)
                                            {{ substr($match->awayTeam->name ?? 'TBD', 0, 2) }}
                                        @else
                                            {{ substr($match->awayPlayer->name ?? 'TBD', 0, 2) }}
                                        @endif
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-white">
                                    @if($league->is_team_based)
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
                        <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Match Status</label>
                        <select name="status" id="status" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="scheduled" {{ $match->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="in_progress" {{ $match->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $match->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="forfeited" {{ $match->status === 'forfeited' ? 'selected' : '' }}>Forfeited</option>
                            <option value="cancelled" {{ $match->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Forfeited By (only shown when status is forfeited) -->
                    <div id="forfeited-section" class="hidden">
                        <label for="forfeited_by" class="block text-sm font-medium text-gray-300 mb-2">Forfeited By</label>
                        <select name="forfeited_by" id="forfeited_by" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select who forfeited</option>
                            <option value="home" {{ $match->forfeited_by === 'home' ? 'selected' : '' }}>
                                @if($league->is_team_based)
                                    {{ $match->homeTeam->name ?? 'Home Team' }}
                                @else
                                    {{ $match->homePlayer->name ?? 'Home Player' }}
                                @endif
                            </option>
                            <option value="away" {{ $match->forfeited_by === 'away' ? 'selected' : '' }}>
                                @if($league->is_team_based)
                                    {{ $match->awayTeam->name ?? 'Away Team' }}
                                @else
                                    {{ $match->awayPlayer->name ?? 'Away Player' }}
                                @endif
                            </option>
                        </select>
                    </div>

                    <!-- Final Scores -->
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label for="home_score" class="block text-sm font-medium text-gray-300 mb-2">
                                @if($league->is_team_based)
                                    {{ $match->homeTeam->name ?? 'Home Team' }}
                                @else
                                    {{ $match->homePlayer->name ?? 'Home Player' }}
                                @endif
                                Score
                            </label>
                <input type="number" name="home_score" id="home_score" value="{{ old('home_score', $match->home_score) }}"
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    min="0">
                        </div>

                        <div>
                            <label for="away_score" name="away_score" id="away_score" class="block text-sm font-medium text-gray-300 mb-2">
                                @if($league->is_team_based)
                                    {{ $match->awayTeam->name ?? 'Away Team' }}
                                @else
                                    {{ $match->awayPlayer->name ?? 'Away Player' }}
                                @endif
                                Score
                            </label>
                <input type="number" name="away_score" id="away_score" value="{{ old('away_score', $match->away_score) }}"
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    min="0">
                        </div>
                    </div>

                    <!-- Set Details (for table tennis) -->
                    <div id="sets-section" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-gray-300">Set Details</label>
                            <button type="button" id="add-set" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                                + Add Set
                            </button>
                        </div>

                        <div id="sets-container" class="space-y-3">
                            @if($match->sets && count($match->sets) > 0)
                                @foreach($match->sets as $index => $set)
                                    <div class="set-row flex items-center space-x-4 p-3 bg-gray-700/50 rounded-lg">
                                        <span class="text-sm font-medium text-gray-300 w-16">Set {{ $index + 1 }}:</span>
                                        <input type="number" name="sets[{{ $index }}][home_score]" value="{{ $set['home_score'] ?? $set['home'] ?? 0 }}"
                                               class="w-20 px-2 py-1 bg-gray-600 border border-gray-500 rounded text-white text-center" min="0">
                                        <span class="text-gray-400">-</span>
                                        <input type="number" name="sets[{{ $index }}][away_score]" value="{{ $set['away_score'] ?? $set['away'] ?? 0 }}"
                                               class="w-20 px-2 py-1 bg-gray-600 border border-gray-500 rounded text-white text-center" min="0">
                                        <button type="button" class="remove-set px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition-colors">
                                            Remove
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Moderator Assignment (only for organization owners) -->
                    @if(isset($isOwner) && $isOwner)
                    <div>
                        <label for="moderator_id" class="block text-sm font-medium text-gray-300 mb-2">Assign Moderator/Referee</label>
                        <select name="moderator_id" id="moderator_id" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">No moderator assigned</option>
                            @php
                                $referees = $organization->organizationUsers()
                                    ->where('role', 'referee')
                                    ->with('user')
                                    ->get()
                                    ->pluck('user');
                            @endphp
                            @foreach($referees as $referee)
                            <option value="{{ $referee->id }}" {{ $match->moderator_id == $referee->id ? 'selected' : '' }}>
                                {{ $referee->name }}
                            </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Assign a referee to oversee this match</p>
                    </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-700">
                        <a href="{{ route('leagues.matches.show', [$league, $match]) }}"
                           class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            Update Results
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let setCount = {{ count($match->sets ?? []) }};

        document.getElementById('add-set').addEventListener('click', function() {
            const container = document.getElementById('sets-container');
            const setRow = document.createElement('div');
            setRow.className = 'set-row flex items-center space-x-4 p-3 bg-gray-700/50 rounded-lg';
            setRow.innerHTML = `
                <span class="text-sm font-medium text-gray-300 w-16">Set ${setCount + 1}:</span>
                <input type="number" name="sets[${setCount}][home]" value="0"
                       class="w-20 px-2 py-1 bg-gray-600 border border-gray-500 rounded text-white text-center" min="0">
                <span class="text-gray-400">-</span>
                <input type="number" name="sets[${setCount}][away]" value="0"
                       class="w-20 px-2 py-1 bg-gray-600 border border-gray-500 rounded text-white text-center" min="0">
                <button type="button" class="remove-set px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition-colors">
                    Remove
                </button>
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
                row.querySelector('input[name*="[home]"]').name = `sets[${index}][home]`;
                row.querySelector('input[name*="[away]"]').name = `sets[${index}][away]`;
            });
        }

        // Handle status change
        document.getElementById('status').addEventListener('change', function() {
            const status = this.value;
            const forfeitedSection = document.getElementById('forfeited-section');
            const scoresSection = document.querySelector('.grid.grid-cols-2.gap-6');
            const setsSection = document.getElementById('sets-section');
            const homeScoreInput = document.getElementById('home_score');
            const awayScoreInput = document.getElementById('away_score');

            if (status === 'forfeited') {
                forfeitedSection.classList.remove('hidden');
                scoresSection.classList.add('hidden');
                setsSection.classList.add('hidden');
                // Make forfeited_by required when status is forfeited
                document.getElementById('forfeited_by').setAttribute('required', 'required');
                // Remove required from scores
                homeScoreInput.removeAttribute('required');
                awayScoreInput.removeAttribute('required');
            } else if (status === 'scheduled') {
                forfeitedSection.classList.add('hidden');
                scoresSection.classList.add('hidden');
                setsSection.classList.add('hidden');
                // Remove required attributes
                document.getElementById('forfeited_by').removeAttribute('required');
                homeScoreInput.removeAttribute('required');
                awayScoreInput.removeAttribute('required');
                // Reset scores to 0
                homeScoreInput.value = '0';
                awayScoreInput.value = '0';
            } else if (status === 'completed') {
                forfeitedSection.classList.add('hidden');
                scoresSection.classList.remove('hidden');
                setsSection.classList.remove('hidden');
                // Remove required from forfeited_by
                document.getElementById('forfeited_by').removeAttribute('required');
                // Make scores required for completed matches
                homeScoreInput.setAttribute('required', 'required');
                awayScoreInput.setAttribute('required', 'required');
                // Set default scores if empty
                if (!homeScoreInput.value || homeScoreInput.value === '') {
                    homeScoreInput.value = '0';
                }
                if (!awayScoreInput.value || awayScoreInput.value === '') {
                    awayScoreInput.value = '0';
                }
            } else {
                forfeitedSection.classList.add('hidden');
                scoresSection.classList.remove('hidden');
                setsSection.classList.remove('hidden');
                // Remove required attributes
                document.getElementById('forfeited_by').removeAttribute('required');
                homeScoreInput.removeAttribute('required');
                awayScoreInput.removeAttribute('required');
            }
        });

        // Initialize on page load
        document.getElementById('status').dispatchEvent(new Event('change'));
    </script>
</x-app-layout>