<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Detalji Meča
                </h2>
                <p class="text-gray-400 mt-1">{{ $competition->name }} • {{ $match->phase ? ucfirst($match->phase) : 'Turnir' }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-2 py-1 text-xs sm:text-sm rounded-full whitespace-nowrap overflow-hidden text-ellipsis max-w-32 sm:max-w-none
                    @if($match->status === 'completed') bg-green-500/20 text-green-400
                    @elseif($match->status === 'in_progress') bg-yellow-500/20 text-yellow-400
                    @elseif($match->status === 'forfeited') bg-red-500/20 text-red-400
                    @elseif($match->status === 'cancelled') bg-orange-500/20 text-orange-400
                    @else bg-gray-500/20 text-gray-400 @endif"
                >
                    {{ ucfirst(str_replace('_', ' ', $match->status)) }}
                    @if($match->status === 'forfeited' && $match->forfeited_by)
                        - {{ $match->forfeited_by === 'home' ? ($competition->is_team_based ? $match->homeTeam->name : $match->homePlayer->name) : ($competition->is_team_based ? $match->awayTeam->name : $match->awayPlayer->name) }} Forfeited
                    @endif
                </span>
                <div class="flex space-x-2">
                    @if((isset($isOwner) && $isOwner) || (isset($isReferee) && $isReferee))
                    <a href="{{ route('referee.competition.match.edit', [$competition, $match]) }}"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                        ✏️ Edit Results
                    </a>
                    @if($match->status !== 'completed')
                    <a href="{{ route('referee.competition.match.live', [$competition, $match]) }}"
                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors">
                        🎯 Live Score
                    </a>
                    @endif
                    @endif
                    <button onclick="shareMatch()"
                       class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-lg transition-colors">
                        📤 Share
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">

                <!-- Match Header -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                    <div class="text-center">
                        <div class="flex items-center justify-center space-x-8 mb-6">
                            <!-- Home Player/Team -->
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-white font-bold text-lg">
                                        @if($competition->is_team_based)
                                            {{ substr($match->homeTeam?->name ?? 'TBD', 0, 2) }}
                                        @else
                                            {{ substr($match->homePlayer?->name ?? 'TBD', 0, 2) }}
                                        @endif
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold text-white">
                                    @if($competition->is_team_based)
                                        {{ $match->homeTeam?->name ?? 'TBD' }}
                                    @else
                                        {{ $match->homePlayer?->name ?? 'TBD' }}
                                    @endif
                                </h3>
                                <div id="home-score" class="text-4xl font-bold text-blue-400 mt-2">{{ $match->home_score ?? 0 }}</div>
                            </div>

                            <!-- VS -->
                            <div class="text-center">
                                <div class="text-gray-400 text-sm mb-2">VS</div>
                                <div class="text-gray-500 text-xs">
                                    @if($match->table)
                                        Stol {{ $match->table->number }}
                                    @else
                                        Bez stola
                                    @endif
                                </div>
                                @if($match->referee)
                                    <div class="text-gray-500 text-xs mt-1">
                                        Sudija: {{ $match->referee->name }}
                                    </div>
                                @endif
                            </div>

                            <!-- Away Player/Team -->
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="text-white font-bold text-lg">
                                        @if($competition->is_team_based)
                                            {{ substr($match->awayTeam?->name ?? 'TBD', 0, 2) }}
                                        @else
                                            {{ substr($match->awayPlayer?->name ?? 'TBD', 0, 2) }}
                                        @endif
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold text-white">
                                    @if($competition->is_team_based)
                                        {{ $match->awayTeam?->name ?? 'TBD' }}
                                    @else
                                        {{ $match->awayPlayer?->name ?? 'TBD' }}
                                    @endif
                                </h3>
                                <div id="away-score" class="text-4xl font-bold text-red-400 mt-2">{{ $match->away_score ?? 0 }}</div>
                            </div>
                        </div>

                        <!-- Match Info -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-700/50">
                            <div class="text-center">
                                <div class="text-gray-400 text-sm">Status</div>
                                <div class="text-white font-medium">{{ ucfirst(str_replace('_', ' ', $match->status)) }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-400 text-sm">Vrijeme</div>
                                <div class="text-white font-medium">
                                    @if($match->played_at)
                                        {{ $match->played_at->format('d.m.Y H:i') }}
                                    @elseif($match->scheduled_at)
                                        {{ $match->scheduled_at->format('d.m.Y H:i') }}
                                    @else
                                        Nije zakazano
                                    @endif
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-400 text-sm">Runda</div>
                                <div class="text-white font-medium">{{ $match->round ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sets Details -->
                @if($match->sets && count($match->sets) > 0)
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-lg font-semibold text-white mb-4">Setovi</h3>
                    <div class="space-y-3">
                        @foreach($match->sets as $index => $set)
                        <div class="flex items-center justify-between p-3 bg-gray-700/30 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-white">Set {{ $index + 1 }}</div>
                                    <div class="text-xs text-gray-400">{{ $set['duration'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-white">
                                    {{ $set['home_score'] ?? $set['home'] ?? 0 }} - {{ $set['away_score'] ?? $set['away'] ?? 0 }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Team Players (if team-based) -->
                @if($competition->is_team_based && $match->homeTeam && $match->awayTeam)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Home Team Players -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-4">{{ $match->homeTeam->name }}</h3>
                        <div class="space-y-2">
                            @forelse($match->homeTeam->players ?? [] as $player)
                            <div class="flex items-center space-x-3 p-2 bg-gray-700/30 rounded-lg">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ substr($player->name, 0, 1) }}</span>
                                </div>
                                <div class="text-sm text-white">{{ $player->name }}</div>
                            </div>
                            @empty
                            <div class="text-gray-400 text-sm">Nema igrača</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Away Team Players -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-4">{{ $match->awayTeam->name }}</h3>
                        <div class="space-y-2">
                            @forelse($match->awayTeam->players ?? [] as $player)
                            <div class="flex items-center space-x-3 p-2 bg-gray-700/30 rounded-lg">
                                <div class="w-8 h-8 bg-gradient-to-r from-red-500 to-red-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ substr($player->name, 0, 1) }}</span>
                                </div>
                                <div class="text-sm text-white">{{ $player->name }}</div>
                            </div>
                            @empty
                            <div class="text-gray-400 text-sm">Nema igrača</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                @if((isset($isOwner) && $isOwner) || (isset($isReferee) && $isReferee))
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-lg font-semibold text-white mb-4">Akcije</h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('referee.competition.match.edit', [$competition, $match]) }}"
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                            ✏️ Edit Results
                        </a>
                        @if($match->status !== 'completed')
                        <a href="{{ route('referee.competition.match.live', [$competition, $match]) }}"
                           class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors">
                            🎯 Live Score
                        </a>
                        @endif
                        <form method="POST" action="{{ route('referee.competition.match.reset', [$competition, $match]) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    onclick="return confirm('Da li ste sigurni da želite resetovati ovaj meč?')"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition-colors">
                                🔄 Reset Match
                            </button>
                        </form>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <script>
        function shareMatch() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('Link meča je kopiran u clipboard!');
            });
        }

        // Auto-refresh for live matches
        @if($match->status === 'in_progress')
        setInterval(() => {
            fetch('{{ route("public.api.match", $match->id) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const matchData = data.data;

                        // Update scores
                        document.getElementById('home-score').textContent = matchData.home_score ?? 0;
                        document.getElementById('away-score').textContent = matchData.away_score ?? 0;

                        // Update status if changed
                        if (matchData.status !== '{{ $match->status }}') {
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating match:', error);
                });
        }, 5000);
        @endif
    </script>
</x-app-layout>