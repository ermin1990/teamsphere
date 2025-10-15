<div>
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
        <h3 class="text-xl font-semibold text-white mb-4">Dodaj Igrača po Email-u</h3>

        <div class="space-y-4">
            <!-- Email Input -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                    Email Igrača
                </label>
                <div class="relative">
                    <input
                        type="email"
                        wire:model.live.debounce.300ms="email"
                        id="email"
                        class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        placeholder="Unesite email adresu igrača..."
                    >
                    @if($isSearching)
                    <div class="absolute right-3 top-3">
                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Search Results -->
            @if($searchMessage)
            <div class="p-4 rounded-lg @if(str_contains($searchMessage, 'successfully') || str_contains($searchMessage, 'can be added')) bg-green-500/20 border border-green-500/30 @elseif(str_contains($searchMessage, 'already')) bg-yellow-500/20 border border-yellow-500/30 @else bg-red-500/20 border border-red-500/30 @endif">
                <div class="flex items-start">
                    @if(str_contains($searchMessage, 'successfully') || str_contains($searchMessage, 'can be added'))
                    <svg class="w-5 h-5 text-green-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    @elseif(str_contains($searchMessage, 'already'))
                    <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    @else
                    <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    @endif
                    <div>
                        <p class="text-white font-medium">{{ $searchMessage }}</p>
                        @if($foundUser)
                        <div class="mt-2 p-3 bg-gray-700/50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-semibold text-sm">{{ substr($foundUser->name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $foundUser->name }}</p>
                                    <p class="text-gray-400 text-sm">{{ $foundUser->email }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            @if($foundUser && !$foundPlayer && !str_contains($searchMessage, 'already'))
            <div class="flex space-x-3">
                <button
                    wire:click="createAndAddPlayer"
                    class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-green-500/25"
                >
                    Kreiraj Igrača i Dodaj u Ligu
                </button>
                <button
                    wire:click="clearSearch"
                    class="px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors"
                >
                    Očisti
                </button>
            </div>
            @elseif($foundPlayer && !str_contains($searchMessage, 'already'))
            <div class="flex space-x-3">
                <button
                    wire:click="addExistingPlayer"
                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-blue-500/25"
                >
                    Dodaj u Ligu
                </button>
                <button
                    wire:click="clearSearch"
                    class="px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors"
                >
                    Očisti
                </button>
            </div>
            @elseif($email && $searchMessage)
            <div class="flex justify-end">
                <button
                    wire:click="clearSearch"
                    class="px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors"
                >
                    Očisti
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
