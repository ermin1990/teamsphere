<!-- League Info -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
        <h3 class="text-xl font-bold text-white mb-4">League Details</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-400">Sport:</span>
                <span class="text-white">{{ $competition->sport->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Type:</span>
                <span class="text-white">{{ $competition->is_team_based ? 'Team League' : 'Individual League' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Status:</span>
                <span class="text-white">{{ ucfirst($competition->status) }}</span>
            </div>
        </div>
    </div>

    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
        <h3 class="text-xl font-bold text-white mb-4">Statistics</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-400">Total Matches:</span>
                <span class="text-white">{{ $competition->matches->count() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Completed:</span>
                <span class="text-white">{{ $competition->matches->where('status', 'completed')->count() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">In Progress:</span>
                <span class="text-white">{{ $competition->matches->where('status', 'in_progress')->count() }}</span>
            </div>
        </div>
    </div>

    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
        <h3 class="text-xl font-bold text-white mb-4">Live Matches</h3>
        @php
            $liveMatches = $competition->matches->where('status', 'in_progress');
        @endphp
        @if($liveMatches->count() > 0)
            <div class="space-y-2">
                @foreach($liveMatches as $match)
                <a href="{{ route('public.matches.show', [$competition, $match]) }}"
                   class="block p-3 bg-green-600/20 hover:bg-green-600/30 rounded-lg transition-colors">
                    <div class="text-sm font-semibold text-green-400">
                        Round {{ $match->round }}
                    </div>
                    <div class="text-xs text-gray-300">
                        @if($competition->is_team_based)
                            {{ $match->homeTeam->name ?? 'TBD' }} vs {{ $match->awayTeam->name ?? 'TBD' }}
                        @else
                            {{ $match->homePlayer->name ?? 'TBD' }} vs {{ $match->awayPlayer->name ?? 'TBD' }}
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-sm">No live matches at the moment</p>
        @endif
    </div>
</div>

<!-- Standings -->
@if($competition->standings->count() > 0)
<div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 border border-gray-700/50 shadow-xl mb-8">
    <h3 class="text-sm font-semibold text-gray-300 mb-4 uppercase tracking-wide">League Standings</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-1.5 px-2 text-gray-400 font-medium">#</th>
                    <th class="text-left py-1.5 px-2 text-gray-400 font-medium">Player</th>
                    <th class="text-center py-1.5 px-2 text-gray-400 font-medium">P</th>
                    <th class="text-center py-1.5 px-2 text-gray-400 font-medium">W</th>
                    <th class="text-center py-1.5 px-2 text-gray-400 font-medium">D</th>
                    <th class="text-center py-1.5 px-2 text-gray-400 font-medium">L</th>
                    <th class="text-center py-1.5 px-2 text-gray-400 font-medium">Pts</th>
                </tr>
            </thead>
            <tbody>
                @foreach($competition->standings->sortByDesc('points') as $index => $standing)
                <tr class="border-b border-gray-700/30 hover:bg-gray-700/10">
                    <td class="py-1.5 px-2 text-white font-medium">{{ $index + 1 }}</td>
                    <td class="py-1.5 px-2 text-white text-sm">{{ $standing->player->name }}</td>
                    <td class="py-1.5 px-2 text-center text-gray-300">{{ $standing->played }}</td>
                    <td class="py-1.5 px-2 text-center text-green-400">{{ $standing->won }}</td>
                    <td class="py-1.5 px-2 text-center text-yellow-400">{{ $standing->drawn }}</td>
                    <td class="py-1.5 px-2 text-center text-red-400">{{ $standing->lost }}</td>
                    <td class="py-1.5 px-2 text-center text-blue-400 font-bold">{{ $standing->points }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Matches -->
@if($competition->matches->count() > 0)
<div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 border border-gray-700/50 shadow-xl mb-8">
    <h3 class="text-sm font-semibold text-gray-300 mb-4 uppercase tracking-wide">Recent Matches</h3>
    <div class="space-y-2">
        @foreach($competition->matches->where('status', 'completed')->sortByDesc('played_at')->take(10) as $match)
        <a href="{{ route('public.matches.show', [$competition, $match]) }}"
           class="block p-2 bg-gray-700/20 hover:bg-gray-700/30 rounded-md transition-colors">
            <div class="flex items-center justify-between">
                <div class="flex-1 truncate pr-2">
                    <span class="text-gray-300 text-sm">
                        @if($competition->is_team_based)
                            {{ $match->homeTeam->name ?? 'TBD' }}
                        @else
                            {{ $match->homePlayer->name ?? 'TBD' }}
                        @endif
                    </span>
                </div>
                <div class="flex-shrink-0 px-2">
                    <span class="font-bold text-white text-sm">{{ $match->home_score }}-{{ $match->away_score }}</span>
                </div>
                <div class="flex-1 truncate pl-2 text-right">
                    <span class="text-gray-300 text-sm">
                        @if($competition->is_team_based)
                            {{ $match->awayTeam->name ?? 'TBD' }}
                        @else
                            {{ $match->awayPlayer->name ?? 'TBD' }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="text-center text-xs text-gray-500 mt-1">
                {{ $match->played_at ? $match->played_at->format('d.m. H:i') : 'TBD' }}
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

<!-- Upcoming Matches -->
@php
    $upcomingMatches = $competition->matches->where('status', 'scheduled')->sortBy('scheduled_at')->take(5);
@endphp
@if($upcomingMatches->count() > 0)
<div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 border border-gray-700/50 shadow-xl mb-8">
    <h3 class="text-sm font-semibold text-gray-300 mb-4 uppercase tracking-wide">Upcoming Matches</h3>
    <div class="space-y-2">
        @foreach($upcomingMatches as $match)
        <div class="p-2 bg-gray-700/20 rounded-md">
            <div class="flex items-center justify-between">
                <div class="flex-1 truncate pr-2">
                    <span class="text-gray-300 text-sm">
                        @if($competition->is_team_based)
                            {{ $match->homeTeam->name ?? 'TBD' }}
                        @else
                            {{ $match->homePlayer->name ?? 'TBD' }}
                        @endif
                    </span>
                </div>
                <div class="flex-shrink-0 px-2">
                    <span class="text-gray-500 text-sm">vs</span>
                </div>
                <div class="flex-1 truncate pl-2 text-right">
                    <span class="text-gray-300 text-sm">
                        @if($competition->is_team_based)
                            {{ $match->awayTeam->name ?? 'TBD' }}
                        @else
                            {{ $match->awayPlayer->name ?? 'TBD' }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="text-center text-xs text-gray-500 mt-1">
                {{ $match->scheduled_at ? $match->scheduled_at->format('d.m. H:i') : 'TBD' }}
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif