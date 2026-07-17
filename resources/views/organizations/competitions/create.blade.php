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
            <!-- AI popuni ligu -->
            <div id="aiAssistantCard" class="relative bg-gradient-to-r from-blue-900/30 to-purple-900/30 backdrop-blur-xl rounded-2xl p-6 border border-blue-500/30 shadow-xl mb-6 overflow-hidden transition-all duration-300">
                <!-- Glatki "shimmer" preliv preko cijele kartice dok AI radi -->
                <div id="aiShimmer" class="hidden absolute inset-0 bg-gradient-to-r from-transparent via-blue-400/10 to-transparent -translate-x-full animate-[shimmer_1.6s_infinite]"></div>

                <label for="ai_description" class="relative block text-sm font-medium text-white mb-2">
                    ✨ Opiši takmičenje riječima (opciono) — AI će pokušati popuniti formu ispod
                </label>
                <textarea id="ai_description" rows="2"
                          class="relative w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                          placeholder="Npr. Turnir u stonom tenisu za 16 igrača, dvojica napreduju po grupi, počinje 5. avgusta"></textarea>
                <div class="relative flex items-center justify-between mt-3">
                    <div id="aiStatus" class="flex items-center gap-2 text-sm text-gray-400">
                        <svg id="aiSpinner" class="hidden w-4 h-4 text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span id="aiStatusText"></span>
                    </div>
                    <button type="button" id="aiSuggestButton"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg transition-all duration-200 font-semibold text-sm disabled:opacity-70 disabled:cursor-not-allowed">
                        Popuni AI-jem
                    </button>
                </div>
            </div>

            <style>
                @keyframes shimmer {
                    100% { transform: translateX(100%); }
                }
            </style>

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

                    <!-- Lokacija / kontakt / kotizacija -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="location" class="block text-sm font-medium text-white mb-2">
                                Lokacija/adresa igranja
                            </label>
                            <input type="text"
                                   id="location"
                                   name="location"
                                   value="{{ old('location') }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Npr. SC Mejdan, teren 2">
                            @error('location')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="organizer_contact" class="block text-sm font-medium text-white mb-2">
                                Kontakt organizatora
                            </label>
                            <input type="text"
                                   id="organizer_contact"
                                   name="organizer_contact"
                                   value="{{ old('organizer_contact') }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Telefon ili email">
                            @error('organizer_contact')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="entry_fee" class="block text-sm font-medium text-white mb-2">
                                Kotizacija/cijena učešća
                            </label>
                            <input type="text"
                                   id="entry_fee"
                                   name="entry_fee"
                                   value="{{ old('entry_fee') }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Npr. 20 KM po sezoni, besplatno">
                            @error('entry_fee')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
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

                    <!-- City / Season / Registration deadline -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="city_id" class="block text-sm font-medium text-white mb-2">Grad</label>
                            <select id="city_id" name="city_id"
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Bez grada</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-400">Koristi se za javno pretraživanje liga po gradu.</p>
                        </div>
                        <div>
                            <label for="season_id" class="block text-sm font-medium text-white mb-2">Sezona</label>
                            <select id="season_id" name="season_id"
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Bez sezone</option>
                                @foreach($seasons as $season)
                                    <option value="{{ $season->id }}" {{ old('season_id', $seasons->firstWhere('is_active', true)?->id) == $season->id ? 'selected' : '' }}>
                                        {{ $season->name }}{{ $season->is_active ? ' (aktivna)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="registration_deadline" class="block text-sm font-medium text-white mb-2">Prijave otvorene do</label>
                            <input type="datetime-local" id="registration_deadline" name="registration_deadline" value="{{ old('registration_deadline') }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
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
                        <p class="text-xs text-gray-400 ml-8">Za društvo koje igra radi zabave: organizator može ručno dodati mečeve i ranije završiti meč/set sa trenutnim rezultatom, bez čekanja da neko zvanično osvoji.</p>

                        <div class="flex items-center pt-2">
                            <input type="checkbox" id="allow_rematches" name="allow_rematches" value="1" {{ old('allow_rematches') ? 'checked' : '' }}
                                   class="w-5 h-5 border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2 rounded">
                            <label for="allow_rematches" class="ml-3 text-sm font-medium text-white">
                                Dozvoli povratne mečeve (revanš)
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 ml-8">Isti par igrača može igrati više puta jedan protiv drugog u ovom takmičenju.</p>
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

        // AI popuni ligu
        document.getElementById('aiSuggestButton').addEventListener('click', async function () {
            const button = this;
            const card = document.getElementById('aiAssistantCard');
            const shimmer = document.getElementById('aiShimmer');
            const spinner = document.getElementById('aiSpinner');
            const statusText = document.getElementById('aiStatusText');
            const description = document.getElementById('ai_description').value.trim();

            const setStatus = (text, colorClass) => {
                statusText.textContent = text;
                statusText.className = colorClass;
            };

            const setLoading = (isLoading) => {
                button.disabled = isLoading;
                spinner.classList.toggle('hidden', !isLoading);
                shimmer.classList.toggle('hidden', !isLoading);
                card.classList.toggle('border-blue-400/60', isLoading);
                card.classList.toggle('shadow-blue-500/20', isLoading);
                card.classList.toggle('shadow-2xl', isLoading);
            };

            if (!description) {
                setStatus('Prvo opišite takmičenje u polju iznad.', 'text-yellow-400');
                return;
            }

            setLoading(true);
            setStatus('AI razmišlja...', 'text-blue-300');

            try {
                const response = await fetch('{{ route("organizations.competitions.ai-suggest", $organization) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ description }),
                });

                const payload = await response.json();

                if (!response.ok) {
                    setStatus(payload.error || 'AI trenutno nije dostupan, popunite ručno.', 'text-red-400');
                    return;
                }

                applyAiSuggestion(payload.data || {});

                const warnings = payload.warnings || [];
                setStatus(
                    warnings.length ? 'Forma popunjena. ' + warnings.join(' ') : 'Forma popunjena — provjerite prije slanja.',
                    warnings.length ? 'text-yellow-400' : 'text-green-400'
                );
            } catch (e) {
                setStatus('AI trenutno nije dostupan, popunite ručno.', 'text-red-400');
            } finally {
                setLoading(false);
            }
        });

        function applyAiSuggestion(data) {
            const setValue = (id, value) => {
                if (value === undefined || value === null) return;
                const el = document.getElementById(id);
                if (el) el.value = value;
            };
            const setChecked = (id, checked) => {
                const el = document.getElementById(id);
                if (el) el.checked = !!checked;
            };

            setValue('name', data.name);
            setValue('description', data.description);
            setValue('location', data.location);
            setValue('organizer_contact', data.organizer_contact);
            setValue('entry_fee', data.entry_fee);
            setValue('start_date', data.start_date);
            setValue('end_date', data.end_date);
            setValue('players_advancing_per_group', data.players_advancing_per_group);

            if (data.city_id) setValue('city_id', String(data.city_id));

            if (data.type === 'league') {
                document.getElementById('league').checked = true;
            } else if (data.type === 'tournament') {
                document.getElementById('tournament').checked = true;
            }

            if (typeof data.is_team_based === 'boolean') {
                setChecked('team_based', data.is_team_based);
                setChecked('individual_based', !data.is_team_based);
            }

            setChecked('is_double_round', data.is_double_round);
            setChecked('is_recreational', data.is_recreational);
            setChecked('allow_rematches', data.allow_rematches);

            toggleCompetitionType();
        }
    </script>
</x-app-layout>