@if($hasDates)
    <!-- League Format (for both team and individual competitions) -->
    <div class="space-y-6">
        <div>
            <label for="format" class="block text-sm font-medium text-white mb-2">
                {{ __('League Format') }} <span class="text-red-400">*</span>
            </label>
            <select id="format"
                    name="format"
                    required
                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                <option value="">{{ __('Select format') }}</option>
                <option value="single_round" {{ old('format') == 'single_round' ? 'selected' : '' }}>
                    {{ __('Single Round Robin') }}
                </option>
                <option value="double_round" {{ old('format') == 'double_round' ? 'selected' : '' }}>
                    {{ __('Double Round Robin') }}
                </option>
                <option value="double_round_knockout" {{ old('format') == 'double_round_knockout' ? 'selected' : '' }}>
                    {{ __('Double Round Robin + Knockout') }}
                </option>
            </select>
            @error('format')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        @if($isTeamBased)
            <!-- Points System (only for team-based competitions) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="points_win" class="block text-sm font-medium text-white mb-2">
                        {{ __('Points for Win') }} <span class="text-red-400">*</span>
                    </label>
                    <input type="number"
                           id="points_win"
                           name="points_win"
                           value="{{ old('points_win', 3) }}"
                           min="0"
                           max="10"
                           required
                           class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    @error('points_win')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="points_draw" class="block text-sm font-medium text-white mb-2">
                        {{ __('Points for Draw') }}
                    </label>
                    <input type="number"
                           id="points_draw"
                           name="points_draw"
                           value="{{ old('points_draw', 1) }}"
                           min="0"
                           max="10"
                           class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    @error('points_draw')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="points_loss" class="block text-sm font-medium text-white mb-2">
                        {{ __('Points for Loss') }} <span class="text-red-400">*</span>
                    </label>
                    <input type="number"
                           id="points_loss"
                           name="points_loss"
                           value="{{ old('points_loss', 0) }}"
                           min="0"
                           max="10"
                           required
                           class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    @error('points_loss')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @else
            <!-- Sets to Win (only for individual competitions) -->
            <div>
                <label for="sets_to_win" class="block text-sm font-medium text-white mb-2">
                    {{ __('Sets to Win Match') }}
                </label>
                <select id="sets_to_win"
                        name="sets_to_win"
                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="2" {{ old('sets_to_win', 2) == 2 ? 'selected' : '' }}>
                        {{ __('Best of 3 (2 sets to win)') }}
                    </option>
                    <option value="3" {{ old('sets_to_win') == 3 ? 'selected' : '' }}>
                        {{ __('Best of 5 (3 sets to win)') }}
                    </option>
                    <option value="4" {{ old('sets_to_win') == 4 ? 'selected' : '' }}>
                        {{ __('Best of 7 (4 sets to win)') }}
                    </option>
                </select>
                @error('sets_to_win')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-400">
                    {{ __('For') }} {{ $sport->name }} {{ __('matches, players need to win this many sets to win the match.') }}
                </p>
            </div>
        @endif

        <!-- Public Visibility -->
        <div class="flex items-center space-x-3 p-4 bg-gray-700/30 rounded-lg border border-gray-600/30">
            <input type="checkbox"
                   id="is_public"
                   name="is_public"
                   value="1"
                   {{ old('is_public', $league->settings['is_public'] ?? false) ? 'checked' : '' }}
                   class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
            <div>
                <label for="is_public" class="text-white font-medium cursor-pointer">
                    {{ __('Make league public') }}
                </label>
                <p class="text-sm text-gray-400 mt-1">
                    {{ __('When enabled, this league will be visible on the public website for everyone to view.') }}
                </p>
            </div>
        </div>
    </div>
@else
    <div class="text-center py-8">
        <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>
        <h4 class="text-white font-semibold mb-2">{{ __('No Settings Required') }}</h4>
        <p class="text-gray-400">{{ __('This league will be created without specific rules. You can set them up later.') }}</p>
    </div>
@endif