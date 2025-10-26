{{-- Quick Result Modal --}}
{{-- Quick Result Modal --}}
<div id="quickResultModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-2xl p-6 max-w-lg w-full border border-gray-700 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-white">⚡ {{ __('Quick Result Entry') }}</h3>
            <button onclick="closeQuickResultModal()" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="quickResultForm" method="POST">
            @csrf
            <div class="space-y-6">
                <!-- Match Info -->
                <div class="bg-gray-700/30 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-xs" id="homeInitials">--</span>
                            </div>
                            <span class="text-white font-medium" id="homePlayerName">Player 1</span>
                        </div>
                        <input type="number" name="home_score" id="homeScoreInput" min="0" max="5" required
                               class="w-20 text-center text-2xl font-bold bg-gray-600/50 border border-gray-500 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-xs" id="awayInitials">--</span>
                            </div>
                            <span class="text-white font-medium" id="awayPlayerName">Player 2</span>
                        </div>
                        <input type="number" name="away_score" id="awayScoreInput" min="0" max="5" required
                               class="w-20 text-center text-2xl font-bold bg-gray-600/50 border border-gray-500 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Set Scores (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-3">{{ __('Set Scores (Optional)') }}</label>
                    <div id="setScoresContainer" class="space-y-2">
                        <!-- Set score inputs will be dynamically added here -->
                    </div>
                    <button type="button" onclick="addSetScore()" class="mt-2 text-blue-400 hover:text-blue-300 text-sm">
                        + {{ __('Add Set Score') }}
                    </button>
                </div>

                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="closeQuickResultModal()"
                            class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        {{ __('Save Result') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Add Match Modal --}}
<div id="addMatchModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-2xl p-6 max-w-md w-full border border-gray-700 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-white">➕ Dodaj novi meč</h3>
            <button onclick="closeAddMatchModal()" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="addMatchForm" onsubmit="event.preventDefault(); addNewMatch();">
            @csrf
            <div class="space-y-4">
                <!-- Match Name -->
                <div>
                    <label for="matchName" class="block text-sm font-medium text-gray-300 mb-2">
                        Naziv meča
                    </label>
                    <input type="text" id="matchName" name="name" required
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="npr. Meč za 3. mjesto">
                </div>

                <!-- Home Player -->
                <div>
                    <label for="homePlayer" class="block text-sm font-medium text-gray-300 mb-2">
                        Prvi igrač
                    </label>
                    <select id="homePlayer" name="home_player_id" required
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Odaberite igrača...</option>
                    </select>
                </div>

                <!-- Away Player -->
                <div>
                    <label for="awayPlayer" class="block text-sm font-medium text-gray-300 mb-2">
                        Drugi igrač
                    </label>
                    <select id="awayPlayer" name="away_player_id" required
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Odaberite igrača...</option>
                    </select>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="button" 
                            onclick="closeAddMatchModal()"
                            class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Odustani
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        ➕ Dodaj meč
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

