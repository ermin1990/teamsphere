<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Postavke Takmičenja</h1>
                        <p class="text-gray-300">{{ $competition->name }}</p>
                    </div>
                    <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Nazad
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
                        <!-- Quick Presets -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <h3 class="text-xl font-semibold text-white mb-4">Brzi Predlošci</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button onclick="applyPreset('standard')" 
                            {{ $competition->status !== 'draft' ? 'disabled' : '' }}
                            class="{{ $competition->status !== 'draft' ? 'bg-gray-600/20 border-gray-500 cursor-not-allowed' : 'bg-blue-600/20 hover:bg-blue-600/30 border-blue-500' }} border-2 text-white p-4 rounded-lg transition-colors text-left">
                        <h4 class="font-semibold mb-2">🏓 Standard (11 poena)</h4>
                        <p class="text-sm text-gray-300">Najbolji od 3, 11 poena, završetak pri 10</p>
                    </button>
                    <button onclick="applyPreset('extended')" 
                            {{ $competition->status !== 'draft' ? 'disabled' : '' }}
                            class="{{ $competition->status !== 'draft' ? 'bg-gray-600/20 border-gray-500 cursor-not-allowed' : 'bg-purple-600/20 hover:bg-purple-600/30 border-purple-500' }} border-2 text-white p-4 rounded-lg transition-colors text-left">
                        <h4 class="font-semibold mb-2">🎯 Produženo (15 poena)</h4>
                        <p class="text-sm text-gray-300">Najbolji od 3, 15 poena, završetak pri 14</p>
                    </button>
                    <button onclick="applyPreset('classic')" 
                            {{ $competition->status !== 'draft' ? 'disabled' : '' }}
                            class="{{ $competition->status !== 'draft' ? 'bg-gray-600/20 border-gray-500 cursor-not-allowed' : 'bg-green-600/20 hover:bg-green-600/30 border-green-500' }} border-2 text-white p-4 rounded-lg transition-colors text-left">
                        <h4 class="font-semibold mb-2">⚡ Classic (21 pts)</h4>
                        <p class="text-sm text-gray-300">Best of 3, 21 points, deuce at 20</p>
                    </button>
                </div>
            </div>

            @if($competition->status !== 'draft')
            <div class="mb-6 bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <p class="text-yellow-400">Postavke se mogu mijenjati samo kada je takmičenje u statusu "Draft". Trenutni status: <strong>{{ ucfirst($competition->status) }}</strong></p>
                </div>
            </div>
            @endif

            <form action="{{ route('organizations.competitions.update-settings', [$organization, $competition]) }}" method="POST" {{ $competition->status !== 'draft' ? 'onsubmit="return false;"' : '' }}>
                @csrf

                <fieldset {{ $competition->status !== 'draft' ? 'disabled' : '' }}>

                <!-- Match Format -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Format Meča</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Sets to Win -->
                        <div>
                            <label for="sets_to_win" class="block text-sm font-medium text-white mb-2">
                                Setova za Pobjedu <span class="text-red-400">*</span>
                            </label>
                            <select id="sets_to_win" name="sets_to_win" required
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1" {{ old('sets_to_win', $competition->sets_to_win ?? 2) == 1 ? 'selected' : '' }}>1 (Najbolji od 1)</option>
                                <option value="2" {{ old('sets_to_win', $competition->sets_to_win ?? 2) == 2 ? 'selected' : '' }}>2 (Najbolji od 3)</option>
                                <option value="3" {{ old('sets_to_win', $competition->sets_to_win ?? 2) == 3 ? 'selected' : '' }}>3 (Najbolji od 5)</option>
                                <option value="4" {{ old('sets_to_win', $competition->sets_to_win ?? 2) == 4 ? 'selected' : '' }}>4 (Najbolji od 7)</option>
                            </select>
                            <p class="text-gray-400 text-xs mt-1">Broj setova koji igrač treba da osvoji za pobjedu u meču</p>
                        </div>

                        <!-- Points per Set -->
                        <div>
                            <label for="points_per_set" class="block text-sm font-medium text-white mb-2">
                                Poena po Setu <span class="text-red-400">*</span>
                            </label>
                            <select id="points_per_set" name="points_per_set" required
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="7" {{ old('points_per_set', $competition->points_per_set ?? 11) == 7 ? 'selected' : '' }}>7 poena</option>
                                <option value="11" {{ old('points_per_set', $competition->points_per_set ?? 11) == 11 ? 'selected' : '' }}>11 poena (Standard)</option>
                                <option value="15" {{ old('points_per_set', $competition->points_per_set ?? 11) == 15 ? 'selected' : '' }}>15 poena</option>
                                <option value="21" {{ old('points_per_set', $competition->points_per_set ?? 11) == 21 ? 'selected' : '' }}>21 poen (Klasično)</option>
                            </select>
                            <p class="text-gray-400 text-xs mt-1">Poeni potrebni za pobjedu u setu</p>
                        </div>

                        <!-- Deuce At -->
                        <div>
                            <label for="deuce_at" class="block text-sm font-medium text-white mb-2">
                                Deuce na
                            </label>
                            <input type="number" id="deuce_at" name="deuce_at" 
                                   value="{{ old('deuce_at', $competition->deuce_at ?? 10) }}"
                                   min="5" max="20"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Poeni na kojima počinje deuce</p>
                        </div>

                        <!-- Must Win by Two -->
                        <div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="must_win_by_two" name="must_win_by_two" value="1"
                                       {{ old('must_win_by_two', $competition->must_win_by_two ?? true) ? 'checked' : '' }}
                                       class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-white font-medium">Mora pobijediti sa dva poena</span>
                                    <p class="text-gray-400 text-xs">Igrač mora pobijediti sa dva poena razlike</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Tiebreak Settings -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Postavke Tiebreak-a</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Has Tiebreak -->
                        <div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="has_tiebreak" name="has_tiebreak" value="1"
                                       {{ old('has_tiebreak', $competition->has_tiebreak ?? false) ? 'checked' : '' }}
                                       onchange="toggleTiebreakPoints()"
                                       class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-white font-medium">Koristi tiebreak u finalnom setu</span>
                                    <p class="text-gray-400 text-xs">Omogući tiebreak u finalnom setu kada je rezultat jednak</p>
                                </div>
                            </label>
                        </div>

                        <!-- Tiebreak Points -->
                        <div id="tiebreakPointsDiv">
                            <label for="tiebreak_points" class="block text-sm font-medium text-white mb-2">
                                Tiebreak Poeni
                            </label>
                            <input type="number" id="tiebreak_points" name="tiebreak_points" 
                                   value="{{ old('tiebreak_points', $competition->tiebreak_points ?? 7) }}"
                                   min="5" max="15"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Poeni potrebni za pobjedu u tiebreak-u</p>
                        </div>
                    </div>
                </div>

                <!-- Group Stage Scoring -->
                @if($competition->type === 'tournament')
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Bodovanje Grupne Faze</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Points for Win -->
                        <div>
                            <label for="points_for_win" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Pobjedu <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_win" name="points_for_win" 
                                   value="{{ old('points_for_win', $competition->points_for_win ?? 2) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za pobjedu u meču</p>
                        </div>

                        <!-- Points for Draw -->
                        <div>
                            <label for="points_for_draw" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Neriješeno <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_draw" name="points_for_draw" 
                                   value="{{ old('points_for_draw', $competition->points_for_draw ?? 1) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za neriješeni meč</p>
                        </div>

                        <!-- Points for Loss -->
                        <div>
                            <label for="points_for_loss" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Poraz <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_loss" name="points_for_loss" 
                                   value="{{ old('points_for_loss', $competition->points_for_loss ?? 0) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za izgubljeni meč</p>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-blue-400 text-sm">
                                    Ovi bodovi se koriste za rangiranje igrača u tabeli grupne faze. Uobičajene postavke:
                                </p>
                                <ul class="text-blue-300 text-xs mt-2 space-y-1">
                                    <li>• Standard: 2 za pobjedu, 1 za neriješeno, 0 za poraz</li>
                                    <li>• Samo pobjede: 3 za pobjedu, 0 za neriješeno, 0 za poraz</li>
                                    <li>• Svi bodovi: 2 za pobjedu, 1 za neriješeno, 0 za poraz</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- League Scoring -->
                @if($competition->type === 'league')
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Bodovanje Lige</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Points for Win -->
                        <div>
                            <label for="points_for_win" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Pobjedu <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_win" name="points_for_win" 
                                   value="{{ old('points_for_win', $competition->points_for_win ?? 2) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za pobjedu u meču</p>
                        </div>

                        <!-- Points for Draw -->
                        <div>
                            <label for="points_for_draw" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Neriješeno <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_draw" name="points_for_draw" 
                                   value="{{ old('points_for_draw', $competition->points_for_draw ?? 1) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za neriješeni meč</p>
                        </div>

                        <!-- Points for Loss -->
                        <div>
                            <label for="points_for_loss" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Poraz <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_loss" name="points_for_loss" 
                                   value="{{ old('points_for_loss', $competition->points_for_loss ?? 0) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za izgubljeni meč</p>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-green-500/10 border border-green-500/20 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-green-400 text-sm">
                                    Ovi bodovi određuju plasman u ligi. Uobičajene postavke:
                                </p>
                                <ul class="text-green-300 text-xs mt-2 space-y-1">
                                    <li>• Standard: 2 za pobjedu, 1 za neriješeno, 0 za poraz</li>
                                    <li>• Samo pobjede: 3 za pobjedu, 0 za neriješeno, 0 za poraz</li>
                                    <li>• Svi bodovi: 2 za pobjedu, 1 za neriješeno, 0 za poraz</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                </div>

                <!-- Save Button -->
                <div class="flex space-x-4">
                    <button type="submit" 
                            {{ $competition->status !== 'draft' ? 'disabled' : '' }}
                            class="flex-1 {{ $competition->status !== 'draft' ? 'bg-gray-600 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700' }} text-white px-6 py-3 rounded-lg transition-colors font-semibold">
                        Sačuvaj Postavke
                    </button>
                    <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors text-center">
                        Otkaži
                    </a>
                </div>

                </fieldset>
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
            let container = document.getElementById('toast-container-bottom-center');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container-bottom-center';
                container.className = 'fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 flex flex-col items-center gap-3 pointer-events-none';
                document.body.appendChild(container);
            }

            const notification = document.createElement('div');
            notification.className = `pointer-events-auto max-w-xl w-full px-6 py-3 rounded-lg shadow-lg transition-opacity duration-300 ease-out ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } text-white opacity-0`;
            notification.textContent = message;

            container.appendChild(notification);
            requestAnimationFrame(() => { notification.classList.remove('opacity-0'); notification.classList.add('opacity-100'); });

            setTimeout(() => {
                notification.classList.remove('opacity-100');
                notification.classList.add('opacity-0');
                setTimeout(() => notification.remove(), 300);
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
