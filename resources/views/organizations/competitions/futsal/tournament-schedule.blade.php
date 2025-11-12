<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-green-900 to-emerald-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">⚽ Raspored Turnira</h1>
                        <p class="text-gray-300">{{ $competition->name }}</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('organizations.competitions.futsal.standings', [$organization, $competition]) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors text-center">
                            📊 Tabela
                        </a>
                        <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors text-center">
                            Nazad
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                <p class="text-green-400">{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                <p class="text-red-400">{{ session('error') }}</p>
            </div>
            @endif

            <!-- Phase Navigation -->
            <div class="mb-6 bg-gray-800/50 backdrop-blur-xl rounded-2xl p-4 border border-gray-700/50 shadow-xl">
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full {{ $competition->current_phase === 'groups' ? 'bg-green-500' : 'bg-gray-600' }}"></div>
                        <span class="text-white text-sm">Grupna Faza</span>
                    </div>
                    <div class="w-8 h-px bg-gray-600"></div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full {{ $competition->current_phase === 'knockout' ? 'bg-green-500' : 'bg-gray-600' }}"></div>
                        <span class="text-white text-sm">Eliminaciona Faza</span>
                    </div>
                    <div class="w-8 h-px bg-gray-600"></div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full {{ $competition->current_phase === 'completed' ? 'bg-green-500' : 'bg-gray-600' }}"></div>
                        <span class="text-white text-sm">Završeno</span>
                    </div>
                </div>
            </div>

            <!-- Group Stage Matches -->
            @if($competition->current_phase === 'groups' || $competition->current_phase === 'knockout')
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-white mb-6">Grupna Faza</h2>

                @if($groups->isEmpty())
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">Nema grupa</h3>
                    <p class="text-gray-400">Grupe još nisu kreirane za ovaj turnir.</p>
                </div>
                @else
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($groups as $group)
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-semibold text-white">Grupa {{ $group->name }}</h3>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 bg-green-600/20 text-green-400 rounded-full text-sm">
                                    {{ $group->futsalStandings->count() }} timova
                                </span>
                            </div>
                        </div>

                        @if($group->futsalMatches->isEmpty())
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-400">Nema zakazanih utakmica</p>
                        </div>
                        @else
                        <div class="space-y-4">
                            @php
                                $matchesByRound = $group->futsalMatches->groupBy('round');
                            @endphp

                            @foreach($matchesByRound as $round => $roundMatches)
                            <div class="bg-gray-700/30 rounded-lg p-4">
                                <h4 class="text-green-400 font-medium mb-3">Kolo {{ $round }}</h4>
                                <div class="space-y-3">
                                    @foreach($roundMatches as $match)
                                    <div class="bg-gray-600/30 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3 flex-1">
                                                <!-- Home Team -->
                                                <div class="flex items-center space-x-2 flex-1">
                                                    @if($match->homeTeam->logo)
                                                    <img src="{{ Storage::url($match->homeTeam->logo) }}"
                                                         alt="{{ $match->homeTeam->name }}"
                                                         class="w-8 h-8 rounded-full object-cover">
                                                    @else
                                                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white font-bold text-xs">⚽</span>
                                                    </div>
                                                    @endif
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-white font-medium text-sm truncate">{{ $match->homeTeam->name }}</p>
                                                    </div>
                                                </div>

                                                <!-- Score -->
                                                <div class="flex items-center space-x-2 px-3">
                                                    @if($match->status === 'completed')
                                                    <span class="text-white font-bold text-lg">{{ $match->home_score ?? 0 }}</span>
                                                    <span class="text-gray-400">-</span>
                                                    <span class="text-white font-bold text-lg">{{ $match->away_score ?? 0 }}</span>
                                                    @else
                                                    <span class="text-gray-400 text-sm">vs</span>
                                                    @endif
                                                </div>

                                                <!-- Away Team -->
                                                <div class="flex items-center space-x-2 flex-1 justify-end">
                                                    <div class="min-w-0 flex-1 text-right">
                                                        <p class="text-white font-medium text-sm truncate">{{ $match->awayTeam->name }}</p>
                                                    </div>
                                                    @if($match->awayTeam->logo)
                                                    <img src="{{ Storage::url($match->awayTeam->logo) }}"
                                                         alt="{{ $match->awayTeam->name }}"
                                                         class="w-8 h-8 rounded-full object-cover">
                                                    @else
                                                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white font-bold text-xs">⚽</span>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="ml-4 text-right">
                                                @if($match->status === 'completed')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-600/20 text-green-400">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Završeno
                                                </span>
                                                @elseif($match->status === 'scheduled')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-600/20 text-blue-400">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Zakazano
                                                </span>
                                                @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-600/20 text-gray-400">
                                                    {{ ucfirst($match->status) }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($match->status === 'completed' && $match->completed_at)
                                        <div class="mt-2 text-xs text-gray-400 text-center">
                                            Završeno: {{ $match->completed_at->format('d.m.Y H:i') }}
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            <!-- Knockout Stage Matches -->
            @if($competition->current_phase === 'knockout' && $knockoutMatches->isNotEmpty())
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-white">Eliminaciona Faza</h2>
                    @if($competition->current_phase === 'knockout')
                    <div class="flex space-x-2">
                        <form method="POST" action="{{ route('organizations.competitions.futsal.advance-knockout', [$organization, $competition]) }}" class="inline">
                            @csrf
                            <input type="hidden" name="current_round" value="{{ collect($knockoutMatches->keys())->max() }}">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                ➡️ Sljedeće Kolo
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <div class="space-y-6">
                    @foreach($knockoutMatches as $round => $matches)
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-semibold text-white">
                                @if($round == 1)
                                    Polufinale
                                @elseif($round == 2)
                                    Finale
                                @else
                                    Kolo {{ $round }}
                                @endif
                            </h3>
                            <span class="px-3 py-1 bg-blue-600/20 text-blue-400 rounded-full text-sm">
                                {{ $matches->count() }} utakmic{{ $matches->count() === 1 ? 'a' : ($matches->count() < 5 ? 'e' : 'a') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($matches as $match)
                            <div class="bg-gray-700/30 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-gray-400 text-sm">Utakmica {{ $match->match_order ?? $loop->iteration }}</span>
                                    @if($match->status === 'completed')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-600/20 text-green-400">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Završeno
                                    </span>
                                    @elseif($match->status === 'scheduled')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-600/20 text-blue-400">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Zakazano
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-600/20 text-gray-400">
                                        {{ ucfirst($match->status) }}
                                    </span>
                                    @endif
                                </div>

                                <div class="space-y-3">
                                    <!-- Home Team -->
                                    <div class="flex items-center justify-between p-3 bg-gray-600/30 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            @if($match->homeTeam->logo)
                                            <img src="{{ Storage::url($match->homeTeam->logo) }}"
                                                 alt="{{ $match->homeTeam->name }}"
                                                 class="w-10 h-10 rounded-full object-cover">
                                            @else
                                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">⚽</span>
                                            </div>
                                            @endif
                                            <div>
                                                <p class="text-white font-medium">{{ $match->homeTeam->name }}</p>
                                                @if($match->homeTeam->futsalStandings->where('competition_id', $competition->id)->first())
                                                <p class="text-xs text-gray-400">
                                                    Grupa {{ $match->homeTeam->futsalStandings->where('competition_id', $competition->id)->first()->tournamentGroup->name ?? 'N/A' }}
                                                </p>
                                                @endif
                                            </div>
                                        </div>
                                        @if($match->status === 'completed')
                                        <span class="text-2xl font-bold text-white">{{ $match->home_score ?? 0 }}</span>
                                        @endif
                                    </div>

                                    <!-- VS -->
                                    <div class="text-center">
                                        <span class="text-gray-400 text-sm">vs</span>
                                    </div>

                                    <!-- Away Team -->
                                    <div class="flex items-center justify-between p-3 bg-gray-600/30 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            @if($match->awayTeam->logo)
                                            <img src="{{ Storage::url($match->awayTeam->logo) }}"
                                                 alt="{{ $match->awayTeam->name }}"
                                                 class="w-10 h-10 rounded-full object-cover">
                                            @else
                                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">⚽</span>
                                            </div>
                                            @endif
                                            <div>
                                                <p class="text-white font-medium">{{ $match->awayTeam->name }}</p>
                                                @if($match->awayTeam->futsalStandings->where('competition_id', $competition->id)->first())
                                                <p class="text-xs text-gray-400">
                                                    Grupa {{ $match->awayTeam->futsalStandings->where('competition_id', $competition->id)->first()->tournamentGroup->name ?? 'N/A' }}
                                                </p>
                                                @endif
                                            </div>
                                        </div>
                                        @if($match->status === 'completed')
                                        <span class="text-2xl font-bold text-white">{{ $match->away_score ?? 0 }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if($match->status === 'completed' && $match->completed_at)
                                <div class="mt-3 text-xs text-gray-400 text-center">
                                    Završeno: {{ $match->completed_at->format('d.m.Y H:i') }}
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Generate Knockout Button -->
            @if($competition->current_phase === 'groups' && $groups->isNotEmpty() && $knockoutMatches->isEmpty())
            <div class="text-center">
                <form method="POST" action="{{ route('organizations.competitions.futsal.generate-knockout', [$organization, $competition]) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors font-semibold">
                        🏆 Generiši Eliminacionu Fazu
                    </button>
                </form>
                <p class="text-gray-400 text-sm mt-2">Ovo će kreirati knockout utakmice na osnovu rezultata grupne faze</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>