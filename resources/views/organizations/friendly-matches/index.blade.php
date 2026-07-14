<x-app-layout>
    <!-- DEBUG: This view is loading successfully -->
    <script>console.log('Friendly matches index view loaded successfully');</script>
    
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

            <!-- Get Started & Recent Matches -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
                @if(isset($isOwner) && $isOwner)
                <!-- Header -->
                <div class="bg-gradient-to-r from-green-600/20 to-blue-600/20 border-b border-gray-700/50 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-white">{{ __('Get Started') }}</h3>
                            <p class="text-gray-400 mt-1">{{ __('Choose a sport to start a new friendly match') }}</p>
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __('Quick access to match creation') }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Content -->
                <div class="p-8">
                    @if(isset($isOwner) && $isOwner)
                    <!-- Sports Selection - sport organizacije (Faza 1: jedan sport po organizaciji) -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-white mb-4">{{ __('Choose Sport & Match Type') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @if($organization->sport->slug === 'stoni-tenis')
                                <!-- Table Tennis -->
                                <div class="bg-gray-700/30 rounded-xl p-4 hover:bg-gray-600/30 transition-all duration-200">
                                    <div class="text-center mb-3">
                                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center mx-auto mb-2">
                                            <span class="text-white text-xl">{{ $organization->sport->icon }}</span>
                                        </div>
                                        <h5 class="text-white font-semibold">{{ $organization->sport->name }}</h5>
                                    </div>

                                    <div class="space-y-2">
                                        <a href="{{ route('organizations.friendly-matches.table-tennis', ['organization' => $organization->slug]) }}?type=individual"
                                           class="w-full bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 hover:border-blue-500/50 rounded-lg p-2 transition-all block text-center text-sm">
                                            <div class="text-blue-400 font-medium">{{ __('Individual Match') }}</div>
                                            <div class="text-gray-400 text-xs">{{ __('1 vs 1') }}</div>
                                        </a>

                                        <a href="{{ route('organizations.friendly-matches.table-tennis', ['organization' => $organization->slug]) }}?type=team"
                                           class="w-full bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/30 hover:border-purple-500/50 rounded-lg p-2 transition-all block text-center text-sm">
                                            <div class="text-purple-400 font-medium">{{ __('Team Match (Doubles)') }}</div>
                                            <div class="text-gray-400 text-xs">{{ __('2 vs 2') }}</div>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <!-- Prijateljski mečevi za ovaj sport jos nisu implementirani -->
                                <div class="bg-gray-700/30 rounded-xl p-4 opacity-50">
                                    <div class="text-center mb-3">
                                        <div class="w-12 h-12 bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl flex items-center justify-center mx-auto mb-2">
                                            <span class="text-white text-xl">{{ $organization->sport->icon }}</span>
                                        </div>
                                        <h5 class="text-gray-400 font-semibold">{{ $organization->sport->name }}</h5>
                                    </div>
                                    <div class="text-center text-gray-500 text-sm">
                                        {{ __('Uskoro dostupno') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-700/50 my-8"></div>
                    @endif

                    <!-- Recent Matches -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-white">{{ __('Recent Matches') }}</h4>
                            <span class="text-sm text-gray-400">{{ __('Latest friendly matches in your organization') }}</span>
                        </div>

                        <livewire:friendly-matches-list :organization-id="$organization->id" :organization="$organization" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>