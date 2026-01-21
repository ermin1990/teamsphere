<!-- Live and Upcoming Matches for Projector -->
@php
    $liveMatches = collect();
    $upcomingMatches = collect();
    
    if($competition->type === 'league') {
        $matches = $competition->is_team_based ? ($competition->teamMatches ?? collect()) : ($competition->leagueMatches ?? collect());
        $liveMatches = collect($matches)->where('status', 'in_progress');
        $upcomingMatches = collect($matches)->where('status', 'scheduled')->sortBy('scheduled_at')->take(8);
    } else {
        $matches = $competition->matches ?? collect();
        $liveMatches = collect($matches)->where('status', 'in_progress');
        $upcomingMatches = collect($matches)->where('status', 'scheduled')->sortBy('round_number')->take(8);
    }
@endphp

@if(count($liveMatches) > 0 || count($upcomingMatches) > 0)
<div class="space-y-6">
    <!-- Live Matches -->
    @if(count($liveMatches) > 0)
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-red-500/50 shadow-2xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold text-white flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-red-500 rounded-full animate-pulse"></div>
                    <span class="text-red-400">UŽIVO</span>
                </div>
            </h2>
            <div class="text-right">
                <p class="text-gray-400 text-sm">{{ $competition->name }}</p>
                <p class="text-gray-500 text-xs">{{ $competition->organization->name }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 {{ count($liveMatches) > 1 ? 'lg:grid-cols-2' : '' }} gap-4">
            @foreach($liveMatches as $match)
            <div class="bg-gradient-to-br from-red-900/40 to-gray-900/40 rounded-xl p-6 border-2 border-red-500/50 shadow-xl">
                <!-- Match Header -->
                <div class="text-center mb-4">
                    @if($competition->type === 'tournament' && $match->tournamentGroup)
                        <span class="inline-block px-3 py-1 bg-purple-500/20 text-purple-400 rounded-full text-sm font-semibold">
                            Grupa {{ $match->tournamentGroup->name }}
                        </span>
                    @else
                        <span class="inline-block px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm font-semibold">
                            Kolo {{ $match->round ?? $match->round_number }}
                        </span>
                    @endif
                </div>

                <!-- Players/Teams -->
                <div class="space-y-4">
                    <!-- Home -->
                    <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg">
                        <div class="flex-1">
                            <p class="text-white font-bold text-xl truncate">
                                @if($competition->is_team_based)
                                    {{ $match->homeTeam->name ?? 'TBD' }}
                                @else
                                    {{ $match->homePlayer->name ?? 'TBD' }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right ml-4">
                            <span class="text-white font-black text-3xl">{{ $match->home_score ?? 0 }}</span>
                        </div>
                    </div>

                    <!-- VS Separator -->
                    <div class="text-center">
                        <span class="text-gray-500 font-bold text-xl">VS</span>
                    </div>

                    <!-- Away -->
                    <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg">
                        <div class="flex-1">
                            <p class="text-white font-bold text-xl truncate">
                                @if($competition->is_team_based)
                                    {{ $match->awayTeam->name ?? 'TBD' }}
                                @else
                                    {{ $match->awayPlayer->name ?? 'TBD' }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right ml-4">
                            <span class="text-white font-black text-3xl">{{ $match->away_score ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Match Info -->
                @if($match->started_at)
                <div class="mt-4 text-center">
                    <p class="text-gray-400 text-sm">
                        Počeo: {{ $match->started_at->format('H:i') }}
                    </p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Upcoming Matches -->
    @if(count($upcomingMatches) > 0)
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-2xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold text-white flex items-center gap-3">
                <span class="text-4xl">📅</span>
                Naredni mečevi
            </h2>
            <div class="text-right">
                <p class="text-gray-400 text-sm">{{ $competition->name }}</p>
                <p class="text-gray-500 text-xs">{{ $competition->organization->name }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($upcomingMatches as $match)
            <div class="bg-gray-700/30 rounded-xl p-4 border border-gray-600/50 hover:border-blue-500/50 transition-all">
                <!-- Match Header -->
                <div class="flex items-center justify-between mb-3">
                    @if($competition->type === 'tournament' && $match->tournamentGroup)
                        <span class="px-2 py-1 bg-purple-500/20 text-purple-400 rounded text-xs font-semibold">
                            Grupa {{ $match->tournamentGroup->name }}
                        </span>
                    @else
                        <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold">
                            Kolo {{ $match->round ?? $match->round_number }}
                        </span>
                    @endif
                    @if($match->scheduled_at)
                        <span class="text-gray-400 text-xs">
                            {{ $match->scheduled_at->format('d.m. H:i') }}
                        </span>
                    @endif
                </div>

                <!-- Players/Teams -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-white font-semibold text-lg truncate flex-1">
                            @if($competition->is_team_based)
                                {{ $match->homeTeam->name ?? 'TBD' }}
                            @else
                                {{ $match->homePlayer->name ?? 'TBD' }}
                            @endif
                        </p>
                        <span class="text-gray-500 ml-2">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-white font-semibold text-lg truncate flex-1">
                            @if($competition->is_team_based)
                                {{ $match->awayTeam->name ?? 'TBD' }}
                            @else
                                {{ $match->awayPlayer->name ?? 'TBD' }}
                            @endif
                        </p>
                        <span class="text-gray-500 ml-2">-</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif
