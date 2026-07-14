<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $organization->name }}
                </h2>
                <p class="text-gray-400 mt-1">Kreiraj Novo Takmičenje</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                <form action="{{ route('organizations.competitions.store', $organization) }}" method="POST" id="competitionForm" class="space-y-8">
                    @csrf

                    <!-- Competition Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-white mb-2">
                            Naziv Takmičenja <span class="text-red-400">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Unesite naziv takmičenja">
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-white mb-2">
                            Opis
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="3"
                                  class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                  placeholder="Opcionalni opis takmičenja">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sport (naslijeđen od organizacije - ne bira se po takmičenju) -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Sport</label>
                        <div class="flex items-center gap-2 w-full px-4 py-3 bg-gray-700/30 border border-gray-600/50 rounded-lg text-gray-300">
                            <span class="text-xl">{{ $organization->sport->icon }}</span>
                            <span>{{ $organization->sport->name }}</span>
                            <span class="text-gray-500 text-xs ml-auto">Sport organizacije "{{ $organization->name }}"</span>
                        </div>
                    </div>

                    <!-- Category Selection -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-white mb-2">
                            Kategorija
                        </label>
                        <select id="category_id"
                                name="category_id"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">Bez kategorije</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">Opcionalno: Odaberite kategoriju za ovaj turnir (npr. Veterani, U18, Amateri...)</p>
                    </div>

                    <!-- Competition Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-white mb-2">
                            Format Takmičenja <span class="text-red-400">*</span>
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" id="tournament" name="type" value="tournament" {{ old('type', 'tournament') === 'tournament' ? 'checked' : '' }}
                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2" onchange="toggleCompetitionType()">
                                <label for="tournament" class="ml-3 text-sm font-medium text-white">
                                    Turnir - Grupna faza + eliminaciona faza
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="league" name="type" value="league" {{ old('type') === 'league' ? 'checked' : '' }}
                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2" onchange="toggleCompetitionType()">
                                <label for="league" class="ml-3 text-sm font-medium text-white">
                                    Liga - Ekipno takmičenje (Corbillon sistem)
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="knockout" name="type" value="knockout" {{ old('type') === 'knockout' ? 'checked' : '' }}
                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2" onchange="toggleCompetitionType()" disabled>
                                <label for="knockout" class="ml-3 text-sm font-medium text-gray-500">
                                    Samo Eliminacija - Direktno eliminaciono takmičenje <span class="text-xs text-gray-400">(uskoro)</span>
                                </label>
                            </div>
                        </div>
                        @error('type')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Team vs Individual -->
                    <div id="playerFormatSection">
                        <label class="block text-sm font-medium text-white mb-2">
                            Format Igrača <span class="text-red-400">*</span>
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" id="individual_based" name="is_team_based" value="0" {{ old('is_team_based', '0') === '0' ? 'checked' : '' }}
                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2">
                                <label for="individual_based" class="ml-3 text-sm font-medium text-white">
                                    Individualno takmičenje
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="team_based" name="is_team_based" value="1" {{ old('is_team_based') === '1' ? 'checked' : '' }}
                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2">
                                <label for="team_based" class="ml-3 text-sm font-medium text-white">
                                    Ekipno takmičenje
                                </label>
                            </div>
                        </div>
                        @error('is_team_based')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- League Settings -->
                    <div id="leagueSettings" style="display: none;" class="space-y-6 p-6 bg-gray-800/30 rounded-2xl border border-gray-700/50">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Postavke Lige
                        </h3>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="is_double_round" name="is_double_round" value="1" {{ old('is_double_round') ? 'checked' : '' }}
                                   class="w-5 h-5 border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2 rounded">
                            <label for="is_double_round" class="ml-3 text-sm font-medium text-white">
                                Dvokružni sistem (Domaćin i Gost)
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 ml-8">Ako je označeno, svaka ekipa će igrati protiv svake ekipe dva puta (jednom kod kuće, jednom u gostima).</p>

                        <div class="flex items-center pt-2 border-t border-gray-700/50">
                            <input type="checkbox" id="is_recreational" name="is_recreational" value="1" {{ old('is_recreational') ? 'checked' : '' }}
                                   class="w-5 h-5 border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2 rounded">
                            <label for="is_recreational" class="ml-3 text-sm font-medium text-white">
                                Rekreativna liga
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 ml-8">Za društvo koje igra radi zabave: možete ručno dodati dodatne mečeve između istih igrača (revanš) i ranije završiti meč/set sa trenutnim rezultatom, bez čekanja da neko zvanično osvoji.</p>
                    </div>

                    <!-- Tournament Settings -->
                    <div id="tournamentSettings" class="space-y-6" style="display: none;">
                        <h3 class="text-lg font-semibold text-white border-b border-gray-600 pb-2">Postavke Turnira</h3>

                        <!-- Players Advancing Per Group -->
                        <div>
                            <label for="players_advancing_per_group" class="block text-sm font-medium text-white mb-2">
                                Igrača Koji Napreduju Po Grupi <span class="text-red-400">*</span>
                            </label>
                            <input type="number"
                                   id="players_advancing_per_group"
                                   name="players_advancing_per_group"
                                   value="{{ old('players_advancing_per_group', 2) }}"
                                   min="1"
                                   max="4"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-400">Koliko igrača napreduje iz svake grupe u eliminacionu fazu (1-4)</p>
                            @error('players_advancing_per_group')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Advancement Method -->
                        <div>
                            <label for="advancement_method" class="block text-sm font-medium text-white mb-2">
                                Metoda Napredovanja <span class="text-red-400">*</span>
                            </label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" id="automatic" name="advancement_method" value="automatic" {{ old('advancement_method', 'automatic') === 'automatic' ? 'checked' : '' }}
                                           class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2">
                                    <label for="automatic" class="ml-3 text-sm font-medium text-white">
                                        Automatski - Najbolji igrači napreduju na osnovu bodova
                                    </label>
                                </div>
                            </div>
                            @error('advancement_method')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Manual Knockout Selection -->
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">
                                Odabir Igrača za Eliminacionu Fazu
                            </label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="manual_knockout_selection" name="manual_knockout_selection" value="1" {{ old('manual_knockout_selection') ? 'checked' : '' }}
                                           class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2 rounded">
                                    <label for="manual_knockout_selection" class="ml-3 text-sm font-medium text-white">
                                        Omogući ručni odabir igrača za eliminacionu fazu
                                    </label>
                                </div>
                                <p class="text-xs text-gray-400 ml-6">Ako je omogućeno, organizator će ručno birati igrače za svaki meč u eliminacionoj fazi umjesto automatskog generisanja</p>
                            </div>
                            @error('manual_knockout_selection')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Competition Duration -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-white mb-2">
                                    Datum Početka <span class="text-red-400">*</span>
                                </label>
                                <input type="date"
                                       id="start_date"
                                       name="start_date"
                                       value="{{ old('start_date', now()->format('Y-m-d')) }}"
                                       required
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-white mb-2">
                                    Datum Završetka
                                </label>
                                <input type="date"
                                       id="end_date"
                                       name="end_date"
                                       value="{{ old('end_date') }}"
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 pt-6 border-t border-gray-600">
                        <a href="{{ route('organizations.show', $organization) }}"
                           class="px-4 py-2 sm:px-6 sm:py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors font-semibold text-center">
                            Otkaži
                        </a>
                        <button type="submit"
                                class="px-4 py-2 sm:px-6 sm:py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg transition-all duration-200 font-semibold shadow-lg hover:shadow-xl text-center">
                            Kreiraj Takmičenje
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleCompetitionType() {
            const tournamentRadio = document.getElementById('tournament');
            const leagueRadio = document.getElementById('league');
            const knockoutRadio = document.getElementById('knockout');
            const tournamentSettings = document.getElementById('tournamentSettings');
            const leagueSettings = document.getElementById('leagueSettings');
            const playerFormatSection = document.getElementById('playerFormatSection');

            if (tournamentRadio.checked) {
                tournamentSettings.style.display = 'block';
                leagueSettings.style.display = 'none';
                playerFormatSection.style.display = 'none';
                // Make tournament fields required
                document.getElementById('players_advancing_per_group').required = true;
                document.getElementById('advancement_method').required = true;
                // Set is_team_based to 0 for tournaments
                document.getElementById('individual_based').checked = true;
            } else if (leagueRadio.checked) {
                tournamentSettings.style.display = 'none';
                leagueSettings.style.display = 'block';
                playerFormatSection.style.display = 'block';
                // Remove required from tournament fields
                document.getElementById('players_advancing_per_group').required = false;
                document.getElementById('advancement_method').required = false;
                // Set is_team_based to 1 for leagues
                document.getElementById('team_based').checked = true;
            } else if (knockoutRadio.checked) {
                tournamentSettings.style.display = 'none';
                leagueSettings.style.display = 'none';
                playerFormatSection.style.display = 'none';
                // Remove required from tournament fields
                document.getElementById('players_advancing_per_group').required = false;
                document.getElementById('advancement_method').required = false;
                // Set is_team_based to 0 for knockout
                document.getElementById('individual_based').checked = true;
            } else {
                tournamentSettings.style.display = 'none';
                leagueSettings.style.display = 'none';
                playerFormatSection.style.display = 'block';
                // Remove required from tournament fields
                document.getElementById('players_advancing_per_group').required = false;
                document.getElementById('advancement_method').required = false;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleCompetitionType();
        });
    </script>
</x-app-layout>