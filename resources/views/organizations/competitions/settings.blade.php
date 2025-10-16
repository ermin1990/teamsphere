<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">{{ __('Competition Settings') }}</h1>
                        <p class="text-gray-300">{{ $competition->name }}</p>
                    </div>
                    <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        {{ __('Back') }}
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                <p class="text-green-400">{{ session('success') }}</p>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                <ul class="list-disc list-inside text-red-400">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Quick Presets -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <h3 class="text-xl font-semibold text-white mb-4">{{ __('Quick Presets') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button onclick="applyPreset('standard')" 
                            class="bg-blue-600/20 hover:bg-blue-600/30 border-2 border-blue-500 text-white p-4 rounded-lg transition-colors text-left">
                        <h4 class="font-semibold mb-2">🏓 Standard (11 pts)</h4>
                        <p class="text-sm text-gray-300">Best of 3, 11 points, deuce at 10</p>
                    </button>
                    <button onclick="applyPreset('extended')" 
                            class="bg-purple-600/20 hover:bg-purple-600/30 border-2 border-purple-500 text-white p-4 rounded-lg transition-colors text-left">
                        <h4 class="font-semibold mb-2">🎯 Extended (15 pts)</h4>
                        <p class="text-sm text-gray-300">Best of 3, 15 points, deuce at 14</p>
                    </button>
                    <button onclick="applyPreset('classic')" 
                            class="bg-green-600/20 hover:bg-green-600/30 border-2 border-green-500 text-white p-4 rounded-lg transition-colors text-left">
                        <h4 class="font-semibold mb-2">⚡ Classic (21 pts)</h4>
                        <p class="text-sm text-gray-300">Best of 3, 21 points, deuce at 20</p>
                    </button>
                </div>
            </div>

            <form action="{{ route('organizations.competitions.update-settings', [$organization, $competition]) }}" method="POST">
                @csrf

                <!-- Match Format -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">{{ __('Match Format') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Sets to Win -->
                        <div>
                            <label for="sets_to_win" class="block text-sm font-medium text-white mb-2">
                                {{ __('Sets to Win') }} <span class="text-red-400">*</span>
                            </label>
                            <select id="sets_to_win" name="sets_to_win" required
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1" {{ old('sets_to_win', $competition->sets_to_win ?? 2) == 1 ? 'selected' : '' }}>1 (Best of 1)</option>
                                <option value="2" {{ old('sets_to_win', $competition->sets_to_win ?? 2) == 2 ? 'selected' : '' }}>2 (Best of 3)</option>
                                <option value="3" {{ old('sets_to_win', $competition->sets_to_win ?? 2) == 3 ? 'selected' : '' }}>3 (Best of 5)</option>
                                <option value="4" {{ old('sets_to_win', $competition->sets_to_win ?? 2) == 4 ? 'selected' : '' }}>4 (Best of 7)</option>
                            </select>
                            <p class="text-gray-400 text-xs mt-1">{{ __('Number of sets a player needs to win the match') }}</p>
                        </div>

                        <!-- Points per Set -->
                        <div>
                            <label for="points_per_set" class="block text-sm font-medium text-white mb-2">
                                {{ __('Points per Set') }} <span class="text-red-400">*</span>
                            </label>
                            <select id="points_per_set" name="points_per_set" required
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="7" {{ old('points_per_set', $competition->points_per_set ?? 11) == 7 ? 'selected' : '' }}>7 points</option>
                                <option value="11" {{ old('points_per_set', $competition->points_per_set ?? 11) == 11 ? 'selected' : '' }}>11 points (Standard)</option>
                                <option value="15" {{ old('points_per_set', $competition->points_per_set ?? 11) == 15 ? 'selected' : '' }}>15 points</option>
                                <option value="21" {{ old('points_per_set', $competition->points_per_set ?? 11) == 21 ? 'selected' : '' }}>21 points (Classic)</option>
                            </select>
                            <p class="text-gray-400 text-xs mt-1">{{ __('Points needed to win a set') }}</p>
                        </div>

                        <!-- Deuce At -->
                        <div>
                            <label for="deuce_at" class="block text-sm font-medium text-white mb-2">
                                {{ __('Deuce At') }}
                            </label>
                            <input type="number" id="deuce_at" name="deuce_at" 
                                   value="{{ old('deuce_at', $competition->deuce_at ?? 10) }}"
                                   min="5" max="20"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">{{ __('Score at which deuce rule applies (e.g., 10 for 11-point game)') }}</p>
                        </div>

                        <!-- Must Win by Two -->
                        <div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="must_win_by_two" name="must_win_by_two" value="1"
                                       {{ old('must_win_by_two', $competition->must_win_by_two ?? true) ? 'checked' : '' }}
                                       class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-white font-medium">{{ __('Must Win by 2 Points') }}</span>
                                    <p class="text-gray-400 text-xs">{{ __('At deuce, must win by 2-point margin') }}</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Tiebreak Settings -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">{{ __('Tiebreak Settings') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Has Tiebreak -->
                        <div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="has_tiebreak" name="has_tiebreak" value="1"
                                       {{ old('has_tiebreak', $competition->has_tiebreak ?? false) ? 'checked' : '' }}
                                       onchange="toggleTiebreakPoints()"
                                       class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-white font-medium">{{ __('Use Tiebreak in Final Set') }}</span>
                                    <p class="text-gray-400 text-xs">{{ __('Play tiebreak instead of regular deuce in final set') }}</p>
                                </div>
                            </label>
                        </div>

                        <!-- Tiebreak Points -->
                        <div id="tiebreakPointsDiv">
                            <label for="tiebreak_points" class="block text-sm font-medium text-white mb-2">
                                {{ __('Tiebreak Points') }}
                            </label>
                            <input type="number" id="tiebreak_points" name="tiebreak_points" 
                                   value="{{ old('tiebreak_points', $competition->tiebreak_points ?? 7) }}"
                                   min="5" max="15"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">{{ __('Points needed to win tiebreak') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Group Stage Scoring -->
                @if($competition->type === 'tournament')
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">{{ __('Group Stage Scoring') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Points for Win -->
                        <div>
                            <label for="points_for_win" class="block text-sm font-medium text-white mb-2">
                                {{ __('Points for Win') }} <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_win" name="points_for_win" 
                                   value="{{ old('points_for_win', $competition->points_for_win ?? 2) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">{{ __('Points awarded for winning a match') }}</p>
                        </div>

                        <!-- Points for Draw -->
                        <div>
                            <label for="points_for_draw" class="block text-sm font-medium text-white mb-2">
                                {{ __('Points for Draw') }} <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_draw" name="points_for_draw" 
                                   value="{{ old('points_for_draw', $competition->points_for_draw ?? 1) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">{{ __('Points awarded for a draw') }}</p>
                        </div>

                        <!-- Points for Loss -->
                        <div>
                            <label for="points_for_loss" class="block text-sm font-medium text-white mb-2">
                                {{ __('Points for Loss') }} <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_loss" name="points_for_loss" 
                                   value="{{ old('points_for_loss', $competition->points_for_loss ?? 0) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">{{ __('Points awarded for losing a match') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-blue-400 text-sm">
                                    {{ __('These points are used to rank players in group stage standings. Common setups:') }}
                                </p>
                                <ul class="text-blue-300 text-xs mt-2 space-y-1">
                                    <li>• {{ __('Standard: 2 for win, 1 for draw, 0 for loss') }}</li>
                                    <li>• {{ __('Win-only: 3 for win, 0 for draw, 0 for loss') }}</li>
                                    <li>• {{ __('All-points: 2 for win, 1 for draw, 0 for loss') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Save Button -->
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors font-semibold">
                        {{ __('Save Settings') }}
                    </button>
                    <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors text-center">
                        {{ __('Cancel') }}
                    </a>
                </div>

            </form>

        </div>
    </div>

    <script>
        // Presets
        const presets = {
            standard: {
                sets_to_win: 2,
                points_per_set: 11,
                deuce_at: 10,
                must_win_by_two: true,
                points_for_win: 2,
                points_for_draw: 1,
                points_for_loss: 0,
                has_tiebreak: false,
                tiebreak_points: 7
            },
            extended: {
                sets_to_win: 2,
                points_per_set: 15,
                deuce_at: 14,
                must_win_by_two: true,
                points_for_win: 2,
                points_for_draw: 1,
                points_for_loss: 0,
                has_tiebreak: false,
                tiebreak_points: 7
            },
            classic: {
                sets_to_win: 2,
                points_per_set: 21,
                deuce_at: 20,
                must_win_by_two: true,
                points_for_win: 3,
                points_for_draw: 0,
                points_for_loss: 0,
                has_tiebreak: false,
                tiebreak_points: 7
            }
        };

        function applyPreset(presetName) {
            const preset = presets[presetName];
            
            document.getElementById('sets_to_win').value = preset.sets_to_win;
            document.getElementById('points_per_set').value = preset.points_per_set;
            document.getElementById('deuce_at').value = preset.deuce_at;
            document.getElementById('must_win_by_two').checked = preset.must_win_by_two;
            document.getElementById('has_tiebreak').checked = preset.has_tiebreak;
            document.getElementById('tiebreak_points').value = preset.tiebreak_points;
            
            if (document.getElementById('points_for_win')) {
                document.getElementById('points_for_win').value = preset.points_for_win;
                document.getElementById('points_for_draw').value = preset.points_for_draw;
                document.getElementById('points_for_loss').value = preset.points_for_loss;
            }

            toggleTiebreakPoints();
            
            // Show notification
            showNotification(`Applied ${presetName.charAt(0).toUpperCase() + presetName.slice(1)} preset!`, 'success');
        }

        function toggleTiebreakPoints() {
            const hasTiebreak = document.getElementById('has_tiebreak').checked;
            const tiebreakDiv = document.getElementById('tiebreakPointsDiv');
            
            if (hasTiebreak) {
                tiebreakDiv.style.display = 'block';
            } else {
                tiebreakDiv.style.display = 'none';
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } text-white`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleTiebreakPoints();
            
            // Auto-update deuce_at when points_per_set changes
            document.getElementById('points_per_set').addEventListener('change', function() {
                const points = parseInt(this.value);
                document.getElementById('deuce_at').value = points - 1;
            });
        });
    </script>
</x-app-layout>
