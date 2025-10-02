<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                    {{ __('Friendly Matches') }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $organization->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('organizations.show', $organization) }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    ← {{ __('Back to Organization') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Sports Selection -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                <h3 class="text-xl font-bold mb-6 text-center">{{ __('Choose Sport & Match Type') }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Table Tennis -->
                    <div class="bg-gray-700/30 rounded-xl p-6 hover:bg-gray-600/30 transition-all duration-200">
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <span class="text-white text-2xl">🏓</span>
                            </div>
                            <h4 class="text-white font-semibold text-lg">{{ __('Table Tennis') }}</h4>
                        </div>

                        <div class="space-y-3">
                            <a href="{{ route('organizations.friendly-matches.table-tennis', ['organization' => $organization->slug]) }}?type=individual"
                               class="w-full bg-blue-600/20 hover:bg-blue-600/30 border-2 border-blue-500/30 hover:border-blue-500/50 rounded-lg p-3 transition-all block text-center">
                                <div class="text-blue-400 font-medium">{{ __('Individual Match') }}</div>
                                <div class="text-gray-400 text-sm">{{ __('1 vs 1') }}</div>
                            </a>

                            <a href="{{ route('organizations.friendly-matches.table-tennis', ['organization' => $organization->slug]) }}?type=team"
                               class="w-full bg-purple-600/20 hover:bg-purple-600/30 border-2 border-purple-500/30 hover:border-purple-500/50 rounded-lg p-3 transition-all block text-center">
                                <div class="text-purple-400 font-medium">{{ __('Team Match (Doubles)') }}</div>
                                <div class="text-gray-400 text-sm">{{ __('2 vs 2') }}</div>
                            </a>
                        </div>
                    </div>

                    <!-- Placeholder for other sports -->
                    <div class="bg-gray-700/30 rounded-xl p-6 opacity-50">
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <span class="text-white text-2xl">⚽</span>
                            </div>
                            <h4 class="text-gray-400 font-semibold text-lg">{{ __('Football') }}</h4>
                        </div>
                        <div class="text-center text-gray-500 text-sm">
                            {{ __('Coming Soon') }}
                        </div>
                    </div>

                    <div class="bg-gray-700/30 rounded-xl p-6 opacity-50">
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <span class="text-white text-2xl">🏀</span>
                            </div>
                            <h4 class="text-gray-400 font-semibold text-lg">{{ __('Basketball') }}</h4>
                        </div>
                        <div class="text-center text-gray-500 text-sm">
                            {{ __('Coming Soon') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Friendly Matches -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                <h3 class="text-xl font-bold mb-6">{{ __('Recent Friendly Matches') }}</h3>

                <livewire:friendly-matches-list :organization-id="$organization->id" />
            </div>
        </div>
    </div>
</x-app-layout>