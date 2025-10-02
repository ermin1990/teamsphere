<div>
    <div class="contents">
        <!-- Player Selection (for individual matches) -->
        @if(!$playersSelected && $matchType === 'individual')
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
            <h3 class="text-xl font-bold mb-6 text-center">
                {{ __('Select Players') }}
            </h3>
            <div class="max-w-2xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Home Player Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">Home Player</label>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($availablePlayers as $player)
                            <button type="button" wire:click="selectHomePlayer({{ $player['id'] }})"
                                    class="w-full p-3 text-left rounded-lg transition-all {{ $homePlayer && $homePlayer['id'] === $player['id'] ? 'bg-blue-600/30 border-2 border-blue-500/50' : 'bg-gray-700/50 hover:bg-gray-600/50 border-2 border-transparent' }}">
                                <div class="font-medium text-white">{{ $player['name'] }}</div>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Away Player Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">Away Player</label>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($availablePlayers as $player)
                            <button type="button" wire:click="selectAwayPlayer({{ $player['id'] }})"
                                    class="w-full p-3 text-left rounded-lg transition-all {{ $awayPlayer && $awayPlayer['id'] === $player['id'] ? 'bg-red-600/30 border-2 border-red-500/50' : 'bg-gray-700/50 hover:bg-gray-600/50 border-2 border-transparent' }}">
                                <div class="font-medium text-white">{{ $player['name'] }}</div>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Selected Players Display -->
                @if($homePlayer || $awayPlayer)
                <div class="mt-6 p-4 bg-gray-700/30 rounded-lg">
                    <div class="text-sm text-gray-400 mb-2">Selected Players:</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-blue-400 font-semibold">{{ $homePlayer ? $homePlayer['name'] : 'Not selected' }}</div>
                            <div class="text-xs text-gray-500">Home</div>
                        </div>
                        <div class="text-center">
                            <div class="text-red-400 font-semibold">{{ $awayPlayer ? $awayPlayer['name'] : 'Not selected' }}</div>
                            <div class="text-xs text-gray-500">Away</div>
                        </div>
                    </div>
                </div>

                <!-- Sets Selection -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-300 mb-3">Sets to Win</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <button type="button" wire:click="setSetsToWin(2)"
                                class="p-3 rounded-lg transition-all {{ $setsToWin === 2 ? 'bg-green-600/30 border-2 border-green-500/50' : 'bg-gray-700/50 hover:bg-gray-600/50 border-2 border-transparent' }}">
                            <div class="text-center">
                                <div class="text-lg font-bold text-white">2</div>
                                <div class="text-xs text-gray-400">Sets</div>
                            </div>
                        </button>
                        <button type="button" wire:click="setSetsToWin(3)"
                                class="p-3 rounded-lg transition-all {{ $setsToWin === 3 ? 'bg-green-600/30 border-2 border-green-500/50' : 'bg-gray-700/50 hover:bg-gray-600/50 border-2 border-transparent' }}">
                            <div class="text-center">
                                <div class="text-lg font-bold text-white">3</div>
                                <div class="text-xs text-gray-400">Sets</div>
                            </div>
                        </button>
                        <button type="button" wire:click="setSetsToWin(5)"
                                class="p-3 rounded-lg transition-all {{ $setsToWin === 5 ? 'bg-green-600/30 border-2 border-green-500/50' : 'bg-gray-700/50 hover:bg-gray-600/50 border-2 border-transparent' }}">
                            <div class="text-center">
                                <div class="text-lg font-bold text-white">5</div>
                                <div class="text-xs text-gray-400">Sets</div>
                            </div>
                        </button>
                        <button type="button" wire:click="setSetsToWin(7)"
                                class="p-3 rounded-lg transition-all {{ $setsToWin === 7 ? 'bg-green-600/30 border-2 border-green-500/50' : 'bg-gray-700/50 hover:bg-gray-600/50 border-2 border-transparent' }}">
                            <div class="text-center">
                                <div class="text-lg font-bold text-white">7</div>
                                <div class="text-xs text-gray-400">Sets</div>
                            </div>
                        </button>
                    </div>
                    <div class="mt-2 text-center text-sm text-gray-400">
                        Best of {{ $setsToWin }} sets (needs {{ ceil($setsToWin / 2) }} sets to win)
                    </div>
                </div>
                @endif

                <!-- Confirm Button -->
                <div class="mt-6 text-center">
                    <button type="button" wire:click="confirmPlayerSelection"
                            class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all duration-200 font-semibold {{ (!$homePlayer || !$awayPlayer) ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ (!$homePlayer || !$awayPlayer) ? 'disabled' : '' }}>
                        Confirm Players & Start Match
                    </button>
                </div>

                <!-- Error Display -->
                @error('players')
                <div class="mt-4 text-center text-red-400 text-sm">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
        @endif

        <!-- Server Selection (show after players are selected) -->
        @if($playersSelected && !$firstServer)
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
            <h3 class="text-xl font-bold mb-6 text-center">
                @if($matchType === 'team')
                    {{ __('Team Match Setup (Doubles)') }}
                @else
                    {{ __('Individual Match Setup') }}
                @endif
            </h3>
            <div class="max-w-md mx-auto">
                <label class="block text-sm font-medium text-gray-300 mb-3">Who serves first?</label>
                <div class="space-y-3">
                    <button type="button" wire:click="selectFirstServer('home')"
                            class="w-full p-4 bg-blue-600/20 hover:bg-blue-600/30 border-2 border-blue-500/30 hover:border-blue-500/50 rounded-lg transition-all">
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-400 mb-1">
                                @if($matchType === 'team')
                                    {{ __('Home Team') }}
                                @else
                                    {{ __('Home Player') }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-400">Serves First</div>
                        </div>
                    </button>
                    <button type="button" wire:click="selectFirstServer('away')"
                            class="w-full p-4 bg-red-600/20 hover:bg-red-600/30 border-2 border-red-500/30 hover:border-red-500/50 rounded-lg transition-all">
                        <div class="text-center">
                            <div class="text-lg font-bold text-red-400 mb-1">
                                @if($matchType === 'team')
                                    {{ __('Away Team') }}
                                @else
                                    {{ __('Away Player') }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-400">Serves First</div>
                        </div>
                    </button>
                    <button type="button" wire:click="selectRandomServer"
                            class="w-full p-4 bg-purple-600/20 hover:bg-purple-600/30 border-2 border-purple-500/30 hover:border-purple-500/50 rounded-lg transition-all">
                        <div class="text-center">
                            <div class="text-lg font-bold text-purple-400 mb-1">
                                🎲 Random
                            </div>
                            <div class="text-sm text-gray-400">Random Server</div>
                        </div>
                    </button>
                </div>
                <div class="mt-6 text-center text-gray-400 text-sm">
                    Select who serves first to start the friendly match
                </div>
            </div>
        </div>
        @endif

        <!-- Live Scoring Interface -->
        @if($firstServer)
        <div class="contents">
            <!-- Players Side by Side -->
            <div class="grid grid-cols-2 gap-4 md:gap-8 mb-8">
                <!-- Player 1 (Home) -->
                <div class="text-center">
                    <div class="mb-4 flex items-center justify-center gap-2">
                        <h3 class="text-3xl font-bold transition-all duration-300 {{ $currentServer === 'home' ? 'text-blue-400 animate-pulse drop-shadow-lg' : 'text-white' }} mb-2">
                            @if($matchType === 'team')
                                {{ __('Home Team') }}
                            @else
                                {{ $homePlayer ? $homePlayer['name'] : __('Home Player') }}
                            @endif
                        </h3>
                        @if($currentServer === 'home')
                            <span class="ml-1 flex items-center">
                                <span class="inline-block w-4 h-4 bg-yellow-300 rounded-full shadow-lg animate-pulse border-2 border-yellow-400" title="Serves" style="margin-left:0.1rem;"></span>
                            </span>
                        @endif
                    </div>

                    <!-- Score Display (no border/shadow, no name below) -->
                    <div class="relative mb-6 flex flex-col items-center min-h-[180px] rounded-lg transition-all duration-300">
                        <div class="text-8xl md:text-9xl font-bold transition-all duration-300 mb-2 {{ $matchCompleted ? '' : 'cursor-pointer hover:text-blue-300 active:scale-95 select-none' }} {{ $currentServer === 'home' ? 'text-blue-400 animate-pulse drop-shadow-lg' : 'text-blue-400' }}"
                             @if(!$matchCompleted) wire:click="addPoint('home')" @endif>
                            {{ $homeScore }}
                        </div>
                    </div>

                    <!-- Score Buttons -->
                    <div class="flex justify-center">
                        <button type="button" @if(!$matchCompleted) wire:click="subtractPoint('home')" @endif
                                class="px-4 py-2 bg-gray-600 {{ $matchCompleted ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-700' }} text-white rounded-lg transition-all duration-200 font-semibold text-sm">
                            -1
                        </button>
                    </div>
                </div>

                <!-- Player 2 (Away) -->
                <div class="text-center">
                    <div class="mb-4 flex items-center justify-center gap-2">
                        <h3 class="text-3xl font-bold transition-all duration-300 {{ $currentServer === 'away' ? 'text-red-400 animate-pulse drop-shadow-lg' : 'text-white' }} mb-2">
                            @if($matchType === 'team')
                                {{ __('Away Team') }}
                            @else
                                {{ $awayPlayer ? $awayPlayer['name'] : __('Away Player') }}
                            @endif
                        </h3>
                        @if($currentServer === 'away')
                            <span class="ml-1 flex items-center">
                                <span class="inline-block w-4 h-4 bg-yellow-300 rounded-full shadow-lg animate-pulse border-2 border-yellow-400" title="Serves" style="margin-left:0.1rem;"></span>
                            </span>
                        @endif
                    </div>

                    <!-- Score Display (no border/shadow, no name below) -->
                    <div class="relative mb-6 flex flex-col items-center min-h-[180px] rounded-lg transition-all duration-300">
                        <div class="text-8xl md:text-9xl font-bold transition-all duration-300 mb-2 {{ $matchCompleted ? '' : 'cursor-pointer hover:text-red-300 active:scale-95 select-none' }} {{ $currentServer === 'away' ? 'text-red-400 animate-pulse drop-shadow-lg' : 'text-red-400' }}"
                             @if(!$matchCompleted) wire:click="addPoint('away')" @endif>
                            {{ $awayScore }}
                        </div>
                    </div>

                    <!-- Score Buttons -->
                    <div class="flex justify-center">
                        <button type="button" @if(!$matchCompleted) wire:click="subtractPoint('away')" @endif
                                class="px-4 py-2 bg-gray-600 {{ $matchCompleted ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-700' }} text-white rounded-lg transition-all duration-200 font-semibold text-sm">
                            -1
                        </button>
                    </div>
                </div>
            </div>

            <!-- Set Information -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center space-x-8 bg-gray-700/30 rounded-lg p-4">
                    <div>
                        <div class="text-sm text-gray-400 mb-1">Current Set</div>
                        <div class="text-2xl font-bold text-white">{{ $currentSet }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-400 mb-1">Sets Won</div>
                        <div class="text-2xl font-bold text-white">
                            <span class="text-blue-400">{{ count(array_filter($sets, fn($set) => $set['home_score'] > $set['away_score'])) }}</span>
                            -
                            <span class="text-red-400">{{ count(array_filter($sets, fn($set) => $set['away_score'] > $set['home_score'])) }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-400 mb-1">Server</div>
                        <div class="text-lg font-bold text-yellow-400">
                            @if($currentServer === 'home')
                                @if($matchType === 'team')
                                    {{ __('Home Team') }}
                                @else
                                    {{ $homePlayer ? $homePlayer['name'] : __('Home Player') }}
                                @endif
                            @else
                                @if($matchType === 'team')
                                    {{ __('Away Team') }}
                                @else
                                    {{ $awayPlayer ? $awayPlayer['name'] : __('Away Player') }}
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Control Buttons -->
            <div class="flex justify-center space-x-4 mb-8">
                <button type="button" wire:click="pauseMatch"
                        class="px-6 py-3 {{ $matchPaused ? 'bg-green-600 hover:bg-green-700' : 'bg-yellow-600 hover:bg-yellow-700' }} text-white rounded-lg transition-all duration-200 font-semibold">
                    {{ $matchPaused ? 'Resume Match' : 'Pause Match' }}
                </button>
                <button type="button" wire:click="undoLastPoint"
                        class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200 font-semibold">
                    Undo Last Point
                </button>
                <button type="button" wire:click="resetSet"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-semibold">
                    Reset Set
                </button>
                @if($matchCompleted)
                <button type="button" wire:click="saveMatch"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 font-semibold">
                    Snimi Meč
                </button>
                @endif
            </div>

            <!-- Match Completion Modal -->
            @if($matchCompleted)
            <div class="fixed inset-0 overflow-y-auto z-[100]">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity">
                        <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                    </div>
                    <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-500 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-white">
                                        Match Completed!
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-300">
                                            @php
                                                $homeSets = count(array_filter($sets, fn($set) => $set['home_score'] > $set['away_score']));
                                                $awaySets = count(array_filter($sets, fn($set) => $set['away_score'] > $set['home_score']));
                                                $winner = $homeSets > $awaySets ? ($matchType === 'team' ? 'Home Team' : ($homePlayer ? $homePlayer['name'] : 'Home Player')) : ($matchType === 'team' ? 'Away Team' : ($awayPlayer ? $awayPlayer['name'] : 'Away Player'));
                                            @endphp
                                            {{ $winner }} wins with {{ max($homeSets, $awaySets) }} sets to {{ min($homeSets, $awaySets) }}!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" wire:click="saveMatch"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
