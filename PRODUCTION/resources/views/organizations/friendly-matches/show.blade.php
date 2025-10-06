<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                    🏓 {{ __('Friendly Match Details') }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $organization->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('organizations.friendly-matches.index', $organization) }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    ← {{ __('Back to Friendly Matches') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl mb-8">
        <h2 class="text-2xl font-bold mb-6 text-center text-white">Detalji prijateljskog meča</h2>

        <!-- Match Header -->
        <div class="text-center mb-8">

            <div class="flex flex-col md:flex-row items-center justify-center space-y-4 md:space-y-0 md:space-x-6 mb-4">
                <!-- Home Team -->
                <div class="flex flex-col items-center">
                    <div class="text-blue-400 font-semibold text-lg text-center">
                        @if(isset($match->home_player2_name) && $match->home_player2_name)
                            <div class="flex flex-col md:flex-row items-center space-y-1 md:space-y-0 md:space-x-2">
                                <span>{{ $match->home_player_name }}</span>
                                <span class="text-sm md:hidden">&</span>
                                <span class="hidden md:inline">&</span>
                                <span>{{ $match->home_player2_name }}</span>
                            </div>
                        @else
                            <span>{{ $match->home_player_name }}</span>
                        @endif
                    </div>
                </div>

                <div class="text-white font-bold text-2xl">vs</div>

                <!-- Away Team -->
                <div class="flex flex-col items-center">
                    <div class="text-red-400 font-semibold text-lg text-center">
                        @if(isset($match->away_player2_name) && $match->away_player2_name)
                            <div class="flex flex-col md:flex-row items-center space-y-1 md:space-y-0 md:space-x-2">
                                <span>{{ $match->away_player_name }}</span>
                                <span class="text-sm md:hidden">&</span>
                                <span class="hidden md:inline">&</span>
                                <span>{{ $match->away_player2_name }}</span>
                            </div>
                        @else
                            <span>{{ $match->away_player_name }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Winner Announcement -->
            <div class="inline-flex items-center px-6 py-3 bg-emerald-600/20 border border-emerald-500/30 rounded-full">
                <svg class="w-5 h-5 text-emerald-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-emerald-400 font-semibold text-lg">
                    Pobjednik:
                    @if(isset($match->home_player2_name) && $match->home_player2_name && str_contains($match->winner_name, $match->home_player_name) && str_contains($match->winner_name, $match->home_player2_name))
                        {{ $match->home_player_name }} & {{ $match->home_player2_name }}
                    @elseif(isset($match->away_player2_name) && $match->away_player2_name && str_contains($match->winner_name, $match->away_player_name) && str_contains($match->winner_name, $match->away_player2_name))
                        {{ $match->away_player_name }} & {{ $match->away_player2_name }}
                    @elseif(isset($match->home_player2_name) && $match->home_player2_name == null && isset($match->away_player2_name) && $match->away_player2_name == null)
                        {{ $match->winner_name }}
                    @else
                        {{ $match->winner_name }}
                    @endif
                </span>
            </div>
        </div>

        <!-- Match Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-700/30 rounded-lg p-4 text-center">
                <div class="text-sm text-gray-400 mb-1">Sport</div>
                <div class="text-lg font-semibold text-white">Stoni tenis</div>
            </div>
            <div class="bg-gray-700/30 rounded-lg p-4 text-center">
                <div class="text-sm text-gray-400 mb-1">Odigrano</div>
                <div class="text-lg font-semibold text-white">{{ $match->completed_at ? $match->completed_at->format('d.m.Y H:i') : '-' }}</div>
            </div>
            <div class="bg-gray-700/30 rounded-lg p-4 text-center">
                <div class="text-sm text-gray-400 mb-1">Trajanje</div>
                <div class="text-lg font-semibold text-white">{{ collect($match->set_durations)->sum() ? gmdate('i:s', collect($match->set_durations)->sum()) : '?' }}</div>
            </div>
        </div>

        <!-- Sets Results -->
        <div class="bg-gray-700/30 rounded-lg p-6">
            <h4 class="font-semibold text-gray-300 mb-4 text-center">Rezultati setova</h4>
            <div class="space-y-3">
                @foreach($match->sets as $index => $set)
                    <div class="flex items-center justify-between bg-gray-800/50 rounded-lg p-4">
                        <div class="text-gray-400 font-medium">Set {{ $index + 1 }}</div>
                        <div class="flex items-center space-x-4">
                            <div class="text-blue-400 font-bold text-xl">{{ $set['home_score'] }}</div>
                            <div class="text-gray-400">-</div>
                            <div class="text-red-400 font-bold text-xl">{{ $set['away_score'] }}</div>
                        </div>
                        <div class="text-sm text-gray-500">
                            @if(isset($match->set_durations[$index]))
                                {{ gmdate('i:s', $match->set_durations[$index]) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('organizations.friendly-matches.index', $organization) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 font-semibold">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Povratak na listu mečeva
        </a>
    </div>
    </div>
</x-app-layout>