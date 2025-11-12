<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-green-900 to-emerald-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">📊 Tabela Turnira</h1>
                        <p class="text-gray-300">{{ $competition->name }}</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('organizations.competitions.futsal.schedule', [$organization, $competition]) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors text-center">
                            📅 Raspored
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

            @if($groups->isEmpty())
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-white mb-2">Nema tabela</h3>
                <p class="text-gray-400">Tabele će biti dostupne nakon što se grupe kreiraju i utakmice odigraju.</p>
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

                    @if($group->futsalStandings->isEmpty())
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-4">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-400">Nema rezultata</p>
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-600">
                                    <th class="text-left text-gray-400 font-medium py-3 px-2">#</th>
                                    <th class="text-left text-gray-400 font-medium py-3 px-2">Tim</th>
                                    <th class="text-center text-gray-400 font-medium py-3 px-2">U</th>
                                    <th class="text-center text-gray-400 font-medium py-3 px-2">P</th>
                                    <th class="text-center text-gray-400 font-medium py-3 px-2">N</th>
                                    <th class="text-center text-gray-400 font-medium py-3 px-2">I</th>
                                    <th class="text-center text-gray-400 font-medium py-3 px-2">G</th>
                                    <th class="text-center text-gray-400 font-medium py-3 px-2">B</th>
                                    <th class="text-center text-gray-400 font-medium py-3 px-2">GR</th>
                                    <th class="text-center text-gray-400 font-medium py-3 px-2">PTS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($group->futsalStandings->sortBy('position') as $standing)
                                <tr class="hover:bg-gray-700/30 transition-colors">
                                    <!-- Position -->
                                    <td class="py-3 px-2">
                                        <div class="flex items-center">
                                            <span class="text-white font-bold w-6 text-center">{{ $standing->position }}</span>
                                        </div>
                                    </td>

                                    <!-- Team -->
                                    <td class="py-3 px-2">
                                        <div class="flex items-center space-x-3">
                                            @if($standing->futsalTeam->logo)
                                            <img src="{{ Storage::url($standing->futsalTeam->logo) }}"
                                                 alt="{{ $standing->futsalTeam->name }}"
                                                 class="w-8 h-8 rounded-full object-cover">
                                            @else
                                            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-xs">⚽</span>
                                            </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <p class="text-white font-medium truncate">{{ $standing->futsalTeam->name }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Played -->
                                    <td class="text-center py-3 px-2">
                                        <span class="text-gray-300">{{ $standing->played }}</span>
                                    </td>

                                    <!-- Won -->
                                    <td class="text-center py-3 px-2">
                                        <span class="text-green-400">{{ $standing->won }}</span>
                                    </td>

                                    <!-- Drawn -->
                                    <td class="text-center py-3 px-2">
                                        <span class="text-yellow-400">{{ $standing->drawn }}</span>
                                    </td>

                                    <!-- Lost -->
                                    <td class="text-center py-3 px-2">
                                        <span class="text-red-400">{{ $standing->lost }}</span>
                                    </td>

                                    <!-- Goals For -->
                                    <td class="text-center py-3 px-2">
                                        <span class="text-gray-300">{{ $standing->goals_for }}</span>
                                    </td>

                                    <!-- Goals Against -->
                                    <td class="text-center py-3 px-2">
                                        <span class="text-gray-300">{{ $standing->goals_against }}</span>
                                    </td>

                                    <!-- Goal Difference -->
                                    <td class="text-center py-3 px-2">
                                        <span class="{{ $standing->goal_difference >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                            {{ $standing->goal_difference > 0 ? '+' : '' }}{{ $standing->goal_difference }}
                                        </span>
                                    </td>

                                    <!-- Points -->
                                    <td class="text-center py-3 px-2">
                                        <span class="text-white font-bold">{{ $standing->points }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="mt-4 pt-4 border-t border-gray-600">
                        <div class="flex flex-wrap gap-4 text-xs text-gray-400">
                            <span>U = Utakmice</span>
                            <span>P = Pobjede</span>
                            <span>N = Neriješeno</span>
                            <span>I = Izgubljeno</span>
                            <span>G = Golovi</span>
                            <span>B = Primljeni golovi</span>
                            <span>GR = Gol razlika</span>
                            <span>PTS = Bodovi</span>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Qualification Info -->
            @if($competition->current_phase === 'groups')
            <div class="mt-8 bg-blue-500/10 border border-blue-500/20 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-blue-400 font-medium mb-1">Kvalifikacije za eliminacionu fazu</p>
                        <p class="text-blue-300 text-sm">
                            Prva {{ $competition->players_advancing_per_group ?? 2 }} tima iz svake grupe prolaze u eliminacionu fazu.
                            @if($competition->players_advancing_per_group == 2)
                                To su pobjednici grupa i drugoplasirani timovi.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endif
            @endif

        </div>
    </div>
</x-app-layout>