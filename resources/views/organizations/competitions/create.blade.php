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

                    <!-- Sport Selection -->
                    <div>
                        <label for="sport_id" class="block text-sm font-medium text-white mb-2">
                            Sport <span class="text-red-400">*</span>
                        </label>
                        <select id="sport_id"
                                name="sport_id"
                                required
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">Odaberite sport</option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
                                    {{ $sport->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('sport_id')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
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
                        </div>
                        @error('is_team_based')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tournament Settings -->
                    <div id="tournamentSettings" class="space-y-6" style="display: none;">
                        <h3 class="text-lg font-semibold text-white border-b border-gray-600 pb-2">Postavke Turnira</h3>

                        <!-- Max Participants -->
                        <div>
                            <label for="max_participants" class="block text-sm font-medium text-white mb-2">
                                Maksimalan Broj Učesnika <span class="text-red-400">*</span>
                            </label>
                            <input type="number"
                                   id="max_participants"
                                   name="max_participants"
                                   value="{{ old('max_participants', 16) }}"
                                   min="4"
                                   max="128"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-400">Broj igrača/timova koji mogu učestvovati (4-128)</p>
                            @error('max_participants')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Group Count -->
                        <div>
                            <label for="group_count" class="block text-sm font-medium text-white mb-2">
                                Broj Grupa <span class="text-red-400">*</span>
                            </label>
                            <input type="number"
                                   id="group_count"
                                   name="group_count"
                                   value="{{ old('group_count', 4) }}"
                                   min="2"
                                   max="16"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-400">U koliko grupa podijeliti igrače (2-16)</p>
                            @error('group_count')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Players Per Group -->
                        <div>
                            <label for="players_per_group" class="block text-sm font-medium text-white mb-2">
                                Igrača Po Grupi <span class="text-red-400">*</span>
                            </label>
                            <input type="number"
                                   id="players_per_group"
                                   name="players_per_group"
                                   value="{{ old('players_per_group', 4) }}"
                                   min="3"
                                   max="8"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-400">Koliko igrača u svakoj grupi (3-8)</p>
                            @error('players_per_group')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

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
                                <div class="flex items-center">
                                    <input type="radio" id="manual" name="advancement_method" value="manual" {{ old('advancement_method') === 'manual' ? 'checked' : '' }}
                                           class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2">
                                    <label for="manual" class="ml-3 text-sm font-medium text-white">
                                        Ručno - Organizator bira ko napreduje
                                    </label>
                                </div>
                            </div>
                            @error('advancement_method')
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
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-600">
                        <a href="{{ route('organizations.show', $organization) }}"
                           class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors font-semibold">
                            Otkaži
                        </a>
                        <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg transition-all duration-200 font-semibold shadow-lg hover:shadow-xl">
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
            const knockoutRadio = document.getElementById('knockout');
            const tournamentSettings = document.getElementById('tournamentSettings');
            const playerFormatSection = document.getElementById('playerFormatSection');

            if (tournamentRadio.checked) {
                tournamentSettings.style.display = 'block';
                playerFormatSection.style.display = 'none';
                // Make tournament fields required
                document.getElementById('max_participants').required = true;
                document.getElementById('group_count').required = true;
                document.getElementById('players_per_group').required = true;
                document.getElementById('players_advancing_per_group').required = true;
                document.getElementById('advancement_method').required = true;
                // Set is_team_based to 0 for tournaments
                document.getElementById('individual_based').checked = true;
            } else if (knockoutRadio.checked) {
                tournamentSettings.style.display = 'none';
                playerFormatSection.style.display = 'none';
                // Remove required from tournament fields
                document.getElementById('max_participants').required = false;
                document.getElementById('group_count').required = false;
                document.getElementById('players_per_group').required = false;
                document.getElementById('players_advancing_per_group').required = false;
                document.getElementById('advancement_method').required = false;
                // Set is_team_based to 0 for knockout
                document.getElementById('individual_based').checked = true;
            } else {
                tournamentSettings.style.display = 'none';
                playerFormatSection.style.display = 'block';
                // Remove required from tournament fields
                document.getElementById('max_participants').required = false;
                document.getElementById('group_count').required = false;
                document.getElementById('players_per_group').required = false;
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