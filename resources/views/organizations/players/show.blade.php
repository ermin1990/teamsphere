<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $player->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $organization->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('players.edit', $player) }}"
                   class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    {{ __('Edit Player') }}
                </a>
                <form action="{{ route('players.destroy', $player) }}"
                      method="POST"
                      class="inline"
                      onsubmit="return confirm('{{ __('Are you sure you want to delete this player?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        {{ __('Delete Player') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Player Info Card -->
            <div class="lg:col-span-2">
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8 border border-white/20">
                    <div class="flex items-start space-x-6 mb-8">
                        <div class="w-20 h-20 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">
                                {{ substr($player->name, 0, 1) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h2 class="text-2xl font-bold text-white">{{ $player->name }}</h2>
                                @if($player->jersey_number)
                                    <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                        #{{ $player->jersey_number }}
                                    </div>
                                @endif
                            </div>
                            @if($player->user)
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-500/20 text-green-400 border border-green-500/30">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ __('Registered User') }}
                                </div>
                            @else
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    {{ __('Named Player') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Player Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($player->email)
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <div class="flex items-center text-white/70 text-sm mb-1">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ __('Email') }}
                                </div>
                                <p class="text-white font-medium">{{ $player->email }}</p>
                            </div>
                        @endif

                        @if($player->date_of_birth)
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <div class="flex items-center text-white/70 text-sm mb-1">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ __('Date of Birth') }}
                                </div>
                                <p class="text-white font-medium">{{ \Carbon\Carbon::parse($player->date_of_birth)->format('F j, Y') }}</p>
                                <p class="text-white/60 text-sm">{{ \Carbon\Carbon::parse($player->date_of_birth)->age }} {{ __('years old') }}</p>
                            </div>
                        @endif

                        @if($player->position)
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <div class="flex items-center text-white/70 text-sm mb-1">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ __('Position') }}
                                </div>
                                <p class="text-white font-medium">{{ $player->position }}</p>
                            </div>
                        @endif

                        @if($player->jersey_number)
                            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                                <div class="flex items-center text-white/70 text-sm mb-1">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    {{ __('Jersey Number') }}
                                </div>
                                <p class="text-white font-medium">#{{ $player->jersey_number }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Status -->
                    <div class="mt-6 pt-6 border-t border-white/20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center text-white/70 text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ __('Status') }}
                                </div>
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $player->is_active ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                                    {{ $player->is_active ? __('Active') : __('Inactive') }}
                                </div>
                            </div>
                            <div class="text-white/50 text-sm">
                                {{ __('Created') }} {{ $player->created_at->format('M j, Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                    <h3 class="text-lg font-semibold text-white mb-4">{{ __('Quick Stats') }}</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-white/70">{{ __('Games Played') }}</span>
                            <span class="text-white font-semibold">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-white/70">{{ __('Wins') }}</span>
                            <span class="text-white font-semibold">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-white/70">{{ __('Points Scored') }}</span>
                            <span class="text-white font-semibold">0</span>
                        </div>
                    </div>
                    <p class="text-white/50 text-sm mt-4">{{ __('Statistics will be available once games are played.') }}</p>
                </div>

                <!-- Organization Info -->
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                    <h3 class="text-lg font-semibold text-white mb-4">{{ __('Organization') }}</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-white/70 text-sm">{{ __('Name') }}</p>
                            <p class="text-white font-medium">{{ $organization->name }}</p>
                        </div>
                        <div>
                            <p class="text-white/70 text-sm">{{ __('Sport') }}</p>
                            <p class="text-white font-medium">{{ $organization->sport->name ?? __('Not set') }}</p>
                        </div>
                        <a href="{{ route('organizations.show', $organization) }}"
                           class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors mt-3 inline-block">
                            {{ __('View Organization') }} →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabovi za mečeve -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20 mt-8">
            <div class="mb-6">
                <div class="flex space-x-1 bg-gray-800/50 p-1 rounded-lg">
                    <button id="league-tab" class="flex-1 py-2 px-4 text-sm font-medium rounded-md transition-all duration-200 bg-purple-600 text-white">
                        {{ __('League Matches') }}
                    </button>
                    <button id="friendly-tab" class="flex-1 py-2 px-4 text-sm font-medium rounded-md transition-all duration-200 text-gray-400 hover:text-white">
                        {{ __('Friendly Matches') }}
                    </button>
                </div>
            </div>

            <!-- Ligaški mečevi -->
            <div id="league-matches" class="tab-content">
                <h3 class="text-lg font-semibold text-white mb-4">{{ __('League Matches') }}</h3>
                @if($matches['league']->count())
                    <div class="space-y-3">
                        @foreach($matches['league'] as $match)
                            <div class="p-4 rounded-lg bg-gray-800/50 border border-gray-700/30 flex flex-col md:flex-row md:items-center md:justify-between">
                                <div>
                                    <div class="font-bold text-white text-lg">
                                        {{ $match->league->name ?? __('League Match') }}
                                    </div>
                                    <div class="text-gray-400 text-sm">
                                        {{ $match->league->sport->name ?? '' }} • {{ $match->played_at ? $match->played_at->format('d.m.Y H:i') : __('Not played yet') }}
                                    </div>
                                    <div class="text-gray-300 text-sm mt-1">
                                        @if($match->homeTeam)
                                            {{ $match->homeTeam->name }}
                                        @elseif($match->homePlayer)
                                            {{ $match->homePlayer->name }}
                                        @endif
                                        <span class="font-bold text-white">vs</span>
                                        @if($match->awayTeam)
                                            {{ $match->awayTeam->name }}
                                        @elseif($match->awayPlayer)
                                            {{ $match->awayPlayer->name }}
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2 md:mt-0 text-right">
                                    <div class="text-2xl font-bold text-blue-400">{{ $match->home_score }} : {{ $match->away_score }}</div>
                                    <a href="{{ route('organizations.leagues.matches.show', [$organization->slug, $match->league->slug, $match->id]) }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors mt-2 inline-block">{{ __('View Match') }} →</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-gray-400 text-center py-6">{{ __('No league matches played yet.') }}</div>
                @endif
            </div>

            <!-- Prijateljski mečevi -->
            <div id="friendly-matches" class="tab-content hidden">
                <h3 class="text-lg font-semibold text-white mb-4">{{ __('Friendly Matches') }}</h3>
                @if($matches['friendly']->count())
                    <div class="space-y-3">
                        @foreach($matches['friendly'] as $match)
                            <div class="p-4 rounded-lg bg-gray-800/50 border border-gray-700/30 flex flex-col md:flex-row md:items-center md:justify-between">
                                <div>
                                    <div class="font-bold text-white text-lg">
                                        {{ __('Friendly Match') }}
                                    </div>
                                    <div class="text-gray-400 text-sm">
                                        {{ __('Table Tennis') }} • {{ $match->completed_at ? $match->completed_at->format('d.m.Y H:i') : __('Not completed yet') }}
                                    </div>
                                    <div class="text-gray-300 text-sm mt-1">
                                        {{ $match->home_player_name ?? $match->homePlayer->name ?? __('Unknown') }}
                                        <span class="font-bold text-white">vs</span>
                                        {{ $match->away_player_name ?? $match->awayPlayer->name ?? __('Unknown') }}
                                    </div>
                                </div>
                                <div class="mt-2 md:mt-0 text-right">
                                    <div class="text-2xl font-bold text-blue-400">
                                        @php
                                            $homeSetsWon = 0;
                                            $awaySetsWon = 0;
                                            if($match->sets && is_array($match->sets)) {
                                                foreach($match->sets as $set) {
                                                    if(($set['home_score'] ?? 0) > ($set['away_score'] ?? 0)) {
                                                        $homeSetsWon++;
                                                    } elseif(($set['away_score'] ?? 0) > ($set['home_score'] ?? 0)) {
                                                        $awaySetsWon++;
                                                    }
                                                }
                                            }
                                        @endphp
                                        {{ $homeSetsWon }} : {{ $awaySetsWon }}
                                    </div>
                                    <a href="{{ route('organizations.friendly-matches.show', [$organization, $match->id]) }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors mt-2 inline-block">{{ __('View Match') }} →</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-gray-400 text-center py-6">{{ __('No friendly matches played yet.') }}</div>
                @endif
            </div>
        </div>

        <script>
            document.getElementById('league-tab').addEventListener('click', function() {
                showTab('league');
            });
            document.getElementById('friendly-tab').addEventListener('click', function() {
                showTab('friendly');
            });

            function showTab(type) {
                // Hide all tabs
                document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
                document.getElementById('league-tab').classList.remove('bg-purple-600', 'text-white');
                document.getElementById('league-tab').classList.add('text-gray-400', 'hover:text-white');
                document.getElementById('friendly-tab').classList.remove('bg-purple-600', 'text-white');
                document.getElementById('friendly-tab').classList.add('text-gray-400', 'hover:text-white');

                // Show selected tab
                document.getElementById(type + '-matches').classList.remove('hidden');
                document.getElementById(type + '-tab').classList.add('bg-purple-600', 'text-white');
                document.getElementById(type + '-tab').classList.remove('text-gray-400', 'hover:text-white');
            }
        </script>
    </div>
</div>
</x-app-layout>