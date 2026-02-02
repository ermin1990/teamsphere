<!-- Tournament Standings for Projector -->
@php
    // If a specific group is selected, show only that group
    $groupsToDisplay = isset($selectedGroup) && $selectedGroup 
        ? collect([$selectedGroup]) 
        : collect($competition->tournamentGroups ?? []);
@endphp

@if($groupsToDisplay->isNotEmpty())
<div class="tournament-standings-container space-y-8">
    @foreach($groupsToDisplay as $group)
        @php
            // Load standings from database via relationship - matching public view logic
            $groupStandings = \App\Models\Standing::where('tournament_group_id', $group->id)
                ->orderBy('position')
                ->with('player')
                ->get();
        @endphp
        @if($groupStandings->count() > 0)
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-2xl">
    <!-- Zoom Controls -->
    <div class="flex justify-center mb-2 opacity-20 hover:opacity-100 transition-opacity duration-300">
        <div class="flex items-center gap-1 scale-75 transform origin-center">
            <button type="button" onclick="window.changeZoom(-0.05)" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-base font-bold backdrop-blur-sm border border-gray-600/50" title="Smanji Prikaz">−</button>
            <button type="button" onclick="window.resetZoom()" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-xs font-bold backdrop-blur-sm border border-gray-600/50" title="Resetuj Prikaz">🔄</button>
            <button type="button" onclick="window.changeZoom(0.05)" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-base font-bold backdrop-blur-sm border border-gray-600/50" title="Povećaj Prikaz">+</button>
            
            <div class="w-px h-6 bg-gray-600/50 mx-1"></div>

            <button type="button" onclick="window.changePlayerFont(-1)" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-xs font-bold backdrop-blur-sm border border-gray-600/50" title="Manji Font Igrača">A-</button>
            <button type="button" onclick="window.resetPlayerFont()" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-xs font-bold backdrop-blur-sm border border-gray-600/50" title="Resetuj Font">A</button>
            <button type="button" onclick="window.changePlayerFont(1)" class="px-2 py-1 rounded bg-gray-800/60 hover:bg-gray-700/60 text-white text-xs font-bold backdrop-blur-sm border border-gray-600/50" title="Veći Font Igrača">A+</button>
        </div>
    </div>            <!-- Group Header -->
            <div class="mb-6 overflow-hidden">
                <h2 class="group-title text-3xl font-bold text-white flex items-center gap-3" style="flex-wrap: nowrap;">
                    <span class="text-4xl flex-shrink-0">🏆</span>
                    <span class="truncate">Grupa {{ $group->name }} - {{ $competition->name }}</span>
                </h2>
            </div>
            
            <!-- Group Layout: Table Top, Matches Bottom (for better space on projector) -->
            <div class="flex flex-col {{ ($resolution ?? 'full') === '1024x768' ? 'gap-6' : 'gap-10' }}">
                <!-- Top Section: Standings Table -->
                <div class="overflow-x-auto w-full">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-purple-500/50">
                                <th class="text-left py-2 px-3 text-gray-400 font-bold text-sm">#</th>
                                <th class="text-left py-2 px-3 text-gray-400 font-bold text-sm">Igrač</th>
                                <th class="text-center py-2 px-2 text-gray-400 font-bold text-xs">O</th>
                                <th class="text-center py-2 px-2 text-gray-400 font-bold text-xs">P</th>
                                <th class="text-center py-2 px-2 text-gray-400 font-bold text-xs">I</th>
                                <th class="text-center py-2 px-2 text-gray-400 font-bold text-xs">S+-</th>
                                <th class="text-center py-2 px-2 text-gray-400 font-bold text-xs">Poeni</th>
                                <th class="text-center py-2 px-2 text-gray-400 font-bold text-sm">Bod</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupStandings as $standing)
                            <tr class="border-b border-gray-700/30 hover:bg-gray-700/20 transition-colors">
                                <td class="py-2 px-3">
                                    <div class="flex items-center gap-1">
                                        @if($standing->position <= 2)
                                            <span class="text-xl">
                                                @if($standing->position == 1) 🥇
                                                @else 🥈
                                                @endif
                                            </span>
                                        @endif
                                        <span class="text-white font-bold text-base">{{ $standing->position }}</span>
                                    </div>
                                </td>
                                <td class="py-2 px-3">
                                    <span class="player-name text-white font-semibold">
                                        {{ $standing->player->name ?? ($standing->team->name ?? 'TBD') }}
                                    </span>
                                </td>
                                <td class="py-2 px-2 text-center text-gray-300 text-sm">{{ $standing->played ?? 0 }}</td>
                                <td class="py-2 px-2 text-center text-green-400 font-bold text-sm">{{ $standing->won ?? 0 }}</td>
                                <td class="py-2 px-2 text-center text-red-400 font-bold text-sm">{{ $standing->lost ?? 0 }}</td>
                                <td class="py-2 px-2 text-center text-blue-300 text-sm">{{ ($standing->sets_won ?? 0) - ($standing->sets_lost ?? 0) }}</td>
                                <td class="py-2 px-2 text-center text-orange-300 text-sm">{{ ($standing->points_won ?? 0) - ($standing->points_lost ?? 0) }}</td>
                                <td class="py-2 px-2 text-center">
                                    <span class="text-purple-400 font-black text-lg">{{ $standing->points ?? 0 }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Bottom Section: Matches -->
                @if($group->matches && count($group->matches) > 0)
                <div class="w-full">
                    <h4 class="text-lg font-bold text-purple-400 mb-3 flex items-center gap-2">
                        <span>🏓</span>
                        <span>Rezultati mečeva u grupi</span>
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-3 pr-2">
                        @foreach($group->matches as $match)
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-lg p-3 border border-gray-700/50 hover:border-purple-500/50 transition-all relative">
                            @if($match->status === 'live')
                                <div class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-ping"></div>
                            @endif
                            
                            <div class="flex items-center gap-2">
                                <!-- Home Player/Team -->
                                <div class="flex-1 text-right overflow-hidden">
                                    <span class="player-name truncate block font-semibold {{ ($match->home_score ?? 0) > ($match->away_score ?? 0) ? 'text-green-500 font-bold' : 'text-white' }}">
                                        @if($competition->is_team_based)
                                            {{ $match->homeTeam ? $match->homeTeam->name : 'TBD' }}
                                        @else
                                            {{ $match->homePlayer ? $match->homePlayer->name : 'TBD' }}
                                        @endif
                                    </span>
                                </div>
                                
                                <!-- Score -->
                                <div class="flex-shrink-0 w-16 text-center">
                                    @if($match->status === 'completed')
                                        <div class="flex items-center justify-center gap-1">
                                            <span class="text-base font-black {{ ($match->home_score ?? 0) > ($match->away_score ?? 0) ? 'text-green-500' : 'text-gray-400' }}">
                                                {{ $match->home_score ?? 0 }}
                                            </span>
                                            <span class="text-gray-500">:</span>
                                            <span class="text-base font-black {{ ($match->away_score ?? 0) > ($match->home_score ?? 0) ? 'text-green-500' : 'text-gray-400' }}">
                                                {{ $match->away_score ?? 0 }}
                                            </span>
                                        </div>
                                    @elseif($match->status === 'live')
                                        <span class="text-red-500 font-bold text-[10px] animate-pulse">⚡ LIVE</span>
                                    @elseif($match->scheduled_at)
                                        <span class="text-gray-500 text-[10px]">{{ $match->scheduled_at->format('H:i') }}</span>
                                    @else
                                        <span class="text-gray-600 text-[10px]">-</span>
                                    @endif
                                </div>
                                
                                <!-- Away Player/Team -->
                                <div class="flex-1 text-left overflow-hidden">
                                    <span class="player-name truncate block font-semibold {{ ($match->away_score ?? 0) > ($match->home_score ?? 0) ? 'text-green-500 font-bold' : 'text-white' }}">
                                        @if($competition->is_team_based)
                                            {{ $match->awayTeam ? $match->awayTeam->name : 'TBD' }}
                                        @else
                                            {{ $match->awayPlayer ? $match->awayPlayer->name : 'TBD' }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="flex items-center justify-center h-full">
                    <p class="text-gray-500 text-lg">Nema mečeva</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    @endforeach

    <!-- Legend -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 border border-gray-700/50">
        <div class="flex flex-wrap gap-6 justify-center text-sm">
            <div class="flex items-center gap-2">
                <span class="text-gray-400">O:</span>
                <span class="text-gray-300">Odigrano</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-400">P:</span>
                <span class="text-gray-300">Pobjeda</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-red-400">I:</span>
                <span class="text-gray-300">Izgubljeno</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-blue-300">S+-:</span>
                <span class="text-gray-300">Razlika setova</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-orange-300">Poeni:</span>
                <span class="text-gray-300">Razlika poena</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-purple-400">Bod:</span>
                <span class="text-gray-300">Bodovi</span>
            </div>
        </div>
    </div>
</div>
@else
<div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-12 border border-gray-700/50 shadow-2xl text-center">
    <p class="text-gray-400 text-2xl">Grupna faza nije dostupna</p>
</div>
@endif
