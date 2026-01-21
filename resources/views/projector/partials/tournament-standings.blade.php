<!-- Tournament Standings for Projector -->
@php
    // If a specific group is selected, show only that group
    $groupsToDisplay = isset($selectedGroup) && $selectedGroup 
        ? collect([$selectedGroup]) 
        : collect($competition->tournamentGroups ?? []);
@endphp

@if($groupsToDisplay->isNotEmpty())
<div class="space-y-8">
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
            <!-- Group Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-white flex items-center gap-3">
                    <span class="text-4xl">🏆</span>
                    Grupa {{ $group->name }} - {{ $competition->name }}
                </h2>
            </div>
            
            <!-- Two Column Layout: Table Left, Matches Right -->
            <div class="grid grid-cols-2 gap-8">
                <!-- Left Column: Standings Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-purple-500/50">
                                <th class="text-left py-3 px-3 text-gray-400 font-bold text-base">#</th>
                                <th class="text-left py-3 px-3 text-gray-400 font-bold text-base">Igrač</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-bold text-sm">O</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-bold text-sm">P</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-bold text-sm">N</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-bold text-sm">I</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-bold text-sm">S+</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-bold text-sm">S-</th>
                                <th class="text-center py-3 px-2 text-gray-400 font-bold text-base">Bod</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupStandings as $standing)
                            <tr class="border-b border-gray-700/30 hover:bg-gray-700/20 transition-colors">
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-1">
                                        @if($standing->position <= 2)
                                            <span class="text-2xl">
                                                @if($standing->position == 1) 🥇
                                                @else 🥈
                                                @endif
                                            </span>
                                        @endif
                                        <span class="text-white font-bold text-lg">{{ $standing->position }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="text-white font-semibold text-base">
                                        {{ $standing->player->name ?? ($standing->team->name ?? 'TBD') }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-center text-gray-300 text-base">{{ $standing->played ?? 0 }}</td>
                                <td class="py-3 px-2 text-center text-green-400 font-bold text-base">{{ $standing->won ?? 0 }}</td>
                                <td class="py-3 px-2 text-center text-yellow-400 font-bold text-base">{{ $standing->drawn ?? 0 }}</td>
                                <td class="py-3 px-2 text-center text-red-400 font-bold text-base">{{ $standing->lost ?? 0 }}</td>
                                <td class="py-3 px-2 text-center text-blue-300 text-base">{{ $standing->sets_won ?? 0 }}</td>
                                <td class="py-3 px-2 text-center text-orange-300 text-base">{{ $standing->sets_lost ?? 0 }}</td>
                                <td class="py-3 px-2 text-center">
                                    <span class="text-purple-400 font-black text-xl">{{ $standing->points ?? 0 }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Right Column: Matches -->
                @if($group->matches && count($group->matches) > 0)
                <div class="overflow-y-auto max-h-[calc(100vh-20rem)]">
                    <h4 class="text-xl font-bold text-purple-400 mb-4 flex items-center gap-2 sticky top-0 bg-gray-800/90 backdrop-blur-xl py-2 z-10">
                        <span>🏓</span>
                        <span>Mečevi</span>
                    </h4>
                    <div class="space-y-3 pr-2">
                        @foreach($group->matches as $match)
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-lg p-3 border border-gray-700/50 hover:border-purple-500/50 transition-all">
                            <div class="flex items-center gap-3">
                                <!-- Home Player/Team -->
                                <div class="flex-1 text-right">
                                    <span class="text-white font-semibold text-sm">
                                        @if($competition->is_team_based)
                                            {{ $match->homeTeam ? $match->homeTeam->name : 'TBD' }}
                                        @else
                                            {{ $match->homePlayer ? $match->homePlayer->name : 'TBD' }}
                                        @endif
                                    </span>
                                </div>
                                
                                <!-- Score -->
                                <div class="flex-shrink-0 w-20 text-center">
                                    @if($match->status === 'completed')
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="text-xl font-black {{ ($match->home_score ?? 0) > ($match->away_score ?? 0) ? 'text-green-400' : 'text-gray-400' }}">
                                                {{ $match->home_score ?? 0 }}
                                            </span>
                                            <span class="text-gray-500">:</span>
                                            <span class="text-xl font-black {{ ($match->away_score ?? 0) > ($match->home_score ?? 0) ? 'text-green-400' : 'text-gray-400' }}">
                                                {{ $match->away_score ?? 0 }}
                                            </span>
                                        </div>
                                    @elseif($match->status === 'live')
                                        <span class="text-red-500 font-bold text-xs animate-pulse">⚡ LIVE</span>
                                    @elseif($match->scheduled_at)
                                        <span class="text-gray-500 text-xs">{{ $match->scheduled_at->format('H:i') }}</span>
                                    @else
                                        <span class="text-gray-600 text-xs">-</span>
                                    @endif
                                </div>
                                
                                <!-- Away Player/Team -->
                                <div class="flex-1 text-left">
                                    <span class="text-white font-semibold text-sm">
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
                <span class="text-yellow-400">N:</span>
                <span class="text-gray-300">Neriješeno</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-red-400">I:</span>
                <span class="text-gray-300">Izgubljeno</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-blue-300">S+:</span>
                <span class="text-gray-300">Dobijeni setovi</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-orange-300">S-:</span>
                <span class="text-gray-300">Izgubljeni setovi</span>
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
