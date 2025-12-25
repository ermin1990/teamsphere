@if($competition->is_team_based)
    <div class="space-y-6">
        <!-- Team Standings -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50">
            <h3 class="text-xl font-bold text-white mb-4">Tabela Ekipa</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Poz</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Ekipa</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">OU</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">P</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">I</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Bod</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($competition->standings->sortByDesc('points') as $index => $standing)
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">{{ $index + 1 }}.</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-white">{{ $standing->team->name }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-300">{{ $standing->played }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-300">{{ $standing->won }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-300">{{ $standing->lost }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-blue-400">{{ $standing->points }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Team Matches -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50">
            <h3 class="text-xl font-bold text-white mb-4">Raspored i Rezultati</h3>
            <div class="space-y-4">
                @foreach($competition->teamMatches->sortBy('round') as $index => $match)
                <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700/30 flex items-center justify-between">
                    <div class="flex-1 text-right pr-4">
                        <span class="text-white font-medium">{{ $match->homeTeam->name }}</span>
                    </div>
                    <div class="flex flex-col items-center px-4 min-w-[100px]">
                        <div class="text-xs text-gray-500 mb-1">Meč {{ $index + 1 }}</div>
                        @if($match->status === 'scheduled')
                            <a href="{{ route('organizations.competitions.team-matches.protocol', [$organization, $competition, $match]) }}" class="bg-blue-600/20 text-blue-400 px-3 py-1 rounded-lg text-xs font-bold hover:bg-blue-600/30 transition">
                                PROTOKOL
                            </a>
                        @else
                            <a href="{{ route('organizations.competitions.team-matches.show', [$organization, $competition, $match]) }}" class="text-2xl font-black text-white hover:text-blue-400 transition">
                                {{ $match->home_score }} : {{ $match->away_score }}
                            </a>
                        @endif
                    </div>
                    <div class="flex-1 text-left pl-4">
                        <span class="text-white font-medium">{{ $match->awayTeam->name }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50">
        <h3 class="text-2xl font-bold text-white mb-4">🏆 Liga</h3>
        <p class="text-gray-400">Liga sadržaj će biti dodat uskoro.</p>
    </div>
@endif
