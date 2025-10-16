<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $organization->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ __('Create New Competition') }}</p>
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
                            {{ __('Competition Name') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="{{ __('Enter competition name') }}">
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-white mb-2">
                            {{ __('Description') }}
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="3"
                                  class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                  placeholder="{{ __('Optional description of the competition') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sport Selection -->
                    <div>
                        <label for="sport_id" class="block text-sm font-medium text-white mb-2">
                            {{ __('Sport') }} <span class="text-red-400">*</span>
                        </label>
                        <select id="sport_id"
                                name="sport_id"
                                required
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">{{ __('Select a sport') }}</option>
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
                            {{ __('Competition Format') }} <span class="text-red-400">*</span>
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" id="league" name="type" value="league" {{ old('type', 'league') === 'league' ? 'checked' : '' }}
                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2" onchange="toggleCompetitionType()">
                                <label for="league" class="ml-3 text-sm font-medium text-white">
                                    {{ __('League') }} - All players compete against each other
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="tournament" name="type" value="tournament" {{ old('type') === 'tournament' ? 'checked' : '' }}
                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2" onchange="toggleCompetitionType()">
                                <label for="tournament" class="ml-3 text-sm font-medium text-white">
                                    {{ __('Tournament') }} - Group stage + knockout elimination
                                </label>
                            </div>
                        </div>
                        @error('type')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Team vs Individual -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            {{ __('Player Format') }} <span class="text-red-400">*</span>
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" id="team_based" name="is_team_based" value="1" {{ old('is_team_based', '0') === '1' ? 'checked' : '' }}
                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2">
                                <label for="team_based" class="ml-3 text-sm font-medium text-white">
                                    {{ __('Team-based competition') }}
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="individual_based" name="is_team_based" value="0" {{ old('is_team_based', '0') === '0' ? 'checked' : '' }}
                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2">
                                <label for="individual_based" class="ml-3 text-sm font-medium text-white">
                                    {{ __('Individual competition') }}
                                </label>
                            </div>
                        </div>
                        @error('is_team_based')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tournament Settings -->
                    <div id="tournamentSettings" class="space-y-6" style="display: none;">
                        <h3 class="text-lg font-semibold text-white border-b border-gray-600 pb-2">Tournament Settings</h3>

                        <!-- Max Participants -->
                        <div>
                            <label for="max_participants" class="block text-sm font-medium text-white mb-2">
                                {{ __('Maximum Participants') }} <span class="text-red-400">*</span>
                            </label>
                            <input type="number"
                                   id="max_participants"
                                   name="max_participants"
                                   value="{{ old('max_participants', 16) }}"
                                   min="4"
                                   max="128"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-400">Number of players/teams that can participate (4-128)</p>
                            @error('max_participants')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Group Count -->
                        <div>
                            <label for="group_count" class="block text-sm font-medium text-white mb-2">
                                {{ __('Number of Groups') }} <span class="text-red-400">*</span>
                            </label>
                            <input type="number"
                                   id="group_count"
                                   name="group_count"
                                   value="{{ old('group_count', 4) }}"
                                   min="2"
                                   max="16"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-400">How many groups to divide players into (2-16)</p>
                            @error('group_count')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Players Per Group -->
                        <div>
                            <label for="players_per_group" class="block text-sm font-medium text-white mb-2">
                                {{ __('Players Per Group') }} <span class="text-red-400">*</span>
                            </label>
                            <input type="number"
                                   id="players_per_group"
                                   name="players_per_group"
                                   value="{{ old('players_per_group', 4) }}"
                                   min="3"
                                   max="8"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-400">How many players in each group (3-8)</p>
                            @error('players_per_group')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Players Advancing Per Group -->
                        <div>
                            <label for="players_advancing_per_group" class="block text-sm font-medium text-white mb-2">
                                {{ __('Players Advancing Per Group') }} <span class="text-red-400">*</span>
                            </label>
                            <input type="number"
                                   id="players_advancing_per_group"
                                   name="players_advancing_per_group"
                                   value="{{ old('players_advancing_per_group', 2) }}"
                                   min="1"
                                   max="4"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-400">How many players advance from each group to knockout (1-4)</p>
                            @error('players_advancing_per_group')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Advancement Method -->
                        <div>
                            <label for="advancement_method" class="block text-sm font-medium text-white mb-2">
                                {{ __('Advancement Method') }} <span class="text-red-400">*</span>
                            </label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" id="automatic" name="advancement_method" value="automatic" {{ old('advancement_method', 'automatic') === 'automatic' ? 'checked' : '' }}
                                           class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2">
                                    <label for="automatic" class="ml-3 text-sm font-medium text-white">
                                        {{ __('Automatic') }} - Top players advance based on points
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="manual" name="advancement_method" value="manual" {{ old('advancement_method') === 'manual' ? 'checked' : '' }}
                                           class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2">
                                    <label for="manual" class="ml-3 text-sm font-medium text-white">
                                        {{ __('Manual') }} - Organizer selects who advances
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
                                    {{ __('Start Date') }} <span class="text-red-400">*</span>
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
                                    {{ __('End Date') }}
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
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg transition-all duration-200 font-semibold shadow-lg hover:shadow-xl">
                            {{ __('Create Competition') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleCompetitionType() {
            const tournamentRadio = document.getElementById('tournament');
            const tournamentSettings = document.getElementById('tournamentSettings');

            if (tournamentRadio.checked) {
                tournamentSettings.style.display = 'block';
                // Make tournament fields required
                document.getElementById('max_participants').required = true;
                document.getElementById('group_count').required = true;
                document.getElementById('players_per_group').required = true;
                document.getElementById('players_advancing_per_group').required = true;
                document.getElementById('advancement_method').required = true;
            } else {
                tournamentSettings.style.display = 'none';
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