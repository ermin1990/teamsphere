<div>
    <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700/50">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('Add Players to Competition') }}
            </h3>
            <div class="flex items-center space-x-3">
                <button type="button"
                        wire:click="$set('showNewPlayerForm', true)"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('New Player') }}
                </button>
                <a href="{{ route('organizations.competitions.bulk-import', [$organization, $competition]) }}"
                   class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('Bulk Import') }}
                </a>
            </div>
        </div>

        <!-- Search Input -->
        <div class="mb-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="{{ __('Search players by name or email...') }}"
                       class="w-full pl-10 pr-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        <!-- Players List -->
        <div class="space-y-2 max-h-64 overflow-y-auto">
            @forelse($availablePlayers as $player)
                <div class="flex items-center p-3 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 transition-colors" wire:key="player-{{ $player->id }}">
                    <input type="checkbox"
                           wire:model.live="selectedPlayers"
                           value="{{ $player->id }}"
                           class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                    <div class="ml-3 flex-1">
                        <div class="text-white font-medium">{{ $player->name }}</div>
                        <div class="text-gray-400 text-sm">{{ $player->email }}</div>
                    </div>
                </div>
            @empty
                @if(!empty($search))
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p>{{ __('No players found matching your search.') }}</p>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p>{{ __('No players available to add.') }}</p>
                        <p class="text-sm mt-2">{{ __('All players from this organization are already in the competition.') }}</p>
                    </div>
                @endif
            @endforelse
        </div>

        @if($availablePlayers->hasPages())
            <div class="mt-4">
                {{ $availablePlayers->links() }}
            </div>
        @endif

        @if(count($availablePlayers) > 0)
            <div class="flex items-center justify-between mt-4">
                <div class="text-sm text-gray-400">
                    {{ count($selectedPlayers) }} {{ __('player(s) selected') }}
                </div>
                <button type="button"
                        wire:click="addSelectedPlayers"
                        :disabled="selectedPlayers.length === 0"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white rounded-lg transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('Add Selected Players') }}
                </button>
            </div>
        @endif
    </div>

    <!-- New Player Modal -->
    @if($showNewPlayerForm)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-gray-800 rounded-2xl p-6 max-w-md w-full border border-gray-700 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-white">{{ __('Create New Player') }}</h3>
                    <button wire:click="$set('showNewPlayerForm', false)" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit="createNewPlayer" class="space-y-4">
                    <div>
                        <label for="newPlayerName" class="block text-sm font-medium text-white mb-2">{{ __('Player Name') }}</label>
                        <input type="text"
                               wire:model="newPlayerName"
                               id="newPlayerName"
                               required
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="{{ __('Enter player name') }}">
                        @error('newPlayerName') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="newPlayerEmail" class="block text-sm font-medium text-white mb-2">{{ __('Email Address') }} <span class="text-gray-400 text-xs">({{ __('optional') }})</span></label>
                        <input type="email"
                               wire:model="newPlayerEmail"
                               id="newPlayerEmail"
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="{{ __('Enter email address (optional)') }}">
                        @error('newPlayerEmail') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex space-x-3">
                        <button type="button"
                                wire:click="$set('showNewPlayerForm', false)"
                                class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            {{ __('Create & Add Player') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="mt-4 p-4 bg-green-500/20 border border-green-500/50 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-green-400">{!! session('success') !!}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-4 p-4 bg-red-500/20 border border-red-500/50 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-red-400">{!! session('error') !!}</span>
            </div>
        </div>
    @endif
</div>
