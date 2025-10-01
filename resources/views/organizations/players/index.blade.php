<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $organization->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ __('Players') }}</p>
            </div>
            <a href="{{ route('organizations.players.create', $organization) }}"
               class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                {{ __('Add Player') }}
            </a>
        </div>
    </x-slot>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($players->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($players as $player)
                    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-200 group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">
                                        {{ substr($player->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="text-white font-semibold text-lg">{{ $player->name }}</h3>
                                    @if($player->user)
                                        <p class="text-green-400 text-sm">{{ __('Registered User') }}</p>
                                    @else
                                        <p class="text-yellow-400 text-sm">{{ __('Named Player') }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($player->jersey_number)
                                <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    #{{ $player->jersey_number }}
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2 mb-4">
                            @if($player->position)
                                <div class="flex items-center text-white/70 text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $player->position }}
                                </div>
                            @endif
                            @if($player->date_of_birth)
                                <div class="flex items-center text-white/70 text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($player->date_of_birth)->format('M j, Y') }}
                                </div>
                            @endif
                            @if($player->email)
                                <div class="flex items-center text-white/70 text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $player->email }}
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-white/20">
                            <a href="{{ route('players.show', $player) }}"
                               class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors">
                                {{ __('View Details') }}
                            </a>
                            <div class="flex space-x-2">
                                <a href="{{ route('players.edit', $player) }}"
                                   class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">
                                    {{ __('Edit') }}
                                </a>
                                <form action="{{ route('players.destroy', $player) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this player?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-400 hover:text-red-300 text-sm font-medium transition-colors">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">{{ __('No Players Yet') }}</h3>
                <p class="text-white/70 mb-8 max-w-md mx-auto">
                    {{ __('Start building your team by adding players. You can add registered users or create named players for statistics tracking.') }}
                </p>
                <a href="{{ route('organizations.players.create', $organization) }}"
                   class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-8 py-4 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl inline-block">
                    {{ __('Add Your First Player') }}
                </a>
            </div>
        @endif
    </div>
</div>
</x-app-layout>