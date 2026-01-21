<!-- All Tournament Groups Grid Display -->
<div class="px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($competition->tournamentGroups as $group)
            @php
                $groupStandings = \App\Models\Standing::where('tournament_group_id', $group->id)
                    ->orderBy('position')
                    ->with('player')
                    ->get();
                    
                $groupMatches = $group->matches()
                    ->with(['homePlayer', 'awayPlayer'])
                    ->orderBy('round_number')
                    ->orderBy('match_order')
                    ->get();
            @endphp
            
            <div class="rounded-xl p-6 border shadow-xl" style="background: var(--bg-card); border-color: var(--border-primary);">
                <!-- Group Header -->
                <h3 class="text-2xl font-bold text-center mb-6" style="color: var(--text-primary);">
                    Grupa {{ $group->name }}
                </h3>

                <!-- Tabela -->
                @if($groupStandings->count() > 0)
                <div class="mb-6">
                    <h5 class="text-lg font-semibold mb-3 uppercase tracking-wide" style="color: var(--text-secondary);">Tabela</h5>
                    
                    <!-- Table Header -->
                    <div class="grid grid-cols-12 gap-2 mb-2 text-xs font-medium px-2" style="color: var(--text-secondary);">
                        <div class="col-span-6"></div>
                        <div class="col-span-1 text-center">P</div>
                        <div class="col-span-1 text-center">I</div>
                        <div class="col-span-1 text-center">Set±</div>
                        <div class="col-span-1 text-center">Gem±</div>
                        <div class="col-span-2 text-center">B</div>
                    </div>
                    
                    <!-- Table Rows -->
                    <div class="space-y-1">
                        @php
                            $advancingPlayers = $competition->players_advancing_per_group ?? 2;
                        @endphp
                        @foreach($groupStandings as $index => $standing)
                        <div class="grid grid-cols-12 gap-2 items-center py-2 px-2 rounded text-sm transition-all duration-200"
                             style="background: {{ $index < $advancingPlayers ? 'rgba(16, 185, 129, 0.1)' : 'var(--bg-tertiary)' }};">
                            <div class="col-span-6 flex items-center space-x-2">
                                <span class="font-bold w-6 text-center" style="color: var(--text-primary);">{{ $index + 1 }}</span>
                                <span class="font-medium truncate" style="color: var(--text-primary);">
                                    {{ $standing->player->name }}
                                    @if($standing->player->position)
                                        <span class="text-xs" style="color: var(--text-tertiary);">({{ $standing->player->position }})</span>
                                    @endif
                                </span>
                            </div>
                            <div class="col-span-1 text-center" style="color: var(--text-primary);">{{ $standing->won ?? 0 }}</div>
                            <div class="col-span-1 text-center" style="color: var(--text-primary);">{{ $standing->lost ?? 0 }}</div>
                            <div class="col-span-1 text-center" style="color: var(--text-primary);">{{ ($standing->sets_won ?? 0) - ($standing->sets_lost ?? 0) }}</div>
                            <div class="col-span-1 text-center">
                                @php $gemDiff = ($standing->points_won ?? 0) - ($standing->points_lost ?? 0); @endphp
                                <span class="{{ $gemDiff >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $gemDiff >= 0 ? '+' : '' }}{{ $gemDiff }}
                                </span>
                            </div>
                            <div class="col-span-2 text-center font-bold" style="color: var(--text-primary);">{{ $standing->points ?? 0 }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Mečevi -->
                @if($groupMatches->count() > 0)
                <div>
                    <h5 class="text-lg font-semibold mb-3 uppercase tracking-wide" style="color: var(--text-secondary);">Mečevi</h5>
                    <div class="space-y-2">
                        @foreach($groupMatches as $match)
                        <div class="rounded-lg p-3 transition-all duration-200" style="background: var(--bg-tertiary);">
                            @if($match->status === 'in_progress')
                            <div class="text-center mb-2">
                                <span class="text-red-400 font-semibold text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                    UŽIVO
                                </span>
                            </div>
                            @endif

                            <div class="flex items-center justify-between">
                                <!-- Home Player -->
                                <div class="flex-1 text-left">
                                    <span class="font-medium {{ $match->winner_id === $match->home_player_id ? 'font-bold' : '' }}"
                                          style="color: var(--text-primary);">
                                        {{ $match->homePlayer->name ?? 'TBD' }}
                                        @if($match->homePlayer && $match->homePlayer->position)
                                            <span class="text-xs" style="color: var(--text-tertiary);">({{ $match->homePlayer->position }})</span>
                                        @endif
                                    </span>
                                </div>

                                <!-- Score -->
                                <div class="flex items-center gap-3 px-4">
                                    <span class="text-xl font-bold {{ $match->winner_id === $match->home_player_id ? 'text-green-400' : '' }}"
                                          style="{{ $match->winner_id !== $match->home_player_id ? 'color: var(--text-primary);' : '' }}">
                                        {{ $match->home_score ?? 0 }}
                                    </span>
                                    <span style="color: var(--text-tertiary);">-</span>
                                    <span class="text-xl font-bold {{ $match->winner_id === $match->away_player_id ? 'text-green-400' : '' }}"
                                          style="{{ $match->winner_id !== $match->away_player_id ? 'color: var(--text-primary);' : '' }}">
                                        {{ $match->away_score ?? 0 }}
                                    </span>
                                </div>

                                <!-- Away Player -->
                                <div class="flex-1 text-right">
                                    <span class="font-medium {{ $match->winner_id === $match->away_player_id ? 'font-bold' : '' }}"
                                          style="color: var(--text-primary);">
                                        {{ $match->awayPlayer->name ?? 'TBD' }}
                                        @if($match->awayPlayer && $match->awayPlayer->position)
                                            <span class="text-xs" style="color: var(--text-tertiary);">({{ $match->awayPlayer->position }})</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Match Info -->
                            @if($match->status === 'completed' && $match->played_at)
                            <div class="text-center mt-2">
                                <span class="text-xs" style="color: var(--text-tertiary);">
                                    {{ \Carbon\Carbon::parse($match->played_at)->format('d.m.Y H:i') }}
                                </span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
