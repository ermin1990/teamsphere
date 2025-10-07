<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $organization->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ __('Create New League') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                <form action="{{ route('organizations.leagues.store', $organization) }}" method="POST" id="leagueForm" class="space-y-8">
                    @csrf

                    <!-- League Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-white mb-2">
                            {{ __('League Name') }} <span class="text-red-400">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="{{ __('Enter league name') }}">
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
                                  placeholder="{{ __('Optional description of the league') }}">{{ old('description') }}</textarea>
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

                    <!-- Team vs Individual -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            {{ __('Competition Type') }} <span class="text-red-400">*</span>
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" id="team_based" name="is_team_based" value="1" {{ old('is_team_based', '1') === '1' ? 'checked' : '' }}
                                       class="border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2">
                                <label for="team_based" class="ml-3 text-sm font-medium text-white">
                                    {{ __('Team-based competition') }}
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="individual_based" name="is_team_based" value="0" {{ old('is_team_based') === '0' ? 'checked' : '' }}
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

                    <!-- League Duration (Optional) -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="has_dates" name="has_dates" value="1" {{ old('has_dates') ? 'checked' : '' }}
                                   class="rounded border-gray-600/50 bg-gray-700/50 text-blue-600 focus:ring-blue-500 focus:ring-2">
                            <label for="has_dates" class="ml-3 text-sm font-medium text-white">
                                {{ __('Set league duration (optional)') }}
                            </label>
                        </div>

                        <div id="datesSection" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ old('has_dates') ? '' : 'hidden' }}">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-white mb-2">
                                    {{ __('Start Date') }}
                                </label>
                                <input type="date"
                                       id="start_date"
                                       name="start_date"
                                       value="{{ old('start_date') }}"
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

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-600/50">
                        <a href="{{ route('organizations.show', $organization) }}"
                           class="px-6 py-3 border border-gray-600/50 text-white/70 hover:text-white hover:border-gray-500 rounded-lg transition-all duration-200">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit"
                                class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-blue-500/25">
                            {{ __('Create League') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hasDatesCheckbox = document.getElementById('has_dates');
            const datesSection = document.getElementById('datesSection');

            // Toggle dates section
            hasDatesCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    datesSection.classList.remove('hidden');
                } else {
                    datesSection.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>